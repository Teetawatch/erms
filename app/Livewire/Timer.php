<?php

namespace App\Livewire;

use App\Models\Task;
use App\Models\WorkLog;
use Livewire\Component;

class Timer extends Component
{
    public $isRunning = false;
    public $selectedTaskId = '';
    public $startTime = null;
    public $elapsedSeconds = 0;
    public $description = '';

    public function getTasksProperty()
    {
        $user = auth()->user();
        return $user->hasRole('admin')
            ? Task::with('project')->whereIn('status', ['todo', 'in_progress', 'review'])->get()
            : $user->assignedTasks()->with('project')->whereIn('status', ['todo', 'in_progress', 'review'])->get();
    }

    public function startTimer()
    {
        if (empty($this->selectedTaskId)) return;

        $this->isRunning = true;
        $this->startTime = now()->timestamp;
        $this->elapsedSeconds = 0;
    }

    public function stopTimer()
    {
        if (!$this->isRunning || !$this->startTime) return;

        $this->isRunning = false;
        $totalSeconds = now()->timestamp - $this->startTime;
        $hours = round($totalSeconds / 3600, 2);

        if ($hours >= 0.01) {
            WorkLog::create([
                'user_id' => auth()->id(),
                'task_id' => $this->selectedTaskId,
                'date' => today(),
                'hours' => $hours,
                'description' => $this->description ?: 'จับเวลาอัตโนมัติ',
            ]);
        }

        $this->reset(['startTime', 'elapsedSeconds', 'description']);
        $this->dispatch('workLogCreated');
    }

    public function render()
    {
        return view('livewire.timer');
    }
}
