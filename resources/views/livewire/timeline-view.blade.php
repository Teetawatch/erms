<div>
    {{-- ═══ Filter Bar (Asana-style inline) ═══ --}}
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
        <div class="card overflow-hidden">
            @php
                $minDate = \Carbon\Carbon::parse($timeline['minDate']);
                $maxDate = \Carbon\Carbon::parse($timeline['maxDate']);
                $totalDays = $timeline['totalDays'];

                $weeks = [];
                $current = $minDate->copy()->startOfWeek();
                while ($current <= $maxDate) {
                    $weeks[] = $current->copy();
                    $current->addWeek();
                }

                $statusColors = [
                    'todo' => '#9ca0a4',
                    'in_progress' => '#4573d2',
                    'review' => '#7c5cfc',
                    'done' => '#5da283',
                ];
            @endphp

            <div class="overflow-x-auto">
                <div class="min-w-[900px]">
                    {{-- Week Headers --}}
                    <div class="flex border-b border-erms-border bg-erms-surface-2/60">
                        <div class="w-52 flex-shrink-0 px-4 py-2.5 border-r border-erms-border-light">
                            <span class="text-[11px] font-semibold text-erms-muted uppercase tracking-wider">งาน</span>
                        </div>
                        <div class="flex-1 relative" style="height: 40px;">
                            @foreach($weeks as $week)
                                @php
                                    $weekStart = max(0, $minDate->diffInDays($week));
                                    $leftPct = ($weekStart / $totalDays) * 100;
                                @endphp
                                <div class="absolute top-0 bottom-0 border-l border-erms-border-light px-2 flex items-center" style="left: {{ $leftPct }}%">
                                    <span class="text-2xs text-erms-muted whitespace-nowrap font-medium">{{ $week->translatedFormat('d M') }}</span>
                                </div>
                            @endforeach

                            {{-- Today marker --}}
                            @php
                                $todayOffset = $minDate->diffInDays(now());
                                $todayPct = ($todayOffset / $totalDays) * 100;
                            @endphp
                            @if($todayPct >= 0 && $todayPct <= 100)
                                <div class="absolute top-0 bottom-0 w-0.5 bg-erms-red z-10" style="left: {{ $todayPct }}%">
                                    <span class="absolute top-0 -translate-x-1/2 text-[9px] text-white font-medium bg-erms-red px-1 py-0.5 rounded-b text-center leading-none">วันนี้</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Task Rows --}}
                    @foreach($timeline['tasks'] as $t)
                        <div class="flex border-b border-erms-border-light hover:bg-erms-surface-2/40 transition-colors duration-75 group">
                            {{-- Task Info --}}
                            <div class="w-52 flex-shrink-0 px-4 py-2.5 border-r border-erms-border-light">
                                <a href="{{ $t['url'] }}" class="text-[13px] font-medium text-erms-text hover:text-erms-blue transition truncate block" wire:navigate>
                                    {{ $t['title'] }}
                                </a>
                                <div class="flex items-center gap-1.5 mt-0.5">
                                    @if($t['assignee_avatar'])
                                        <img src="{{ $t['assignee_avatar'] }}" alt="" class="w-4 h-4 rounded-full ring-1 ring-erms-border-light">
                                    @endif
                                    <span class="text-2xs text-erms-muted">{{ $t['start_date'] }} → {{ $t['due_date'] }}</span>
                                </div>
                            </div>

                            {{-- Gantt Bar --}}
                            <div class="flex-1 relative py-2.5 px-1">
                                <div class="absolute top-1/2 -translate-y-1/2 rounded-md h-6 flex items-center px-2 text-white text-2xs font-medium cursor-pointer transition-all duration-150 hover:brightness-110 hover:shadow-asana-md"
                                     style="left: {{ $t['left'] }}%; width: {{ max($t['width'], 2) }}%; background: {{ $statusColors[$t['status']] ?? '#4573d2' }};"
                                     title="{{ $t['title'] }} ({{ $t['progress'] }}%)">
                                    @if($t['width'] > 8)
                                        <span class="truncate">{{ $t['title'] }}</span>
                                    @endif
                                    @if($t['progress'] > 0)
                                        <div class="absolute inset-0 rounded-md bg-white/20" style="width: {{ $t['progress'] }}%"></div>
                                    @endif
                                </div>

                                {{-- Dependencies --}}
                                @foreach($t['dependencies'] as $depId)
                                    @php $depTask = collect($timeline['tasks'])->firstWhere('id', $depId); @endphp
                                    @if($depTask)
                                        <div class="absolute top-1/2 h-px bg-erms-muted/30" style="left: {{ $depTask['left'] + $depTask['width'] }}%; width: {{ max($t['left'] - ($depTask['left'] + $depTask['width']), 0.5) }}%;"></div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Legend --}}
        <div class="flex items-center gap-4 mt-3">
            @php $sLabels = ['todo' => 'รอดำเนินการ', 'in_progress' => 'กำลังดำเนินการ', 'review' => 'ตรวจสอบ', 'done' => 'เสร็จสิ้น']; @endphp
            @foreach($statusColors as $status => $color)
                <div class="flex items-center gap-1.5">
                    <div class="w-3 h-2 rounded-sm" style="background: {{ $color }}"></div>
                    <span class="text-2xs text-erms-muted">{{ $sLabels[$status] }}</span>
                </div>
            @endforeach
        </div>
    @endif
</div>
