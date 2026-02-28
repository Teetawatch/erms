<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    public function index()
    {
        return view('calendar.index');
    }

    public function events(Request $request)
    {
        $user = $request->user();

        $tasks = $user->hasRole('admin')
            ? Task::with('project', 'assignee')->whereNotNull('due_date')->get()
            : $user->assignedTasks()->with('project')->whereNotNull('due_date')->get();

        $events = $tasks->map(function ($task) {
            $colors = [
                'urgent' => '#f43f5e',
                'high' => '#f97316',
                'medium' => '#fbbf24',
                'low' => '#22d3a0',
            ];
            return [
                'id' => $task->id,
                'title' => $task->title,
                'start' => $task->due_date->format('Y-m-d'),
                'color' => $colors[$task->priority] ?? '#4f8ef7',
                'url' => route('tasks.show', $task),
                'extendedProps' => [
                    'project' => $task->project->name ?? '',
                    'status' => $task->status,
                    'priority' => $task->priority,
                ],
            ];
        });

        return response()->json($events);
    }
}
