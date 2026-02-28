<x-app-layout>
    <x-slot name="header">แก้ไขผู้ใช้: {{ $user->name }}</x-slot>

    <div class="max-w-lg">
        <form action="{{ route('admin.users.update', $user) }}" method="POST" class="card p-6 space-y-4">
            @csrf @method('PUT')
            <div>
                <label class="block text-sm text-erms-muted mb-1">ชื่อ *</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" class="input-field" required>
                @error('name') <p class="text-xs text-erms-red mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm text-erms-muted mb-1">อีเมล *</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" class="input-field" required>
                @error('email') <p class="text-xs text-erms-red mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm text-erms-muted mb-1">รหัสผ่านใหม่ (เว้นว่างถ้าไม่เปลี่ยน)</label>
                <input type="password" name="password" class="input-field">
                @error('password') <p class="text-xs text-erms-red mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm text-erms-muted mb-1">ยืนยันรหัสผ่านใหม่</label>
                <input type="password" name="password_confirmation" class="input-field">
            </div>
            <div>
                <label class="block text-sm text-erms-muted mb-1">แผนก</label>
                <select name="department_id" class="input-field">
                    <option value="">ไม่ระบุ</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" @selected($user->department_id == $dept->id)>{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm text-erms-muted mb-1">บทบาท *</label>
                <select name="role" class="input-field" required>
                    @foreach($roles as $role)
                        @php $roleLabels = ['admin' => 'ผู้ดูแลระบบ', 'manager' => 'ผู้จัดการ', 'employee' => 'พนักงาน']; @endphp
                        <option value="{{ $role->name }}" @selected($user->hasRole($role->name))>{{ $roleLabels[$role->name] ?? $role->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="btn-primary">อัปเดตผู้ใช้</button>
                <a href="{{ route('admin.users.index') }}" class="btn-secondary">ยกเลิก</a>
            </div>
        </form>
    </div>
</x-app-layout>
