<div>
    {{-- ═══ Bulk Actions Bar (shown when tasks selected) ═══ --}}
    @if(count($selectedTasks) > 0)
        <div class="bg-erms-blue/5 border border-erms-blue/20 rounded-lg px-4 py-2 mb-3 flex items-center gap-3">
            <span class="text-sm text-erms-blue font-medium">เลือกแล้ว {{ count($selectedTasks) }} รายการ</span>
            <select wire:model.live="bulkStatus" class="input-field !w-auto !py-1 !text-xs !pr-8">
                <option value="">เปลี่ยนสถานะ...</option>
                <option value="todo">รอดำเนินการ</option>
                <option value="in_progress">กำลังดำเนินการ</option>
                <option value="review">ตรวจสอบ</option>
                <option value="done">เสร็จสิ้น</option>
            </select>
            <button wire:click="bulkStatusChange" wire:disabled="!$bulkStatus" class="btn-primary !text-xs !py-1">
                ใช้
            </button>
            <button wire:click="$set('selectedTasks', [])" class="btn-ghost !text-xs !py-1">
                ยกเลิก
            </button>
        </div>
    @endif

    {{-- ═══ Filter Bar (Asana-style inline) ═══ --}}
    <div class="flex flex-wrap items-center gap-2 mb-3">
        <div class="relative flex-1 min-w-[180px] max-w-xs">
            <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-erms-muted pointer-events-none" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="text" wire:model.live.debounce.300ms="search" class="input-field !pl-9 !py-1.5 !text-[13px]" placeholder="ค้นหางาน...">
        </div>
        <select wire:model.live="filterStatus" class="input-field !w-auto !py-1.5 !text-[13px] !pr-8">
            <option value="">สถานะทั้งหมด</option>
            <option value="todo">รอดำเนินการ</option>
            <option value="in_progress">กำลังดำเนินการ</option>
            <option value="review">ตรวจสอบ</option>
            <option value="done">เสร็จสิ้น</option>
        </select>
        <select wire:model.live="filterPriority" class="input-field !w-auto !py-1.5 !text-[13px] !pr-8">
            <option value="">ความสำคัญทั้งหมด</option>
            <option value="urgent">เร่งด่วน</option>
            <option value="high">สูง</option>
            <option value="medium">ปานกลาง</option>
            <option value="low">ต่ำ</option>
        </select>
        <select wire:model.live="filterAssignee" class="input-field !w-auto !py-1.5 !text-[13px] !pr-8">
            <option value="">ผู้รับผิดชอบทั้งหมด</option>
            @foreach($this->users as $user)
                <option value="{{ $user->id }}">{{ $user->name }}</option>
            @endforeach
        </select>
    </div>

    {{-- ═══ Task List (Asana-style rows) ═══ --}}
    @php
        $statusLabels = ['todo' => 'รอดำเนินการ', 'in_progress' => 'กำลังดำเนินการ', 'review' => 'ตรวจสอบ', 'done' => 'เสร็จสิ้น'];
        $priorityLabels = ['low' => 'ต่ำ', 'medium' => 'ปานกลาง', 'high' => 'สูง', 'urgent' => 'เร่งด่วน'];
        $sortArrow = fn($col) => $sortBy === $col ? ($sortDir === 'asc' ? '↑' : '↓') : '';
    @endphp

    <div class="card overflow-hidden">
        {{-- Column Headers --}}
        <div class="flex items-center border-b border-erms-border bg-erms-surface-2/60 text-[11px] font-semibold text-erms-muted uppercase tracking-wider select-none">
            <div class="w-8 px-3 py-2.5">
                <input type="checkbox" wire:model.live="selectAll" class="rounded border-erms-border text-erms-blue focus:ring-erms-blue/20">
            </div>
            <div class="flex-1 min-w-0 px-4 py-2.5">
                <button wire:click="sort('title')" class="flex items-center gap-1 hover:text-erms-text cursor-pointer transition">
                    ชื่องาน {!! $sortArrow('title') !!}
                </button>
            </div>
            <div class="w-28 px-3 py-2.5 hidden md:block">ผู้รับผิดชอบ</div>
            <div class="w-28 px-3 py-2.5 hidden sm:block">
                <button wire:click="sort('due_date')" class="flex items-center gap-1 hover:text-erms-text cursor-pointer transition">
                    กำหนดส่ง {!! $sortArrow('due_date') !!}
                </button>
            </div>
            <div class="w-32 px-3 py-2.5">
                <button wire:click="sort('status')" class="flex items-center gap-1 hover:text-erms-text cursor-pointer transition">
                    สถานะ {!! $sortArrow('status') !!}
                </button>
            </div>
            <div class="w-24 px-3 py-2.5 hidden lg:block">
                <button wire:click="sort('priority')" class="flex items-center gap-1 hover:text-erms-text cursor-pointer transition">
                    ความสำคัญ {!! $sortArrow('priority') !!}
                </button>
            </div>
        </div>

        {{-- Task Rows --}}
        <div class="divide-y divide-erms-border-light">
            @forelse($this->tasks as $task)
                <div class="task-row group" wire:key="task-row-{{ $task->id }}">
                    {{-- Selection Checkbox --}}
                    <div class="w-8 px-3 py-2.5">
                        <input type="checkbox" wire:model.live="selectedTasks" value="{{ $task->id }}" class="rounded border-erms-border text-erms-blue focus:ring-erms-blue/20">
                    </div>
                    
                    {{-- Task Name with Checkbox --}}
                    <div class="flex-1 min-w-0 flex items-center gap-3 pr-3">
                        <button wire:click="quickStatusChange({{ $task->id }}, '{{ $task->status === 'done' ? 'todo' : 'done' }}')"
                                class="task-checkbox {{ $task->status === 'done' ? 'checked' : '' }}" title="สลับสถานะ">
                            @if($task->status === 'done')
                                <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            @endif
                        </button>
                        <div class="min-w-0 flex-1">
                            <a href="{{ route('tasks.show', $task) }}" class="text-[13px] font-medium hover:text-erms-blue transition truncate block {{ $task->status === 'done' ? 'line-through text-erms-muted' : 'text-erms-text' }}" wire:navigate>
                                {{ $task->title }}
                            </a>
                            <span class="text-2xs text-erms-muted">{{ $task->project->name ?? '' }}</span>
                        </div>
                    </div>

                    {{-- Assignee --}}
                    <div class="w-28 px-3 hidden md:flex items-center">
                        @if($task->assignee)
                            <div class="flex items-center gap-1.5" title="{{ $task->assignee->name }}">
                                <img src="{{ $task->assignee->avatar_url }}" alt="" class="w-5 h-5 rounded-full ring-1 ring-erms-border-light">
                                <span class="text-2xs text-erms-text-secondary truncate">{{ $task->assignee->name }}</span>
                            </div>
                        @else
                            <span class="text-2xs text-erms-muted">—</span>
                        @endif
                    </div>

                    {{-- Due Date --}}
                    <div class="w-28 px-3 hidden sm:flex items-center">
                        @if($task->due_date)
                            @php
                                $isOverdue = $task->due_date->isPast() && $task->status !== 'done';
                                $isDueSoon = !$isOverdue && $task->status !== 'done' && $task->due_date->diffInDays(now()) <= 2;
                                $dueDateClass = $isOverdue ? 'text-erms-red font-medium' : ($isDueSoon ? 'text-erms-orange font-medium' : 'text-erms-muted');
                            @endphp
                            <span class="text-2xs {{ $dueDateClass }}" title="{{ $isOverdue ? 'เลยกำหนด' : ($isDueSoon ? 'ใกล้ถึงกำหนด' : '') }}">
                                {{ $task->due_date->translatedFormat('d M Y') }}
                            </span>
                        @else
                            <span class="text-2xs text-erms-muted">—</span>
                        @endif
                    </div>

                    {{-- Status --}}
                    <div class="w-32 px-3">
                        <select wire:change="quickStatusChange({{ $task->id }}, $event.target.value)"
                                class="badge-{{ str_replace('_', '-', $task->status) }} !text-2xs border-0 cursor-pointer focus:outline-none focus:ring-1 focus:ring-erms-blue/30 pr-5 appearance-auto bg-transparent">
                            @foreach($statusLabels as $val => $label)
                                <option value="{{ $val }}" @selected($task->status === $val)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Priority --}}
                    <div class="w-24 px-3 hidden lg:flex items-center">
                        <span class="badge-{{ $task->priority }}">{{ $priorityLabels[$task->priority] ?? $task->priority }}</span>
                    </div>
                </div>
            @empty
                <div class="py-12 text-center">
                    <svg class="w-10 h-10 mx-auto text-erms-muted/50 mb-2" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    <p class="text-[13px] text-erms-muted">ไม่พบงาน</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Pagination --}}
    <div class="mt-4">
        {{ $this->tasks->links() }}
    </div>
</div>
