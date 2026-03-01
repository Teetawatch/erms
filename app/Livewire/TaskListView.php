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
    public $selectedTasks = [];
    public $selectAll = false;
    public $bulkStatus = '';

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

    public function updatedSelectAll()
    {
        if ($this->selectAll) {
            $this->selectedTasks = $this->tasks->pluck('id')->map(fn($id) => (string) $id)->toArray();
        } else {
            $this->selectedTasks = [];
        }
    }

    public function bulkStatusChange()
    {
        if (empty($this->selectedTasks) || !$this->bulkStatus) return;

        $tasks = Task::whereIn('id', $this->selectedTasks)->get();
        foreach ($tasks as $task) {
            $oldStatus = $task->status;
            $task->update(['status' => $this->bulkStatus]);

            \App\Models\TaskUpdate::create([
                'task_id' => $task->id,
                'user_id' => auth()->id(),
                'old_status' => $oldStatus,
                'new_status' => $this->bulkStatus,
                'note' => 'เปลี่ยนแบบกลุ่ม',
            ]);

            if ($oldStatus !== $this->bulkStatus && $task->assigned_to && $task->assigned_to !== auth()->id()) {
                $task->assignee?->notify(new TaskStatusChanged($task, $oldStatus, $this->bulkStatus, auth()->user()->name));
            }
        }

        $this->dispatch('toast', message: 'เปลี่ยนสถานะงาน ' . count($this->selectedTasks) . ' รายการสำเร็จ', type: 'success');
        $this->reset(['selectedTasks', 'selectAll', 'bulkStatus']);
    }

    public function render()
    {
        return view('livewire.task-list-view');
    }
}
