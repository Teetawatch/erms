<x-app-layout>
    <x-slot name="header">สร้างโครงการใหม่</x-slot>

    <div class="max-w-2xl">
        <form action="{{ route('projects.store') }}" method="POST" class="card p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-sm text-erms-muted mb-1">ชื่อโครงการ *</label>
                <input type="text" name="name" value="{{ old('name') }}" class="input-field" required>
                @error('name') <p class="text-xs text-erms-red mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm text-erms-muted mb-1">รายละเอียด</label>
                <textarea name="description" class="input-field" rows="4">{{ old('description') }}</textarea>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm text-erms-muted mb-1">สถานะ</label>
                    <select name="status" class="input-field">
                        <option value="planning">วางแผน</option>
                        <option value="in_progress">กำลังดำเนินการ</option>
                        <option value="done">เสร็จสิ้น</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm text-erms-muted mb-1">กำหนดส่ง</label>
                    <input type="date" name="deadline" value="{{ old('deadline') }}" class="input-field">
                </div>
            </div>
            <div>
                <label class="block text-sm text-erms-muted mb-1">สมาชิก</label>
                <div class="grid grid-cols-2 gap-2 mt-2 max-h-48 overflow-y-auto">
                    @foreach($users as $user)
                        <label class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-erms-surface-2 cursor-pointer">
                            <input type="checkbox" name="members[]" value="{{ $user->id }}" class="rounded border-erms-border bg-white text-erms-blue focus:ring-erms-blue/20">
                            <span class="text-sm">{{ $user->name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="btn-primary">สร้างโครงการ</button>
                <a href="{{ route('projects.index') }}" class="btn-secondary">ยกเลิก</a>
            </div>
        </form>
    </div>
</x-app-layout>
