<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index()
    {
        $departments = Department::withCount('users')->latest()->paginate(15);
        return view('admin.departments.index', compact('departments'));
    }

    public function create()
    {
        return view('admin.departments.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        Department::create($validated);

        return redirect()->route('admin.departments.index')->with('success', 'สร้างแผนกเรียบร้อย');
    }

    public function edit(Department $department)
    {
        return view('admin.departments.edit', compact('department'));
    }

    public function update(Request $request, Department $department)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $department->update($validated);

        return redirect()->route('admin.departments.index')->with('success', 'อัปเดตแผนกเรียบร้อย');
    }

    public function destroy(Department $department)
    {
        if ($department->users()->count() > 0) {
            return back()->with('error', 'ไม่สามารถลบแผนกที่มีผู้ใช้งานได้');
        }
        $department->delete();
        return redirect()->route('admin.departments.index')->with('success', 'ลบแผนกเรียบร้อย');
    }
}
