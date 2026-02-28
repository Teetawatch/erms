<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\WorkLog;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user();

        $totalProjects = $user->hasRole('admin')
            ? Project::count()
            : $user->projects()->count();

        $tasksToday = $user->hasRole('admin')
            ? Task::whereDate('due_date', today())->count()
            : $user->assignedTasks()->whereDate('due_date', today())->count();

        $hoursThisWeek = WorkLog::where('user_id', $user->id)
            ->whereBetween('date', [now()->startOfWeek(), now()->endOfWeek()])
            ->sum('hours');

        $pendingReview = $user->hasRole('admin')
            ? Task::where('status', 'review')->count()
            : $user->assignedTasks()->where('status', 'review')->count();

        $myTasks = $user->assignedTasks()
            ->with('project')
            ->whereIn('status', ['todo', 'in_progress', 'review'])
            ->orderByRaw("CASE priority WHEN 'urgent' THEN 1 WHEN 'high' THEN 2 WHEN 'medium' THEN 3 WHEN 'low' THEN 4 ELSE 5 END")
            ->take(10)
            ->get();

        return view('dashboard', compact(
            'totalProjects', 'tasksToday', 'hoursThisWeek',
            'pendingReview', 'myTasks'
        ));
    }
}
