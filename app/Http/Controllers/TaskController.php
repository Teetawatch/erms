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
        $task->load(['project', 'assignee', 'comments.user', 'attachments.user', 'taskUpdates.user', 'workLogs.user']);
        return view('tasks.show', compact('task'));
    }
}
