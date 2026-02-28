<div>
    @php
        $statusLabels = ['todo' => 'รอดำเนินการ', 'in_progress' => 'กำลังดำเนินการ', 'review' => 'ตรวจสอบ', 'done' => 'เสร็จสิ้น'];
        $priorityLabels = ['low' => 'ต่ำ', 'medium' => 'ปานกลาง', 'high' => 'สูง', 'urgent' => 'เร่งด่วน'];
        $statusColors = ['todo' => '#9ca0a4', 'in_progress' => '#4573d2', 'review' => '#7c5cfc', 'done' => '#5da283'];
    @endphp

    <div class="grid grid-cols-1 lg:grid-cols-[1fr_280px] gap-6">
        {{-- ═══ Main Content ═══ --}}
        <div class="space-y-5 min-w-0">
            {{-- Task Header --}}
            <div>
                {{-- Breadcrumb --}}
                @if($task->parent)
                    <div class="mb-2 text-2xs text-erms-muted flex items-center gap-1">
                        <a href="{{ route('tasks.show', $task->parent) }}" class="hover:text-erms-blue transition" wire:navigate>{{ $task->parent->title }}</a>
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                        <span class="text-erms-text-secondary">{{ $task->title }}</span>
                    </div>
                @endif

                {{-- Title Row --}}
                <div class="flex items-start gap-3 mb-3">
                    <button wire:click="quickStatusChange('{{ $task->status === 'done' ? 'todo' : 'done' }}')"
                            class="task-checkbox mt-1 {{ $task->status === 'done' ? 'checked' : '' }} !w-6 !h-6"
                            title="{{ $task->status === 'done' ? 'ทำเครื่องหมายไม่เสร็จ' : 'ทำเครื่องหมายเสร็จ' }}">
                        @if($task->status === 'done')
                            <svg class="w-3.5 h-3.5 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        @endif
                    </button>
                    <h1 class="text-xl font-semibold text-erms-text leading-tight {{ $task->status === 'done' ? 'line-through text-erms-muted' : '' }}">
                        {{ $task->title }}
                    </h1>
                </div>

                {{-- Status & Meta Badges --}}
                <div class="flex items-center gap-2 flex-wrap ml-9">
                    <select wire:change="quickStatusChange($event.target.value)"
                            class="badge-{{ str_replace('_', '-', $task->status) }} border-0 cursor-pointer focus:outline-none focus:ring-1 focus:ring-erms-blue/30 pr-5 appearance-auto text-2xs">
                        @foreach($statusLabels as $val => $label)
                            <option value="{{ $val }}" @selected($task->status === $val)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <span class="badge-{{ $task->priority }}">{{ $priorityLabels[$task->priority] ?? $task->priority }}</span>
                    @if($task->project)
                        <a href="{{ route('projects.show', $task->project) }}" class="text-2xs text-erms-blue hover:underline font-medium" wire:navigate>{{ $task->project->name }}</a>
                    @endif
                    @if($task->isBlocked())
                        <span class="badge-urgent">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            ถูกบล็อก
                        </span>
                    @endif
                </div>
            </div>

            {{-- Description --}}
            @if($task->description)
                <div class="ml-9">
                    <p class="text-[13px] text-erms-text-secondary leading-relaxed">{{ $task->description }}</p>
                </div>
            @endif

            {{-- Progress --}}
            <div class="ml-9">
                <div class="flex items-center justify-between mb-1.5">
                    <span class="text-2xs font-medium text-erms-text-secondary uppercase tracking-wide">ความคืบหน้า</span>
                    @if($editingProgress)
                        <div class="flex items-center gap-2">
                            <input type="number" wire:model="progress" min="0" max="100" class="input-field !w-16 !text-xs !py-1 text-center">
                            <button wire:click="updateProgress" class="text-2xs text-erms-blue font-medium hover:underline cursor-pointer">บันทึก</button>
                            <button wire:click="$set('editingProgress', false)" class="text-2xs text-erms-muted hover:text-erms-text cursor-pointer">ยกเลิก</button>
                        </div>
                    @else
                        <button wire:click="$set('editingProgress', true)" class="text-2xs text-erms-blue font-medium hover:underline cursor-pointer">{{ $task->progress }}%</button>
                    @endif
                </div>
                <div class="progress-bar">
                    <div class="fill" style="width: {{ $task->progress }}%"></div>
                </div>
            </div>

            {{-- Tags --}}
            @if($task->tags && count($task->tags))
                <div class="flex items-center gap-1 ml-9 flex-wrap">
                    @foreach($task->tags as $tag)
                        <span class="inline-flex items-center px-2 py-0.5 text-2xs font-medium rounded-full bg-erms-blue-light text-erms-blue">{{ $tag }}</span>
                    @endforeach
                </div>
            @endif

            <div class="divider"></div>

            {{-- ═══ Subtasks ═══ --}}
            <div>
                <div class="flex items-center justify-between mb-2">
                    <h2 class="text-[13px] font-semibold text-erms-text flex items-center gap-2">
                        งานย่อย
                        @if($task->subtasks->count())
                            <span class="text-2xs font-normal text-erms-muted">{{ $task->subtasks->where('status', 'done')->count() }}/{{ $task->subtasks->count() }}</span>
                        @endif
                    </h2>
                    <button wire:click="$toggle('showSubtaskForm')" class="btn-ghost !text-2xs !text-erms-blue">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        เพิ่ม
                    </button>
                </div>

                @if($showSubtaskForm)
                    <div class="mb-2 bg-erms-surface-2/60 rounded-lg p-3">
                        <form wire:submit="addSubtask" class="flex gap-2">
                            <input type="text" wire:model="subtaskTitle" class="input-field flex-1 !text-[13px] !py-1.5" placeholder="ชื่องานย่อย..." autofocus>
                            <button type="submit" class="btn-primary !text-xs !py-1.5">เพิ่ม</button>
                            <button type="button" wire:click="$set('showSubtaskForm', false)" class="btn-secondary !text-xs !py-1.5">ยกเลิก</button>
                        </form>
                        @error('subtaskTitle') <p class="text-xs text-erms-red mt-1">{{ $message }}</p> @enderror
                    </div>
                @endif

                <div class="space-y-0">
                    @forelse($task->subtasks->sortBy('sort_order') as $subtask)
                        <div class="flex items-center gap-3 py-1.5 px-1 rounded-md hover:bg-erms-surface-2/60 transition group">
                            <button wire:click="toggleSubtask({{ $subtask->id }})" class="task-checkbox">
                                @if($subtask->status === 'done')
                                    <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                @endif
                            </button>
                            <a href="{{ route('tasks.show', $subtask) }}" class="flex-1 text-[13px] {{ $subtask->status === 'done' ? 'line-through text-erms-muted' : 'text-erms-text' }} hover:text-erms-blue transition" wire:navigate>
                                {{ $subtask->title }}
                            </a>
                            @if($subtask->assignee)
                                <img src="{{ $subtask->assignee->avatar_url }}" alt="" class="w-5 h-5 rounded-full ring-1 ring-erms-border-light" title="{{ $subtask->assignee->name }}">
                            @endif
                            <button wire:click="deleteSubtask({{ $subtask->id }})" wire:confirm="ลบงานย่อยนี้?" class="opacity-0 group-hover:opacity-100 text-erms-muted hover:text-erms-red transition cursor-pointer">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>
                    @empty
                        @if(!$showSubtaskForm)
                            <p class="text-2xs text-erms-muted py-3 text-center">ยังไม่มีงานย่อย</p>
                        @endif
                    @endforelse
                </div>
            </div>

            <div class="divider"></div>

            {{-- ═══ Dependencies ═══ --}}
            <div>
                <div class="flex items-center justify-between mb-2">
                    <h2 class="text-[13px] font-semibold text-erms-text">งานที่ต้องทำก่อน</h2>
                    <button wire:click="$toggle('showDependencyForm')" class="btn-ghost !text-2xs !text-erms-blue">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        เพิ่ม
                    </button>
                </div>

                @if($showDependencyForm)
                    <div class="mb-2 bg-erms-surface-2/60 rounded-lg p-3">
                        <form wire:submit="addDependency" class="flex gap-2">
                            <select wire:model="dependsOnTaskId" class="input-field flex-1 !text-[13px] !py-1.5">
                                <option value="">เลือกงานที่ต้องทำก่อน</option>
                                @foreach($this->availableTasks as $t)
                                    <option value="{{ $t->id }}">{{ $t->title }}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn-primary !text-xs !py-1.5">เพิ่ม</button>
                            <button type="button" wire:click="$set('showDependencyForm', false)" class="btn-secondary !text-xs !py-1.5">ยกเลิก</button>
                        </form>
                        @error('dependsOnTaskId') <p class="text-xs text-erms-red mt-1">{{ $message }}</p> @enderror
                    </div>
                @endif

                <div class="space-y-0">
                    @forelse($task->dependencies as $dep)
                        <div class="flex items-center gap-3 py-1.5 px-1 rounded-md hover:bg-erms-surface-2/60 transition group">
                            <span class="task-checkbox {{ $dep->dependsOnTask->status === 'done' ? 'checked' : '' }}">
                                @if($dep->dependsOnTask->status === 'done')
                                    <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                @endif
                            </span>
                            <a href="{{ route('tasks.show', $dep->dependsOnTask) }}" class="flex-1 text-[13px] hover:text-erms-blue transition" wire:navigate>
                                {{ $dep->dependsOnTask->title }}
                            </a>
                            <span class="badge-{{ str_replace('_', '-', $dep->dependsOnTask->status) }}">{{ $statusLabels[$dep->dependsOnTask->status] ?? $dep->dependsOnTask->status }}</span>
                            <button wire:click="removeDependency({{ $dep->id }})" class="opacity-0 group-hover:opacity-100 text-erms-muted hover:text-erms-red transition cursor-pointer">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                    @empty
                        @if(!$showDependencyForm)
                            <p class="text-2xs text-erms-muted py-3 text-center">ไม่มีงานที่ต้องทำก่อน</p>
                        @endif
                    @endforelse
                </div>
            </div>

            {{-- Custom Fields --}}
            @if($task->customFieldValues->count())
                <div class="divider"></div>
                <div>
                    <h2 class="text-[13px] font-semibold text-erms-text mb-2">ฟิลด์กำหนดเอง</h2>
                    <div class="space-y-2">
                        @foreach($task->customFieldValues as $cfv)
                            <div class="flex items-center justify-between py-1">
                                <span class="text-2xs text-erms-text-secondary">{{ $cfv->customField->name }}</span>
                                <span class="text-[13px] font-medium">{{ $cfv->value ?? '—' }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="divider"></div>

            {{-- ═══ Comments ═══ --}}
            <div>
                <h2 class="text-[13px] font-semibold text-erms-text mb-3">
                    ความคิดเห็น
                    @if($task->comments->count())
                        <span class="text-2xs font-normal text-erms-muted">({{ $task->comments->count() }})</span>
                    @endif
                </h2>
                <div class="space-y-3 mb-4">
                    @foreach($task->comments as $comment)
                        <div class="flex gap-2.5">
                            <img src="{{ $comment->user->avatar_url }}" alt="" class="w-7 h-7 rounded-full ring-1 ring-erms-border-light flex-shrink-0 mt-0.5">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-baseline gap-2 mb-0.5">
                                    <span class="text-[13px] font-medium text-erms-text">{{ $comment->user->name }}</span>
                                    <span class="text-2xs text-erms-muted">{{ $comment->created_at->diffForHumans() }}</span>
                                </div>
                                <div class="text-[13px] text-erms-text-secondary leading-relaxed">{!! nl2br(e($comment->body)) !!}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <form action="{{ route('tasks.comments.store', $task) }}" method="POST" class="flex gap-2 items-start">
                    @csrf
                    <img src="{{ auth()->user()->avatar_url }}" alt="" class="w-7 h-7 rounded-full ring-1 ring-erms-border-light flex-shrink-0 mt-0.5">
                    <div class="flex-1 flex gap-2">
                        <input type="text" name="body" class="input-field flex-1 !text-[13px] !py-1.5" placeholder="เพิ่มความคิดเห็น..." required>
                        <button type="submit" class="btn-primary !py-1.5 !text-xs">ส่ง</button>
                    </div>
                </form>
            </div>

            <div class="divider"></div>

            {{-- ═══ Attachments ═══ --}}
            <div>
                <h2 class="text-[13px] font-semibold text-erms-text mb-3">
                    ไฟล์แนบ
                    @if($task->attachments->count())
                        <span class="text-2xs font-normal text-erms-muted">({{ $task->attachments->count() }})</span>
                    @endif
                </h2>
                @if($task->attachments->count())
                    <div class="space-y-1.5 mb-3">
                        @foreach($task->attachments as $attachment)
                            <div class="flex items-center justify-between bg-erms-surface-2/60 rounded-lg px-3 py-2 group">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <div class="w-8 h-8 rounded-md bg-erms-blue-light flex items-center justify-center flex-shrink-0">
                                        <svg class="w-4 h-4 text-erms-blue" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-[13px] font-medium truncate">{{ $attachment->file_name }}</p>
                                        <p class="text-2xs text-erms-muted">{{ number_format($attachment->file_size / 1024, 1) }} KB · {{ $attachment->user->name }} · {{ $attachment->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition">
                                    <a href="{{ route('attachments.download', $attachment) }}" class="btn-icon !w-7 !h-7" title="ดาวน์โหลด">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                    </a>
                                    <form method="POST" action="{{ route('attachments.destroy', $attachment) }}" onsubmit="return confirm('ลบไฟล์นี้?')">
                                        @csrf @method('DELETE')
                                        <button class="btn-icon !w-7 !h-7 text-erms-red" title="ลบ">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
                <form action="{{ route('tasks.attachments.store', $task) }}" method="POST" enctype="multipart/form-data" class="flex gap-2">
                    @csrf
                    <input type="file" name="file" class="input-field flex-1 !text-[13px] !py-1.5 file:mr-3 file:py-0.5 file:px-2.5 file:rounded-md file:border-0 file:text-2xs file:bg-erms-blue-light file:text-erms-blue file:cursor-pointer file:font-medium" required>
                    <button type="submit" class="btn-secondary !text-xs !py-1.5">อัปโหลด</button>
                </form>
            </div>
        </div>

        {{-- ═══ Right Sidebar (Task Details) ═══ --}}
        <div class="space-y-4">
            {{-- Detail Fields --}}
            <div class="card p-4 space-y-3">
                <h3 class="text-[11px] font-semibold text-erms-muted uppercase tracking-wider mb-2">รายละเอียด</h3>

                <div class="flex items-center justify-between">
                    <span class="text-2xs text-erms-text-secondary">ผู้รับผิดชอบ</span>
                    @if($task->assignee)
                        <div class="flex items-center gap-1.5">
                            <img src="{{ $task->assignee->avatar_url }}" alt="" class="w-5 h-5 rounded-full ring-1 ring-erms-border-light">
                            <span class="text-[13px] font-medium">{{ $task->assignee->name }}</span>
                        </div>
                    @else
                        <span class="text-2xs text-erms-muted">ไม่ระบุ</span>
                    @endif
                </div>

                <div class="flex items-center justify-between">
                    <span class="text-2xs text-erms-text-secondary">วันเริ่มต้น</span>
                    <span class="text-[13px]">{{ $task->start_date?->translatedFormat('d M Y') ?? '—' }}</span>
                </div>

                <div class="flex items-center justify-between">
                    <span class="text-2xs text-erms-text-secondary">กำหนดส่ง</span>
                    <span class="text-[13px] {{ $task->due_date?->isPast() && $task->status !== 'done' ? 'text-erms-red font-medium' : '' }}">
                        {{ $task->due_date?->translatedFormat('d M Y') ?? '—' }}
                    </span>
                </div>

                @if($task->estimated_hours)
                    <div class="flex items-center justify-between">
                        <span class="text-2xs text-erms-text-secondary">ชั่วโมงประมาณ</span>
                        <span class="text-[13px]">{{ $task->estimated_hours }} ชม.</span>
                    </div>
                @endif
            </div>

            {{-- Activity / History --}}
            <div class="card">
                <div class="px-4 py-3 border-b border-erms-border-light">
                    <h3 class="text-[11px] font-semibold text-erms-muted uppercase tracking-wider">ประวัติ</h3>
                </div>
                <div class="max-h-72 overflow-y-auto">
                    @forelse($task->taskUpdates->sortByDesc('created_at') as $update)
                        <div class="px-4 py-2.5 border-b border-erms-border-light last:border-0">
                            <div class="flex items-center gap-1.5 mb-0.5">
                                <span class="text-2xs font-medium text-erms-text">{{ $update->user->name }}</span>
                                <span class="text-2xs text-erms-muted">{{ $update->created_at->diffForHumans() }}</span>
                            </div>
                            <div class="flex items-center gap-1 text-2xs">
                                @if($update->old_status)
                                    <span class="badge-{{ str_replace('_', '-', $update->old_status) }}">{{ $statusLabels[$update->old_status] ?? $update->old_status }}</span>
                                    <svg class="w-3 h-3 text-erms-muted" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                                @endif
                                <span class="badge-{{ str_replace('_', '-', $update->new_status) }}">{{ $statusLabels[$update->new_status] ?? $update->new_status }}</span>
                            </div>
                            @if($update->note)
                                <p class="text-2xs text-erms-muted mt-0.5">{{ $update->note }}</p>
                            @endif
                        </div>
                    @empty
                        <div class="px-4 py-6 text-center text-erms-muted text-2xs">ไม่มีประวัติ</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
