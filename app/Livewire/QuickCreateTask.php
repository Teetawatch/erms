<?php

namespace App\Livewire;

use App\Models\Project;
use App\Models\Task;
use App\Models\TaskUpdate;
use App\Models\User;
use Livewire\Component;

class QuickCreateTask extends Component
{
    public $showModal = false;

    public $title = '';
    public $projectId = '';
    public $assignedTo = '';
    public $priority = 'medium';
    public $dueDate = '';
    public $description = '';

    protected $listeners = ['open-quick-create' => 'openModal'];

    protected $rules = [
        'title' => 'required|string|max:255',
        'projectId' => 'required|exists:projects,id',
        'assignedTo' => 'nullable|exists:users,id',
        'priority' => 'required|in:low,medium,high,urgent',
        'dueDate' => 'nullable|date',
        'description' => 'nullable|string|max:2000',
    ];

    protected $messages = [
        'title.required' => 'กรุณากรอกชื่องาน',
        'projectId.required' => 'กรุณาเลือกโครงการ',
    ];

    public function openModal()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function createTask()
    {
        $this->validate();

        $task = Task::create([
            'project_id' => $this->projectId,
            'title' => $this->title,
            'description' => $this->description ?: null,
            'status' => 'todo',
            'priority' => $this->priority,
            'assigned_to' => $this->assignedTo ?: null,
            'due_date' => $this->dueDate ?: null,
            'sort_order' => Task::where('project_id', $this->projectId)->where('status', 'todo')->max('sort_order') + 1,
        ]);

        TaskUpdate::create([
            'task_id' => $task->id,
            'user_id' => auth()->id(),
            'old_status' => null,
            'new_status' => 'todo',
            'note' => 'สร้างงานใหม่',
        ]);

        $this->showModal = false;
        $this->resetForm();

        $this->dispatch('toast', message: 'สร้างงานสำเร็จ', type: 'success');
        $this->dispatch('task-created');
    }

    public function getProjectsProperty()
    {
        $user = auth()->user();
        return $user->hasRole('admin')
            ? Project::select('id', 'name')->orderBy('name')->get()
            : $user->projects()->select('projects.id', 'projects.name')->orderBy('projects.name')->get();
    }

    public function getUsersProperty()
    {
        return User::select('id', 'name')->orderBy('name')->get();
    }

    private function resetForm()
    {
        $this->title = '';
        $this->projectId = '';
        $this->assignedTo = '';
        $this->priority = 'medium';
        $this->dueDate = '';
        $this->description = '';
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.quick-create-task');
    }
}
