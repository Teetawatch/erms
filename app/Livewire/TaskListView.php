<?php

namespace App\Livewire;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Notifications\TaskStatusChanged;
use Livewire\Component;
use Livewire\WithPagination;

class TaskListView extends Component
{
    use WithPagination;

    public $projectId = null;
    public $search = '';
    public $filterStatus = '';
    public $filterPriority = '';
    public $filterAssignee = '';
    public $sortBy = 'created_at';
    public $sortDir = 'desc';

    protected $queryString = [
        'search' => ['except' => ''],
        'filterStatus' => ['except' => ''],
        'filterPriority' => ['except' => ''],
        'filterAssignee' => ['except' => ''],
        'sortBy' => ['except' => 'created_at'],
        'sortDir' => ['except' => 'desc'],
    ];

    public function mount($projectId = null)
    {
        $this->projectId = $projectId;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function sort($column)
    {
        if ($this->sortBy === $column) {
            $this->sortDir = $this->sortDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDir = 'asc';
        }
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

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'like', "%{$this->search}%")
                  ->orWhere('description', 'like', "%{$this->search}%");
            });
        }

        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        if ($this->filterPriority) {
            $query->where('priority', $this->filterPriority);
        }

        if ($this->filterAssignee) {
            $query->where('assigned_to', $this->filterAssignee);
        }

        return $query->orderBy($this->sortBy, $this->sortDir)->paginate(20);
    }

    public function getUsersProperty()
    {
        return User::select('id', 'name')->get();
    }

    public function quickStatusChange($taskId, $status)
    {
        $task = Task::find($taskId);
        if (!$task) return;

        $oldStatus = $task->status;
        $task->update(['status' => $status]);

        \App\Models\TaskUpdate::create([
            'task_id' => $task->id,
            'user_id' => auth()->id(),
            'old_status' => $oldStatus,
            'new_status' => $status,
            'note' => null,
        ]);

        if ($oldStatus !== $status && $task->assigned_to && $task->assigned_to !== auth()->id()) {
            $task->assignee?->notify(new TaskStatusChanged($task, $oldStatus, $status, auth()->user()->name));
        }
    }

    public function render()
    {
        return view('livewire.task-list-view');
    }
}
