<div>
    @php
        $statusLabels = ['todo' => 'รอดำเนินการ', 'in_progress' => 'กำลังดำเนินการ', 'review' => 'ตรวจสอบ', 'done' => 'เสร็จสิ้น'];
        $priorityLabels = ['low' => 'ต่ำ', 'medium' => 'ปานกลาง', 'high' => 'สูง', 'urgent' => 'เร่งด่วน'];
    @endphp

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Main Content --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Task Info --}}
            <div class="card p-6">
                {{-- Parent task breadcrumb --}}
                @if($task->parent)
                    <div class="mb-3 text-xs text-erms-muted flex items-center gap-1">
                        <a href="{{ route('tasks.show', $task->parent) }}" class="hover:text-erms-blue transition" wire:navigate>{{ $task->parent->title }}</a>
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        <span class="text-erms-text">{{ $task->title }}</span>
                    </div>
                @endif

                <div class="flex items-center gap-2 mb-3 flex-wrap">
                    {{-- Status dropdown --}}
                    <select wire:change="quickStatusChange($event.target.value)" class="badge-{{ str_replace('_', '-', $task->status) }} border-0 cursor-pointer focus:outline-none focus:ring-1 focus:ring-erms-blue pr-6 appearance-auto">
                        @foreach($statusLabels as $val => $label)
                            <option value="{{ $val }}" @selected($task->status === $val)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <span class="badge-{{ $task->priority }}">{{ $priorityLabels[$task->priority] ?? $task->priority }}</span>
                    @if($task->project)
                        <a href="{{ route('projects.show', $task->project) }}" class="text-xs text-erms-blue hover:underline" wire:navigate>{{ $task->project->name }}</a>
                    @endif
                    @if($task->isBlocked())
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-medium rounded-full bg-erms-red/10 text-erms-red">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            ถูกบล็อก
                        </span>
                    @endif
                </div>

                @if($task->description)
                    <p class="text-sm text-erms-muted mb-4">{{ $task->description }}</p>
                @endif

                {{-- Progress --}}
                <div class="mb-4">
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-xs text-erms-muted">ความคืบหน้า</span>
                        @if($editingProgress)
                            <div class="flex items-center gap-2">
                                <input type="number" wire:model="progress" min="0" max="100" class="input-field w-20 text-xs py-1">
                                <button wire:click="updateProgress" class="text-xs text-erms-blue hover:underline cursor-pointer">บันทึก</button>
                                <button wire:click="$set('editingProgress', false)" class="text-xs text-erms-muted hover:text-erms-text cursor-pointer">ยกเลิก</button>
                            </div>
                        @else
                            <button wire:click="$set('editingProgress', true)" class="text-xs text-erms-blue hover:underline cursor-pointer">{{ $task->progress }}%</button>
                        @endif
                    </div>
                    <div class="progress-bar">
                        <div class="fill" style="width: {{ $task->progress }}%"></div>
                    </div>
                </div>

                <div class="flex items-center gap-6 text-xs text-erms-muted flex-wrap">
                    @if($task->assignee)
                        <div class="flex items-center gap-2">
                            <img src="{{ $task->assignee->avatar_url }}" alt="" class="w-5 h-5 rounded-full">
                            <span>{{ $task->assignee->name }}</span>
                        </div>
                    @endif
                    @if($task->start_date)
                        <span>เริ่ม: {{ $task->start_date->translatedFormat('d M Y') }}</span>
                    @endif
                    @if($task->due_date)
                        <span class="{{ $task->due_date->isPast() && $task->status !== 'done' ? 'text-erms-red font-medium' : '' }}">
                            กำหนดส่ง: {{ $task->due_date->translatedFormat('d M Y') }}
                        </span>
                    @endif
                    @if($task->estimated_hours)
                        <span>ประมาณ: {{ $task->estimated_hours }} ชม.</span>
                    @endif
                </div>

                {{-- Tags --}}
                @if($task->tags && count($task->tags))
                    <div class="flex items-center gap-1 mt-3 flex-wrap">
                        @foreach($task->tags as $tag)
                            <span class="inline-flex items-center px-2 py-0.5 text-[10px] font-medium rounded-full bg-erms-blue/10 text-erms-blue">{{ $tag }}</span>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Subtasks --}}
            <div class="card">
                <div class="px-5 py-4 border-b border-erms-border flex items-center justify-between">
                    <h2 class="font-heading font-bold text-base">
                        งานย่อย
                        @if($task->subtasks->count())
                            <span class="text-sm font-normal text-erms-muted">({{ $task->subtasks->where('status', 'done')->count() }}/{{ $task->subtasks->count() }})</span>
                        @endif
                    </h2>
                    <button wire:click="$toggle('showSubtaskForm')" class="text-erms-blue hover:text-erms-blue/80 text-sm cursor-pointer flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        เพิ่มงานย่อย
                    </button>
                </div>

                @if($showSubtaskForm)
                    <div class="px-5 py-3 border-b border-erms-border bg-erms-surface-2/50">
                        <form wire:submit="addSubtask" class="flex gap-2">
                            <input type="text" wire:model="subtaskTitle" class="input-field flex-1" placeholder="ชื่องานย่อย..." autofocus>
                            <button type="submit" class="btn-primary text-xs">เพิ่ม</button>
                            <button type="button" wire:click="$set('showSubtaskForm', false)" class="btn-secondary text-xs">ยกเลิก</button>
                        </form>
                        @error('subtaskTitle') <p class="text-xs text-erms-red mt-1 px-1">{{ $message }}</p> @enderror
                    </div>
                @endif

                <div class="divide-y divide-erms-border/50">
                    @forelse($task->subtasks->sortBy('sort_order') as $subtask)
                        <div class="px-5 py-2.5 flex items-center gap-3 hover:bg-erms-surface-2/50 transition group">
                            <button wire:click="toggleSubtask({{ $subtask->id }})" class="flex-shrink-0 cursor-pointer">
                                @if($subtask->status === 'done')
                                    <svg class="w-5 h-5 text-erms-green" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                                @else
                                    <svg class="w-5 h-5 text-erms-muted hover:text-erms-blue transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="2"/></svg>
                                @endif
                            </button>
                            <a href="{{ route('tasks.show', $subtask) }}" class="flex-1 text-sm {{ $subtask->status === 'done' ? 'line-through text-erms-muted' : '' }} hover:text-erms-blue transition" wire:navigate>
                                {{ $subtask->title }}
                            </a>
                            @if($subtask->assignee)
                                <img src="{{ $subtask->assignee->avatar_url }}" alt="" class="w-5 h-5 rounded-full" title="{{ $subtask->assignee->name }}">
                            @endif
                            <button wire:click="deleteSubtask({{ $subtask->id }})" wire:confirm="ลบงานย่อยนี้?" class="opacity-0 group-hover:opacity-100 text-erms-muted hover:text-erms-red transition cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>
                    @empty
                        @if(!$showSubtaskForm)
                            <div class="px-5 py-6 text-center text-erms-muted text-sm">ยังไม่มีงานย่อย</div>
                        @endif
                    @endforelse
                </div>
            </div>

            {{-- Dependencies --}}
            <div class="card">
                <div class="px-5 py-4 border-b border-erms-border flex items-center justify-between">
                    <h2 class="font-heading font-bold text-base">งานที่ต้องทำก่อน</h2>
                    <button wire:click="$toggle('showDependencyForm')" class="text-erms-blue hover:text-erms-blue/80 text-sm cursor-pointer flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        เพิ่ม
                    </button>
                </div>

                @if($showDependencyForm)
                    <div class="px-5 py-3 border-b border-erms-border bg-erms-surface-2/50">
                        <form wire:submit="addDependency" class="flex gap-2">
                            <select wire:model="dependsOnTaskId" class="input-field flex-1">
                                <option value="">เลือกงานที่ต้องทำก่อน</option>
                                @foreach($this->availableTasks as $t)
                                    <option value="{{ $t->id }}">{{ $t->title }}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn-primary text-xs">เพิ่ม</button>
                            <button type="button" wire:click="$set('showDependencyForm', false)" class="btn-secondary text-xs">ยกเลิก</button>
                        </form>
                        @error('dependsOnTaskId') <p class="text-xs text-erms-red mt-1">{{ $message }}</p> @enderror
                    </div>
                @endif

                <div class="divide-y divide-erms-border/50">
                    @forelse($task->dependencies as $dep)
                        <div class="px-5 py-2.5 flex items-center gap-3 hover:bg-erms-surface-2/50 transition group">
                            @if($dep->dependsOnTask->status === 'done')
                                <svg class="w-5 h-5 text-erms-green flex-shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                            @else
                                <svg class="w-5 h-5 text-erms-orange flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            @endif
                            <a href="{{ route('tasks.show', $dep->dependsOnTask) }}" class="flex-1 text-sm hover:text-erms-blue transition" wire:navigate>
                                {{ $dep->dependsOnTask->title }}
                            </a>
                            <span class="badge-{{ str_replace('_', '-', $dep->dependsOnTask->status) }} text-[10px]">{{ $statusLabels[$dep->dependsOnTask->status] ?? $dep->dependsOnTask->status }}</span>
                            <button wire:click="removeDependency({{ $dep->id }})" class="opacity-0 group-hover:opacity-100 text-erms-muted hover:text-erms-red transition cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                    @empty
                        @if(!$showDependencyForm)
                            <div class="px-5 py-4 text-center text-erms-muted text-sm">ไม่มีงานที่ต้องทำก่อน</div>
                        @endif
                    @endforelse
                </div>
            </div>

            {{-- Custom Field Values --}}
            @if($task->customFieldValues->count())
                <div class="card">
                    <div class="px-5 py-4 border-b border-erms-border">
                        <h2 class="font-heading font-bold text-base">ฟิลด์กำหนดเอง</h2>
                    </div>
                    <div class="p-5 space-y-3">
                        @foreach($task->customFieldValues as $cfv)
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-erms-muted">{{ $cfv->customField->name }}</span>
                                <span class="text-sm font-medium">{{ $cfv->value ?? '-' }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Comments --}}
            <div class="card">
                <div class="px-5 py-4 border-b border-erms-border">
                    <h2 class="font-heading font-bold text-base">ความคิดเห็น ({{ $task->comments->count() }})</h2>
                </div>
                <div class="divide-y divide-erms-border/50">
                    @foreach($task->comments as $comment)
                        <div class="px-5 py-3">
                            <div class="flex items-center gap-2 mb-1">
                                <img src="{{ $comment->user->avatar_url }}" alt="" class="w-6 h-6 rounded-full">
                                <span class="text-sm font-medium">{{ $comment->user->name }}</span>
                                <span class="text-xs text-erms-muted">{{ $comment->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-sm text-erms-text pl-8">{!! nl2br(e($comment->body)) !!}</p>
                        </div>
                    @endforeach
                </div>
                <div class="p-5 border-t border-erms-border">
                    <form action="{{ route('tasks.comments.store', $task) }}" method="POST" class="flex gap-3">
                        @csrf
                        <input type="text" name="body" class="input-field flex-1" placeholder="เพิ่มความคิดเห็น... ใช้ @ชื่อ เพื่อแท็กคน" required>
                        <button type="submit" class="btn-primary">ส่ง</button>
                    </form>
                </div>
            </div>

            {{-- Attachments --}}
            <div class="card">
                <div class="px-5 py-4 border-b border-erms-border">
                    <h2 class="font-heading font-bold text-base">ไฟล์แนบ ({{ $task->attachments->count() }})</h2>
                </div>
                <div class="p-5">
                    @if($task->attachments->count())
                        <div class="space-y-2 mb-4">
                            @foreach($task->attachments as $attachment)
                                <div class="flex items-center justify-between bg-erms-surface-2 rounded-lg px-4 py-2.5">
                                    <div class="flex items-center gap-3">
                                        <svg class="w-5 h-5 text-erms-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                        <div>
                                            <p class="text-sm">{{ $attachment->file_name }}</p>
                                            <p class="text-xs text-erms-muted">{{ number_format($attachment->file_size / 1024, 1) }} KB &bull; {{ $attachment->user->name }} &bull; {{ $attachment->created_at->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('attachments.download', $attachment) }}" class="text-erms-blue hover:underline text-xs">ดาวน์โหลด</a>
                                        <form method="POST" action="{{ route('attachments.destroy', $attachment) }}" onsubmit="return confirm('ลบไฟล์นี้?')">
                                            @csrf @method('DELETE')
                                            <button class="text-erms-red hover:underline text-xs">ลบ</button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                    <form action="{{ route('tasks.attachments.store', $task) }}" method="POST" enctype="multipart/form-data" class="flex gap-3">
                        @csrf
                        <input type="file" name="file" class="input-field flex-1 file:mr-3 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-xs file:bg-erms-blue/10 file:text-erms-blue file:cursor-pointer" required>
                        <button type="submit" class="btn-secondary text-xs">อัปโหลด</button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            {{-- Task History --}}
            <div class="card">
                <div class="px-5 py-4 border-b border-erms-border">
                    <h2 class="font-heading font-bold text-sm">ประวัติการเปลี่ยนแปลง</h2>
                </div>
                <div class="divide-y divide-erms-border/50 max-h-64 overflow-y-auto">
                    @forelse($task->taskUpdates->sortByDesc('created_at') as $update)
                        <div class="px-5 py-2.5">
                            <p class="text-xs">
                                <span class="font-medium">{{ $update->user->name }}</span>
                                @if($update->old_status)
                                    <span class="badge-{{ str_replace('_', '-', $update->old_status) }} mx-0.5">{{ $statusLabels[$update->old_status] ?? $update->old_status }}</span>
                                    <svg class="inline w-3 h-3 text-erms-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                @endif
                                <span class="badge-{{ str_replace('_', '-', $update->new_status) }} mx-0.5">{{ $statusLabels[$update->new_status] ?? $update->new_status }}</span>
                            </p>
                            @if($update->note)
                                <p class="text-xs text-erms-muted mt-0.5">{{ $update->note }}</p>
                            @endif
                            <p class="text-xs text-erms-muted mt-0.5">{{ $update->created_at->diffForHumans() }}</p>
                        </div>
                    @empty
                        <div class="px-5 py-4 text-center text-erms-muted text-xs">ไม่มีประวัติ</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
