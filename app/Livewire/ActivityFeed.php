<?php

namespace App\Livewire;

use App\Models\TaskUpdate;
use Livewire\Component;

class ActivityFeed extends Component
{
    public $limit = 15;

    public function getActivitiesProperty()
    {
        return TaskUpdate::with(['task.project', 'user'])
            ->latest()
            ->take($this->limit)
            ->get();
    }

    public function render()
    {
        return view('livewire.activity-feed');
    }
}
