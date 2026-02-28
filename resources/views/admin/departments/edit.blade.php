<x-app-layout>
    <x-slot name="header">แก้ไขแผนก: {{ $department->name }}</x-slot>

    <div class="max-w-lg">
        <form action="{{ route('admin.departments.update', $department) }}" method="POST" class="card p-6 space-y-4">
            @csrf @method('PUT')
            <div>
                <label class="block text-sm text-erms-muted mb-1">ชื่อแผนก *</label>
                <input type="text" name="name" value="{{ old('name', $department->name) }}" class="input-field" required>
                @error('name') <p class="text-xs text-erms-red mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm text-erms-muted mb-1">รายละเอียด</label>
                <textarea name="description" class="input-field" rows="3">{{ old('description', $department->description) }}</textarea>
            </div>
            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="btn-primary">อัปเดตแผนก</button>
                <a href="{{ route('admin.departments.index') }}" class="btn-secondary">ยกเลิก</a>
            </div>
        </form>
    </div>
</x-app-layout>
