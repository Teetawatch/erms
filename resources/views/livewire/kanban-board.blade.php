<div wire:poll.15s>
    {{-- ═══ Create Task Modal (Asana-style) ═══ --}}
    @if($showCreateModal)
    <div class="modal-overlay" x-data x-transition @click.self="$wire.set('showCreateModal', false)" @keydown.escape.window="$wire.set('showCreateModal', false)">
        <div class="modal-content max-w-xl" @click.stop>
            <div class="px-6 py-4 border-b border-erms-border-light flex items-center justify-between">
                <h3 class="font-semibold text-base">สร้างงานใหม่</h3>
                <button wire:click="$set('showCreateModal', false)" class="btn-icon">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form wire:submit="createTask" class="p-6 space-y-4">
                <div>
                    <label class="block text-[13px] font-medium text-erms-text-secondary mb-1.5">ชื่องาน *</label>
                    <input type="text" wire:model="newTitle" class="input-field" placeholder="เขียนชื่องาน..." required autofocus>
                    @error('newTitle') <p class="text-xs text-erms-red mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-[13px] font-medium text-erms-text-secondary mb-1.5">โครงการ *</label>
                    <select wire:model="newProjectId" class="input-field" required>
                        <option value="">เลือกโครงการ</option>
                        @foreach($this->projects as $project)
                            <option value="{{ $project->id }}">{{ $project->name }}</option>
                        @endforeach
                    </select>
                    @error('newProjectId') <p class="text-xs text-erms-red mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[13px] font-medium text-erms-text-secondary mb-1.5">ความสำคัญ</label>
                        <select wire:model="newPriority" class="input-field">
                            <option value="low">ต่ำ</option>
                            <option value="medium">ปานกลาง</option>
                            <option value="high">สูง</option>
                            <option value="urgent">เร่งด่วน</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[13px] font-medium text-erms-text-secondary mb-1.5">ชั่วโมงประมาณ</label>
                        <input type="number" wire:model="newEstimatedHours" class="input-field" min="0" placeholder="ชม.">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[13px] font-medium text-erms-text-secondary mb-1.5">วันเริ่มต้น</label>
                        <input type="date" wire:model="newStartDate" class="input-field">
                    </div>
                    <div>
                        <label class="block text-[13px] font-medium text-erms-text-secondary mb-1.5">กำหนดส่ง</label>
                        <input type="date" wire:model="newDueDate" class="input-field">
                    </div>
                </div>
                <div>
                    <label class="block text-[13px] font-medium text-erms-text-secondary mb-1.5">มอบหมายให้</label>
                    <select wire:model="newAssignedTo" class="input-field">
                        <option value="">ไม่ระบุ</option>
                        @foreach($this->users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[13px] font-medium text-erms-text-secondary mb-1.5">รายละเอียด</label>
                    <textarea wire:model="newDescription" class="input-field" rows="3" placeholder="เพิ่มรายละเอียด..."></textarea>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" wire:click="$set('showCreateModal', false)" class="btn-secondary">ยกเลิก</button>
                    <button type="submit" class="btn-primary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        สร้างงาน
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- ═══ Filter Bar ═══ --}}
    <div class="flex flex-wrap items-center gap-2 mb-4">
        <div class="relative flex-1 min-w-[160px] max-w-[220px]">
            <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-erms-muted pointer-events-none" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="text" wire:model.live.debounce.300ms="searchQuery" class="input-field !pl-9 !py-1.5 !text-[13px]" placeholder="ค้นหางาน...">
        </div>
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
        @if($filterPriority || $filterAssignee || $searchQuery)
            <button wire:click="clearFilters" class="text-2xs text-erms-red hover:underline font-medium cursor-pointer flex items-center gap-1">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                ล้างตัวกรอง
            </button>
        @endif
    </div>

    {{-- ═══ Kanban Board (Asana-style columns) ═══ --}}
    <div class="flex gap-3 overflow-x-auto pb-4 -mx-1 px-1" x-data="kanbanDragDrop()" x-init="initSortable()">
        @php
            $statuses = [
                'todo' => ['label' => 'รอดำเนินการ', 'color' => '#9ca0a4', 'dotClass' => 'bg-erms-muted'],
                'in_progress' => ['label' => 'กำลังดำเนินการ', 'color' => '#4573d2', 'dotClass' => 'bg-erms-blue'],
                'review' => ['label' => 'ตรวจสอบ', 'color' => '#7c5cfc', 'dotClass' => 'bg-erms-purple'],
                'done' => ['label' => 'เสร็จสิ้น', 'color' => '#5da283', 'dotClass' => 'bg-erms-green'],
            ];
            $priorityLabels = ['low' => 'ต่ำ', 'medium' => 'ปานกลาง', 'high' => 'สูง', 'urgent' => 'เร่งด่วน'];
        @endphp

        @foreach($statuses as $status => $config)
            <div class="flex-shrink-0 w-[280px] lg:w-[300px]">
                {{-- Column Header --}}
                <div class="flex items-center justify-between mb-2 px-1">
                    <div class="flex items-center gap-2">
                        <span class="status-dot" style="background-color: {{ $config['color'] }}"></span>
                        <h3 class="text-[13px] font-semibold text-erms-text">{{ $config['label'] }}</h3>
                        <span class="text-[11px] text-erms-muted font-medium">
                            {{ isset($this->tasks[$status]) ? $this->tasks[$status]->count() : 0 }}
                        </span>
                    </div>
                    <button wire:click="openCreateModal('{{ $status }}')" class="btn-icon !w-6 !h-6" aria-label="เพิ่มงาน">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    </button>
                </div>

                {{-- Task Cards Container --}}
                <div class="kanban-column space-y-2 min-h-[120px] rounded-lg p-1.5 bg-erms-surface-2/50" data-status="{{ $status }}">
                    @foreach(($this->tasks[$status] ?? collect()) as $task)
                        @php
                            $priorityColors = ['urgent' => 'border-l-erms-red', 'high' => 'border-l-erms-orange', 'medium' => 'border-l-erms-yellow', 'low' => 'border-l-erms-green'];
                        @endphp
                        <div class="kanban-card group border-l-[3px] {{ $priorityColors[$task->priority] ?? '' }}" data-task-id="{{ $task->id }}">
                            {{-- Card Top: Title + Priority --}}
                            <div class="flex items-start gap-2 mb-1.5">
                                <button wire:click="$dispatch('open-task-panel', { taskId: {{ $task->id }} })"
                                        class="task-checkbox flex-shrink-0 mt-0.5 {{ $task->status === 'done' ? 'checked' : '' }}"
                                        title="{{ $task->status === 'done' ? 'เสร็จแล้ว' : 'ทำเครื่องหมายเสร็จ' }}">
                                    @if($task->status === 'done')
                                        <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                    @endif
                                </button>
                                <a href="{{ route('tasks.show', $task) }}" class="text-[13px] font-medium text-erms-text hover:text-erms-blue transition leading-snug flex-1 {{ $task->status === 'done' ? 'line-through text-erms-muted' : '' }}" wire:navigate>
                                    {{ $task->title }}
                                </a>
                            </div>

                            {{-- Card Meta Row --}}
                            <div class="flex items-center gap-1.5 flex-wrap mt-2">
                                @if($task->project && !$projectId)
                                    <span class="text-2xs text-erms-muted bg-erms-surface-2 px-1.5 py-0.5 rounded">{{ $task->project->name }}</span>
                                @endif
                                @if($task->dependencies->count() > 0)
                                    @php
                                        $blocked = $task->dependencies->contains(fn($dep) => $dep->dependsOnTask && $dep->dependsOnTask->status !== 'done');
                                    @endphp
                                    @if($blocked)
                                        <span class="text-2xs text-erms-red bg-erms-red/10 px-1.5 py-0.5 rounded flex items-center gap-1" title="รองานอื่น">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            ถูกบล็อก
                                        </span>
                                    @endif
                                @endif
                                <span class="badge-{{ $task->priority }}">{{ $priorityLabels[$task->priority] ?? $task->priority }}</span>
                            </div>

                            {{-- Card Footer --}}
                            <div class="flex items-center justify-between mt-2.5 pt-2 border-t border-erms-border-light">
                                <div class="flex items-center gap-2">
                                    @if($task->assignee)
                                        <img src="{{ $task->assignee->avatar_url }}" alt="{{ $task->assignee->name }}" class="w-5 h-5 rounded-full ring-1 ring-erms-border-light" title="{{ $task->assignee->name }}">
                                    @endif
                                    @if($task->subtasks->count())
                                        <span class="text-2xs text-erms-muted flex items-center gap-0.5" title="งานย่อย">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                            {{ $task->subtasks->where('status', 'done')->count() }}/{{ $task->subtasks->count() }}
                                        </span>
                                    @endif
                                </div>
                                @if($task->due_date)
                                    @php
                                        $isOverdue = $task->due_date->isPast() && $task->status !== 'done';
                                        $isDueSoon = !$isOverdue && $task->status !== 'done' && $task->due_date->diffInDays(now()) <= 2;
                                        $dueDateClass = $isOverdue ? 'text-erms-red font-medium' : ($isDueSoon ? 'text-erms-orange font-medium' : 'text-erms-muted');
                                    @endphp
                                    <span class="text-2xs flex items-center gap-1 {{ $dueDateClass }}">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        {{ $task->due_date->translatedFormat('d M') }}
                                        @if($isOverdue) เลยกำหนด @elseif($isDueSoon) ใกล้ถึง @endif
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endforeach

                    {{-- Add Task Placeholder --}}
                    <button wire:click="openCreateModal('{{ $status }}')"
                            class="w-full text-left px-3 py-2 rounded-lg text-[13px] text-erms-muted hover:bg-white hover:shadow-asana-card transition flex items-center gap-2 cursor-pointer opacity-0 group-hover:opacity-100 focus:opacity-100">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        เพิ่มงาน
                    </button>
                </div>
            </div>
        @endforeach
    </div>
</div>

@script
<script>
    Alpine.data('kanbanDragDrop', () => ({
        initSortable() {
            if (typeof Sortable === 'undefined') return;

            document.querySelectorAll('.kanban-column').forEach(column => {
                new Sortable(column, {
                    group: 'kanban',
                    animation: 200,
                    easing: 'cubic-bezier(0.4, 0, 0.2, 1)',
                    ghostClass: 'sortable-ghost',
                    dragClass: 'sortable-drag',
                    chosenClass: 'sortable-chosen',
                    delay: 50,
                    delayOnTouchOnly: true,
                    fallbackTolerance: 3,
                    onEnd: (evt) => {
                        const taskId = evt.item.dataset.taskId;
                        const newStatus = evt.to.dataset.status;
                        const newIndex = evt.newIndex;
                        $wire.taskMoved(parseInt(taskId), newStatus, newIndex);
                    }
                });
            });
        }
    }));
</script>
@endscript
