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

            {{-- ═══ Comments (Asana-style inline) ═══ --}}
            <div>
                <h2 class="text-[13px] font-semibold text-erms-text mb-3">
                    ความคิดเห็น
                    @if($task->comments->count())
                        <span class="text-2xs font-normal text-erms-muted">({{ $task->comments->count() }})</span>
                    @endif
                </h2>
                <div class="space-y-3 mb-4">
                    @foreach($task->comments as $comment)
                        <div class="flex gap-2.5 group/comment">
                            <img src="{{ $comment->user->avatar_url }}" alt="" class="w-7 h-7 rounded-full ring-1 ring-erms-border-light flex-shrink-0 mt-0.5">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-0.5">
                                    <span class="text-[13px] font-medium text-erms-text">{{ $comment->user->name }}</span>
                                    <span class="text-2xs text-erms-muted">{{ $comment->created_at->diffForHumans() }}</span>
                                    @if($comment->user_id === auth()->id() || auth()->user()->hasRole('admin'))
                                        <button wire:click="deleteComment({{ $comment->id }})" wire:confirm="ลบความคิดเห็นนี้?"
                                                class="opacity-0 group-hover/comment:opacity-100 text-erms-muted hover:text-erms-red transition cursor-pointer ml-auto">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    @endif
                                </div>
                                <div class="text-[13px] text-erms-text-secondary leading-relaxed">{!! preg_replace('/@(\S+)/', '<span class="text-erms-blue font-medium">@$1</span>', nl2br(e($comment->body))) !!}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="flex gap-2 items-start">
                    <img src="{{ auth()->user()->avatar_url }}" alt="" class="w-7 h-7 rounded-full ring-1 ring-erms-border-light flex-shrink-0 mt-0.5">
                    <div class="flex-1">
                        <form wire:submit="addComment" class="flex gap-2">
                            <div class="flex-1 relative" x-data="commentMention()" @click.away="showSuggestions = false">
                                <input type="text" wire:model="commentBody" x-ref="commentInput"
                                       @input="onInput($event)" @keydown.escape="showSuggestions = false"
                                       @keydown.arrow-down.prevent="highlightNext()" @keydown.arrow-up.prevent="highlightPrev()"
                                       @keydown.enter="if(showSuggestions && suggestions.length) { $event.preventDefault(); selectSuggestion(); }"
                                       class="input-field flex-1 !text-[13px] !py-1.5 w-full" placeholder="เพิ่มความคิดเห็น... (ใช้ @ เพื่อกล่าวถึง)">
                                {{-- @Mention dropdown --}}
                                <div x-show="showSuggestions && suggestions.length > 0" x-cloak
                                     class="absolute bottom-full left-0 mb-1 w-56 bg-white border border-erms-border rounded-lg shadow-asana-lg py-1 z-50 max-h-40 overflow-y-auto">
                                    <template x-for="(user, idx) in suggestions" :key="user.id">
                                        <button type="button" @click="selectUser(user)"
                                                :class="idx === highlightedIndex ? 'bg-erms-surface-2' : ''"
                                                class="flex items-center gap-2 w-full px-3 py-1.5 text-left hover:bg-erms-surface-2 transition">
                                            <img :src="user.avatar" class="w-5 h-5 rounded-full" alt="">
                                            <span class="text-[13px] text-erms-text" x-text="user.name"></span>
                                        </button>
                                    </template>
                                </div>
                            </div>
                            <button type="submit" class="btn-primary !py-1.5 !text-xs" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="addComment">ส่ง</span>
                                <span wire:loading wire:target="addComment">...</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="divider"></div>

            {{-- ═══ Attachments (Asana-style inline) ═══ --}}
            <div>
                <h2 class="text-[13px] font-semibold text-erms-text mb-3">
                    ไฟล์แนบและลิงก์
                    @if($task->attachments->count())
                        <span class="text-2xs font-normal text-erms-muted">({{ $task->attachments->count() }})</span>
                    @endif
                </h2>

                {{-- Attachments List --}}
                @if($task->attachments->count())
                    <div class="space-y-1.5 mb-3">
                        @foreach($task->attachments as $attachment)
                            <div class="flex items-center justify-between bg-erms-surface-2/60 rounded-lg px-3 py-2 group">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    @if($attachment->isLink())
                                        @php
                                            $isGoogleDrive = str_contains($attachment->external_url ?? '', 'drive.google.com');
                                            $iconBg = $isGoogleDrive ? 'bg-green-100' : 'bg-erms-purple-light';
                                            $iconColor = $isGoogleDrive ? 'text-green-600' : 'text-erms-purple';
                                        @endphp
                                        <div class="w-8 h-8 rounded-md {{ $iconBg }} flex items-center justify-center flex-shrink-0">
                                            @if($isGoogleDrive)
                                                <svg class="w-4 h-4 {{ $iconColor }}" viewBox="0 0 24 24" fill="currentColor"><path d="M7.71 3.5L1.15 15l3.43 5.5 6.56-11.5L7.71 3.5zm2.86 5l-6.57 11.5h13.14L23.71 8.5H10.57zm6.86-5L12 12l6.57 11.5 3.43-6L17.43 3.5z"/></svg>
                                            @else
                                                <svg class="w-4 h-4 {{ $iconColor }}" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                                            @endif
                                        </div>
                                    @else
                                        @php
                                            $ext = strtolower(pathinfo($attachment->file_name, PATHINFO_EXTENSION));
                                            $isImage = in_array($ext, ['jpg','jpeg','png','gif','webp','svg']);
                                            $isPdf = $ext === 'pdf';
                                            $fileIconBg = $isImage ? 'bg-erms-purple-light' : ($isPdf ? 'bg-red-50' : 'bg-erms-blue-light');
                                            $fileIconColor = $isImage ? 'text-erms-purple' : ($isPdf ? 'text-red-500' : 'text-erms-blue');
                                        @endphp
                                        <div class="w-8 h-8 rounded-md {{ $fileIconBg }} flex items-center justify-center flex-shrink-0">
                                            @if($isImage)
                                                <svg class="w-4 h-4 {{ $fileIconColor }}" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            @else
                                                <svg class="w-4 h-4 {{ $fileIconColor }}" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                            @endif
                                        </div>
                                    @endif
                                    <div class="min-w-0">
                                        @if($attachment->isLink())
                                            <a href="{{ $attachment->external_url }}" target="_blank" rel="noopener" class="text-[13px] font-medium truncate block text-erms-blue hover:underline">{{ $attachment->file_name }}</a>
                                            <p class="text-2xs text-erms-muted">ลิงก์ · {{ $attachment->user->name }} · {{ $attachment->created_at->diffForHumans() }}</p>
                                        @else
                                            <a href="{{ route('attachments.download', $attachment) }}" class="text-[13px] font-medium truncate block text-erms-text hover:text-erms-blue transition">{{ $attachment->file_name }}</a>
                                            <p class="text-2xs text-erms-muted">{{ number_format($attachment->file_size / 1024, 1) }} KB · {{ $attachment->user->name }} · {{ $attachment->created_at->diffForHumans() }}</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition">
                                    @if($attachment->isLink())
                                        <a href="{{ $attachment->external_url }}" target="_blank" rel="noopener" class="btn-icon !w-7 !h-7" title="เปิดลิงก์">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                        </a>
                                    @else
                                        <a href="{{ route('attachments.download', $attachment) }}" class="btn-icon !w-7 !h-7" title="ดาวน์โหลด">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                        </a>
                                    @endif
                                    <button wire:click="deleteAttachment({{ $attachment->id }})" wire:confirm="ลบไฟล์นี้?"
                                            class="btn-icon !w-7 !h-7 text-erms-red" title="ลบ">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                {{-- Livewire File Upload --}}
                <div class="space-y-2">
                    <div x-data="{ dragging: false }" class="relative">
                        <div x-on:dragover.prevent="dragging = true" x-on:dragleave.prevent="dragging = false" x-on:drop.prevent="dragging = false"
                             :class="dragging ? 'border-erms-blue bg-erms-blue/5' : 'border-erms-border-light'"
                             class="border-2 border-dashed rounded-lg p-3 transition text-center cursor-pointer hover:border-erms-blue/50">
                            <input type="file" wire:model="uploadFiles" multiple class="absolute inset-0 opacity-0 cursor-pointer" />
                            <div class="flex items-center justify-center gap-2 text-2xs text-erms-muted">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                                <span>ลากไฟล์มาวางหรือคลิกเลือก (สูงสุด 10MB)</span>
                            </div>
                        </div>
                        <div wire:loading wire:target="uploadFiles" class="text-2xs text-erms-blue mt-1 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                            กำลังอัปโหลด...
                        </div>
                    </div>
                    @if(count($uploadFiles ?? []) > 0)
                        <div class="flex items-center gap-2">
                            <span class="text-2xs text-erms-text-secondary">{{ count($uploadFiles) }} ไฟล์พร้อมอัปโหลด</span>
                            <button wire:click="uploadAttachments" class="btn-primary !text-xs !py-1">อัปโหลด</button>
                            <button wire:click="$set('uploadFiles', [])" class="btn-ghost !text-xs !py-1">ยกเลิก</button>
                        </div>
                    @endif
                    @error('uploadFiles.*') <p class="text-2xs text-erms-red">{{ $message }}</p> @enderror

                    {{-- Add Link (Google Drive, etc.) --}}
                    @if($showLinkForm)
                        <div class="bg-erms-surface-2/60 rounded-lg p-3 space-y-2">
                            <div class="flex items-center gap-2 text-[13px] font-medium text-erms-text mb-2">
                                <svg class="w-4 h-4 text-erms-purple" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                                เพิ่มลิงก์ (Google Drive, URL อื่นๆ)
                            </div>
                            <input type="text" wire:model="linkName" placeholder="ชื่อลิงก์" class="input-field !text-[13px] !py-1.5 w-full" />
                            @error('linkName') <p class="text-2xs text-erms-red">{{ $message }}</p> @enderror
                            <input type="url" wire:model="linkUrl" placeholder="https://drive.google.com/..." class="input-field !text-[13px] !py-1.5 w-full" />
                            @error('linkUrl') <p class="text-2xs text-erms-red">{{ $message }}</p> @enderror
                            <div class="flex items-center gap-2 pt-1">
                                <button wire:click="addLinkAttachment" class="btn-primary !text-xs !py-1.5">บันทึกลิงก์</button>
                                <button wire:click="$set('showLinkForm', false)" class="btn-ghost !text-xs !py-1">ยกเลิก</button>
                            </div>
                        </div>
                    @else
                        <button wire:click="$set('showLinkForm', true)" class="text-2xs text-erms-text-secondary hover:text-erms-blue transition flex items-center gap-1 mt-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                            เพิ่มลิงก์ Google Drive / URL
                        </button>
                    @endif
                </div>
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
