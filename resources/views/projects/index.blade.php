<x-app-layout>
    <x-slot name="header">โครงการ</x-slot>

    <div class="flex items-center justify-between mb-5">
        <p class="text-[13px] text-erms-text-secondary">โครงการทั้งหมด {{ $projects->total() }} รายการ</p>
        @can('manage-all-projects')
        <a href="{{ route('projects.create') }}" class="btn-primary !text-[13px]">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            สร้างโครงการ
        </a>
        @endcan
    </div>

    @php
        $statusColors = ['planning' => 'badge-medium', 'in_progress' => 'badge-in-progress', 'done' => 'badge-done'];
        $statusLabels = ['planning' => 'วางแผน', 'in_progress' => 'กำลังดำเนินการ', 'done' => 'เสร็จสิ้น'];
        $dotColors = ['planning' => 'bg-erms-yellow', 'in_progress' => 'bg-erms-blue', 'done' => 'bg-erms-green'];
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-3">
        @forelse($projects as $project)
            <a href="{{ route('projects.show', $project) }}" class="card card-hover p-4 block group">
                {{-- Header --}}
                <div class="flex items-start gap-3 mb-3">
                    <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0 {{ $dotColors[$project->status] ?? 'bg-erms-muted' }} bg-opacity-20">
                        <span class="w-3 h-3 rounded-full {{ $dotColors[$project->status] ?? 'bg-erms-muted' }}"></span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-[13px] font-semibold text-erms-text group-hover:text-erms-blue transition truncate">{{ $project->name }}</h3>
                        <span class="{{ $statusColors[$project->status] ?? 'badge-todo' }} mt-0.5">{{ $statusLabels[$project->status] ?? $project->status }}</span>
                    </div>
                </div>

                @if($project->description)
                    <p class="text-2xs text-erms-text-secondary mb-3 line-clamp-2 leading-relaxed">{{ $project->description }}</p>
                @endif

                {{-- Progress --}}
                <div class="progress-bar mb-1.5">
                    <div class="fill" style="width: {{ $project->progress }}%"></div>
                </div>
                <div class="flex items-center justify-between text-2xs text-erms-muted mb-3">
                    <span>{{ $project->progress }}% เสร็จ · {{ $project->tasks->where('status', 'done')->count() }}/{{ $project->tasks->count() }} งาน</span>
                    @if($project->deadline)
                        <span class="{{ $project->deadline->isPast() ? 'text-erms-red font-medium' : '' }}">{{ $project->deadline->translatedFormat('d M Y') }}</span>
                    @endif
                </div>

                {{-- Members --}}
                <div class="flex items-center justify-between">
                    <div class="avatar-stack">
                        @foreach($project->members->take(5) as $member)
                            <img src="{{ $member->avatar_url }}" alt="{{ $member->name }}" title="{{ $member->name }}">
                        @endforeach
                        @if($project->members->count() > 5)
                            <span class="w-6 h-6 rounded-full bg-erms-surface-2 ring-2 ring-white flex items-center justify-center text-2xs text-erms-muted font-medium">+{{ $project->members->count() - 5 }}</span>
                        @endif
                    </div>
                    <svg class="w-4 h-4 text-erms-muted opacity-0 group-hover:opacity-100 transition" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </div>
            </a>
        @empty
            <div class="col-span-full py-16 text-center">
                <svg class="w-12 h-12 mx-auto text-erms-muted/40 mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                <p class="text-[13px] text-erms-muted">ยังไม่มีโครงการ</p>
            </div>
        @endforelse
    </div>

    <div class="mt-5">
        {{ $projects->links() }}
    </div>
</x-app-layout>
