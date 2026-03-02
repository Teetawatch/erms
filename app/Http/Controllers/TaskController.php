<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $projectId = $request->query('project_id');
        return view('tasks.index', compact('projectId'));
    }

    public function show(Task $task)
    {
        $user = auth()->user();

        if (!$user->hasRole('admin')) {
            $canView = $task->assigned_to === $user->id;

            if (!$canView && $user->hasRole('manager')) {
                $projectIds = $user->projects()->pluck('projects.id')->toArray();
                $canView = in_array($task->project_id, $projectIds);
            }

            abort_unless($canView, 403, 'คุณไม่มีสิทธิ์ดูงานนี้');
        }

        $task->load(['project', 'assignee', 'comments.user', 'attachments.user', 'taskUpdates.user']);
        return view('tasks.show', compact('task'));
    }
}
