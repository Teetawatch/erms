<x-app-layout>
    <x-slot name="header">โครงการ</x-slot>

    <div class="flex items-center justify-between mb-6">
        <p class="text-sm text-erms-muted">โครงการทั้งหมด {{ $projects->total() }} รายการ</p>
        @can('manage-all-projects')
        <a href="{{ route('projects.create') }}" class="btn-primary">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            สร้างโครงการ
        </a>
        @endcan
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        @forelse($projects as $project)
            <a href="{{ route('projects.show', $project) }}" class="card p-5 block">
                <div class="flex items-start justify-between mb-3">
                    <h3 class="font-heading font-bold text-sm">{{ $project->name }}</h3>
                    @php
                        $statusColors = ['planning' => 'badge-medium', 'in_progress' => 'badge-in-progress', 'done' => 'badge-done'];
                        $statusLabels = ['planning' => 'วางแผน', 'in_progress' => 'กำลังดำเนินการ', 'done' => 'เสร็จสิ้น'];
                    @endphp
                    <span class="{{ $statusColors[$project->status] ?? 'badge-todo' }}">{{ $statusLabels[$project->status] ?? $project->status }}</span>
                </div>
                @if($project->description)
                    <p class="text-xs text-erms-muted mb-3 line-clamp-2">{{ $project->description }}</p>
                @endif
                <div class="progress-bar mb-2">
                    <div class="fill" style="width: {{ $project->progress }}%"></div>
                </div>
                <div class="flex items-center justify-between text-xs text-erms-muted">
                    <span>{{ $project->progress }}% เสร็จ ({{ $project->tasks->where('status', 'done')->count() }}/{{ $project->tasks->count() }})</span>
                    @if($project->deadline)
                        <span class="{{ $project->deadline->isPast() ? 'text-erms-red' : '' }}">{{ $project->deadline->translatedFormat('d M Y') }}</span>
                    @endif
                </div>
                <div class="flex items-center gap-1 mt-3">
                    @foreach($project->members->take(4) as $member)
                        <img src="{{ $member->avatar_url }}" alt="{{ $member->name }}" class="w-6 h-6 rounded-full border border-erms-border" title="{{ $member->name }}">
                    @endforeach
                    @if($project->members->count() > 4)
                        <span class="w-6 h-6 rounded-full bg-erms-surface-2 border border-erms-border flex items-center justify-center text-[10px] text-erms-muted">+{{ $project->members->count() - 4 }}</span>
                    @endif
                </div>
            </a>
        @empty
            <div class="col-span-full text-center py-12 text-erms-muted">ยังไม่มีโครงการ</div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $projects->links() }}
    </div>
</x-app-layout>
