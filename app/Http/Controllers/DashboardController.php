<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user();
        $isAdmin = $user->hasRole('admin');

        $totalProjects = $isAdmin
            ? Project::count()
            : $user->projects()->count();

        $tasksToday = $isAdmin
            ? Task::whereDate('due_date', today())->count()
            : $user->assignedTasks()->whereDate('due_date', today())->count();

        $completedThisWeek = $isAdmin
            ? Task::where('status', 'done')->whereBetween('updated_at', [now()->startOfWeek(), now()->endOfWeek()])->count()
            : $user->assignedTasks()->where('status', 'done')->whereBetween('updated_at', [now()->startOfWeek(), now()->endOfWeek()])->count();

        $pendingReview = $isAdmin
            ? Task::where('status', 'review')->count()
            : $user->assignedTasks()->where('status', 'review')->count();

        $overdueTasks = $isAdmin
            ? Task::where('status', '!=', 'done')->whereDate('due_date', '<', today())->count()
            : $user->assignedTasks()->where('status', '!=', 'done')->whereDate('due_date', '<', today())->count();

        $myTasks = $user->assignedTasks()
            ->with('project')
            ->whereIn('status', ['todo', 'in_progress', 'review'])
            ->orderByRaw("CASE priority WHEN 'urgent' THEN 1 WHEN 'high' THEN 2 WHEN 'medium' THEN 3 WHEN 'low' THEN 4 ELSE 5 END")
            ->take(10)
            ->get();

        // Status breakdown for chart
        $statusQuery = $isAdmin ? Task::query() : $user->assignedTasks();
        $statusBreakdown = [
            'todo' => (clone $statusQuery)->where('status', 'todo')->count(),
            'in_progress' => (clone $statusQuery)->where('status', 'in_progress')->count(),
            'review' => (clone $statusQuery)->where('status', 'review')->count(),
            'done' => (clone $statusQuery)->where('status', 'done')->count(),
        ];

        // Workload by user (admin only)
        $workloadData = [];
        if ($isAdmin) {
            $workloadData = User::withCount(['assignedTasks as open_tasks' => function ($q) {
                $q->whereIn('status', ['todo', 'in_progress', 'review']);
            }, 'assignedTasks as done_tasks' => function ($q) {
                $q->where('status', 'done');
            }, 'assignedTasks as total_tasks'])
            ->has('assignedTasks')
            ->take(10)
            ->get();
        }

        return view('dashboard', compact(
            'totalProjects', 'tasksToday', 'completedThisWeek',
            'pendingReview', 'overdueTasks', 'myTasks',
            'statusBreakdown', 'workloadData'
        ));
    }
}
