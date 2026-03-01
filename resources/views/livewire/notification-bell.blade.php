<div class="relative" wire:poll.15s x-data="{ open: false }">
    <button @click="open = !open" class="relative text-erms-muted hover:text-erms-text transition" aria-label="การแจ้งเตือน">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
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
                        'task_assigned' => ['icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z', 'color' => 'text-erms-blue'],
                        'new_comment' => ['icon' => 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z', 'color' => 'text-erms-purple'],
                        'mentioned_in_comment' => ['icon' => 'M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207', 'color' => 'text-erms-orange'],
                        'task_status_changed' => ['icon' => 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15', 'color' => 'text-erms-green'],
                        'deadline_reminder' => ['icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', 'color' => 'text-erms-red'],
                    ];
                    $iconData = $iconMap[$type] ?? ['icon' => 'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9', 'color' => 'text-erms-muted'];
                @endphp
                <button wire:click="markAndNavigate('{{ $notification->id }}', '{{ $taskId ? route('tasks.show', $taskId) : route('dashboard') }}')"
                   class="flex items-start gap-3 px-4 py-3 border-b border-erms-border-light/50 hover:bg-erms-surface-2 transition w-full text-left {{ is_null($notification->read_at) ? 'bg-erms-blue/5' : '' }}">
                    <div class="flex-shrink-0 mt-0.5">
                        <svg class="w-4 h-4 {{ $iconData['color'] }}" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $iconData['icon'] }}"/></svg>
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
                    <svg class="w-8 h-8 mx-auto text-erms-muted/40 mb-2" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    <p class="text-[13px] text-erms-muted">ไม่มีการแจ้งเตือน</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
