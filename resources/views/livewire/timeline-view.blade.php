<div>
    {{-- ═══ Filter Bar ═══ --}}
    <div class="flex flex-wrap items-center gap-2 mb-3">
        <select wire:model.live="filterStatus" class="input-field !w-auto !py-1.5 !text-[13px] !pr-8">
            <option value="">สถานะทั้งหมด</option>
            <option value="todo">รอดำเนินการ</option>
            <option value="in_progress">กำลังดำเนินการ</option>
            <option value="review">ตรวจสอบ</option>
            <option value="done">เสร็จสิ้น</option>
        </select>
        <select wire:model.live="filterAssignee" class="input-field !w-auto !py-1.5 !text-[13px] !pr-8">
            <option value="">ผู้รับผิดชอบทั้งหมด</option>
            @foreach($this->users as $user)
                <option value="{{ $user->id }}">{{ $user->name }}</option>
            @endforeach
        </select>
        <span class="text-2xs text-erms-muted flex items-center gap-1 ml-2">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            แสดงเฉพาะงานที่มีวันเริ่มต้นและกำหนดส่ง
        </span>
    </div>

    @php $timeline = $this->timelineData; @endphp

    @if(count($timeline['tasks']) === 0)
        <div class="card py-16 text-center">
            <svg class="w-12 h-12 mx-auto text-erms-muted/40 mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            <p class="text-[13px] text-erms-muted">ไม่มีงานที่มีวันเริ่มต้นและกำหนดส่ง</p>
            <p class="text-2xs text-erms-muted mt-1">เพิ่ม "วันเริ่มต้น" และ "กำหนดส่ง" ให้กับงานเพื่อดูใน Timeline</p>
        </div>
    @else
        @php
            $minDate = \Carbon\Carbon::parse($timeline['minDate']);
            $maxDate = \Carbon\Carbon::parse($timeline['maxDate']);
            $totalDays = $timeline['totalDays'];
            $statusColors = ['todo' => '#9ca0a4', 'in_progress' => '#4573d2', 'review' => '#7c5cfc', 'done' => '#5da283'];
            $statusLabels = ['todo' => 'รอดำเนินการ', 'in_progress' => 'กำลังดำเนินการ', 'review' => 'ตรวจสอบ', 'done' => 'เสร็จสิ้น'];
            $priorityLabels = ['low' => 'ต่ำ', 'medium' => 'ปานกลาง', 'high' => 'สูง', 'urgent' => 'เร่งด่วน'];

            // Generate day markers
            $weeks = [];
            $current = $minDate->copy()->startOfWeek();
            while ($current <= $maxDate) { $weeks[] = $current->copy(); $current->addWeek(); }

            $todayOffset = $minDate->diffInDays(now());
            $todayPct = ($todayOffset / $totalDays) * 100;
        @endphp

        {{-- Summary stats --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-4">
            <div class="card p-3 text-center">
                <p class="text-xl font-bold text-erms-text">{{ count($timeline['tasks']) }}</p>
                <p class="text-2xs text-erms-muted">งานทั้งหมด</p>
            </div>
            <div class="card p-3 text-center">
                <p class="text-xl font-bold text-erms-green">{{ collect($timeline['tasks'])->where('status', 'done')->count() }}</p>
                <p class="text-2xs text-erms-muted">เสร็จแล้ว</p>
            </div>
            <div class="card p-3 text-center">
                <p class="text-xl font-bold text-erms-blue">{{ collect($timeline['tasks'])->whereIn('status', ['in_progress','review'])->count() }}</p>
                <p class="text-2xs text-erms-muted">กำลังดำเนินการ</p>
            </div>
            <div class="card p-3 text-center">
                @php $overdue = collect($timeline['tasks'])->filter(fn($t) => $t['status'] !== 'done' && \Carbon\Carbon::parse($t['due_date'])->isPast())->count(); @endphp
                <p class="text-xl font-bold {{ $overdue > 0 ? 'text-erms-red' : 'text-erms-muted' }}">{{ $overdue }}</p>
                <p class="text-2xs text-erms-muted">เลยกำหนด</p>
            </div>
        </div>

        <div class="card overflow-hidden">
            <div class="overflow-x-auto">
                <div class="min-w-[900px]">
                    {{-- Week Headers --}}
                    <div class="flex border-b border-erms-border bg-erms-surface-2/60 sticky top-0 z-10">
                        <div class="w-56 flex-shrink-0 px-4 py-2.5 border-r border-erms-border-light">
                            <span class="text-[11px] font-semibold text-erms-muted uppercase tracking-wider">งาน</span>
                        </div>
                        <div class="flex-1 relative" style="height: 40px;">
                            @foreach($weeks as $week)
                                @php $leftPct = (max(0, $minDate->diffInDays($week)) / $totalDays) * 100; @endphp
                                <div class="absolute top-0 bottom-0 border-l border-erms-border-light px-2 flex items-center" style="left: {{ $leftPct }}%">
                                    <span class="text-2xs text-erms-muted whitespace-nowrap font-medium">{{ $week->translatedFormat('d M') }}</span>
                                </div>
                            @endforeach

                            @if($todayPct >= 0 && $todayPct <= 100)
                                <div class="absolute top-0 bottom-0 w-0.5 bg-erms-red z-10" style="left: {{ $todayPct }}%">
                                    <span class="absolute top-0 -translate-x-1/2 text-[9px] text-white font-medium bg-erms-red px-1 py-0.5 rounded-b text-center leading-none">วันนี้</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Task Rows --}}
                    @foreach($timeline['tasks'] as $idx => $t)
                        <div class="flex border-b border-erms-border-light hover:bg-erms-surface-2/40 transition-colors duration-75 group"
                             x-data="{ showTooltip: false }" style="min-height: 44px;">
                            {{-- Task Info --}}
                            <div class="w-56 flex-shrink-0 px-4 py-2 border-r border-erms-border-light flex items-center gap-2.5">
                                <span class="w-2 h-2 rounded-full flex-shrink-0" style="background: {{ $statusColors[$t['status']] ?? '#9ca0a4' }}"></span>
                                <div class="min-w-0 flex-1">
                                    <a href="{{ $t['url'] }}" class="text-[13px] font-medium text-erms-text hover:text-erms-blue transition truncate block leading-tight" wire:navigate>
                                        {{ $t['title'] }}
                                    </a>
                                    <div class="flex items-center gap-1.5 mt-0.5">
                                        @if($t['assignee_avatar'])
                                            <img src="{{ $t['assignee_avatar'] }}" alt="" class="w-3.5 h-3.5 rounded-full">
                                        @endif
                                        <span class="text-2xs text-erms-muted">{{ \Carbon\Carbon::parse($t['start_date'])->translatedFormat('d M') }} – {{ \Carbon\Carbon::parse($t['due_date'])->translatedFormat('d M') }}</span>
                                    </div>
                                </div>
                            </div>

                            {{-- Gantt Bar --}}
                            <div class="flex-1 relative py-2 px-1">
                                {{-- Week grid lines --}}
                                @foreach($weeks as $week)
                                    @php $leftPct = (max(0, $minDate->diffInDays($week)) / $totalDays) * 100; @endphp
                                    <div class="absolute top-0 bottom-0 border-l border-erms-border-light/50" style="left: {{ $leftPct }}%"></div>
                                @endforeach

                                {{-- Today line --}}
                                @if($todayPct >= 0 && $todayPct <= 100)
                                    <div class="absolute top-0 bottom-0 w-0.5 bg-erms-red/20" style="left: {{ $todayPct }}%"></div>
                                @endif

                                {{-- Dependency lines --}}
                                @foreach($t['dependencies'] as $depId)
                                    @php $depTask = collect($timeline['tasks'])->firstWhere('id', $depId); @endphp
                                    @if($depTask)
                                        @php
                                            $depEnd = $depTask['left'] + $depTask['width'];
                                            $gap = max($t['left'] - $depEnd, 0);
                                        @endphp
                                        <svg class="absolute top-0 left-0 w-full h-full overflow-visible pointer-events-none" style="z-index: 2;">
                                            <line x1="{{ $depEnd }}%" y1="50%" x2="{{ $t['left'] }}%" y2="50%"
                                                  stroke="#9ca0a4" stroke-width="1.5" stroke-dasharray="4,3" />
                                            <polygon points="{{ $t['left'] - 0.3 }}% 40%, {{ $t['left'] }}% 50%, {{ $t['left'] - 0.3 }}% 60%"
                                                     fill="#9ca0a4" transform="translate(-2,0)" />
                                        </svg>
                                    @endif
                                @endforeach

                                {{-- Bar --}}
                                <div class="absolute top-1/2 -translate-y-1/2 rounded-md h-7 flex items-center px-2 text-white text-2xs font-medium cursor-pointer transition-all duration-150 hover:brightness-110 hover:shadow-asana-md group/bar"
                                     style="left: {{ $t['left'] }}%; width: {{ max($t['width'], 2) }}%; background: {{ $statusColors[$t['status']] ?? '#4573d2' }}; z-index: 3;"
                                     @mouseenter="showTooltip = true" @mouseleave="showTooltip = false">
                                    {{-- Progress fill --}}
                                    @if($t['progress'] > 0)
                                        <div class="absolute inset-y-0 left-0 rounded-l-md bg-white/20" style="width: {{ $t['progress'] }}%"></div>
                                    @endif
                                    @if($t['width'] > 6)
                                        <span class="truncate relative z-10">{{ $t['title'] }}</span>
                                    @endif
                                    @if($t['subtask_count'] > 0 && $t['width'] > 12)
                                        <span class="ml-auto text-white/70 text-[10px] relative z-10">{{ $t['subtask_done'] }}/{{ $t['subtask_count'] }}</span>
                                    @endif
                                </div>

                                {{-- Tooltip --}}
                                <div x-show="showTooltip" x-transition.opacity x-cloak
                                     class="absolute z-50 bg-white border border-erms-border rounded-lg shadow-asana-lg p-3 w-56 pointer-events-none"
                                     style="left: {{ min($t['left'] + $t['width']/2, 75) }}%; top: -8px; transform: translateY(-100%);">
                                    <p class="text-[13px] font-medium text-erms-text truncate">{{ $t['title'] }}</p>
                                    <div class="mt-1.5 space-y-1">
                                        <div class="flex justify-between text-2xs"><span class="text-erms-muted">สถานะ</span><span class="font-medium" style="color: {{ $statusColors[$t['status']] }}">{{ $statusLabels[$t['status']] ?? $t['status'] }}</span></div>
                                        <div class="flex justify-between text-2xs"><span class="text-erms-muted">ความสำคัญ</span><span class="badge-{{ $t['priority'] }}">{{ $priorityLabels[$t['priority']] ?? $t['priority'] }}</span></div>
                                        @if($t['assignee'])<div class="flex justify-between text-2xs"><span class="text-erms-muted">ผู้รับผิดชอบ</span><span class="font-medium text-erms-text">{{ $t['assignee'] }}</span></div>@endif
                                        <div class="flex justify-between text-2xs"><span class="text-erms-muted">ระยะเวลา</span><span class="text-erms-text">{{ \Carbon\Carbon::parse($t['start_date'])->diffInDays(\Carbon\Carbon::parse($t['due_date'])) + 1 }} วัน</span></div>
                                        @if($t['progress'] > 0)<div class="flex justify-between text-2xs"><span class="text-erms-muted">ความคืบหน้า</span><span class="text-erms-text">{{ $t['progress'] }}%</span></div>@endif
                                        @if($t['subtask_count'] > 0)<div class="flex justify-between text-2xs"><span class="text-erms-muted">งานย่อย</span><span class="text-erms-text">{{ $t['subtask_done'] }}/{{ $t['subtask_count'] }}</span></div>@endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Legend --}}
        <div class="flex items-center gap-4 mt-3 flex-wrap">
            @foreach($statusColors as $status => $color)
                <div class="flex items-center gap-1.5">
                    <div class="w-3 h-2 rounded-sm" style="background: {{ $color }}"></div>
                    <span class="text-2xs text-erms-muted">{{ $statusLabels[$status] }}</span>
                </div>
            @endforeach
            <div class="flex items-center gap-1.5">
                <svg class="w-5 h-px" style="overflow:visible"><line x1="0" y1="0" x2="20" y2="0" stroke="#9ca0a4" stroke-width="1.5" stroke-dasharray="4,3" /></svg>
                <span class="text-2xs text-erms-muted">dependency</span>
            </div>
            <div class="flex items-center gap-1.5">
                <div class="w-0.5 h-3 bg-erms-red rounded"></div>
                <span class="text-2xs text-erms-muted">วันนี้</span>
            </div>
        </div>
    @endif
</div>
