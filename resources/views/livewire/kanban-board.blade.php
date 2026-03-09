<div wire:poll.15s>
    {{-- ═══ Create Task Modal (Asana-style) ═══ --}}
    @if($showCreateModal)
    <div class="modal-overlay" x-data x-transition @click.self="$wire.set('showCreateModal', false)" @keydown.escape.window="$wire.set('showCreateModal', false)">
        <div class="modal-content max-w-xl" @click.stop>
            <div class="px-6 py-4 border-b border-erms-border-light flex items-center justify-between">
                <h3 class="font-semibold text-base">สร้างงานใหม่</h3>
                <button wire:click="$set('showCreateModal', false)" class="btn-icon">
                    <i class="fa-solid fa-xmark text-lg"></i>
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
                        <i class="fa-solid fa-plus"></i>
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
            <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-erms-muted pointer-events-none text-sm"></i>
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
                <i class="fa-solid fa-xmark"></i>
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
                        <i class="fa-solid fa-plus text-sm"></i>
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
                                        <i class="fa-solid fa-check text-white text-xs"></i>
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
                                            <i class="fa-solid fa-circle-exclamation"></i>
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
                                            <i class="fa-solid fa-list-check text-xs"></i>
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
                                        <i class="fa-regular fa-calendar"></i>
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
                        <i class="fa-solid fa-plus"></i>
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
