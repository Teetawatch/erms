<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\TimeEntry;
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
            $year = substr($month, 0, 4);
            $mon = substr($month, 5, 2);

            $tasks = Task::with('project', 'assignee')
                ->where('assigned_to', $selectedUserId)
                ->whereNull('parent_id')
                ->where(function ($q) use ($year, $mon) {
                    $q->where(function ($q2) use ($year, $mon) {
                        $q2->whereYear('created_at', $year)->whereMonth('created_at', $mon);
                    })->orWhere(function ($q2) use ($year, $mon) {
                        $q2->whereYear('updated_at', $year)->whereMonth('updated_at', $mon);
                    })->orWhere(function ($q2) use ($year, $mon) {
                        $q2->whereYear('due_date', $year)->whereMonth('due_date', $mon);
                    });
                })
                ->orderBy('updated_at', 'desc')
                ->get();
            $completedTasks = $tasks->where('status', 'done')->count();
        }

        // Overview stats
        $user = auth()->user();
        $isAdmin = $user->hasRole('admin');
        $isManager = $user->hasRole('manager');

        $allTasks = Task::whereNull('parent_id')->visibleTo($user)->get();

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
        // Manager can also see their project health (already covered by $user->projects())
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

        // Team workload (admin/manager)
        $teamWorkload = collect();
        if ($isAdmin) {
            $teamWorkload = User::withCount([
                'assignedTasks as active_tasks' => fn($q) => $q->whereIn('status', ['todo', 'in_progress', 'review']),
                'assignedTasks as done_tasks' => fn($q) => $q->where('status', 'done'),
                'assignedTasks as overdue_tasks' => fn($q) => $q->where('status', '!=', 'done')->whereNotNull('due_date')->where('due_date', '<', now()),
            ])->get();
        } elseif ($isManager) {
            $projectIds = $user->projects()->pluck('projects.id');
            $teamUserIds = \DB::table('project_user')->whereIn('project_id', $projectIds)->pluck('user_id')->unique();
            $teamWorkload = User::whereIn('id', $teamUserIds)->withCount([
                'assignedTasks as active_tasks' => fn($q) => $q->whereIn('status', ['todo', 'in_progress', 'review']),
                'assignedTasks as done_tasks' => fn($q) => $q->where('status', 'done'),
                'assignedTasks as overdue_tasks' => fn($q) => $q->where('status', '!=', 'done')->whereNotNull('due_date')->where('due_date', '<', now()),
            ])->get();
        }

        // ═══ Project Monthly Report ═══
        $projectMonthlyTasks = collect();
        $projectTimeEntries = collect();
        $projectTotalHours = 0;
        $projectEstimatedHours = 0;
        if ($selectedProjectId) {
            $year = substr($month, 0, 4);
            $mon = substr($month, 5, 2);

            $projectMonthlyTasks = Task::with(['assignee', 'timeEntries'])
                ->where('project_id', $selectedProjectId)
                ->whereNull('parent_id')
                ->where(function ($q) use ($year, $mon) {
                    $q->where(function ($q2) use ($year, $mon) {
                        $q2->whereYear('created_at', $year)->whereMonth('created_at', $mon);
                    })->orWhere(function ($q2) use ($year, $mon) {
                        $q2->whereYear('updated_at', $year)->whereMonth('updated_at', $mon);
                    });
                })
                ->orderBy('status')
                ->orderBy('updated_at', 'desc')
                ->get();

            $projectTimeEntries = TimeEntry::with(['user', 'task'])
                ->whereHas('task', fn($q) => $q->where('project_id', $selectedProjectId))
                ->whereYear('date_worked', $year)
                ->whereMonth('date_worked', $mon)
                ->orderBy('date_worked', 'desc')
                ->get();

            $projectTotalHours = $projectTimeEntries->sum('hours');
            $projectEstimatedHours = Task::where('project_id', $selectedProjectId)
                ->whereNull('parent_id')
                ->sum('estimated_hours');
        }

        // ═══ Time Tracking Summary for User Report ═══
        $userTimeEntries = collect();
        $userTotalHours = 0;
        if ($selectedUserId) {
            $year = substr($month, 0, 4);
            $mon = substr($month, 5, 2);

            $userTimeEntries = TimeEntry::with(['task.project'])
                ->where('user_id', $selectedUserId)
                ->whereYear('date_worked', $year)
                ->whereMonth('date_worked', $mon)
                ->orderBy('date_worked', 'desc')
                ->get();

            $userTotalHours = $userTimeEntries->sum('hours');
        }

        return view('reports.index', compact(
            'users', 'projects', 'tasks', 'completedTasks', 'selectedUserId',
            'selectedProjectId', 'month', 'tab', 'overview', 'projectsHealth', 'teamWorkload',
            'projectMonthlyTasks', 'projectTimeEntries', 'projectTotalHours', 'projectEstimatedHours',
            'userTimeEntries', 'userTotalHours'
        ));
    }

    public function exportPdf(Request $request)
    {
        $userId = $request->query('user_id');
        $month = $request->query('month', now()->format('Y-m'));

        $user = User::findOrFail($userId);
        $year = substr($month, 0, 4);
        $mon = substr($month, 5, 2);

        $tasks = Task::with('project')
            ->where('assigned_to', $userId)
            ->whereNull('parent_id')
            ->where(function ($q) use ($year, $mon) {
                $q->where(function ($q2) use ($year, $mon) {
                    $q2->whereYear('created_at', $year)->whereMonth('created_at', $mon);
                })->orWhere(function ($q2) use ($year, $mon) {
                    $q2->whereYear('updated_at', $year)->whereMonth('updated_at', $mon);
                })->orWhere(function ($q2) use ($year, $mon) {
                    $q2->whereYear('due_date', $year)->whereMonth('due_date', $mon);
                });
            })
            ->orderBy('updated_at', 'desc')
            ->get();

        $completedTasks = $tasks->where('status', 'done')->count();

        $pdf = Pdf::loadView('reports.pdf', compact('user', 'tasks', 'completedTasks', 'month'));
        return $pdf->download("tasks-{$user->name}-{$month}.pdf");
    }
}
