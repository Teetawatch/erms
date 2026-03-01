<x-app-layout>
    <x-slot name="header">รายงาน</x-slot>

    @php
        $statusLabels = ['todo' => 'รอดำเนินการ', 'in_progress' => 'กำลังดำเนินการ', 'review' => 'ตรวจสอบ', 'done' => 'เสร็จสิ้น'];
        $priorityLabels = ['urgent' => 'เร่งด่วน', 'high' => 'สูง', 'medium' => 'ปานกลาง', 'low' => 'ต่ำ'];
        $healthLabels = ['on_track' => 'ปกติ', 'needs_attention' => 'ต้องดูแล', 'at_risk' => 'เสี่ยง'];
        $healthColors = ['on_track' => 'text-erms-green bg-erms-green/10', 'needs_attention' => 'text-erms-orange bg-erms-orange/10', 'at_risk' => 'text-erms-red bg-erms-red/10'];
    @endphp

    {{-- Tabs --}}
    <div x-data="{ tab: '{{ $tab }}' }" class="space-y-6">
        <div class="flex items-center gap-1 bg-erms-surface border border-erms-border rounded-lg p-1 w-fit">
            <a href="{{ route('reports.index', ['tab' => 'overview']) }}"
               @click.prevent="tab = 'overview'"
               :class="tab === 'overview' ? 'bg-erms-blue text-white shadow-sm' : 'text-erms-muted hover:text-erms-text hover:bg-erms-surface-2'"
               class="flex items-center gap-1.5 px-3 py-1.5 rounded-md text-sm font-medium transition cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                ภาพรวม
            </a>
            <a href="{{ route('reports.index', ['tab' => 'projects']) }}"
               @click.prevent="tab = 'projects'"
               :class="tab === 'projects' ? 'bg-erms-blue text-white shadow-sm' : 'text-erms-muted hover:text-erms-text hover:bg-erms-surface-2'"
               class="flex items-center gap-1.5 px-3 py-1.5 rounded-md text-sm font-medium transition cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                สุขภาพโครงการ
            </a>
            @if(auth()->user()->hasRole('admin'))
            <a href="{{ route('reports.index', ['tab' => 'team']) }}"
               @click.prevent="tab = 'team'"
               :class="tab === 'team' ? 'bg-erms-blue text-white shadow-sm' : 'text-erms-muted hover:text-erms-text hover:bg-erms-surface-2'"
               class="flex items-center gap-1.5 px-3 py-1.5 rounded-md text-sm font-medium transition cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Workload ทีม
            </a>
            @endif
            <a href="{{ route('reports.index', ['tab' => 'user']) }}"
               @click.prevent="tab = 'user'"
               :class="tab === 'user' ? 'bg-erms-blue text-white shadow-sm' : 'text-erms-muted hover:text-erms-text hover:bg-erms-surface-2'"
               class="flex items-center gap-1.5 px-3 py-1.5 rounded-md text-sm font-medium transition cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                รายบุคคล
            </a>
        </div>

        {{-- ═══════ OVERVIEW TAB ═══════ --}}
        <div x-show="tab === 'overview'" x-cloak>
            {{-- KPI Cards --}}
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 mb-6">
                <div class="card p-4 text-center">
                    <p class="text-2xl font-bold text-erms-text">{{ $overview['total'] }}</p>
                    <p class="text-2xs text-erms-muted mt-0.5">งานทั้งหมด</p>
                </div>
                <div class="card p-4 text-center">
                    <p class="text-2xl font-bold text-erms-green">{{ $overview['done'] }}</p>
                    <p class="text-2xs text-erms-muted mt-0.5">เสร็จแล้ว</p>
                </div>
                <div class="card p-4 text-center">
                    <p class="text-2xl font-bold text-erms-blue">{{ $overview['in_progress'] }}</p>
                    <p class="text-2xs text-erms-muted mt-0.5">กำลังดำเนินการ</p>
                </div>
                <div class="card p-4 text-center">
                    <p class="text-2xl font-bold text-erms-purple">{{ $overview['review'] }}</p>
                    <p class="text-2xs text-erms-muted mt-0.5">ตรวจสอบ</p>
                </div>
                <div class="card p-4 text-center">
                    <p class="text-2xl font-bold text-erms-muted">{{ $overview['todo'] }}</p>
                    <p class="text-2xs text-erms-muted mt-0.5">รอดำเนินการ</p>
                </div>
                <div class="card p-4 text-center">
                    <p class="text-2xl font-bold {{ $overview['overdue'] > 0 ? 'text-erms-red' : 'text-erms-muted' }}">{{ $overview['overdue'] }}</p>
                    <p class="text-2xs text-erms-muted mt-0.5">เลยกำหนด</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Completion Rate --}}
                <div class="card p-6">
                    <h3 class="font-heading font-bold text-sm mb-4">อัตราการเสร็จงาน</h3>
                    <div class="flex items-center gap-6">
                        <div class="relative w-28 h-28 flex-shrink-0">
                            <svg viewBox="0 0 36 36" class="w-full h-full -rotate-90">
                                <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#e8ecee" stroke-width="3"/>
                                <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#5da283" stroke-width="3" stroke-dasharray="{{ $overview['completion_rate'] }}, 100" stroke-linecap="round"/>
                            </svg>
                            <div class="absolute inset-0 flex items-center justify-center">
                                <span class="text-xl font-bold text-erms-text">{{ $overview['completion_rate'] }}%</span>
                            </div>
                        </div>
                        <div class="space-y-2 flex-1">
                            @foreach(['done' => ['เสร็จ', 'bg-erms-green'], 'in_progress' => ['กำลังทำ', 'bg-erms-blue'], 'review' => ['ตรวจสอบ', 'bg-erms-purple'], 'todo' => ['รอทำ', 'bg-erms-muted']] as $s => $info)
                                <div class="flex items-center gap-2">
                                    <div class="w-2.5 h-2.5 rounded-full {{ $info[1] }}"></div>
                                    <span class="text-2xs text-erms-muted flex-1">{{ $info[0] }}</span>
                                    <span class="text-2xs font-medium text-erms-text">{{ $overview[$s] ?? 0 }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Priority Breakdown --}}
                <div class="card p-6">
                    <h3 class="font-heading font-bold text-sm mb-4">แจกแจงตามความสำคัญ</h3>
                    <div class="space-y-3">
                        @php $maxP = max(1, max($overview['by_priority'])); @endphp
                        @foreach(['urgent' => ['เร่งด่วน', 'bg-erms-red'], 'high' => ['สูง', 'bg-erms-orange'], 'medium' => ['ปานกลาง', 'bg-erms-yellow'], 'low' => ['ต่ำ', 'bg-erms-green']] as $p => $info)
                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-2xs text-erms-muted">{{ $info[0] }}</span>
                                    <span class="text-2xs font-medium text-erms-text">{{ $overview['by_priority'][$p] }}</span>
                                </div>
                                <div class="w-full h-2 bg-erms-surface-2 rounded-full overflow-hidden">
                                    <div class="{{ $info[1] }} h-full rounded-full transition-all" style="width: {{ ($overview['by_priority'][$p] / $maxP) * 100 }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══════ PROJECT HEALTH TAB ═══════ --}}
        <div x-show="tab === 'projects'" x-cloak>
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                @forelse($projectsHealth as $p)
                    <a href="{{ route('projects.show', $p['id']) }}" class="card p-5 hover:shadow-asana-md transition group" wire:navigate>
                        <div class="flex items-start justify-between mb-3">
                            <h4 class="text-[13px] font-semibold text-erms-text group-hover:text-erms-blue transition truncate flex-1 mr-2">{{ $p['name'] }}</h4>
                            <span class="text-2xs font-medium px-2 py-0.5 rounded-full flex-shrink-0 {{ $healthColors[$p['health']] }}">{{ $healthLabels[$p['health']] }}</span>
                        </div>
                        <div class="progress-bar mb-2"><div class="fill" style="width: {{ $p['progress'] }}%"></div></div>
                        <div class="flex items-center justify-between text-2xs text-erms-muted">
                            <span>{{ $p['progress'] }}% เสร็จ</span>
                            <span>{{ $p['done'] }}/{{ $p['total'] }} งาน</span>
                        </div>
                        @if($p['overdue'] > 0)
                            <div class="mt-2 flex items-center gap-1 text-2xs text-erms-red">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                {{ $p['overdue'] }} งานเลยกำหนด
                            </div>
                        @endif
                    </a>
                @empty
                    <div class="col-span-full card p-12 text-center text-erms-muted">ไม่มีโครงการ</div>
                @endforelse
            </div>
        </div>

        {{-- ═══════ TEAM WORKLOAD TAB ═══════ --}}
        @if(auth()->user()->hasRole('admin'))
        <div x-show="tab === 'team'" x-cloak>
            <div class="card overflow-hidden">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-erms-border bg-erms-surface-2">
                            <th class="text-left px-5 py-3 text-xs font-medium text-erms-muted uppercase tracking-wider">สมาชิก</th>
                            <th class="text-center px-5 py-3 text-xs font-medium text-erms-muted uppercase tracking-wider">งานที่กำลังทำ</th>
                            <th class="text-center px-5 py-3 text-xs font-medium text-erms-muted uppercase tracking-wider">เสร็จแล้ว</th>
                            <th class="text-center px-5 py-3 text-xs font-medium text-erms-muted uppercase tracking-wider">เลยกำหนด</th>
                            <th class="text-left px-5 py-3 text-xs font-medium text-erms-muted uppercase tracking-wider">Workload</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-erms-border/50">
                        @php $maxActive = max(1, $teamWorkload->max('active_tasks')); @endphp
                        @foreach($teamWorkload->sortByDesc('active_tasks') as $member)
                            <tr class="hover:bg-erms-surface-2/60 transition">
                                <td class="px-5 py-3">
                                    <div class="flex items-center gap-2.5">
                                        <img src="{{ $member->avatar_url }}" alt="" class="w-7 h-7 rounded-full ring-1 ring-erms-border-light">
                                        <span class="font-medium text-erms-text">{{ $member->name }}</span>
                                    </div>
                                </td>
                                <td class="px-5 py-3 text-center font-medium text-erms-blue">{{ $member->active_tasks }}</td>
                                <td class="px-5 py-3 text-center font-medium text-erms-green">{{ $member->done_tasks }}</td>
                                <td class="px-5 py-3 text-center font-medium {{ $member->overdue_tasks > 0 ? 'text-erms-red' : 'text-erms-muted' }}">{{ $member->overdue_tasks }}</td>
                                <td class="px-5 py-3">
                                    @php
                                        $loadPct = ($member->active_tasks / $maxActive) * 100;
                                        $loadColor = $member->active_tasks > 8 ? 'bg-erms-red' : ($member->active_tasks > 5 ? 'bg-erms-orange' : 'bg-erms-blue');
                                    @endphp
                                    <div class="w-full h-2 bg-erms-surface-2 rounded-full overflow-hidden">
                                        <div class="{{ $loadColor }} h-full rounded-full transition-all" style="width: {{ $loadPct }}%"></div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        {{-- ═══════ USER REPORT TAB ═══════ --}}
        <div x-show="tab === 'user'" x-cloak>
            <div class="card p-5 mb-6">
                <form method="GET" action="{{ route('reports.index') }}" class="flex flex-wrap items-end gap-4">
                    <input type="hidden" name="tab" value="user">
                    <div>
                        <label class="block text-sm text-erms-muted mb-1">พนักงาน</label>
                        <select name="user_id" class="input-field w-48">
                            <option value="">เลือกพนักงาน</option>
                            @foreach($users as $u)
                                <option value="{{ $u->id }}" @selected($selectedUserId == $u->id)>{{ $u->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm text-erms-muted mb-1">เดือน</label>
                        <input type="month" name="month" value="{{ $month }}" class="input-field w-44">
                    </div>
                    <button type="submit" class="btn-primary">ดูรายงาน</button>
                    @if($selectedUserId && $tasks->count())
                        <a href="{{ route('reports.export-pdf', ['user_id' => $selectedUserId, 'month' => $month]) }}" class="btn-secondary">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            PDF
                        </a>
                    @endif
                </form>
            </div>

            @if($selectedUserId && $tasks->count())
                <div class="card p-5 mb-6">
                    <div class="flex items-center justify-between">
                        <h3 class="font-heading font-bold text-base">สรุปงานที่มอบหมาย</h3>
                        <span class="text-2xl font-heading font-bold text-erms-green">{{ $completedTasks }}/{{ $tasks->count() }}</span>
                    </div>
                </div>
                <div class="card overflow-hidden">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-erms-border bg-erms-surface-2">
                                <th class="text-left px-5 py-3 text-xs font-medium text-erms-muted uppercase tracking-wider">วันที่</th>
                                <th class="text-left px-5 py-3 text-xs font-medium text-erms-muted uppercase tracking-wider">งาน</th>
                                <th class="text-left px-5 py-3 text-xs font-medium text-erms-muted uppercase tracking-wider">โครงการ</th>
                                <th class="text-left px-5 py-3 text-xs font-medium text-erms-muted uppercase tracking-wider">สถานะ</th>
                                <th class="text-left px-5 py-3 text-xs font-medium text-erms-muted uppercase tracking-wider">ความสำคัญ</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-erms-border/50">
                            @foreach($tasks as $task)
                                <tr class="hover:bg-erms-surface-2 transition">
                                    <td class="px-5 py-3 text-erms-muted whitespace-nowrap text-2xs">{{ $task->created_at->translatedFormat('d M Y') }}</td>
                                    <td class="px-5 py-3"><a href="{{ route('tasks.show', $task) }}" class="text-[13px] font-medium hover:text-erms-blue transition" wire:navigate>{{ $task->title }}</a></td>
                                    <td class="px-5 py-3 text-erms-muted text-2xs">{{ $task->project->name ?? '-' }}</td>
                                    <td class="px-5 py-3"><span class="badge-{{ str_replace('_','-',$task->status) }}">{{ $statusLabels[$task->status] ?? $task->status }}</span></td>
                                    <td class="px-5 py-3"><span class="badge-{{ $task->priority }}">{{ $priorityLabels[$task->priority] ?? $task->priority }}</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @elseif($selectedUserId)
                <div class="card p-8 text-center text-erms-muted">ไม่พบข้อมูลงานในเดือนที่เลือก</div>
            @else
                <div class="card p-8 text-center text-erms-muted">กรุณาเลือกพนักงานเพื่อดูรายงาน</div>
            @endif
        </div>
    </div>
</x-app-layout>
