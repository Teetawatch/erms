<?php

namespace App\Livewire;

use App\Models\Task;
use Livewire\Component;

class TimelineView extends Component
{
    public $projectId = null;
    public $filterStatus = '';
    public $filterAssignee = '';

    public function mount($projectId = null)
    {
        $this->projectId = $projectId;
    }

    public function getTasksProperty()
    {
        $user = auth()->user();
        $query = Task::with(['assignee', 'project', 'dependencies.dependsOnTask', 'subtasks'])
            ->whereNull('parent_id')
            ->whereNotNull('start_date')
            ->whereNotNull('due_date');

        if ($this->projectId) {
            $query->where('project_id', $this->projectId);
        } elseif (!$user->hasRole('admin')) {
            $query->where('assigned_to', $user->id);
        }

        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        if ($this->filterAssignee) {
            $query->where('assigned_to', $this->filterAssignee);
        }

        return $query->orderBy('start_date')->get();
    }

    public function getTimelineDataProperty()
    {
        $tasks = $this->tasks;
        if ($tasks->isEmpty()) return ['tasks' => [], 'minDate' => now()->format('Y-m-d'), 'maxDate' => now()->addMonth()->format('Y-m-d'), 'totalDays' => 30];

        $minDate = $tasks->min('start_date');
        $maxDate = $tasks->max('due_date');

        $totalDays = max($minDate->diffInDays($maxDate) + 1, 1);

        return [
            'tasks' => $tasks->map(function ($task) use ($minDate, $totalDays) {
                $startOffset = $minDate->diffInDays($task->start_date);
                $duration = max($task->start_date->diffInDays($task->due_date) + 1, 1);
                $leftPercent = ($startOffset / $totalDays) * 100;
                $widthPercent = ($duration / $totalDays) * 100;

                return [
                    'id' => $task->id,
                    'title' => $task->title,
                    'status' => $task->status,
                    'priority' => $task->priority,
                    'progress' => $task->progress,
                    'assignee' => $task->assignee?->name,
                    'assignee_avatar' => $task->assignee?->avatar_url,
                    'start_date' => $task->start_date->format('Y-m-d'),
                    'due_date' => $task->due_date->format('Y-m-d'),
                    'left' => round($leftPercent, 2),
                    'width' => round($widthPercent, 2),
                    'dependencies' => $task->dependencies->pluck('depends_on_task_id')->toArray(),
                    'subtask_count' => $task->subtasks->count(),
                    'subtask_done' => $task->subtasks->where('status', 'done')->count(),
                    'url' => route('tasks.show', $task),
                ];
            })->values()->toArray(),
            'minDate' => $minDate->format('Y-m-d'),
            'maxDate' => $maxDate->format('Y-m-d'),
            'totalDays' => $totalDays,
        ];
    }

    public function getUsersProperty()
    {
        return \App\Models\User::select('id', 'name')->get();
    }

    public function render()
    {
        return view('livewire.timeline-view');
    }
}
