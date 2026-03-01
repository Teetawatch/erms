<div>
    @if($showModal)
        <div class="modal-overlay" x-data x-trap.noscroll="true" @click.self="$wire.set('showModal', false)" @keydown.escape.window="$wire.set('showModal', false)">
            <div class="w-full max-w-lg mx-auto mt-[15vh]" @click.stop>
                {{-- Search Input --}}
                <div class="bg-white rounded-xl shadow-asana-lg border border-erms-border overflow-hidden">
                    <div class="flex items-center gap-3 px-4 py-3 border-b border-erms-border-light">
                        <svg class="w-5 h-5 text-erms-muted flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <input type="text" wire:model.live.debounce.300ms="query" autofocus
                               class="flex-1 text-[15px] text-erms-text border-0 outline-none bg-transparent placeholder:text-erms-muted"
                               placeholder="ค้นหางาน, โครงการ, คน...">
                        <kbd class="text-[10px] text-erms-muted bg-erms-surface-2 border border-erms-border rounded px-1.5 py-0.5 font-mono">ESC</kbd>
                    </div>

                    {{-- Results --}}
                    <div class="max-h-[50vh] overflow-y-auto">
                        @if(strlen($query) >= 2)
                            @if(count($results) > 0)
                                @php
                                    $grouped = collect($results)->groupBy('type');
                                    $typeLabels = ['task' => 'งาน', 'project' => 'โครงการ', 'user' => 'ผู้ใช้'];
                                    $typeIcons = [
                                        'task' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
                                        'project' => 'M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z',
                                        'user' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
                                    ];
                                    $statusColors = ['todo' => '#9ca0a4', 'in_progress' => '#4573d2', 'review' => '#7c5cfc', 'done' => '#5da283', 'planning' => '#f8b400'];
                                @endphp
                                @foreach($grouped as $type => $items)
                                    <div class="px-3 pt-3 pb-1">
                                        <p class="text-[11px] font-semibold text-erms-muted uppercase tracking-wider px-1">{{ $typeLabels[$type] ?? $type }}</p>
                                    </div>
                                    @foreach($items as $item)
                                        <button wire:click="navigateTo('{{ $item['url'] }}')"
                                                class="flex items-center gap-3 w-full px-4 py-2.5 text-left hover:bg-erms-surface-2 transition cursor-pointer group">
                                            <div class="flex-shrink-0">
                                                @if($type === 'task' && isset($item['status']))
                                                    <span class="w-3 h-3 rounded-full block" style="background-color: {{ $statusColors[$item['status']] ?? '#9ca0a4' }}"></span>
                                                @elseif($type === 'project' && isset($item['status']))
                                                    <span class="w-3 h-3 rounded-full block" style="background-color: {{ $statusColors[$item['status']] ?? '#9ca0a4' }}"></span>
                                                @else
                                                    <svg class="w-4 h-4 text-erms-muted" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $typeIcons[$type] ?? $typeIcons['task'] }}"/></svg>
                                                @endif
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-[13px] font-medium text-erms-text truncate group-hover:text-erms-blue transition">{{ $item['title'] }}</p>
                                                @if(!empty($item['subtitle']))
                                                    <p class="text-2xs text-erms-muted truncate">{{ $item['subtitle'] }}</p>
                                                @endif
                                            </div>
                                            @if($type === 'task' && isset($item['priority']))
                                                <span class="badge-{{ $item['priority'] }} flex-shrink-0">{{ $item['priority'] }}</span>
                                            @endif
                                            <svg class="w-4 h-4 text-erms-muted opacity-0 group-hover:opacity-100 transition flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                                        </button>
                                    @endforeach
                                @endforeach
                            @else
                                <div class="px-4 py-8 text-center">
                                    <svg class="w-8 h-8 mx-auto text-erms-muted/40 mb-2" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                    <p class="text-[13px] text-erms-muted">ไม่พบผลลัพธ์สำหรับ "{{ $query }}"</p>
                                </div>
                            @endif
                        @else
                            <div class="px-4 py-6 text-center">
                                <p class="text-[13px] text-erms-muted">พิมพ์อย่างน้อย 2 ตัวอักษรเพื่อค้นหา</p>
                            </div>
                        @endif
                    </div>

                    {{-- Footer --}}
                    <div class="flex items-center justify-between px-4 py-2 border-t border-erms-border-light bg-erms-surface/50">
                        <div class="flex items-center gap-3 text-2xs text-erms-muted">
                            <span class="flex items-center gap-1"><kbd class="bg-erms-surface-2 border border-erms-border rounded px-1 py-0.5 font-mono text-[10px]">↑↓</kbd> เลื่อน</span>
                            <span class="flex items-center gap-1"><kbd class="bg-erms-surface-2 border border-erms-border rounded px-1 py-0.5 font-mono text-[10px]">↵</kbd> เปิด</span>
                        </div>
                        <span class="text-2xs text-erms-muted">ERMS Search</span>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
