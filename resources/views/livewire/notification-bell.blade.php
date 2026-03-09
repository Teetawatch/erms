<div class="relative" wire:poll.15s x-data="{ open: false }">
    <button @click="open = !open" class="relative text-erms-muted hover:text-erms-text transition" aria-label="การแจ้งเตือน">
        <i class="fa-solid fa-bell text-lg"></i>
        @if($this->unreadCount > 0)
            <span class="absolute -top-1 -right-1 w-4 h-4 text-[10px] font-bold text-white bg-erms-red rounded-full flex items-center justify-center animate-pulse">
                {{ $this->unreadCount > 9 ? '9+' : $this->unreadCount }}
            </span>
        @endif
    </button>

    <div x-show="open" @click.away="open = false"
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="absolute right-0 mt-2 w-80 bg-white border border-erms-border rounded-xl shadow-asana-lg overflow-hidden z-50">
        <div class="flex items-center justify-between px-4 py-3 border-b border-erms-border-light">
            <h3 class="text-[13px] font-semibold text-erms-text">การแจ้งเตือน</h3>
            @if($this->unreadCount > 0)
                <button wire:click="markAllAsRead" class="text-2xs text-erms-blue hover:underline font-medium cursor-pointer">อ่านทั้งหมด</button>
            @endif
        </div>
        <div class="max-h-80 overflow-y-auto">
            @forelse($this->notifications as $notification)
                @php
                    $taskId = $notification->data['task_id'] ?? null;
                    $type = $notification->data['type'] ?? '';
                    $iconMap = [
                        'task_assigned' => ['icon' => 'fa-user', 'color' => 'text-erms-blue'],
                        'new_comment' => ['icon' => 'fa-comment', 'color' => 'text-erms-purple'],
                        'mentioned_in_comment' => ['icon' => 'fa-at', 'color' => 'text-erms-orange'],
                        'task_status_changed' => ['icon' => 'fa-arrows-rotate', 'color' => 'text-erms-green'],
                        'deadline_reminder' => ['icon' => 'fa-clock', 'color' => 'text-erms-red'],
                    ];
                    $iconData = $iconMap[$type] ?? ['icon' => 'fa-bell', 'color' => 'text-erms-muted'];
                @endphp
                <button wire:click="markAndNavigate('{{ $notification->id }}', '{{ $taskId ? route('tasks.show', $taskId) : route('dashboard') }}')"
                   class="flex items-start gap-3 px-4 py-3 border-b border-erms-border-light/50 hover:bg-erms-surface-2 transition w-full text-left {{ is_null($notification->read_at) ? 'bg-erms-blue/5' : '' }}">
                    <div class="flex-shrink-0 mt-0.5">
                        <i class="fa-solid {{ $iconData['icon'] }} {{ $iconData['color'] }}"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-[13px] text-erms-text leading-snug {{ is_null($notification->read_at) ? 'font-medium' : '' }}">{{ $notification->data['message'] ?? 'แจ้งเตือนใหม่' }}</p>
                        <p class="text-2xs text-erms-muted mt-0.5">{{ $notification->created_at->diffForHumans() }}</p>
                    </div>
                    @if(is_null($notification->read_at))
                        <span class="w-2 h-2 rounded-full bg-erms-blue flex-shrink-0 mt-1.5"></span>
                    @endif
                </button>
            @empty
                <div class="px-4 py-10 text-center">
                    <i class="fa-solid fa-bell text-3xl text-erms-muted/40 mb-2"></i>
                    <p class="text-[13px] text-erms-muted">ไม่มีการแจ้งเตือน</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
