<div>
    {{-- Work Log Form --}}
    <div class="card p-6 mb-6">
        <h2 class="font-heading font-bold text-base mb-4">{{ $editingId ? 'แก้ไขบันทึกเวลา' : 'บันทึกเวลาทำงาน' }}</h2>
        <form wire:submit="save" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm text-erms-muted mb-1">งาน *</label>
                    <select wire:model="task_id" class="input-field" required>
                        <option value="">เลือกงาน</option>
                        @foreach($this->tasks as $task)
                            <option value="{{ $task->id }}">{{ $task->title }} ({{ $task->project->name ?? '-' }})</option>
                        @endforeach
                    </select>
                    @error('task_id') <p class="text-xs text-erms-red mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm text-erms-muted mb-1">วันที่ *</label>
                    <input type="date" wire:model="date" class="input-field" required>
                    @error('date') <p class="text-xs text-erms-red mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm text-erms-muted mb-1">ชั่วโมง *</label>
                    <input type="number" wire:model="hours" class="input-field" step="0.25" min="0.25" max="24" placeholder="0.00" required>
                    @error('hours') <p class="text-xs text-erms-red mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm text-erms-muted mb-1">รายละเอียด</label>
                    <input type="text" wire:model="description" class="input-field" placeholder="สิ่งที่ทำ...">
                </div>
            </div>
            <div class="flex items-center gap-3">
                <button type="submit" class="btn-primary">
                    {{ $editingId ? 'อัปเดต' : 'บันทึก' }}
                </button>
                @if($editingId)
                    <button type="button" wire:click="cancelEdit" class="btn-secondary">ยกเลิก</button>
                @endif
            </div>
        </form>
    </div>

    {{-- Week Summary --}}
    <div class="card p-5 mb-6">
        <div class="flex items-center justify-between">
            <h3 class="text-sm font-medium text-erms-muted">ชั่วโมงสัปดาห์นี้</h3>
            <span class="text-2xl font-heading font-bold text-erms-green">{{ number_format($this->weekHours, 1) }} ชม.</span>
        </div>
        <div class="progress-bar mt-3">
            <div class="fill" style="width: {{ min(($this->weekHours / 40) * 100, 100) }}%"></div>
        </div>
        <p class="text-xs text-erms-muted mt-1">เป้าหมาย 40 ชั่วโมง/สัปดาห์</p>
    </div>

    {{-- Today's Logs --}}
    <div class="card">
        <div class="px-5 py-4 border-b border-erms-border">
            <h2 class="font-heading font-bold text-base">บันทึกวันนี้</h2>
        </div>
        <div class="divide-y divide-erms-border/50">
            @forelse($this->todayLogs as $log)
                <div class="flex items-center gap-4 px-5 py-3">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium">{{ $log->task->title ?? '-' }}</p>
                        <p class="text-xs text-erms-muted">{{ $log->task->project->name ?? '-' }}</p>
                        @if($log->description)
                            <p class="text-xs text-erms-muted mt-0.5">{{ $log->description }}</p>
                        @endif
                    </div>
                    <span class="text-sm font-medium text-erms-blue">{{ number_format($log->hours, 2) }} ชม.</span>
                    <div class="flex items-center gap-1">
                        <button wire:click="edit({{ $log->id }})" class="text-erms-muted hover:text-erms-blue transition p-1" aria-label="แก้ไข">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </button>
                        <button wire:click="delete({{ $log->id }})" wire:confirm="ต้องการลบรายการนี้?" class="text-erms-muted hover:text-erms-red transition p-1" aria-label="ลบ">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>
                </div>
            @empty
                <div class="px-5 py-8 text-center text-erms-muted text-sm">ยังไม่มีบันทึกวันนี้</div>
            @endforelse
        </div>
    </div>
</div>
