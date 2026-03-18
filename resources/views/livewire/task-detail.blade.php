<div>
    @php
        $statusLabels = ['todo' => 'รอดำเนินการ', 'in_progress' => 'กำลังดำเนินการ', 'review' => 'ตรวจสอบ', 'done' => 'เสร็จสิ้น'];
        $priorityLabels = ['low' => 'ต่ำ', 'medium' => 'ปานกลาง', 'high' => 'สูง', 'urgent' => 'เร่งด่วน'];
        $statusColors = ['todo' => '#9ca0a4', 'in_progress' => '#4573d2', 'review' => '#7c5cfc', 'done' => '#5da283'];
        $canManage = $this->canManageTask();
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
                        <i class="fa-solid fa-chevron-right text-xs"></i>
                        <span class="text-erms-text-secondary">{{ $task->title }}</span>
                    </div>
                @endif

                {{-- Title Row --}}
                <div class="flex items-start gap-3 mb-3">
                    @if($canManage)
                    <button wire:click="quickStatusChange('{{ $task->status === 'done' ? 'todo' : 'done' }}')"
                            class="task-checkbox mt-1 {{ $task->status === 'done' ? 'checked' : '' }} !w-6 !h-6"
                            title="{{ $task->status === 'done' ? 'ทำเครื่องหมายไม่เสร็จ' : 'ทำเครื่องหมายเสร็จ' }}">
                        @if($task->status === 'done')
                            <i class="fa-solid fa-check text-white text-sm"></i>
                        @endif
                    </button>
                    @else
                    <span class="task-checkbox mt-1 {{ $task->status === 'done' ? 'checked' : '' }} !w-6 !h-6">
                        @if($task->status === 'done')
                            <i class="fa-solid fa-check text-white text-sm"></i>
                        @endif
                    </span>
                    @endif
                    <h1 class="text-xl font-semibold text-erms-text leading-tight {{ $task->status === 'done' ? 'line-through text-erms-muted' : '' }}">
                        {{ $task->title }}
                    </h1>
                </div>

                {{-- Status & Meta Badges --}}
                <div class="flex items-center gap-2 flex-wrap ml-9">
                    @if($canManage)
                    <select wire:change="quickStatusChange($event.target.value)"
                            class="badge-{{ str_replace('_', '-', $task->status) }} border-0 cursor-pointer focus:outline-none focus:ring-1 focus:ring-erms-blue/30 pr-5 appearance-none text-2xs">
                        @foreach($statusLabels as $val => $label)
                            <option value="{{ $val }}" @selected($task->status === $val)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @else
                    <span class="badge-{{ str_replace('_', '-', $task->status) }} text-2xs">{{ $statusLabels[$task->status] ?? $task->status }}</span>
                    @endif
                    <span class="badge-{{ $task->priority }}">{{ $priorityLabels[$task->priority] ?? $task->priority }}</span>
                    @if($task->project)
                        <a href="{{ route('projects.show', $task->project) }}" class="text-2xs text-erms-blue hover:underline font-medium" wire:navigate>{{ $task->project->name }}</a>
                    @endif
                    @if($task->isBlocked())
                        <span class="badge-urgent">
                            <i class="fa-solid fa-lock text-xs"></i>
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
                    @if($canManage)
                        @if($editingProgress)
                            <div class="flex items-center gap-2">
                                <input type="number" wire:model="progress" min="0" max="100" class="input-field !w-16 !text-xs !py-1 text-center">
                                <button wire:click="updateProgress" class="text-2xs text-erms-blue font-medium hover:underline cursor-pointer">บันทึก</button>
                                <button wire:click="$set('editingProgress', false)" class="text-2xs text-erms-muted hover:text-erms-text cursor-pointer">ยกเลิก</button>
                            </div>
                        @else
                            <button wire:click="$set('editingProgress', true)" class="text-2xs text-erms-blue font-medium hover:underline cursor-pointer">{{ $task->progress }}%</button>
                        @endif
                    @else
                        <span class="text-2xs text-erms-text-secondary">{{ $task->progress }}%</span>
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
                    @if($canManage)
                    <button wire:click="$toggle('showSubtaskForm')" class="btn-ghost !text-2xs !text-erms-blue">
                        <i class="fa-solid fa-plus"></i>
                        เพิ่ม
                    </button>
                    @endif
                </div>

                @if($canManage && $showSubtaskForm)
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
                                    <i class="fa-solid fa-check text-white text-xs"></i>
                                @endif
                            </button>
                            <a href="{{ route('tasks.show', $subtask) }}" class="flex-1 text-[13px] {{ $subtask->status === 'done' ? 'line-through text-erms-muted' : 'text-erms-text' }} hover:text-erms-blue transition" wire:navigate>
                                {{ $subtask->title }}
                            </a>
                            @if($subtask->assignee)
                                <img src="{{ $subtask->assignee->avatar_url }}" alt="" class="w-5 h-5 rounded-full ring-1 ring-erms-border-light" title="{{ $subtask->assignee->name }}">
                            @endif
                            @if($canManage)
                            <button wire:click="deleteSubtask({{ $subtask->id }})" wire:confirm="ลบงานย่อยนี้?" class="opacity-0 group-hover:opacity-100 text-erms-muted hover:text-erms-red transition cursor-pointer">
                                <i class="fa-solid fa-trash text-xs"></i>
                            </button>
                            @endif
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
                    @if($canManage)
                    <button wire:click="$toggle('showDependencyForm')" class="btn-ghost !text-2xs !text-erms-blue">
                        <i class="fa-solid fa-plus"></i>
                        เพิ่ม
                    </button>
                    @endif
                </div>

                @if($canManage && $showDependencyForm)
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
                                    <i class="fa-solid fa-check text-white text-xs"></i>
                                @endif
                            </span>
                            <a href="{{ route('tasks.show', $dep->dependsOnTask) }}" class="flex-1 text-[13px] hover:text-erms-blue transition" wire:navigate>
                                {{ $dep->dependsOnTask->title }}
                            </a>
                            <span class="badge-{{ str_replace('_', '-', $dep->dependsOnTask->status) }}">{{ $statusLabels[$dep->dependsOnTask->status] ?? $dep->dependsOnTask->status }}</span>
                            @if($canManage)
                            <button wire:click="removeDependency({{ $dep->id }})" class="opacity-0 group-hover:opacity-100 text-erms-muted hover:text-erms-red transition cursor-pointer">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                            @endif
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
                            @if($comment->is_anonymous && $comment->user_id !== auth()->id() && !auth()->user()->hasRole('admin'))
                                <div class="w-7 h-7 rounded-full bg-erms-surface-2 flex items-center justify-center flex-shrink-0 mt-0.5 ring-1 ring-erms-border-light">
                                    <i class="fa-solid fa-user text-erms-muted text-sm"></i>
                                </div>
                            @else
                                <img src="{{ $comment->user->avatar_url }}" alt="" class="w-7 h-7 rounded-full ring-1 ring-erms-border-light flex-shrink-0 mt-0.5">
                            @endif
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-0.5">
                                    @if($comment->is_anonymous && $comment->user_id !== auth()->id() && !auth()->user()->hasRole('admin'))
                                        <span class="text-[13px] font-medium text-erms-muted italic">ไม่ระบุตัวตน</span>
                                    @else
                                        <span class="text-[13px] font-medium text-erms-text">{{ $comment->user->name }}</span>
                                        @if($comment->is_anonymous)
                                            <span class="text-2xs text-erms-muted italic">(ไม่ระบุตัวตน)</span>
                                        @endif
                                    @endif
                                    <span class="text-2xs text-erms-muted">{{ $comment->created_at->diffForHumans() }}</span>
                                    @if($comment->user_id === auth()->id() || auth()->user()->hasRole('admin'))
                                        <button wire:click="deleteComment({{ $comment->id }})" wire:confirm="ลบความคิดเห็นนี้?"
                                                class="opacity-0 group-hover/comment:opacity-100 text-erms-muted hover:text-erms-red transition cursor-pointer ml-auto">
                                            <i class="fa-solid fa-trash text-xs"></i>
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
                        <form wire:submit="addComment" class="space-y-2">
                            <div class="flex gap-2">
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
                            </div>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" wire:model="isAnonymousComment" class="rounded border-erms-border text-erms-blue focus:ring-erms-blue/30 w-3.5 h-3.5">
                                <span class="text-2xs text-erms-muted">ส่งแบบไม่ระบุตัวตน</span>
                                <i class="fa-solid fa-eye-slash text-erms-muted text-xs"></i>
                            </label>
                        </form>
                    </div>
                </div>
            </div>

            <div class="divider"></div>

            {{-- ═══ Time Tracking ═══ --}}
            <div>
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-[13px] font-semibold text-erms-text flex items-center gap-2">
                        <i class="fa-solid fa-clock text-erms-blue"></i>
                        บันทึกเวลา
                        @if($task->timeEntries->count())
                            <span class="text-2xs font-normal text-erms-muted">({{ number_format($task->timeEntries->sum('hours'), 2) }} ชม.)</span>
                        @endif
                    </h2>
                    <button wire:click="$toggle('showTimeEntryForm')" class="btn-ghost !text-2xs !text-erms-blue">
                        <i class="fa-solid fa-plus"></i>
                        เพิ่ม
                    </button>
                </div>

                @if($task->estimated_hours)
                    @php $totalLogged = $task->timeEntries->sum('hours'); $pct = min(100, ($totalLogged / $task->estimated_hours) * 100); @endphp
                    <div class="mb-3 bg-erms-surface-2/60 rounded-lg p-3">
                        <div class="flex items-center justify-between text-2xs mb-1.5">
                            <span class="text-erms-text-secondary">บันทึกแล้ว / ประมาณการ</span>
                            <span class="font-medium {{ $pct > 100 ? 'text-erms-red' : 'text-erms-text' }}">{{ number_format($totalLogged, 2) }} / {{ $task->estimated_hours }} ชม.</span>
                        </div>
                        <div class="w-full h-2 bg-erms-surface rounded-full overflow-hidden">
                            <div class="{{ $pct > 100 ? 'bg-erms-red' : 'bg-erms-blue' }} h-full rounded-full transition-all" style="width: {{ min($pct, 100) }}%"></div>
                        </div>
                    </div>
                @endif

                @if($showTimeEntryForm)
                    <div class="mb-3 bg-erms-surface-2/60 rounded-lg p-3 space-y-2">
                        <form wire:submit="addTimeEntry" class="space-y-2">
                            {{-- Date --}}
                            <div>
                                <label class="text-2xs text-erms-muted mb-0.5 block">วันที่ *</label>
                                <input type="date" wire:model="timeEntryDate" class="input-field !text-[13px] !py-1.5 w-full">
                                @error('timeEntryDate') <p class="text-2xs text-erms-red mt-0.5">{{ $message }}</p> @enderror
                            </div>
                            {{-- Start / End time --}}
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="text-2xs text-erms-muted mb-0.5 block">เวลาเริ่ม *</label>
                                    <input type="time" wire:model.live="timeEntryStartTime" class="input-field !text-[13px] !py-1.5 w-full">
                                    @error('timeEntryStartTime') <p class="text-2xs text-erms-red mt-0.5">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="text-2xs text-erms-muted mb-0.5 block">เวลาสิ้นสุด *</label>
                                    <input type="time" wire:model.live="timeEntryEndTime" class="input-field !text-[13px] !py-1.5 w-full">
                                    @error('timeEntryEndTime') <p class="text-2xs text-erms-red mt-0.5">{{ $message }}</p> @enderror
                                </div>
                            </div>
                            {{-- Auto-calculated hours preview --}}
                            @if($timeEntryStartTime && $timeEntryEndTime)
                                @php
                                    $s = \Carbon\Carbon::createFromTimeString($timeEntryStartTime);
                                    $e = \Carbon\Carbon::createFromTimeString($timeEntryEndTime);
                                    $calcHours = $e->greaterThan($s) ? round($s->diffInMinutes($e) / 60, 2) : null;
                                @endphp
                                @if($calcHours !== null)
                                    <div class="flex items-center gap-1.5 text-2xs bg-erms-blue-light/60 text-erms-blue rounded-md px-2.5 py-1.5">
                                        <i class="fa-solid fa-calculator"></i>
                                        <span>รวม <strong>{{ $calcHours }}</strong> ชั่วโมง
                                            ({{ $timeEntryStartTime }} – {{ $timeEntryEndTime }})</span>
                                    </div>
                                @endif
                            @endif
                            {{-- Overtime toggle --}}
                            <label class="flex items-center gap-2 cursor-pointer select-none">
                                <input type="checkbox" wire:model="timeEntryIsOvertime" class="checkbox checkbox-sm checkbox-warning">
                                <span class="text-2xs text-erms-text-secondary">นอกเวลางาน / วันหยุด</span>
                            </label>
                            {{-- Description --}}
                            <div>
                                <label class="text-2xs text-erms-muted mb-0.5 block">รายละเอียด</label>
                                <input type="text" wire:model="timeEntryDescription" class="input-field !text-[13px] !py-1.5 w-full" placeholder="อธิบายงานที่ทำ...">
                            </div>
                            <div class="flex items-center gap-2 pt-1">
                                <button type="submit" class="btn-primary !text-xs !py-1.5">บันทึก</button>
                                <button type="button" wire:click="$set('showTimeEntryForm', false)" class="btn-secondary !text-xs !py-1.5">ยกเลิก</button>
                            </div>
                        </form>
                    </div>
                @endif

                @if($task->timeEntries->count())
                    <div class="space-y-1">
                        @foreach($task->timeEntries->sortByDesc('date_worked') as $entry)
                            <div class="flex items-center justify-between bg-erms-surface-2/40 rounded-lg px-3 py-2 group hover:bg-erms-surface-2/60 transition">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <div class="w-8 h-8 rounded-md {{ $entry->is_overtime ? 'bg-amber-100' : 'bg-erms-blue-light' }} flex items-center justify-center flex-shrink-0">
                                        <i class="fa-solid fa-clock {{ $entry->is_overtime ? 'text-amber-500' : 'text-erms-blue' }}"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <span class="text-[13px] font-semibold {{ $entry->is_overtime ? 'text-amber-500' : 'text-erms-blue' }}">
                                                {{ number_format($entry->hours, 2) }} ชม.
                                            </span>
                                            @if($entry->start_time && $entry->end_time)
                                                <span class="text-2xs text-erms-muted font-mono">
                                                    {{ \Carbon\Carbon::createFromTimeString($entry->start_time)->format('H:i') }}
                                                    –
                                                    {{ \Carbon\Carbon::createFromTimeString($entry->end_time)->format('H:i') }}
                                                </span>
                                            @endif
                                            <span class="text-2xs text-erms-muted">{{ $entry->date_worked->translatedFormat('d M Y') }}</span>
                                            @if($entry->is_overtime)
                                                <span class="text-2xs bg-amber-100 text-amber-600 rounded px-1.5 py-0.5 font-medium">นอกเวลา</span>
                                            @endif
                                        </div>
                                        <div class="flex items-center gap-1.5 text-2xs text-erms-muted">
                                            <span>{{ $entry->user->name }}</span>
                                            @if($entry->description)
                                                <span>&middot;</span>
                                                <span class="truncate">{{ $entry->description }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @if($entry->user_id === auth()->id() || auth()->user()->hasRole('admin'))
                                    <button wire:click="deleteTimeEntry({{ $entry->id }})" wire:confirm="ลบบันทึกเวลานี้?"
                                            class="opacity-0 group-hover:opacity-100 text-erms-muted hover:text-erms-red transition cursor-pointer flex-shrink-0">
                                        <i class="fa-solid fa-trash text-xs"></i>
                                    </button>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    @if(!$showTimeEntryForm)
                        <p class="text-2xs text-erms-muted py-3 text-center">ยังไม่มีบันทึกเวลา</p>
                    @endif
                @endif
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
                                                <i class="fa-brands fa-google-drive {{ $iconColor }}"></i>
                                            @else
                                                <i class="fa-solid fa-link {{ $iconColor }}"></i>
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
                                                <i class="fa-solid fa-image {{ $fileIconColor }}"></i>
                                            @else
                                                <i class="fa-solid fa-paperclip {{ $fileIconColor }}"></i>
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
                                            <i class="fa-solid fa-up-right-from-square text-xs"></i>
                                        </a>
                                    @else
                                        <a href="{{ route('attachments.download', $attachment) }}" class="btn-icon !w-7 !h-7" title="ดาวน์โหลด">
                                            <i class="fa-solid fa-download text-xs"></i>
                                        </a>
                                    @endif
                                    <button wire:click="deleteAttachment({{ $attachment->id }})" wire:confirm="ลบไฟล์นี้?"
                                            class="btn-icon !w-7 !h-7 text-erms-red" title="ลบ">
                                        <i class="fa-solid fa-trash text-xs"></i>
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
                                <i class="fa-solid fa-cloud-arrow-up"></i>
                                <span>ลากไฟล์มาวางหรือคลิกเลือก (สูงสุด 10MB)</span>
                            </div>
                        </div>
                        <div wire:loading wire:target="uploadFiles" class="text-2xs text-erms-blue mt-1 flex items-center gap-1">
                            <i class="fa-solid fa-spinner fa-spin"></i>
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
                                <i class="fa-solid fa-link text-erms-purple"></i>
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
                            <i class="fa-solid fa-link"></i>
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
                                    <i class="fa-solid fa-arrow-right text-erms-muted text-xs"></i>
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
