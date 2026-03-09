<div>
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="font-heading font-bold text-lg">เทมเพลตงาน</h2>
            <p class="text-sm text-erms-muted">สร้างและใช้เทมเพลตเพื่อลดงานซ้ำ</p>
        </div>
        <button wire:click="$set('showCreateModal', true)" class="btn-primary">
            <i class="fa-solid fa-plus mr-1.5"></i>
            สร้างเทมเพลต
        </button>
    </div>

    {{-- Template List --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        @forelse($this->templates as $template)
            <div class="card p-5">
                <div class="flex items-start justify-between mb-3">
                    <div>
                        <h3 class="font-heading font-bold text-sm">{{ $template->name }}</h3>
                        @if($template->is_global)
                            <span class="inline-flex items-center px-1.5 py-0.5 text-[10px] font-medium rounded bg-erms-blue/10 text-erms-blue mt-1">ทั่วไป</span>
                        @endif
                    </div>
                    <div class="flex items-center gap-1">
                        <button wire:click="openUseModal({{ $template->id }})" class="text-erms-blue hover:text-erms-blue/80 cursor-pointer" title="ใช้เทมเพลต">
                            <i class="fa-solid fa-plus"></i>
                        </button>
                        <button wire:click="deleteTemplate({{ $template->id }})" wire:confirm="ลบเทมเพลตนี้?" class="text-erms-muted hover:text-erms-red cursor-pointer" title="ลบ">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>
                </div>
                @if($template->description)
                    <p class="text-xs text-erms-muted mb-3 line-clamp-2">{{ $template->description }}</p>
                @endif
                <div class="flex items-center gap-2 text-xs text-erms-muted">
                    <span>สร้างโดย {{ $template->creator->name }}</span>
                    <span>&bull;</span>
                    <span>{{ $template->created_at->diffForHumans() }}</span>
                </div>
                @if(!empty($template->task_data['subtasks']))
                    <div class="mt-2 text-xs text-erms-muted">
                        <span>{{ count($template->task_data['subtasks']) }} งานย่อย</span>
                    </div>
                @endif
            </div>
        @empty
            <div class="col-span-full card p-12 text-center text-erms-muted">
                <i class="fa-solid fa-file-lines text-5xl text-erms-muted/50 mb-3"></i>
                <p>ยังไม่มีเทมเพลต</p>
                <p class="text-xs mt-1">สร้างเทมเพลตเพื่อใช้สร้างงานซ้ำได้เร็วขึ้น</p>
            </div>
        @endforelse
    </div>

    {{-- Create Template Modal --}}
    @if($showCreateModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60" x-data x-transition>
        <div class="bg-erms-surface border border-erms-border rounded-xl w-full max-w-lg mx-4 shadow-2xl">
            <div class="px-6 py-4 border-b border-erms-border flex items-center justify-between">
                <h3 class="font-heading font-bold text-base">สร้างเทมเพลตใหม่</h3>
                <button wire:click="$set('showCreateModal', false)" class="text-erms-muted hover:text-erms-text cursor-pointer">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>
            <form wire:submit="createTemplate" class="p-6 space-y-4">
                <div>
                    <label class="block text-sm text-erms-muted mb-1">ชื่อเทมเพลต *</label>
                    <input type="text" wire:model="name" class="input-field" placeholder="เช่น เปิดโปรเจกต์ใหม่" required>
                    @error('name') <p class="text-xs text-erms-red mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm text-erms-muted mb-1">รายละเอียด</label>
                    <textarea wire:model="description" class="input-field" rows="3" placeholder="รายละเอียดเทมเพลต..."></textarea>
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" wire:model="isGlobal" id="isGlobal" class="rounded border-erms-border text-erms-blue focus:ring-erms-blue/20">
                    <label for="isGlobal" class="text-sm text-erms-muted">แชร์ให้ทุกคนใช้ได้</label>
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" wire:click="$set('showCreateModal', false)" class="btn-secondary">ยกเลิก</button>
                    <button type="submit" class="btn-primary">สร้างเทมเพลต</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- Use Template Modal --}}
    @if($showUseModal && $selectedTemplate)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60" x-data x-transition>
        <div class="bg-erms-surface border border-erms-border rounded-xl w-full max-w-lg mx-4 shadow-2xl">
            <div class="px-6 py-4 border-b border-erms-border flex items-center justify-between">
                <h3 class="font-heading font-bold text-base">ใช้เทมเพลต: {{ $selectedTemplate->name }}</h3>
                <button wire:click="$set('showUseModal', false)" class="text-erms-muted hover:text-erms-text cursor-pointer">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>
            <form wire:submit="useTemplate" class="p-6 space-y-4">
                <div>
                    <label class="block text-sm text-erms-muted mb-1">โครงการ *</label>
                    <select wire:model="useProjectId" class="input-field" required>
                        <option value="">เลือกโครงการ</option>
                        @foreach($this->projects as $project)
                            <option value="{{ $project->id }}">{{ $project->name }}</option>
                        @endforeach
                    </select>
                    @error('useProjectId') <p class="text-xs text-erms-red mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm text-erms-muted mb-1">มอบหมายให้</label>
                    <select wire:model="useAssignedTo" class="input-field">
                        <option value="">ไม่ระบุ</option>
                        @foreach($this->users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                @if(!empty($selectedTemplate->task_data['subtasks']))
                    <div class="bg-erms-surface-2 rounded-lg p-3">
                        <p class="text-xs text-erms-muted mb-2">จะสร้างงานย่อยดังนี้:</p>
                        <ul class="space-y-1">
                            @foreach($selectedTemplate->task_data['subtasks'] as $sub)
                                <li class="text-xs flex items-center gap-2">
                                    <i class="fa-solid fa-chevron-right text-erms-muted text-xs"></i>
                                    {{ $sub['title'] }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" wire:click="$set('showUseModal', false)" class="btn-secondary">ยกเลิก</button>
                    <button type="submit" class="btn-primary">สร้างงานจากเทมเพลต</button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
