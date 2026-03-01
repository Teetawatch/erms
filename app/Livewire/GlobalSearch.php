<?php

namespace App\Livewire;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Livewire\Component;

class GlobalSearch extends Component
{
    public $showModal = false;
    public $query = '';
    public $results = [];

    protected $listeners = ['open-search' => 'openSearch'];

    public function openSearch()
    {
        $this->showModal = true;
        $this->query = '';
        $this->results = [];
    }

    public function updatedQuery()
    {
        if (strlen($this->query) < 2) {
            $this->results = [];
            return;
        }

        $user = auth()->user();
        $isAdmin = $user->hasRole('admin');
        $search = '%' . $this->query . '%';

        // Search tasks
        $taskQuery = Task::where(function ($q) use ($search) {
                $q->where('title', 'like', $search)
                  ->orWhere('description', 'like', $search);
            })
            ->whereNull('parent_id');

        if (!$isAdmin) {
            $taskQuery->where(function ($q) use ($user) {
                $q->where('assigned_to', $user->id)
                  ->orWhereIn('project_id', $user->projects()->pluck('projects.id'));
            });
        }

        $tasks = $taskQuery->with('project')
            ->select('id', 'title', 'status', 'project_id', 'priority')
            ->take(5)
            ->get()
            ->map(fn ($t) => [
                'type' => 'task',
                'id' => $t->id,
                'title' => $t->title,
                'subtitle' => $t->project?->name ?? '',
                'status' => $t->status,
                'priority' => $t->priority,
                'url' => route('tasks.show', $t->id),
            ]);

        // Search projects
        $projectQuery = Project::where(function ($q) use ($search) {
            $q->where('name', 'like', $search)
              ->orWhere('description', 'like', $search);
        });

        if (!$isAdmin) {
            $projectQuery->whereIn('id', $user->projects()->pluck('projects.id'));
        }

        $projects = $projectQuery->select('id', 'name', 'status')
            ->take(3)
            ->get()
            ->map(fn ($p) => [
                'type' => 'project',
                'id' => $p->id,
                'title' => $p->name,
                'subtitle' => '',
                'status' => $p->status,
                'url' => route('projects.show', $p->id),
            ]);

        // Search users (admin only)
        $users = collect();
        if ($isAdmin) {
            $users = User::where('name', 'like', $search)
                ->orWhere('email', 'like', $search)
                ->select('id', 'name', 'email')
                ->take(3)
                ->get()
                ->map(fn ($u) => [
                    'type' => 'user',
                    'id' => $u->id,
                    'title' => $u->name,
                    'subtitle' => $u->email,
                    'url' => route('admin.users.edit', $u->id),
                ]);
        }

        $this->results = $tasks->merge($projects)->merge($users)->toArray();
    }

    public function navigateTo($url)
    {
        $this->showModal = false;
        $this->query = '';
        $this->results = [];
        return $this->redirect($url, navigate: true);
    }

    public function render()
    {
        return view('livewire.global-search');
    }
}
