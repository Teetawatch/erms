<?php

namespace App\Livewire;

use App\Models\Task;
use App\Models\TaskTemplate;
use App\Models\TaskUpdate;
use Livewire\Component;

class TaskTemplateManager extends Component
{
    public $showCreateModal = false;
    public $showUseModal = false;
    public $selectedTemplate = null;

    public $name = '';
    public $description = '';
    public $isGlobal = false;

    // For using template
    public $useProjectId = '';
    public $useAssignedTo = '';

    public function createTemplate()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        TaskTemplate::create([
            'name' => $this->name,
            'description' => $this->description,
            'task_data' => [
                'title' => $this->name,
                'description' => $this->description,
                'priority' => 'medium',
                'status' => 'todo',
            ],
            'created_by' => auth()->id(),
            'is_global' => $this->isGlobal,
        ]);

        $this->reset(['name', 'description', 'isGlobal', 'showCreateModal']);
    }

    public function createFromTask($taskId)
    {
        $task = Task::with('subtasks')->find($taskId);
        if (!$task) return;

        $subtasksData = $task->subtasks->map(fn($s) => [
            'title' => $s->title,
            'priority' => $s->priority,
        ])->toArray();

        TaskTemplate::create([
            'name' => $task->title . ' (เทมเพลต)',
            'description' => $task->description,
            'task_data' => [
                'title' => $task->title,
                'description' => $task->description,
                'priority' => $task->priority,
                'estimated_hours' => $task->estimated_hours,
                'tags' => $task->tags,
                'subtasks' => $subtasksData,
            ],
            'created_by' => auth()->id(),
            'is_global' => false,
        ]);
    }

    public function openUseModal($templateId)
    {
        $this->selectedTemplate = TaskTemplate::find($templateId);
        $this->showUseModal = true;
    }

    public function useTemplate()
    {
        $this->validate([
            'useProjectId' => 'required|exists:projects,id',
        ]);

        if (!$this->selectedTemplate) return;

        $data = $this->selectedTemplate->task_data;

        $task = Task::create([
            'project_id' => $this->useProjectId,
            'title' => $data['title'] ?? $this->selectedTemplate->name,
            'description' => $data['description'] ?? null,
            'priority' => $data['priority'] ?? 'medium',
            'status' => 'todo',
            'assigned_to' => $this->useAssignedTo ?: null,
            'estimated_hours' => $data['estimated_hours'] ?? null,
            'tags' => $data['tags'] ?? null,
            'sort_order' => Task::where('project_id', $this->useProjectId)->max('sort_order') + 1,
        ]);

        TaskUpdate::create([
            'task_id' => $task->id,
            'user_id' => auth()->id(),
            'old_status' => null,
            'new_status' => 'todo',
            'note' => 'สร้างจากเทมเพลต: ' . $this->selectedTemplate->name,
        ]);

        if (!empty($data['subtasks'])) {
            foreach ($data['subtasks'] as $i => $sub) {
                Task::create([
                    'project_id' => $this->useProjectId,
                    'parent_id' => $task->id,
                    'title' => $sub['title'],
                    'priority' => $sub['priority'] ?? 'medium',
                    'status' => 'todo',
                    'assigned_to' => $this->useAssignedTo ?: null,
                    'sort_order' => $i,
                ]);
            }
        }

        $this->reset(['useProjectId', 'useAssignedTo', 'showUseModal', 'selectedTemplate']);
    }

    public function deleteTemplate($templateId)
    {
        TaskTemplate::where('id', $templateId)
            ->where(function ($q) {
                $q->where('created_by', auth()->id());
                if (auth()->user()->hasRole('admin')) {
                    $q->orWhereNotNull('id');
                }
            })->delete();
    }

    public function getTemplatesProperty()
    {
        $user = auth()->user();
        return TaskTemplate::where('is_global', true)
            ->orWhere('created_by', $user->id)
            ->with('creator')
            ->latest()
            ->get();
    }

    public function getProjectsProperty()
    {
        $user = auth()->user();
        return $user->hasRole('admin')
            ? \App\Models\Project::select('id', 'name')->get()
            : $user->projects()->select('projects.id', 'projects.name')->get();
    }

    public function getUsersProperty()
    {
        return \App\Models\User::select('id', 'name')->get();
    }

    public function render()
    {
        return view('livewire.task-template-manager');
    }
}
