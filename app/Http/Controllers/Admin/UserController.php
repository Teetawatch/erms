<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with(['department', 'roles'])->latest()->paginate(15);
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $departments = Department::all();
        $roles = Role::all();
        return view('admin.users.create', compact('departments', 'roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'department_id' => 'nullable|exists:departments,id',
            'role' => 'required|exists:roles,name',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'department_id' => $validated['department_id'],
            'email_verified_at' => now(),
        ]);

        $user->assignRole($validated['role']);

        return redirect()->route('admin.users.index')->with('success', 'สร้างผู้ใช้เรียบร้อย');
    }

    public function edit(User $user)
    {
        $departments = Department::all();
        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'departments', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'department_id' => 'nullable|exists:departments,id',
            'role' => 'required|exists:roles,name',
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'department_id' => $validated['department_id'],
        ]);

        if (!empty($validated['password'])) {
            $user->update(['password' => Hash::make($validated['password'])]);
        }

        $user->syncRoles([$validated['role']]);

        return redirect()->route('admin.users.index')->with('success', 'อัปเดตผู้ใช้เรียบร้อย');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'ไม่สามารถลบตัวเองได้');
        }
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'ลบผู้ใช้เรียบร้อย');
    }
}
