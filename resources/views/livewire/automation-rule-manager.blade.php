<div>
    <div class="flex items-center justify-between mb-4">
        <h3 class="font-heading font-bold text-base">กฎอัตโนมัติ</h3>
        <button wire:click="$set('showCreateModal', true)" class="btn-primary text-xs">
            <i class="fa-solid fa-plus mr-1"></i>
            เพิ่มกฎ
        </button>
    </div>

    {{-- Rules List --}}
    <div class="space-y-3">
        @forelse($this->rules as $rule)
            <div class="card p-4 flex items-center gap-4">
                <button wire:click="toggleActive({{ $rule->id }})" class="flex-shrink-0 cursor-pointer">
                    @if($rule->is_active)
                        <div class="w-10 h-6 bg-erms-green rounded-full relative transition">
                            <div class="absolute right-0.5 top-0.5 w-5 h-5 bg-white rounded-full shadow"></div>
                        </div>
                    @else
                        <div class="w-10 h-6 bg-erms-muted/30 rounded-full relative transition">
                            <div class="absolute left-0.5 top-0.5 w-5 h-5 bg-white rounded-full shadow"></div>
                        </div>
                    @endif
                </button>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium {{ !$rule->is_active ? 'text-erms-muted' : '' }}">{{ $rule->name }}</p>
                    <div class="flex items-center gap-2 mt-1 text-xs text-erms-muted">
                        @php
                            $triggerLabels = [
                                'status_changed' => 'เมื่อสถานะเปลี่ยน',
                                'due_date_reached' => 'เมื่อถึงกำหนด',
                                'task_created' => 'เมื่อสร้างงาน',
                                'task_assigned' => 'เมื่อมอบหมายงาน',
                            ];
                            $actionLabels = [
                                'change_status' => 'เปลี่ยนสถานะ',
                                'assign_user' => 'มอบหมายให้',
                                'send_notification' => 'ส่งแจ้งเตือน',
                                'set_priority' => 'ตั้งความสำคัญ',
                            ];
                        @endphp
                        <span class="inline-flex items-center px-1.5 py-0.5 rounded bg-erms-blue/10 text-erms-blue text-[10px]">{{ $triggerLabels[$rule->trigger_type] ?? $rule->trigger_type }}</span>
                        <i class="fa-solid fa-arrow-right text-xs"></i>
                        <span class="inline-flex items-center px-1.5 py-0.5 rounded bg-erms-green/10 text-erms-green text-[10px]">{{ $actionLabels[$rule->action_type] ?? $rule->action_type }}</span>
                    </div>
                </div>
                <button wire:click="deleteRule({{ $rule->id }})" wire:confirm="ลบกฎนี้?" class="text-erms-muted hover:text-erms-red cursor-pointer transition">
                    <i class="fa-solid fa-trash"></i>
                </button>
            </div>
        @empty
            <div class="card p-8 text-center text-erms-muted text-sm">
                <i class="fa-solid fa-bolt text-4xl text-erms-muted/50 mb-2"></i>
                ยังไม่มีกฎอัตโนมัติ
            </div>
        @endforelse
    </div>

    {{-- Create Rule Modal --}}
    @if($showCreateModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60" x-data x-transition>
        <div class="bg-erms-surface border border-erms-border rounded-xl w-full max-w-lg mx-4 shadow-2xl">
            <div class="px-6 py-4 border-b border-erms-border flex items-center justify-between">
                <h3 class="font-heading font-bold text-base">เพิ่มกฎอัตโนมัติ</h3>
                <button wire:click="$set('showCreateModal', false)" class="text-erms-muted hover:text-erms-text cursor-pointer">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>
            <form wire:submit="createRule" class="p-6 space-y-4">
                <div>
                    <label class="block text-sm text-erms-muted mb-1">ชื่อกฎ *</label>
                    <input type="text" wire:model="name" class="input-field" placeholder="เช่น ย้ายไป Done เมื่อ Review เสร็จ" required>
                    @error('name') <p class="text-xs text-erms-red mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm text-erms-muted mb-1">เงื่อนไข (Trigger)</label>
                        <select wire:model.live="triggerType" class="input-field">
                            <option value="status_changed">เมื่อสถานะเปลี่ยน</option>
                            <option value="due_date_reached">เมื่อถึงกำหนด</option>
                            <option value="task_created">เมื่อสร้างงาน</option>
                            <option value="task_assigned">เมื่อมอบหมายงาน</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm text-erms-muted mb-1">การกระทำ (Action)</label>
                        <select wire:model.live="actionType" class="input-field">
                            <option value="change_status">เปลี่ยนสถานะ</option>
                            <option value="assign_user">มอบหมายให้</option>
                            <option value="send_notification">ส่งแจ้งเตือน</option>
                            <option value="set_priority">ตั้งความสำคัญ</option>
                        </select>
                    </div>
                </div>

                @if($triggerType === 'status_changed')
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm text-erms-muted mb-1">จากสถานะ</label>
                            <select wire:model="triggerConditionFrom" class="input-field">
                                <option value="">ทุกสถานะ</option>
                                <option value="todo">รอดำเนินการ</option>
                                <option value="in_progress">กำลังดำเนินการ</option>
                                <option value="review">ตรวจสอบ</option>
                                <option value="done">เสร็จสิ้น</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm text-erms-muted mb-1">เป็นสถานะ</label>
                            <select wire:model="triggerConditionTo" class="input-field">
                                <option value="">ทุกสถานะ</option>
                                <option value="todo">รอดำเนินการ</option>
                                <option value="in_progress">กำลังดำเนินการ</option>
                                <option value="review">ตรวจสอบ</option>
                                <option value="done">เสร็จสิ้น</option>
                            </select>
                        </div>
                    </div>
                @endif

                @if($actionType === 'change_status')
                    <div>
                        <label class="block text-sm text-erms-muted mb-1">เปลี่ยนเป็นสถานะ</label>
                        <select wire:model="actionValue" class="input-field">
                            <option value="todo">รอดำเนินการ</option>
                            <option value="in_progress">กำลังดำเนินการ</option>
                            <option value="review">ตรวจสอบ</option>
                            <option value="done">เสร็จสิ้น</option>
                        </select>
                    </div>
                @elseif($actionType === 'set_priority')
                    <div>
                        <label class="block text-sm text-erms-muted mb-1">ตั้งความสำคัญเป็น</label>
                        <select wire:model="actionValue" class="input-field">
                            <option value="low">ต่ำ</option>
                            <option value="medium">ปานกลาง</option>
                            <option value="high">สูง</option>
                            <option value="urgent">เร่งด่วน</option>
                        </select>
                    </div>
                @elseif($actionType === 'send_notification')
                    <div>
                        <label class="block text-sm text-erms-muted mb-1">ข้อความแจ้งเตือน</label>
                        <input type="text" wire:model="actionValue" class="input-field" placeholder="เช่น งานถูกเปลี่ยนสถานะ">
                    </div>
                @endif

                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" wire:click="$set('showCreateModal', false)" class="btn-secondary">ยกเลิก</button>
                    <button type="submit" class="btn-primary">สร้างกฎ</button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
