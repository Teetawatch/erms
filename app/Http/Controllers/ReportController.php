<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $users = User::select('id', 'name')->get();
        $projects = Project::select('id', 'name')->get();

        $selectedUserId = $request->query('user_id');
        $selectedProjectId = $request->query('project_id');
        $month = $request->query('month', now()->format('Y-m'));
        $tab = $request->query('tab', 'user');

        $tasks = collect();
        $completedTasks = 0;

        if ($selectedUserId) {
            $tasks = Task::with('project', 'assignee')
                ->where('assigned_to', $selectedUserId)
                ->whereYear('created_at', substr($month, 0, 4))
                ->whereMonth('created_at', substr($month, 5, 2))
                ->orderBy('created_at', 'desc')
                ->get();
            $completedTasks = $tasks->where('status', 'done')->count();
        }

        // Overview stats
        $user = auth()->user();
        $isAdmin = $user->hasRole('admin');

        $allTasks = Task::whereNull('parent_id');
        if (!$isAdmin) {
            $allTasks->where('assigned_to', $user->id);
        }
        $allTasks = $allTasks->get();

        $overview = [
            'total' => $allTasks->count(),
            'done' => $allTasks->where('status', 'done')->count(),
            'in_progress' => $allTasks->where('status', 'in_progress')->count(),
            'review' => $allTasks->where('status', 'review')->count(),
            'todo' => $allTasks->where('status', 'todo')->count(),
            'overdue' => $allTasks->filter(fn($t) => $t->status !== 'done' && $t->due_date && $t->due_date->isPast())->count(),
            'by_priority' => [
                'urgent' => $allTasks->where('priority', 'urgent')->count(),
                'high' => $allTasks->where('priority', 'high')->count(),
                'medium' => $allTasks->where('priority', 'medium')->count(),
                'low' => $allTasks->where('priority', 'low')->count(),
            ],
        ];
        $overview['completion_rate'] = $overview['total'] > 0 ? round(($overview['done'] / $overview['total']) * 100) : 0;

        // Project health
        $projectQuery = $isAdmin ? Project::query() : $user->projects();
        $projectsHealth = $projectQuery->withCount([
            'tasks',
            'tasks as done_tasks_count' => fn($q) => $q->where('status', 'done'),
            'tasks as overdue_tasks_count' => fn($q) => $q->where('status', '!=', 'done')->whereNotNull('due_date')->where('due_date', '<', now()),
        ])->get()->map(fn($p) => [
            'id' => $p->id,
            'name' => $p->name,
            'status' => $p->status,
            'total' => $p->tasks_count,
            'done' => $p->done_tasks_count,
            'overdue' => $p->overdue_tasks_count,
            'progress' => $p->tasks_count > 0 ? round(($p->done_tasks_count / $p->tasks_count) * 100) : 0,
            'health' => $p->overdue_tasks_count > 2 ? 'at_risk' : ($p->overdue_tasks_count > 0 ? 'needs_attention' : 'on_track'),
        ]);

        // Team workload (admin only)
        $teamWorkload = collect();
        if ($isAdmin) {
            $teamWorkload = User::withCount([
                'assignedTasks as active_tasks' => fn($q) => $q->whereIn('status', ['todo', 'in_progress', 'review']),
                'assignedTasks as done_tasks' => fn($q) => $q->where('status', 'done'),
                'assignedTasks as overdue_tasks' => fn($q) => $q->where('status', '!=', 'done')->whereNotNull('due_date')->where('due_date', '<', now()),
            ])->get();
        }

        return view('reports.index', compact(
            'users', 'projects', 'tasks', 'completedTasks', 'selectedUserId',
            'selectedProjectId', 'month', 'tab', 'overview', 'projectsHealth', 'teamWorkload'
        ));
    }

    public function exportPdf(Request $request)
    {
        $userId = $request->query('user_id');
        $month = $request->query('month', now()->format('Y-m'));

        $user = User::findOrFail($userId);
        $tasks = Task::with('project')
            ->where('assigned_to', $userId)
            ->whereYear('created_at', substr($month, 0, 4))
            ->whereMonth('created_at', substr($month, 5, 2))
            ->orderBy('created_at')
            ->get();

        $completedTasks = $tasks->where('status', 'done')->count();

        $pdf = Pdf::loadView('reports.pdf', compact('user', 'tasks', 'completedTasks', 'month'));
        return $pdf->download("tasks-{$user->name}-{$month}.pdf");
    }
}
