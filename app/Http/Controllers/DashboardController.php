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
        $isManager = $user->hasRole('manager');

        $totalProjects = $isAdmin
            ? Project::count()
            : $user->projects()->count();

        $tasksToday = Task::visibleTo($user)->whereDate('due_date', today())->count();

        $completedThisWeek = Task::visibleTo($user)->where('status', 'done')->whereBetween('updated_at', [now()->startOfWeek(), now()->endOfWeek()])->count();

        $pendingReview = Task::visibleTo($user)->where('status', 'review')->count();

        $overdueTasks = Task::visibleTo($user)->where('status', '!=', 'done')->whereDate('due_date', '<', today())->count();

        $myTasks = $user->assignedTasks()
            ->with('project')
            ->whereIn('status', ['todo', 'in_progress', 'review'])
            ->orderByRaw("CASE priority WHEN 'urgent' THEN 1 WHEN 'high' THEN 2 WHEN 'medium' THEN 3 WHEN 'low' THEN 4 ELSE 5 END")
            ->take(10)
            ->get();

        // Status breakdown for chart
        $statusBreakdown = [
            'todo' => Task::visibleTo($user)->where('status', 'todo')->count(),
            'in_progress' => Task::visibleTo($user)->where('status', 'in_progress')->count(),
            'review' => Task::visibleTo($user)->where('status', 'review')->count(),
            'done' => Task::visibleTo($user)->where('status', 'done')->count(),
        ];

        // Workload by user (admin/manager)
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
        } elseif ($isManager) {
            $projectIds = $user->projects()->pluck('projects.id');
            $teamUserIds = \DB::table('project_user')->whereIn('project_id', $projectIds)->pluck('user_id')->unique();
            $workloadData = User::whereIn('id', $teamUserIds)
            ->withCount(['assignedTasks as open_tasks' => function ($q) {
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
