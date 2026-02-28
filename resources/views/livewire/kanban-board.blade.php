<div wire:poll.15s>
    {{-- Create Task Modal --}}
    @if($showCreateModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60" x-data x-transition>
        <div class="bg-erms-surface border border-erms-border rounded-xl w-full max-w-lg mx-4 shadow-2xl">
            <div class="px-6 py-4 border-b border-erms-border flex items-center justify-between">
                <h3 class="font-heading font-bold text-base">สร้างงานใหม่</h3>
                <button wire:click="$set('showCreateModal', false)" class="text-erms-muted hover:text-erms-text">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form wire:submit="createTask" class="p-6 space-y-4">
                <div>
                    <label class="block text-sm text-erms-muted mb-1">ชื่องาน *</label>
                    <input type="text" wire:model="newTitle" class="input-field" placeholder="ชื่องาน..." required>
                    @error('newTitle') <p class="text-xs text-erms-red mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm text-erms-muted mb-1">โครงการ *</label>
                    <select wire:model="newProjectId" class="input-field" required>
                        <option value="">เลือกโครงการ</option>
                        @foreach($this->projects as $project)
                            <option value="{{ $project->id }}">{{ $project->name }}</option>
                        @endforeach
                    </select>
                    @error('newProjectId') <p class="text-xs text-erms-red mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm text-erms-muted mb-1">ความสำคัญ</label>
                        <select wire:model="newPriority" class="input-field">
                            <option value="low">ต่ำ</option>
                            <option value="medium">ปานกลาง</option>
                            <option value="high">สูง</option>
                            <option value="urgent">เร่งด่วน</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm text-erms-muted mb-1">กำหนดส่ง</label>
                        <input type="date" wire:model="newDueDate" class="input-field">
                    </div>
                </div>
                <div>
                    <label class="block text-sm text-erms-muted mb-1">มอบหมายให้</label>
                    <select wire:model="newAssignedTo" class="input-field">
                        <option value="">ไม่ระบุ</option>
                        @foreach($this->users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm text-erms-muted mb-1">รายละเอียด</label>
                    <textarea wire:model="newDescription" class="input-field" rows="3" placeholder="รายละเอียด..."></textarea>
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" wire:click="$set('showCreateModal', false)" class="btn-secondary">ยกเลิก</button>
                    <button type="submit" class="btn-primary">สร้างงาน</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- Kanban Columns --}}
    <div class="flex gap-4 overflow-x-auto pb-4" x-data="kanbanDragDrop()" x-init="initSortable()">
        @php
            $statuses = [
                'todo' => ['label' => 'รอดำเนินการ', 'color' => 'erms-muted'],
                'in_progress' => ['label' => 'กำลังดำเนินการ', 'color' => 'erms-orange'],
                'review' => ['label' => 'ตรวจสอบ', 'color' => 'erms-purple'],
                'done' => ['label' => 'เสร็จสิ้น', 'color' => 'erms-green'],
            ];
        @endphp

        @foreach($statuses as $status => $config)
            <div class="flex-shrink-0 w-72 lg:w-80">
                <div class="bg-erms-surface/50 rounded-xl border border-erms-border p-3">
                    {{-- Column Header --}}
                    <div class="flex items-center justify-between mb-3 px-1">
                        <div class="flex items-center gap-2">
                            <div class="w-2 h-2 rounded-full bg-{{ $config['color'] }}"></div>
                            <h3 class="text-sm font-medium text-{{ $config['color'] }}">{{ $config['label'] }}</h3>
                            <span class="text-xs text-erms-muted bg-erms-surface-2 px-1.5 py-0.5 rounded">
                                {{ isset($this->tasks[$status]) ? $this->tasks[$status]->count() : 0 }}
                            </span>
                        </div>
                        <button wire:click="openCreateModal('{{ $status }}')" class="text-erms-muted hover:text-erms-text transition" aria-label="เพิ่มงาน">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        </button>
                    </div>

                    {{-- Task Cards --}}
                    <div class="kanban-column space-y-2 min-h-[200px]" data-status="{{ $status }}">
                        @foreach(($this->tasks[$status] ?? collect()) as $task)
                            <div class="card p-3 cursor-grab active:cursor-grabbing" data-task-id="{{ $task->id }}">
                                <div class="flex items-start justify-between gap-2 mb-2">
                                    <a href="{{ route('tasks.show', $task) }}" class="text-sm font-medium hover:text-erms-blue transition flex-1" wire:navigate>
                                        {{ $task->title }}
                                    </a>
                                    @php $priorityLabels = ['low' => 'ต่ำ', 'medium' => 'ปานกลาง', 'high' => 'สูง', 'urgent' => 'เร่งด่วน']; @endphp
                                    <span class="badge-{{ $task->priority }} flex-shrink-0">{{ $priorityLabels[$task->priority] ?? $task->priority }}</span>
                                </div>
                                @if($task->project)
                                    <p class="text-xs text-erms-muted mb-2">{{ $task->project->name }}</p>
                                @endif
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        @if($task->assignee)
                                            <img src="{{ $task->assignee->avatar_url }}" alt="" class="w-5 h-5 rounded-full" title="{{ $task->assignee->name }}">
                                        @endif
                                    </div>
                                    @if($task->due_date)
                                        <span class="text-xs {{ $task->due_date->isPast() ? 'text-erms-red' : 'text-erms-muted' }}">
                                            {{ $task->due_date->translatedFormat('d M') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
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
                    animation: 150,
                    ghostClass: 'opacity-30',
                    dragClass: 'rotate-2',
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
