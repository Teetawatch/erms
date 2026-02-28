<x-app-layout>
    <x-slot name="header">แดชบอร์ด</x-slot>

    {{-- Stats Row --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="card p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-erms-muted uppercase tracking-wider">โครงการทั้งหมด</p>
                    <p class="text-2xl font-heading font-bold mt-1">{{ $totalProjects }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-erms-blue/15 flex items-center justify-center">
                    <svg class="w-5 h-5 text-erms-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                </div>
            </div>
        </div>
        <div class="card p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-erms-muted uppercase tracking-wider">งานวันนี้</p>
                    <p class="text-2xl font-heading font-bold mt-1">{{ $tasksToday }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-erms-orange/15 flex items-center justify-center">
                    <svg class="w-5 h-5 text-erms-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                </div>
            </div>
        </div>
        <div class="card p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-erms-muted uppercase tracking-wider">ชั่วโมงสัปดาห์นี้</p>
                    <p class="text-2xl font-heading font-bold mt-1">{{ number_format($hoursThisWeek, 1) }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-erms-green/15 flex items-center justify-center">
                    <svg class="w-5 h-5 text-erms-green" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>
        <div class="card p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-erms-muted uppercase tracking-wider">รอตรวจสอบ</p>
                    <p class="text-2xl font-heading font-bold mt-1">{{ $pendingReview }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-erms-purple/15 flex items-center justify-center">
                    <svg class="w-5 h-5 text-erms-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content: My Tasks + Activity Feed --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- My Tasks --}}
        <div class="lg:col-span-2">
            <div class="card">
                <div class="px-5 py-4 border-b border-erms-border">
                    <h2 class="font-heading font-bold text-base">งานของฉัน</h2>
                </div>
                <div class="divide-y divide-erms-border/50">
                    @forelse($myTasks as $task)
                        <a href="{{ route('tasks.show', $task) }}" class="flex items-center gap-4 px-5 py-3 hover:bg-erms-surface-2 transition">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium truncate">{{ $task->title }}</p>
                                <p class="text-xs text-erms-muted mt-0.5">{{ $task->project->name ?? '-' }}</p>
                            </div>
                            @php
                                $priorityLabels = ['low' => 'ต่ำ', 'medium' => 'ปานกลาง', 'high' => 'สูง', 'urgent' => 'เร่งด่วน'];
                                $statusLabels = ['todo' => 'รอดำเนินการ', 'in_progress' => 'กำลังดำเนินการ', 'review' => 'ตรวจสอบ', 'done' => 'เสร็จสิ้น'];
                            @endphp
                            <span class="badge-{{ $task->priority }}">{{ $priorityLabels[$task->priority] ?? $task->priority }}</span>
                            <span class="badge-{{ str_replace('_', '-', $task->status) }}">{{ $statusLabels[$task->status] ?? $task->status }}</span>
                            @if($task->due_date)
                                <span class="text-xs text-erms-muted whitespace-nowrap">{{ $task->due_date->translatedFormat('d M') }}</span>
                            @endif
                        </a>
                    @empty
                        <div class="px-5 py-8 text-center text-erms-muted text-sm">ไม่มีงานที่ต้องทำ</div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Activity Feed --}}
        <div class="lg:col-span-1">
            <livewire:activity-feed />
        </div>
    </div>
</x-app-layout>
