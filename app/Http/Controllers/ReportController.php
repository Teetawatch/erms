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
        $month = $request->query('month', now()->format('Y-m'));

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

        return view('reports.index', compact('users', 'projects', 'tasks', 'completedTasks', 'selectedUserId', 'month'));
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
