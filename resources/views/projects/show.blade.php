<x-app-layout>
    <x-slot name="header">{{ $project->name }}</x-slot>

    @if(session('success'))
        <div class="mb-4 px-4 py-3 bg-erms-green/15 border border-erms-green/20 rounded-lg text-sm text-erms-green">
            {{ session('success') }}
        </div>
    @endif

    {{-- Project Info --}}
    <div class="card p-6 mb-6">
        <div class="flex items-start justify-between mb-4">
            <div>
                @php
                    $statusColors = ['planning' => 'badge-medium', 'in_progress' => 'badge-in-progress', 'done' => 'badge-done'];
                    $statusLabels = ['planning' => 'วางแผน', 'in_progress' => 'กำลังดำเนินการ', 'done' => 'เสร็จสิ้น'];
                @endphp
                <span class="{{ $statusColors[$project->status] ?? 'badge-todo' }}">{{ $statusLabels[$project->status] ?? $project->status }}</span>
                @if($project->deadline)
                    <span class="text-xs text-erms-muted ml-2">กำหนดส่ง: {{ $project->deadline->translatedFormat('d M Y') }}</span>
                @endif
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('projects.edit', $project) }}" class="btn-secondary text-xs">แก้ไข</a>
                <form method="POST" action="{{ route('projects.destroy', $project) }}" onsubmit="return confirm('ต้องการลบโครงการนี้?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn-danger text-xs">ลบ</button>
                </form>
            </div>
        </div>
        @if($project->description)
            <p class="text-sm text-erms-muted mb-4">{{ $project->description }}</p>
        @endif
        <div class="progress-bar mb-2">
            <div class="fill" style="width: {{ $project->progress }}%"></div>
        </div>
        <p class="text-xs text-erms-muted">{{ $project->progress }}% เสร็จสิ้น</p>

        {{-- Members --}}
        <div class="mt-4 pt-4 border-t border-erms-border">
            <p class="text-xs text-erms-muted mb-2">สมาชิก ({{ $project->members->count() }})</p>
            <div class="flex items-center gap-2 flex-wrap">
                @foreach($project->members as $member)
                    <div class="flex items-center gap-2 bg-erms-surface-2 rounded-full pl-1 pr-3 py-1">
                        <img src="{{ $member->avatar_url }}" alt="" class="w-5 h-5 rounded-full">
                        <span class="text-xs">{{ $member->name }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Tasks --}}
    <div class="mb-4 flex items-center justify-between">
        <h2 class="font-heading font-bold text-base">งานในโครงการ</h2>
        <a href="{{ route('tasks.index', ['project_id' => $project->id]) }}" class="btn-secondary text-xs">ดูบอร์ดงาน</a>
    </div>

    <livewire:kanban-board :project-id="$project->id" />
</x-app-layout>
