<div>
    <div class="flex items-center justify-between mb-4">
        <h3 class="font-heading font-bold text-base">ฟิลด์กำหนดเอง</h3>
        <button wire:click="$set('showCreateModal', true)" class="btn-primary text-xs">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            เพิ่มฟิลด์
        </button>
    </div>

    {{-- Fields List --}}
    <div class="space-y-2">
        @forelse($this->fields as $field)
            <div class="card p-4 flex items-center gap-4">
                <div class="w-8 h-8 rounded-lg bg-erms-blue/10 flex items-center justify-center flex-shrink-0">
                    @switch($field->type)
                        @case('text')
                            <svg class="w-4 h-4 text-erms-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/></svg>
                            @break
                        @case('number')
                            <svg class="w-4 h-4 text-erms-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/></svg>
                            @break
                        @case('date')
                            <svg class="w-4 h-4 text-erms-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            @break
                        @case('select')
                            <svg class="w-4 h-4 text-erms-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                            @break
                        @case('checkbox')
                            <svg class="w-4 h-4 text-erms-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            @break
                    @endswitch
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium">{{ $field->name }}</p>
                    <div class="flex items-center gap-2 mt-0.5">
                        @php
                            $typeLabels = ['text' => 'ข้อความ', 'number' => 'ตัวเลข', 'date' => 'วันที่', 'select' => 'ตัวเลือก', 'checkbox' => 'ช่องเลือก'];
                        @endphp
                        <span class="text-xs text-erms-muted">{{ $typeLabels[$field->type] ?? $field->type }}</span>
                        @if($field->is_required)
                            <span class="text-xs text-erms-red">จำเป็น</span>
                        @endif
                        @if($field->type === 'select' && $field->options)
                            <span class="text-xs text-erms-muted">({{ implode(', ', $field->options) }})</span>
                        @endif
                    </div>
                </div>
                <button wire:click="deleteField({{ $field->id }})" wire:confirm="ลบฟิลด์นี้?" class="text-erms-muted hover:text-erms-red cursor-pointer transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </button>
            </div>
        @empty
            <div class="card p-8 text-center text-erms-muted text-sm">
                ยังไม่มีฟิลด์กำหนดเอง
            </div>
        @endforelse
    </div>

    {{-- Create Field Modal --}}
    @if($showCreateModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60" x-data x-transition>
        <div class="bg-erms-surface border border-erms-border rounded-xl w-full max-w-lg mx-4 shadow-2xl">
            <div class="px-6 py-4 border-b border-erms-border flex items-center justify-between">
                <h3 class="font-heading font-bold text-base">เพิ่มฟิลด์กำหนดเอง</h3>
                <button wire:click="$set('showCreateModal', false)" class="text-erms-muted hover:text-erms-text cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form wire:submit="createField" class="p-6 space-y-4">
                <div>
                    <label class="block text-sm text-erms-muted mb-1">ชื่อฟิลด์ *</label>
                    <input type="text" wire:model="name" class="input-field" placeholder="เช่น หมายเลขงบประมาณ" required>
                    @error('name') <p class="text-xs text-erms-red mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm text-erms-muted mb-1">ประเภท</label>
                    <select wire:model.live="type" class="input-field">
                        <option value="text">ข้อความ</option>
                        <option value="number">ตัวเลข</option>
                        <option value="date">วันที่</option>
                        <option value="select">ตัวเลือก (Dropdown)</option>
                        <option value="checkbox">ช่องเลือก (Checkbox)</option>
                    </select>
                </div>
                @if($type === 'select')
                    <div>
                        <label class="block text-sm text-erms-muted mb-1">ตัวเลือก (คั่นด้วย ,)</label>
                        <input type="text" wire:model="options" class="input-field" placeholder="เช่น ตัวเลือก 1, ตัวเลือก 2, ตัวเลือก 3">
                    </div>
                @endif
                <div class="flex items-center gap-2">
                    <input type="checkbox" wire:model="isRequired" id="isRequired" class="rounded border-erms-border text-erms-blue focus:ring-erms-blue/20">
                    <label for="isRequired" class="text-sm text-erms-muted">จำเป็นต้องกรอก</label>
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" wire:click="$set('showCreateModal', false)" class="btn-secondary">ยกเลิก</button>
                    <button type="submit" class="btn-primary">เพิ่มฟิลด์</button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
