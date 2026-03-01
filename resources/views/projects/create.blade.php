<x-app-layout>
    <x-slot name="header">สร้างโครงการใหม่</x-slot>

    @php $templates = \App\Models\Project::where('is_template', true)->select('id','name','description')->get(); @endphp
    @if($templates->count() > 0)
        <div class="max-w-2xl mb-6" x-data="{ open: false }">
            <button @click="open = !open" class="flex items-center gap-2 text-sm text-erms-blue hover:underline font-medium cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                สร้างจากเทมเพลตโครงการ
            </button>
            <div x-show="open" x-cloak class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-3">
                @foreach($templates as $tpl)
                    <a href="{{ route('projects.create', ['template_id' => $tpl->id]) }}" class="card p-4 hover:border-erms-blue transition group">
                        <h4 class="text-[13px] font-semibold group-hover:text-erms-blue">{{ $tpl->name }}</h4>
                        @if($tpl->description)<p class="text-2xs text-erms-muted mt-1 line-clamp-2">{{ $tpl->description }}</p>@endif
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    <div class="max-w-2xl">
        <form action="{{ route('projects.store') }}" method="POST" class="card p-6 space-y-4">
            @csrf
            @if(request('template_id'))
                <input type="hidden" name="template_id" value="{{ request('template_id') }}">
                @php $tplProject = \App\Models\Project::find(request('template_id')); @endphp
                @if($tplProject)
                    <div class="bg-erms-blue/5 border border-erms-blue/20 rounded-lg px-3 py-2 text-sm text-erms-blue flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        สร้างจากเทมเพลต: {{ $tplProject->name }}
                    </div>
                @endif
            @endif
            <div>
                <label class="block text-sm text-erms-muted mb-1">ชื่อโครงการ *</label>
                <input type="text" name="name" value="{{ old('name', request('template_id') ? ($tplProject->name ?? '') . ' (สำเนา)' : '') }}" class="input-field" required>
                @error('name') <p class="text-xs text-erms-red mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm text-erms-muted mb-1">รายละเอียด</label>
                <textarea name="description" class="input-field" rows="4">{{ old('description') }}</textarea>
            </div>
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm text-erms-muted mb-1">สถานะ</label>
                    <select name="status" class="input-field">
                        <option value="planning">วางแผน</option>
                        <option value="in_progress">กำลังดำเนินการ</option>
                        <option value="done">เสร็จสิ้น</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm text-erms-muted mb-1">วันเริ่มต้น</label>
                    <input type="date" name="start_date" value="{{ old('start_date') }}" class="input-field">
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
