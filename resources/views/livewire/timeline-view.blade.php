<div>
    {{-- Filters --}}
    <div class="card p-4 mb-4">
        <div class="flex flex-wrap items-end gap-3">
            <div>
                <label class="block text-xs text-erms-muted mb-1">สถานะ</label>
                <select wire:model.live="filterStatus" class="input-field w-40">
                    <option value="">ทั้งหมด</option>
                    <option value="todo">รอดำเนินการ</option>
                    <option value="in_progress">กำลังดำเนินการ</option>
                    <option value="review">ตรวจสอบ</option>
                    <option value="done">เสร็จสิ้น</option>
                </select>
            </div>
            <div>
                <label class="block text-xs text-erms-muted mb-1">ผู้รับผิดชอบ</label>
                <select wire:model.live="filterAssignee" class="input-field w-40">
                    <option value="">ทั้งหมด</option>
                    @foreach($this->users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="text-xs text-erms-muted flex items-center gap-2 pb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                แสดงเฉพาะงานที่มีวันเริ่มต้นและกำหนดส่ง
            </div>
        </div>
    </div>

    @php $timeline = $this->timelineData; @endphp

    @if(count($timeline['tasks']) === 0)
        <div class="card p-12 text-center">
            <svg class="w-12 h-12 mx-auto text-erms-muted mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            <p class="text-erms-muted">ไม่มีงานที่มีวันเริ่มต้นและกำหนดส่ง</p>
            <p class="text-xs text-erms-muted mt-1">เพิ่ม "วันเริ่มต้น" และ "กำหนดส่ง" ให้กับงานเพื่อดูใน Timeline</p>
        </div>
    @else
        <div class="card overflow-hidden">
            {{-- Date Header --}}
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
            @endphp

            <div class="overflow-x-auto">
                <div class="min-w-[900px]">
                    {{-- Week Headers --}}
                    <div class="flex border-b border-erms-border bg-erms-surface-2">
                        <div class="w-56 flex-shrink-0 px-4 py-2 border-r border-erms-border">
                            <span class="text-xs font-medium text-erms-muted uppercase">งาน</span>
                        </div>
                        <div class="flex-1 relative" style="height: 48px;">
                            @foreach($weeks as $week)
                                @php
                                    $weekStart = max(0, $minDate->diffInDays($week));
                                    $leftPct = ($weekStart / $totalDays) * 100;
                                @endphp
                                <div class="absolute top-0 bottom-0 border-l border-erms-border/50 px-2 py-2" style="left: {{ $leftPct }}%">
                                    <span class="text-[10px] text-erms-muted whitespace-nowrap">{{ $week->translatedFormat('d M') }}</span>
                                </div>
                            @endforeach

                            {{-- Today marker --}}
                            @php
                                $todayOffset = $minDate->diffInDays(now());
                                $todayPct = ($todayOffset / $totalDays) * 100;
                            @endphp
                            @if($todayPct >= 0 && $todayPct <= 100)
                                <div class="absolute top-0 bottom-0 w-0.5 bg-erms-red z-10" style="left: {{ $todayPct }}%">
                                    <span class="absolute -top-0 -translate-x-1/2 text-[9px] text-erms-red font-medium bg-erms-red/10 px-1 rounded">วันนี้</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Task Rows --}}
                    @php
                        $statusColors = [
                            'todo' => '#94a3b8',
                            'in_progress' => '#f97316',
                            'review' => '#8b5cf6',
                            'done' => '#22d3a0',
                        ];
                        $priorityLabels = ['low' => 'ต่ำ', 'medium' => 'ปานกลาง', 'high' => 'สูง', 'urgent' => 'เร่งด่วน'];
                    @endphp

                    @foreach($timeline['tasks'] as $t)
                        <div class="flex border-b border-erms-border/50 hover:bg-erms-surface-2/50 transition group">
                            {{-- Task Info --}}
                            <div class="w-56 flex-shrink-0 px-4 py-3 border-r border-erms-border">
                                <a href="{{ $t['url'] }}" class="text-sm font-medium hover:text-erms-blue transition truncate block" wire:navigate>
                                    {{ $t['title'] }}
                                </a>
                                <div class="flex items-center gap-2 mt-1">
                                    @if($t['assignee_avatar'])
                                        <img src="{{ $t['assignee_avatar'] }}" alt="" class="w-4 h-4 rounded-full">
                                    @endif
                                    <span class="text-[10px] text-erms-muted">{{ $t['start_date'] }} → {{ $t['due_date'] }}</span>
                                </div>
                            </div>

                            {{-- Gantt Bar --}}
                            <div class="flex-1 relative py-3 px-1">
                                <div class="absolute top-1/2 -translate-y-1/2 rounded-md h-7 flex items-center px-2 text-white text-[10px] font-medium cursor-pointer transition-all hover:brightness-110 shadow-sm"
                                     style="left: {{ $t['left'] }}%; width: {{ max($t['width'], 2) }}%; background: {{ $statusColors[$t['status']] ?? '#4f8ef7' }};"
                                     title="{{ $t['title'] }} ({{ $t['progress'] }}%)">
                                    @if($t['width'] > 8)
                                        <span class="truncate">{{ $t['title'] }}</span>
                                    @endif
                                    {{-- Progress fill --}}
                                    @if($t['progress'] > 0)
                                        <div class="absolute inset-0 rounded-md opacity-30 bg-white" style="width: {{ $t['progress'] }}%"></div>
                                    @endif
                                </div>

                                {{-- Dependency arrows --}}
                                @foreach($t['dependencies'] as $depId)
                                    @php
                                        $depTask = collect($timeline['tasks'])->firstWhere('id', $depId);
                                    @endphp
                                    @if($depTask)
                                        <div class="absolute top-1/2 h-0.5 bg-erms-muted/40" style="left: {{ $depTask['left'] + $depTask['width'] }}%; width: {{ max($t['left'] - ($depTask['left'] + $depTask['width']), 0.5) }}%;"></div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Legend --}}
        <div class="flex items-center gap-4 mt-3 text-xs text-erms-muted">
            @foreach($statusColors as $status => $color)
                @php $sLabels = ['todo' => 'รอดำเนินการ', 'in_progress' => 'กำลังดำเนินการ', 'review' => 'ตรวจสอบ', 'done' => 'เสร็จสิ้น']; @endphp
                <div class="flex items-center gap-1">
                    <div class="w-3 h-3 rounded" style="background: {{ $color }}"></div>
                    <span>{{ $sLabels[$status] }}</span>
                </div>
            @endforeach
        </div>
    @endif
</div>
