<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use App\Models\WorkLog;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $users = User::select('id', 'name')->get();
        $projects = Project::select('id', 'name')->get();

        $selectedUserId = $request->query('user_id');
        $month = $request->query('month', now()->format('Y-m'));

        $workLogs = collect();
        $totalHours = 0;

        if ($selectedUserId) {
            $workLogs = WorkLog::with('task.project', 'user')
                ->where('user_id', $selectedUserId)
                ->whereYear('date', substr($month, 0, 4))
                ->whereMonth('date', substr($month, 5, 2))
                ->orderBy('date', 'desc')
                ->get();
            $totalHours = $workLogs->sum('hours');
        }

        return view('reports.index', compact('users', 'projects', 'workLogs', 'totalHours', 'selectedUserId', 'month'));
    }

    public function exportPdf(Request $request)
    {
        $userId = $request->query('user_id');
        $month = $request->query('month', now()->format('Y-m'));

        $user = User::findOrFail($userId);
        $workLogs = WorkLog::with('task.project')
            ->where('user_id', $userId)
            ->whereYear('date', substr($month, 0, 4))
            ->whereMonth('date', substr($month, 5, 2))
            ->orderBy('date')
            ->get();

        $totalHours = $workLogs->sum('hours');

        $pdf = Pdf::loadView('reports.pdf', compact('user', 'workLogs', 'totalHours', 'month'));
        return $pdf->download("worklog-{$user->name}-{$month}.pdf");
    }
}
