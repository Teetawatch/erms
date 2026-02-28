<div class="relative" wire:poll.15s x-data="{ open: false }">
    <button @click="open = !open" class="relative text-erms-muted hover:text-erms-text transition" aria-label="การแจ้งเตือน">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
        @if($this->unreadCount > 0)
            <span class="absolute -top-1 -right-1 w-4 h-4 text-[10px] font-bold text-white bg-erms-red rounded-full flex items-center justify-center">
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
         class="absolute right-0 mt-2 w-80 bg-white border border-erms-border rounded-xl shadow-lg overflow-hidden z-50">
        <div class="flex items-center justify-between px-4 py-3 border-b border-erms-border">
            <h3 class="text-sm font-medium text-erms-text">การแจ้งเตือน</h3>
            @if($this->unreadCount > 0)
                <button wire:click="markAllAsRead" class="text-xs text-erms-blue hover:underline">อ่านทั้งหมด</button>
            @endif
        </div>
        <div class="max-h-64 overflow-y-auto">
            @forelse($this->notifications as $notification)
                <div wire:click="markAsRead('{{ $notification->id }}')"
                     class="px-4 py-3 border-b border-erms-border/50 cursor-pointer hover:bg-erms-surface-2 transition {{ is_null($notification->read_at) ? 'bg-erms-blue/5' : '' }}">
                    <p class="text-sm text-erms-text">{{ $notification->data['message'] ?? 'แจ้งเตือนใหม่' }}</p>
                    <p class="text-xs text-erms-muted mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                </div>
            @empty
                <div class="px-4 py-8 text-center text-erms-muted text-sm">
                    ไม่มีการแจ้งเตือน
                </div>
            @endforelse
        </div>
    </div>
</div>
