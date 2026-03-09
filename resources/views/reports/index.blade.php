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
                <i class="fa-solid fa-chart-column"></i>
                ภาพรวม
            </a>
            <a href="{{ route('reports.index', ['tab' => 'projects']) }}"
               @click.prevent="tab = 'projects'"
               :class="tab === 'projects' ? 'bg-erms-blue text-white shadow-sm' : 'text-erms-muted hover:text-erms-text hover:bg-erms-surface-2'"
               class="flex items-center gap-1.5 px-3 py-1.5 rounded-md text-sm font-medium transition cursor-pointer">
                <i class="fa-solid fa-folder"></i>
                สุขภาพโครงการ
            </a>
            @if(auth()->user()->hasRole('admin'))
            <a href="{{ route('reports.index', ['tab' => 'team']) }}"
               @click.prevent="tab = 'team'"
               :class="tab === 'team' ? 'bg-erms-blue text-white shadow-sm' : 'text-erms-muted hover:text-erms-text hover:bg-erms-surface-2'"
               class="flex items-center gap-1.5 px-3 py-1.5 rounded-md text-sm font-medium transition cursor-pointer">
                <i class="fa-solid fa-users"></i>
                Workload ทีม
            </a>
            @endif
            <a href="{{ route('reports.index', ['tab' => 'project_monthly']) }}"
               @click.prevent="tab = 'project_monthly'"
               :class="tab === 'project_monthly' ? 'bg-erms-blue text-white shadow-sm' : 'text-erms-muted hover:text-erms-text hover:bg-erms-surface-2'"
               class="flex items-center gap-1.5 px-3 py-1.5 rounded-md text-sm font-medium transition cursor-pointer">
                <i class="fa-solid fa-clock"></i>
                รายโครงการ
            </a>
            <a href="{{ route('reports.index', ['tab' => 'user']) }}"
               @click.prevent="tab = 'user'"
               :class="tab === 'user' ? 'bg-erms-blue text-white shadow-sm' : 'text-erms-muted hover:text-erms-text hover:bg-erms-surface-2'"
               class="flex items-center gap-1.5 px-3 py-1.5 rounded-md text-sm font-medium transition cursor-pointer">
                <i class="fa-solid fa-user"></i>
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
                                <i class="fa-solid fa-circle-exclamation"></i>
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

        {{-- ═══════ PROJECT MONTHLY REPORT TAB ═══════ --}}
        <div x-show="tab === 'project_monthly'" x-cloak>
            <div class="card p-5 mb-6">
                <form method="GET" action="{{ route('reports.index') }}" class="flex flex-wrap items-end gap-4">
                    <input type="hidden" name="tab" value="project_monthly">
                    <div>
                        <label class="block text-sm text-erms-muted mb-1">โครงการ</label>
                        <select name="project_id" class="input-field w-56">
                            <option value="">เลือกโครงการ</option>
                            @foreach($projects as $proj)
                                <option value="{{ $proj->id }}" @selected($selectedProjectId == $proj->id)>{{ $proj->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm text-erms-muted mb-1">เดือน</label>
                        <input type="month" name="month" value="{{ $month }}" class="input-field w-44">
                    </div>
                    <button type="submit" class="btn-primary">ดูรายงาน</button>
                </form>
            </div>

            @if($selectedProjectId && $projectMonthlyTasks->count())
                {{-- Summary Cards --}}
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
                    <div class="card p-4 text-center">
                        <p class="text-2xl font-bold text-erms-text">{{ $projectMonthlyTasks->count() }}</p>
                        <p class="text-2xs text-erms-muted mt-0.5">งานทั้งหมด</p>
                    </div>
                    <div class="card p-4 text-center">
                        <p class="text-2xl font-bold text-erms-green">{{ $projectMonthlyTasks->where('status', 'done')->count() }}</p>
                        <p class="text-2xs text-erms-muted mt-0.5">เสร็จแล้ว</p>
                    </div>
                    <div class="card p-4 text-center">
                        <p class="text-2xl font-bold text-erms-blue">{{ number_format($projectTotalHours, 2) }}</p>
                        <p class="text-2xs text-erms-muted mt-0.5">ชั่วโมงที่บันทึก</p>
                    </div>
                    <div class="card p-4 text-center">
                        <p class="text-2xl font-bold {{ $projectEstimatedHours > 0 && $projectTotalHours > $projectEstimatedHours ? 'text-erms-red' : 'text-erms-muted' }}">{{ number_format($projectEstimatedHours, 0) }}</p>
                        <p class="text-2xs text-erms-muted mt-0.5">ชั่วโมงประมาณการ</p>
                    </div>
                </div>

                {{-- Tasks Table --}}
                <div class="card overflow-hidden mb-6">
                    <div class="px-5 py-3 border-b border-erms-border bg-erms-surface-2">
                        <h3 class="text-xs font-semibold text-erms-muted uppercase tracking-wider">รายการงานประจำเดือน</h3>
                    </div>
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-erms-border">
                                <th class="text-left px-5 py-3 text-xs font-medium text-erms-muted uppercase tracking-wider">งาน</th>
                                <th class="text-left px-5 py-3 text-xs font-medium text-erms-muted uppercase tracking-wider">ผู้รับผิดชอบ</th>
                                <th class="text-left px-5 py-3 text-xs font-medium text-erms-muted uppercase tracking-wider">สถานะ</th>
                                <th class="text-left px-5 py-3 text-xs font-medium text-erms-muted uppercase tracking-wider">ความสำคัญ</th>
                                <th class="text-right px-5 py-3 text-xs font-medium text-erms-muted uppercase tracking-wider">ชั่วโมงบันทึก</th>
                                <th class="text-right px-5 py-3 text-xs font-medium text-erms-muted uppercase tracking-wider">ชั่วโมงประมาณ</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-erms-border/50">
                            @foreach($projectMonthlyTasks as $task)
                                <tr class="hover:bg-erms-surface-2 transition">
                                    <td class="px-5 py-3">
                                        <a href="{{ route('tasks.show', $task) }}" class="text-[13px] font-medium hover:text-erms-blue transition" wire:navigate>{{ $task->title }}</a>
                                    </td>
                                    <td class="px-5 py-3">
                                        @if($task->assignee)
                                            <div class="flex items-center gap-1.5">
                                                <img src="{{ $task->assignee->avatar_url }}" alt="" class="w-5 h-5 rounded-full ring-1 ring-erms-border-light">
                                                <span class="text-2xs text-erms-text">{{ $task->assignee->name }}</span>
                                            </div>
                                        @else
                                            <span class="text-2xs text-erms-muted">ไม่ระบุ</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3"><span class="badge-{{ str_replace('_','-',$task->status) }}">{{ $statusLabels[$task->status] ?? $task->status }}</span></td>
                                    <td class="px-5 py-3"><span class="badge-{{ $task->priority }}">{{ $priorityLabels[$task->priority] ?? $task->priority }}</span></td>
                                    <td class="px-5 py-3 text-right text-[13px] font-medium text-erms-blue">{{ number_format($task->timeEntries->sum('hours'), 2) }}</td>
                                    <td class="px-5 py-3 text-right text-[13px] text-erms-muted">{{ $task->estimated_hours ? number_format($task->estimated_hours, 0) : '—' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Time Entries Breakdown --}}
                @if($projectTimeEntries->count())
                    <div class="card overflow-hidden">
                        <div class="px-5 py-3 border-b border-erms-border bg-erms-surface-2">
                            <h3 class="text-xs font-semibold text-erms-muted uppercase tracking-wider">รายละเอียดเวลาที่บันทึก</h3>
                        </div>
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-erms-border">
                                    <th class="text-left px-5 py-3 text-xs font-medium text-erms-muted uppercase tracking-wider">วันที่</th>
                                    <th class="text-left px-5 py-3 text-xs font-medium text-erms-muted uppercase tracking-wider">พนักงาน</th>
                                    <th class="text-left px-5 py-3 text-xs font-medium text-erms-muted uppercase tracking-wider">งาน</th>
                                    <th class="text-left px-5 py-3 text-xs font-medium text-erms-muted uppercase tracking-wider">รายละเอียด</th>
                                    <th class="text-right px-5 py-3 text-xs font-medium text-erms-muted uppercase tracking-wider">ชั่วโมง</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-erms-border/50">
                                @foreach($projectTimeEntries as $entry)
                                    <tr class="hover:bg-erms-surface-2 transition">
                                        <td class="px-5 py-3 text-2xs text-erms-muted whitespace-nowrap">{{ $entry->date_worked->translatedFormat('d M Y') }}</td>
                                        <td class="px-5 py-3">
                                            <div class="flex items-center gap-1.5">
                                                <img src="{{ $entry->user->avatar_url }}" alt="" class="w-5 h-5 rounded-full ring-1 ring-erms-border-light">
                                                <span class="text-2xs text-erms-text">{{ $entry->user->name }}</span>
                                            </div>
                                        </td>
                                        <td class="px-5 py-3 text-[13px] text-erms-text">{{ $entry->task->title }}</td>
                                        <td class="px-5 py-3 text-2xs text-erms-muted">{{ $entry->description ?? '—' }}</td>
                                        <td class="px-5 py-3 text-right text-[13px] font-medium text-erms-blue">{{ number_format($entry->hours, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="bg-erms-surface-2">
                                    <td colspan="4" class="px-5 py-3 text-xs font-semibold text-erms-text text-right">รวมชั่วโมง</td>
                                    <td class="px-5 py-3 text-right text-sm font-bold text-erms-blue">{{ number_format($projectTotalHours, 2) }} ชม.</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @endif
            @elseif($selectedProjectId)
                <div class="card p-8 text-center text-erms-muted">ไม่พบข้อมูลงานในเดือนที่เลือก</div>
            @else
                <div class="card p-8 text-center text-erms-muted">กรุณาเลือกโครงการเพื่อดูรายงาน</div>
            @endif
        </div>

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
                            <i class="fa-solid fa-file-pdf mr-1"></i>
                            PDF
                        </a>
                    @endif
                </form>
            </div>

            @if($selectedUserId && $tasks->count())
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 mb-6">
                    <div class="card p-4 text-center">
                        <p class="text-2xl font-bold text-erms-green">{{ $completedTasks }}/{{ $tasks->count() }}</p>
                        <p class="text-2xs text-erms-muted mt-0.5">งานเสร็จ/ทั้งหมด</p>
                    </div>
                    <div class="card p-4 text-center">
                        <p class="text-2xl font-bold text-erms-blue">{{ number_format($userTotalHours, 2) }}</p>
                        <p class="text-2xs text-erms-muted mt-0.5">ชั่วโมงที่บันทึก</p>
                    </div>
                    <div class="card p-4 text-center">
                        @php $avgHoursPerTask = $tasks->count() > 0 ? $userTotalHours / $tasks->count() : 0; @endphp
                        <p class="text-2xl font-bold text-erms-purple">{{ number_format($avgHoursPerTask, 1) }}</p>
                        <p class="text-2xs text-erms-muted mt-0.5">เฉลี่ย ชม./งาน</p>
                    </div>
                </div>

                <div class="card overflow-hidden mb-6">
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

                {{-- User Time Entries --}}
                @if($userTimeEntries->count())
                    <div class="card overflow-hidden">
                        <div class="px-5 py-3 border-b border-erms-border bg-erms-surface-2">
                            <h3 class="text-xs font-semibold text-erms-muted uppercase tracking-wider flex items-center gap-1.5">
                                <i class="fa-solid fa-clock"></i>
                                บันทึกเวลาการทำงาน
                            </h3>
                        </div>
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-erms-border">
                                    <th class="text-left px-5 py-3 text-xs font-medium text-erms-muted uppercase tracking-wider">วันที่</th>
                                    <th class="text-left px-5 py-3 text-xs font-medium text-erms-muted uppercase tracking-wider">งาน</th>
                                    <th class="text-left px-5 py-3 text-xs font-medium text-erms-muted uppercase tracking-wider">โครงการ</th>
                                    <th class="text-left px-5 py-3 text-xs font-medium text-erms-muted uppercase tracking-wider">รายละเอียด</th>
                                    <th class="text-right px-5 py-3 text-xs font-medium text-erms-muted uppercase tracking-wider">ชั่วโมง</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-erms-border/50">
                                @foreach($userTimeEntries as $entry)
                                    <tr class="hover:bg-erms-surface-2 transition">
                                        <td class="px-5 py-3 text-2xs text-erms-muted whitespace-nowrap">{{ $entry->date_worked->translatedFormat('d M Y') }}</td>
                                        <td class="px-5 py-3 text-[13px] text-erms-text">{{ $entry->task->title }}</td>
                                        <td class="px-5 py-3 text-2xs text-erms-muted">{{ $entry->task->project->name ?? '-' }}</td>
                                        <td class="px-5 py-3 text-2xs text-erms-muted">{{ $entry->description ?? '—' }}</td>
                                        <td class="px-5 py-3 text-right text-[13px] font-medium text-erms-blue">{{ number_format($entry->hours, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="bg-erms-surface-2">
                                    <td colspan="4" class="px-5 py-3 text-xs font-semibold text-erms-text text-right">รวมชั่วโมง</td>
                                    <td class="px-5 py-3 text-right text-sm font-bold text-erms-blue">{{ number_format($userTotalHours, 2) }} ชม.</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @endif
            @elseif($selectedUserId)
                <div class="card p-8 text-center text-erms-muted">ไม่พบข้อมูลงานในเดือนที่เลือก</div>
            @else
                <div class="card p-8 text-center text-erms-muted">กรุณาเลือกพนักงานเพื่อดูรายงาน</div>
            @endif
        </div>
    </div>
</x-app-layout>
