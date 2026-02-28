<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $projects = $user->hasRole('admin')
            ? Project::with(['creator', 'members', 'tasks'])->latest()->paginate(12)
            : $user->projects()->with(['creator', 'members', 'tasks'])->latest()->paginate(12);

        return view('projects.index', compact('projects'));
    }

    public function create()
    {
        $users = User::select('id', 'name')->get();
        return view('projects.create', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:planning,in_progress,done',
            'deadline' => 'nullable|date',
            'members' => 'array',
            'members.*' => 'exists:users,id',
        ]);

        $project = Project::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'status' => $validated['status'],
            'deadline' => $validated['deadline'] ?? null,
            'created_by' => $request->user()->id,
        ]);

        if (!empty($validated['members'])) {
            $project->members()->sync($validated['members']);
        }

        return redirect()->route('projects.show', $project)->with('success', 'สร้างโครงการเรียบร้อย');
    }

    public function show(Project $project)
    {
        $project->load(['creator', 'members', 'tasks.assignee']);
        return view('projects.show', compact('project'));
    }

    public function edit(Project $project)
    {
        $users = User::select('id', 'name')->get();
        $project->load('members');
        return view('projects.edit', compact('project', 'users'));
    }

    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:planning,in_progress,done',
            'deadline' => 'nullable|date',
            'members' => 'array',
            'members.*' => 'exists:users,id',
        ]);

        $project->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'status' => $validated['status'],
            'deadline' => $validated['deadline'] ?? null,
        ]);

        if (isset($validated['members'])) {
            $project->members()->sync($validated['members']);
        }

        return redirect()->route('projects.show', $project)->with('success', 'อัปเดตโครงการเรียบร้อย');
    }

    public function destroy(Project $project)
    {
        $project->delete();
        return redirect()->route('projects.index')->with('success', 'ลบโครงการเรียบร้อย');
    }
}
