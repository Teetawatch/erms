<x-app-layout>
    <x-slot name="header">หน้าหลัก</x-slot>

    {{-- ═══ Welcome Banner ═══ --}}
    <div class="mb-6">
        <h2 class="text-lg font-semibold text-erms-text">สวัสดี {{ auth()->user()->name }}</h2>
        <p class="text-[13px] text-erms-text-secondary mt-0.5">นี่คือภาพรวมงานของคุณวันนี้</p>
    </div>

    {{-- ═══ Stats Row (Asana-style compact cards) ═══ --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 mb-6">
        @php
            $stats = [
                ['label' => 'โครงการ', 'value' => $totalProjects, 'color' => 'blue', 'icon' => 'fa-folder'],
                ['label' => 'งานวันนี้', 'value' => $tasksToday, 'color' => 'orange', 'icon' => 'fa-clipboard-check'],
                ['label' => 'เสร็จสัปดาห์นี้', 'value' => $completedThisWeek, 'color' => 'green', 'icon' => 'fa-check'],
                ['label' => 'รอตรวจสอบ', 'value' => $pendingReview, 'color' => 'purple', 'icon' => 'fa-eye'],
                ['label' => 'เกินกำหนด', 'value' => $overdueTasks, 'color' => 'red', 'icon' => 'fa-circle-exclamation'],
            ];
        @endphp
        @foreach($stats as $stat)
            <div class="card p-4">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-erms-{{ $stat['color'] }}-light flex items-center justify-center flex-shrink-0">
                        <i class="fa-solid {{ $stat['icon'] }} text-erms-{{ $stat['color'] }}"></i>
                    </div>
                    <div>
                        <p class="text-xl font-semibold text-erms-text leading-none {{ $stat['color'] === 'red' && $stat['value'] > 0 ? 'text-erms-red' : '' }}">{{ $stat['value'] }}</p>
                        <p class="text-2xs text-erms-muted mt-0.5">{{ $stat['label'] }}</p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- ═══ Charts Row ═══ --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">
        {{-- Status Breakdown --}}
        <div class="card p-5">
            <h3 class="text-[13px] font-semibold text-erms-text mb-4">สถานะงานทั้งหมด</h3>
            @php
                $total = array_sum($statusBreakdown);
                $statusMeta = [
                    'todo' => ['label' => 'รอดำเนินการ', 'color' => '#9ca0a4'],
                    'in_progress' => ['label' => 'กำลังดำเนินการ', 'color' => '#4573d2'],
                    'review' => ['label' => 'ตรวจสอบ', 'color' => '#7c5cfc'],
                    'done' => ['label' => 'เสร็จสิ้น', 'color' => '#5da283'],
                ];
            @endphp
            @if($total > 0)
                <div class="flex rounded-full h-2.5 overflow-hidden mb-4">
                    @foreach($statusBreakdown as $status => $count)
                        @if($count > 0)
                            <div class="transition-all duration-300" style="width: {{ ($count / $total) * 100 }}%; background: {{ $statusMeta[$status]['color'] }}" title="{{ $statusMeta[$status]['label'] }}: {{ $count }}"></div>
                        @endif
                    @endforeach
                </div>
                <div class="grid grid-cols-2 gap-2.5">
                    @foreach($statusBreakdown as $status => $count)
                        <div class="flex items-center gap-2">
                            <div class="w-2.5 h-2.5 rounded-full flex-shrink-0" style="background: {{ $statusMeta[$status]['color'] }}"></div>
                            <span class="text-2xs text-erms-text-secondary flex-1">{{ $statusMeta[$status]['label'] }}</span>
                            <span class="text-[13px] font-semibold">{{ $count }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="py-6 text-center">
                    <p class="text-[13px] text-erms-muted">ยังไม่มีงาน</p>
                </div>
            @endif
        </div>

        {{-- Workload / Summary --}}
        @if(count($workloadData) > 0)
            <div class="card p-5">
                <h3 class="text-[13px] font-semibold text-erms-text mb-4">ปริมาณงานของแต่ละคน</h3>
                <div class="space-y-3">
                    @foreach($workloadData as $w)
                        @php $wTotal = $w->open_tasks + $w->done_tasks; @endphp
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-2xs font-medium text-erms-text truncate">{{ $w->name }}</span>
                                <span class="text-2xs text-erms-muted">{{ $w->open_tasks }} เปิด · {{ $w->done_tasks }} เสร็จ</span>
                            </div>
                            <div class="flex rounded-full h-1.5 overflow-hidden bg-erms-surface-2">
                                @if($wTotal > 0)
                                    <div class="bg-erms-green rounded-full transition-all duration-300" style="width: {{ ($w->done_tasks / $wTotal) * 100 }}%"></div>
                                    <div class="bg-erms-orange transition-all duration-300" style="width: {{ ($w->open_tasks / $wTotal) * 100 }}%"></div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <div class="card p-5">
                <h3 class="text-[13px] font-semibold text-erms-text mb-4">สรุปข้อมูล</h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-[13px] text-erms-text-secondary">งานทั้งหมดของฉัน</span>
                        <span class="text-[13px] font-semibold">{{ $statusBreakdown['todo'] + $statusBreakdown['in_progress'] + $statusBreakdown['review'] + $statusBreakdown['done'] }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-[13px] text-erms-text-secondary">กำลังทำอยู่</span>
                        <span class="text-[13px] font-semibold text-erms-blue">{{ $statusBreakdown['in_progress'] }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-[13px] text-erms-text-secondary">เสร็จแล้ว</span>
                        <span class="text-[13px] font-semibold text-erms-green">{{ $statusBreakdown['done'] }}</span>
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- ═══ My Tasks + Activity Feed ═══ --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        {{-- My Tasks --}}
        <div class="lg:col-span-2">
            <div class="card">
                <div class="px-4 py-3 border-b border-erms-border-light flex items-center justify-between">
                    <h2 class="text-[13px] font-semibold text-erms-text">งานของฉัน</h2>
                    <a href="{{ route('tasks.index') }}" class="text-2xs text-erms-blue hover:underline font-medium">ดูทั้งหมด →</a>
                </div>
                @php
                    $priorityLabels = ['low' => 'ต่ำ', 'medium' => 'ปานกลาง', 'high' => 'สูง', 'urgent' => 'เร่งด่วน'];
                    $statusLabels = ['todo' => 'รอดำเนินการ', 'in_progress' => 'กำลังดำเนินการ', 'review' => 'ตรวจสอบ', 'done' => 'เสร็จสิ้น'];
                @endphp
                <div class="divide-y divide-erms-border-light">
                    @forelse($myTasks as $task)
                        <a href="{{ route('tasks.show', $task) }}" class="task-row" wire:navigate>
                            <div class="flex-1 min-w-0 flex items-center gap-3">
                                <span class="task-checkbox {{ $task->status === 'done' ? 'checked' : '' }}">
                                    @if($task->status === 'done')
                                        <i class="fa-solid fa-check text-white text-xs"></i>
                                    @endif
                                </span>
                                <div class="min-w-0">
                                    <p class="text-[13px] font-medium truncate {{ $task->status === 'done' ? 'line-through text-erms-muted' : '' }}">{{ $task->title }}</p>
                                    <p class="text-2xs text-erms-muted">{{ $task->project->name ?? '' }}</p>
                                </div>
                            </div>
                            <span class="badge-{{ $task->priority }} hidden sm:inline-flex">{{ $priorityLabels[$task->priority] ?? $task->priority }}</span>
                            <span class="badge-{{ str_replace('_', '-', $task->status) }} ml-2">{{ $statusLabels[$task->status] ?? $task->status }}</span>
                            @if($task->due_date)
                                <span class="text-2xs whitespace-nowrap ml-2 {{ $task->due_date->isPast() ? 'text-erms-red font-medium' : 'text-erms-muted' }}">{{ $task->due_date->translatedFormat('d M') }}</span>
                            @endif
                        </a>
                    @empty
                        <div class="py-10 text-center">
                            <i class="fa-solid fa-circle-check text-4xl text-erms-muted/40 mb-2"></i>
                            <p class="text-[13px] text-erms-muted">ไม่มีงานที่ต้องทำ</p>
                        </div>
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
