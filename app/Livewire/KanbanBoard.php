<?php

namespace App\Livewire;

use App\Models\Project;
use App\Models\Task;
use App\Models\TaskUpdate;
use App\Models\User;
use App\Notifications\TaskAssigned;
use App\Notifications\TaskStatusChanged;
use Livewire\Component;

class KanbanBoard extends Component
{
    public $projectId = null;
    public $showCreateModal = false;
    public $editingTask = null;

    // Filters
    public $filterPriority = '';
    public $filterAssignee = '';
    public $searchQuery = '';

    public $newTitle = '';
    public $newDescription = '';
    public $newPriority = 'medium';
    public $newAssignedTo = '';
    public $newDueDate = '';
    public $newStartDate = '';
    public $newEstimatedHours = '';
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
        $query = Task::with(['assignee', 'project', 'subtasks'])
            ->whereNull('parent_id');

        if ($this->projectId) {
            $query->where('project_id', $this->projectId);
        } elseif (!$user->hasRole('admin')) {
            $query->where('assigned_to', $user->id);
        }

        if ($this->filterPriority) {
            $query->where('priority', $this->filterPriority);
        }

        if ($this->filterAssignee) {
            $query->where('assigned_to', $this->filterAssignee);
        }

        if ($this->searchQuery) {
            $query->where('title', 'like', '%' . $this->searchQuery . '%');
        }

        return $query->orderBy('sort_order')->get()->groupBy('status');
    }

    public function clearFilters()
    {
        $this->filterPriority = '';
        $this->filterAssignee = '';
        $this->searchQuery = '';
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
            'newStartDate' => 'nullable|date',
            'newDueDate' => 'nullable|date',
            'newEstimatedHours' => 'nullable|integer|min:0',
        ]);

        $task = Task::create([
            'project_id' => $this->newProjectId,
            'title' => $this->newTitle,
            'description' => $this->newDescription,
            'status' => $this->newStatus,
            'priority' => $this->newPriority,
            'assigned_to' => $this->newAssignedTo ?: null,
            'start_date' => $this->newStartDate ?: null,
            'due_date' => $this->newDueDate ?: null,
            'estimated_hours' => $this->newEstimatedHours ?: null,
            'sort_order' => Task::where('status', $this->newStatus)->max('sort_order') + 1,
        ]);

        TaskUpdate::create([
            'task_id' => $task->id,
            'user_id' => auth()->id(),
            'old_status' => null,
            'new_status' => $this->newStatus,
            'note' => 'สร้างงานใหม่',
        ]);

        // Notify assigned user
        if ($task->assigned_to && $task->assigned_to !== auth()->id()) {
            User::find($task->assigned_to)?->notify(new TaskAssigned($task));
        }

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

        // Notify assigned user about status change
        if ($oldStatus !== $newStatus && $task->assigned_to && $task->assigned_to !== auth()->id()) {
            $task->assignee?->notify(new TaskStatusChanged($task, $oldStatus, $newStatus, auth()->user()->name));
        }
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
        $this->newStartDate = '';
        $this->newDueDate = '';
        $this->newEstimatedHours = '';
        $this->newProjectId = $this->projectId ?? '';
        $this->newStatus = 'todo';
    }

    public function render()
    {
        return view('livewire.kanban-board');
    }
}
