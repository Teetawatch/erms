<div>
    @if($showModal)
        {{-- Overlay --}}
        <div class="modal-overlay" wire:click.self="$set('showModal', false)" x-data x-trap.noscroll="true" @keydown.escape.window="$wire.set('showModal', false)">
            {{-- Modal --}}
            <div class="modal-content max-w-md" @click.stop>
                {{-- Header --}}
                <div class="flex items-center justify-between px-5 py-3.5 border-b border-erms-border-light">
                    <h3 class="text-[15px] font-semibold text-erms-text">สร้างงานใหม่</h3>
                    <button wire:click="$set('showModal', false)" class="btn-icon" aria-label="ปิด">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                {{-- Form --}}
                <form wire:submit="createTask" class="p-5 space-y-3.5">
                    {{-- Title --}}
                    <div>
                        <input type="text" wire:model="title" placeholder="ชื่องาน" autofocus
                               class="input-field !text-[15px] !font-medium !py-2.5" />
                        @error('title') <p class="text-2xs text-erms-red mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Description (collapsible) --}}
                    <div x-data="{ showDesc: false }">
                        <button type="button" @click="showDesc = !showDesc" class="text-2xs text-erms-text-secondary hover:text-erms-text transition flex items-center gap-1">
                            <i class="fa-solid fa-align-left"></i>
                            เพิ่มรายละเอียด
                        </button>
                        <div x-show="showDesc" x-collapse>
                            <textarea wire:model="description" rows="2" placeholder="รายละเอียด (ไม่บังคับ)"
                                      class="input-field !text-[13px] mt-2"></textarea>
                        </div>
                    </div>

                    {{-- Project & Assignee --}}
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="text-2xs font-medium text-erms-text-secondary mb-1 block">โครงการ</label>
                            <select wire:model="projectId" class="input-field !text-[13px] !py-1.5">
                                <option value="">เลือกโครงการ</option>
                                @foreach($this->projects as $project)
                                    <option value="{{ $project->id }}">{{ $project->name }}</option>
                                @endforeach
                            </select>
                            @error('projectId') <p class="text-2xs text-erms-red mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="text-2xs font-medium text-erms-text-secondary mb-1 block">มอบหมาย</label>
                            <select wire:model="assignedTo" class="input-field !text-[13px] !py-1.5">
                                <option value="">ไม่ระบุ</option>
                                @foreach($this->users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Priority & Due Date --}}
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="text-2xs font-medium text-erms-text-secondary mb-1 block">ความสำคัญ</label>
                            <select wire:model="priority" class="input-field !text-[13px] !py-1.5">
                                <option value="low">ต่ำ</option>
                                <option value="medium">ปานกลาง</option>
                                <option value="high">สูง</option>
                                <option value="urgent">เร่งด่วน</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-2xs font-medium text-erms-text-secondary mb-1 block">กำหนดส่ง</label>
                            <input type="date" wire:model="dueDate" class="input-field !text-[13px] !py-1.5" />
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center justify-end gap-2 pt-2">
                        <button type="button" wire:click="$set('showModal', false)" class="btn-secondary !text-[13px] !py-1.5">ยกเลิก</button>
                        <button type="submit" class="btn-primary !text-[13px] !py-1.5" wire:loading.attr="disabled">
                            <i wire:loading wire:target="createTask" class="fa-solid fa-spinner fa-spin"></i>
                            <span wire:loading.remove wire:target="createTask">สร้างงาน</span>
                            <span wire:loading wire:target="createTask">กำลังสร้าง...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
