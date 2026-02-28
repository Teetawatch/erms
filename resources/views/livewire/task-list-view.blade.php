<div>
    {{-- Filters --}}
    <div class="card p-4 mb-4">
        <div class="flex flex-wrap items-end gap-3">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs text-erms-muted mb-1">ค้นหา</label>
                <input type="text" wire:model.live.debounce.300ms="search" class="input-field" placeholder="ค้นหางาน...">
            </div>
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
                <label class="block text-xs text-erms-muted mb-1">ความสำคัญ</label>
                <select wire:model.live="filterPriority" class="input-field w-36">
                    <option value="">ทั้งหมด</option>
                    <option value="urgent">เร่งด่วน</option>
                    <option value="high">สูง</option>
                    <option value="medium">ปานกลาง</option>
                    <option value="low">ต่ำ</option>
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
        </div>
    </div>

    {{-- Task Table --}}
    <div class="card overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-erms-border bg-erms-surface-2">
                    <th class="text-left px-4 py-3 text-xs font-medium text-erms-muted uppercase tracking-wider">
                        <button wire:click="sort('title')" class="flex items-center gap-1 hover:text-erms-text cursor-pointer">
                            งาน
                            @if($sortBy === 'title')
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="{{ $sortDir === 'asc' ? 'M5 10l5-5 5 5H5z' : 'M5 10l5 5 5-5H5z' }}"/></svg>
                            @endif
                        </button>
                    </th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-erms-muted uppercase tracking-wider">โครงการ</th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-erms-muted uppercase tracking-wider">ผู้รับผิดชอบ</th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-erms-muted uppercase tracking-wider">
                        <button wire:click="sort('status')" class="flex items-center gap-1 hover:text-erms-text cursor-pointer">
                            สถานะ
                            @if($sortBy === 'status')
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="{{ $sortDir === 'asc' ? 'M5 10l5-5 5 5H5z' : 'M5 10l5 5 5-5H5z' }}"/></svg>
                            @endif
                        </button>
                    </th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-erms-muted uppercase tracking-wider">
                        <button wire:click="sort('priority')" class="flex items-center gap-1 hover:text-erms-text cursor-pointer">
                            ความสำคัญ
                            @if($sortBy === 'priority')
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="{{ $sortDir === 'asc' ? 'M5 10l5-5 5 5H5z' : 'M5 10l5 5 5-5H5z' }}"/></svg>
                            @endif
                        </button>
                    </th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-erms-muted uppercase tracking-wider">
                        <button wire:click="sort('due_date')" class="flex items-center gap-1 hover:text-erms-text cursor-pointer">
                            กำหนดส่ง
                            @if($sortBy === 'due_date')
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="{{ $sortDir === 'asc' ? 'M5 10l5-5 5 5H5z' : 'M5 10l5 5 5-5H5z' }}"/></svg>
                            @endif
                        </button>
                    </th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-erms-muted uppercase tracking-wider">งานย่อย</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-erms-border/50">
                @php
                    $statusLabels = ['todo' => 'รอดำเนินการ', 'in_progress' => 'กำลังดำเนินการ', 'review' => 'ตรวจสอบ', 'done' => 'เสร็จสิ้น'];
                    $priorityLabels = ['low' => 'ต่ำ', 'medium' => 'ปานกลาง', 'high' => 'สูง', 'urgent' => 'เร่งด่วน'];
                @endphp
                @forelse($this->tasks as $task)
                    <tr class="hover:bg-erms-surface-2 transition group">
                        <td class="px-4 py-3">
                            <a href="{{ route('tasks.show', $task) }}" class="font-medium text-sm hover:text-erms-blue transition" wire:navigate>
                                {{ $task->title }}
                            </a>
                        </td>
                        <td class="px-4 py-3 text-xs text-erms-muted">{{ $task->project->name ?? '-' }}</td>
                        <td class="px-4 py-3">
                            @if($task->assignee)
                                <div class="flex items-center gap-2">
                                    <img src="{{ $task->assignee->avatar_url }}" alt="" class="w-5 h-5 rounded-full">
                                    <span class="text-xs">{{ $task->assignee->name }}</span>
                                </div>
                            @else
                                <span class="text-xs text-erms-muted">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <select wire:change="quickStatusChange({{ $task->id }}, $event.target.value)" class="text-xs rounded-full px-2 py-1 border border-erms-border bg-white cursor-pointer focus:outline-none focus:ring-1 focus:ring-erms-blue">
                                @foreach($statusLabels as $val => $label)
                                    <option value="{{ $val }}" @selected($task->status === $val)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td class="px-4 py-3">
                            <span class="badge-{{ $task->priority }}">{{ $priorityLabels[$task->priority] ?? $task->priority }}</span>
                        </td>
                        <td class="px-4 py-3 text-xs {{ $task->due_date?->isPast() && $task->status !== 'done' ? 'text-erms-red font-medium' : 'text-erms-muted' }}">
                            {{ $task->due_date?->translatedFormat('d M Y') ?? '-' }}
                        </td>
                        <td class="px-4 py-3 text-xs text-erms-muted">
                            @if($task->subtasks->count())
                                {{ $task->subtasks->where('status', 'done')->count() }}/{{ $task->subtasks->count() }}
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-erms-muted text-sm">ไม่พบงาน</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-4">
        {{ $this->tasks->links() }}
    </div>
</div>
