<?php

namespace App\Livewire;

use App\Models\Task;
use App\Models\WorkLog;
use Livewire\Component;

class WorkLogForm extends Component
{
    public $task_id = '';
    public $date = '';
    public $hours = '';
    public $description = '';
    public $editingId = null;

    public function mount()
    {
        $this->date = now()->format('Y-m-d');
    }

    public function getTasksProperty()
    {
        $user = auth()->user();
        return $user->hasRole('admin')
            ? Task::with('project')->whereIn('status', ['todo', 'in_progress', 'review'])->get()
            : $user->assignedTasks()->with('project')->whereIn('status', ['todo', 'in_progress', 'review'])->get();
    }

    public function getTodayLogsProperty()
    {
        return WorkLog::with('task.project')
            ->where('user_id', auth()->id())
            ->whereDate('date', today())
            ->latest()
            ->get();
    }

    public function getWeekHoursProperty()
    {
        return WorkLog::where('user_id', auth()->id())
            ->whereBetween('date', [now()->startOfWeek(), now()->endOfWeek()])
            ->sum('hours');
    }

    public function save()
    {
        $this->validate([
            'task_id' => 'required|exists:tasks,id',
            'date' => 'required|date',
            'hours' => 'required|numeric|min:0.25|max:24',
            'description' => 'nullable|string|max:1000',
        ]);

        if ($this->editingId) {
            $workLog = WorkLog::findOrFail($this->editingId);
            $workLog->update([
                'task_id' => $this->task_id,
                'date' => $this->date,
                'hours' => $this->hours,
                'description' => $this->description,
            ]);
        } else {
            WorkLog::create([
                'user_id' => auth()->id(),
                'task_id' => $this->task_id,
                'date' => $this->date,
                'hours' => $this->hours,
                'description' => $this->description,
            ]);
        }

        $this->reset(['task_id', 'hours', 'description', 'editingId']);
        $this->date = now()->format('Y-m-d');
    }

    public function edit($id)
    {
        $log = WorkLog::findOrFail($id);
        $this->editingId = $log->id;
        $this->task_id = $log->task_id;
        $this->date = $log->date->format('Y-m-d');
        $this->hours = $log->hours;
        $this->description = $log->description;
    }

    public function delete($id)
    {
        WorkLog::where('id', $id)->where('user_id', auth()->id())->delete();
    }

    public function cancelEdit()
    {
        $this->reset(['task_id', 'hours', 'description', 'editingId']);
        $this->date = now()->format('Y-m-d');
    }

    public function render()
    {
        return view('livewire.work-log-form');
    }
}
