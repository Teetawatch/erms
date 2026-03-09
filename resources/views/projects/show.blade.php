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
                @if($project->start_date)
                    <span class="text-xs text-erms-muted ml-2">เริ่ม: {{ $project->start_date->translatedFormat('d M Y') }}</span>
                @endif
                @if($project->deadline)
                    <span class="text-xs text-erms-muted ml-2">กำหนดส่ง: {{ $project->deadline->translatedFormat('d M Y') }}</span>
                @endif
            </div>
            @if(auth()->user()->hasRole('admin') || $project->created_by === auth()->id())
            <div class="flex items-center gap-2">
                <a href="{{ route('projects.edit', $project) }}" class="btn-secondary text-xs">แก้ไข</a>
                <form method="POST" action="{{ route('projects.destroy', $project) }}" onsubmit="return confirm('ต้องการลบโครงการนี้?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn-danger text-xs">ลบ</button>
                </form>
            </div>
            @endif
        </div>
        @if($project->description)
            <p class="text-sm text-erms-muted mb-4">{{ $project->description }}</p>
        @endif
        <div class="progress-bar mb-2">
            <div class="fill" style="width: {{ $project->progress }}%"></div>
        </div>
        <p class="text-xs text-erms-muted">{{ $project->progress }}% เสร็จสิ้น ({{ $project->tasks->where('status', 'done')->count() }}/{{ $project->tasks->count() }} งาน)</p>

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

    {{-- Project Tabs --}}
    <div x-data="{ tab: 'tasks' }" class="space-y-4">
        <div class="flex items-center gap-1 bg-erms-surface border border-erms-border rounded-lg p-1 w-fit">
            <button @click="tab = 'tasks'"
                    :class="tab === 'tasks' ? 'bg-erms-blue text-white shadow-sm' : 'text-erms-muted hover:text-erms-text hover:bg-erms-surface-2'"
                    class="flex items-center gap-1.5 px-3 py-1.5 rounded-md text-sm font-medium transition cursor-pointer">
                <i class="fa-solid fa-clipboard-check"></i>
                งาน
            </button>
            <button @click="tab = 'timeline'"
                    :class="tab === 'timeline' ? 'bg-erms-blue text-white shadow-sm' : 'text-erms-muted hover:text-erms-text hover:bg-erms-surface-2'"
                    class="flex items-center gap-1.5 px-3 py-1.5 rounded-md text-sm font-medium transition cursor-pointer">
                <i class="fa-solid fa-calendar-days"></i>
                Timeline
            </button>
            <button @click="tab = 'custom_fields'"
                    :class="tab === 'custom_fields' ? 'bg-erms-blue text-white shadow-sm' : 'text-erms-muted hover:text-erms-text hover:bg-erms-surface-2'"
                    class="flex items-center gap-1.5 px-3 py-1.5 rounded-md text-sm font-medium transition cursor-pointer">
                <i class="fa-solid fa-tags"></i>
                ฟิลด์กำหนดเอง
            </button>
            <button @click="tab = 'automation'"
                    :class="tab === 'automation' ? 'bg-erms-blue text-white shadow-sm' : 'text-erms-muted hover:text-erms-text hover:bg-erms-surface-2'"
                    class="flex items-center gap-1.5 px-3 py-1.5 rounded-md text-sm font-medium transition cursor-pointer">
                <i class="fa-solid fa-bolt"></i>
                Automation
            </button>
        </div>

        {{-- Tasks Tab --}}
        <div x-show="tab === 'tasks'" x-cloak>
            <livewire:kanban-board :project-id="$project->id" />
        </div>

        {{-- Timeline Tab --}}
        <div x-show="tab === 'timeline'" x-cloak>
            <livewire:timeline-view :project-id="$project->id" />
        </div>

        {{-- Custom Fields Tab --}}
        <div x-show="tab === 'custom_fields'" x-cloak>
            <livewire:custom-field-manager :project-id="$project->id" />
        </div>

        {{-- Automation Tab --}}
        <div x-show="tab === 'automation'" x-cloak>
            <livewire:automation-rule-manager :project-id="$project->id" />
        </div>
    </div>
</x-app-layout>
