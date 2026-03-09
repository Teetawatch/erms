<div>
    <div class="flex items-center justify-between mb-4">
        <h3 class="font-heading font-bold text-base">ฟิลด์กำหนดเอง</h3>
        <button wire:click="$set('showCreateModal', true)" class="btn-primary text-xs">
            <i class="fa-solid fa-plus mr-1"></i>
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
                            <i class="fa-solid fa-font text-erms-blue"></i>
                            @break
                        @case('number')
                            <i class="fa-solid fa-hashtag text-erms-blue"></i>
                            @break
                        @case('date')
                            <i class="fa-solid fa-calendar text-erms-blue"></i>
                            @break
                        @case('select')
                            <i class="fa-solid fa-list text-erms-blue"></i>
                            @break
                        @case('checkbox')
                            <i class="fa-solid fa-square-check text-erms-blue"></i>
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
                    <i class="fa-solid fa-trash"></i>
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
                    <i class="fa-solid fa-xmark text-lg"></i>
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
