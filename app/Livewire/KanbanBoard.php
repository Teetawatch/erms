<?php

namespace App\Livewire;

use App\Models\Project;
use App\Models\Task;
use App\Models\TaskUpdate;
use Livewire\Component;

class KanbanBoard extends Component
{
    public $projectId = null;
    public $showCreateModal = false;
    public $editingTask = null;

    public $newTitle = '';
    public $newDescription = '';
    public $newPriority = 'medium';
    public $newAssignedTo = '';
    public $newDueDate = '';
    public $newProjectId = '';
    public $newStatus = 'todo';

    protected $listeners = ['taskMoved'];

    public function mount($projectId = null)
    {
        $this->projectId = $projectId;
    }

    public function getTasksProperty()
    {
        $user = auth()->user();
        $query = Task::with(['assignee', 'project']);

        if ($this->projectId) {
            $query->where('project_id', $this->projectId);
        } elseif (!$user->hasRole('admin')) {
            $query->where('assigned_to', $user->id);
        }

        return $query->orderBy('sort_order')->get()->groupBy('status');
    }

    public function getProjectsProperty()
    {
        $user = auth()->user();
        return $user->hasRole('admin')
            ? Project::select('id', 'name')->get()
            : $user->projects()->select('projects.id', 'projects.name')->get();
    }

    public function getUsersProperty()
    {
        return \App\Models\User::select('id', 'name')->get();
    }

    public function openCreateModal($status = 'todo')
    {
        $this->resetForm();
        $this->newStatus = $status;
        $this->newProjectId = $this->projectId ?? '';
        $this->showCreateModal = true;
    }

    public function createTask()
    {
        $this->validate([
            'newTitle' => 'required|string|max:255',
            'newProjectId' => 'required|exists:projects,id',
            'newPriority' => 'required|in:low,medium,high,urgent',
            'newAssignedTo' => 'nullable|exists:users,id',
            'newDueDate' => 'nullable|date',
        ]);

        $task = Task::create([
            'project_id' => $this->newProjectId,
            'title' => $this->newTitle,
            'description' => $this->newDescription,
            'status' => $this->newStatus,
            'priority' => $this->newPriority,
            'assigned_to' => $this->newAssignedTo ?: null,
            'due_date' => $this->newDueDate ?: null,
            'sort_order' => Task::where('status', $this->newStatus)->max('sort_order') + 1,
        ]);

        TaskUpdate::create([
            'task_id' => $task->id,
            'user_id' => auth()->id(),
            'old_status' => null,
            'new_status' => $this->newStatus,
            'note' => 'สร้างงานใหม่',
        ]);

        $this->showCreateModal = false;
        $this->resetForm();
    }

    public function taskMoved($taskId, $newStatus, $newOrder = 0)
    {
        $task = Task::find($taskId);
        if (!$task) return;

        $oldStatus = $task->status;
        if ($oldStatus !== $newStatus) {
            TaskUpdate::create([
                'task_id' => $task->id,
                'user_id' => auth()->id(),
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'note' => null,
            ]);
        }

        $task->update([
            'status' => $newStatus,
            'sort_order' => $newOrder,
        ]);
    }

    public function deleteTask($taskId)
    {
        Task::find($taskId)?->delete();
    }

    private function resetForm()
    {
        $this->newTitle = '';
        $this->newDescription = '';
        $this->newPriority = 'medium';
        $this->newAssignedTo = '';
        $this->newDueDate = '';
        $this->newProjectId = $this->projectId ?? '';
        $this->newStatus = 'todo';
    }

    public function render()
    {
        return view('livewire.kanban-board');
    }
}
