<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? config('app.name', 'ERMS') }}</title>

        <!-- Fonts: Inter + Noto Sans Thai -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Noto+Sans+Thai:wght@300;400;500;600;700&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="font-sans antialiased bg-erms-bg text-erms-text" x-data="appShell()" @keydown.window="handleKeyboardShortcut($event)">
        <div class="min-h-screen flex">
            {{-- Mobile Sidebar Overlay --}}
            <div x-show="sidebarOpen"
                 x-transition:enter="transition-opacity ease-out duration-200"
                 x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="transition-opacity ease-in duration-150"
                 x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 class="fixed inset-0 z-40 bg-black/40 lg:hidden"
                 @click="sidebarOpen = false" x-cloak></div>

            {{-- ═══════ SIDEBAR (Asana Dark) ═══════ --}}
            <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
                   class="fixed inset-y-0 left-0 z-50 w-[240px] bg-erms-sidebar-bg flex flex-col transition-transform duration-200 ease-asana lg:translate-x-0 lg:static lg:z-auto">

                {{-- Workspace Header --}}
                <div class="flex items-center gap-2.5 px-4 h-[52px] flex-shrink-0">
                    <div class="w-[26px] h-[26px] rounded-md bg-gradient-to-br from-erms-blue to-erms-purple flex items-center justify-center flex-shrink-0">
                        <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    </div>
                    <span class="font-semibold text-[15px] text-white tracking-tight">ERMS</span>
                </div>

                {{-- Navigation --}}
                <nav class="flex-1 px-3 py-2 space-y-0.5 overflow-y-auto">
                    {{-- Main Navigation --}}
                    <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <svg class="sidebar-icon" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        <span>หน้าหลัก</span>
                    </a>
                    <a href="{{ route('tasks.index') }}" class="sidebar-link {{ request()->routeIs('tasks.*') ? 'active' : '' }}">
                        <svg class="sidebar-icon" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>งานของฉัน</span>
                    </a>
                    <a href="{{ route('calendar') }}" class="sidebar-link {{ request()->routeIs('calendar') ? 'active' : '' }}">
                        <svg class="sidebar-icon" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span>ปฏิทิน</span>
                    </a>
                    <a href="{{ route('reports.index') }}" class="sidebar-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                        <svg class="sidebar-icon" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        <span>รายงาน</span>
                    </a>

                    {{-- Divider --}}
                    <div class="!my-3 border-t border-white/10"></div>

                    {{-- Projects Section --}}
                    <div class="px-3 mb-1.5 flex items-center justify-between">
                        <span class="text-[11px] font-semibold text-erms-sidebar-text uppercase tracking-widest">โครงการ</span>
                        @can('manage-all-projects')
                        <a href="{{ route('projects.create') }}" class="text-erms-sidebar-text hover:text-white transition" aria-label="สร้างโครงการ">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        </a>
                        @endcan
                    </div>
                    @php
                        $sidebarProjects = auth()->user()->hasRole('admin')
                            ? \App\Models\Project::select('id', 'name', 'status')->latest()->take(8)->get()
                            : auth()->user()->projects()->select('projects.id', 'projects.name', 'projects.status')->latest('projects.created_at')->take(8)->get();
                        $projectColors = ['planning' => 'bg-erms-yellow', 'in_progress' => 'bg-erms-blue', 'done' => 'bg-erms-green'];
                    @endphp
                    @foreach($sidebarProjects as $proj)
                        <a href="{{ route('projects.show', $proj) }}" class="sidebar-link {{ request()->is('projects/'.$proj->id.'*') ? 'active' : '' }}">
                            <span class="w-2.5 h-2.5 rounded-full {{ $projectColors[$proj->status] ?? 'bg-erms-muted' }} flex-shrink-0 opacity-80"></span>
                            <span class="truncate">{{ $proj->name }}</span>
                        </a>
                    @endforeach
                    <a href="{{ route('projects.index') }}" class="sidebar-link opacity-60 hover:opacity-100">
                        <svg class="sidebar-icon" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        <span>ดูโครงการทั้งหมด</span>
                    </a>

                    {{-- Tools Section --}}
                    <div class="!my-3 border-t border-white/10"></div>
                    <a href="{{ route('templates.index') }}" class="sidebar-link {{ request()->routeIs('templates.*') ? 'active' : '' }}">
                        <svg class="sidebar-icon" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>เทมเพลต</span>
                    </a>

                    @role('admin')
                    <div class="!my-3 border-t border-white/10"></div>
                    <div class="px-3 mb-1.5">
                        <span class="text-[11px] font-semibold text-erms-sidebar-text uppercase tracking-widest">จัดการระบบ</span>
                    </div>
                    <a href="{{ route('admin.users.index') }}" class="sidebar-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                        <svg class="sidebar-icon" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        <span>ผู้ใช้งาน</span>
                    </a>
                    <a href="{{ route('admin.departments.index') }}" class="sidebar-link {{ request()->routeIs('admin.departments.*') ? 'active' : '' }}">
                        <svg class="sidebar-icon" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        <span>แผนก</span>
                    </a>
                    <a href="{{ route('admin.audit-log') }}" class="sidebar-link {{ request()->routeIs('admin.audit-log') ? 'active' : '' }}">
                        <svg class="sidebar-icon" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>บันทึกการใช้งาน</span>
                    </a>
                    @endrole
                </nav>

                {{-- User Section --}}
                <div class="px-3 py-3 border-t border-white/10 flex-shrink-0">
                    <a href="{{ route('profile') }}" class="flex items-center gap-2.5 px-2 py-1.5 rounded-md hover:bg-erms-sidebar-hover transition group">
                        <img src="{{ auth()->user()->avatar_url }}" alt="" class="w-7 h-7 rounded-full ring-2 ring-white/10">
                        <div class="flex-1 min-w-0">
                            <p class="text-[13px] font-medium text-white truncate">{{ auth()->user()->name }}</p>
                            @php $roleLabels = ['admin' => 'ผู้ดูแลระบบ', 'manager' => 'ผู้จัดการ', 'employee' => 'พนักงาน']; @endphp
                            <p class="text-[11px] text-erms-sidebar-text truncate">{{ $roleLabels[auth()->user()->roles->first()?->name] ?? auth()->user()->roles->first()?->name ?? 'ผู้ใช้' }}</p>
                        </div>
                        <svg class="w-4 h-4 text-erms-sidebar-text group-hover:text-white transition opacity-0 group-hover:opacity-100" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </a>
                </div>
            </aside>

            {{-- ═══════ MAIN CONTENT AREA ═══════ --}}
            <div class="flex-1 flex flex-col min-h-screen min-w-0">
                {{-- Top Bar (Asana-style) --}}
                <header class="sticky top-0 z-30 bg-white border-b border-erms-border flex-shrink-0">
                    <div class="flex items-center justify-between h-[52px] px-4 lg:px-6">
                        <div class="flex items-center gap-3 flex-1 min-w-0">
                            {{-- Mobile Menu --}}
                            <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden btn-icon flex-shrink-0" aria-label="เปิด/ปิดเมนู">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                            </button>

                            {{-- Page Title --}}
                            @if (isset($header))
                                <h1 class="font-semibold text-base text-erms-text truncate">{{ $header }}</h1>
                            @endif
                        </div>

                        {{-- Right Actions --}}
                        <div class="flex items-center gap-1">
                            {{-- Search --}}
                            <button @click="$dispatch('open-search')" class="search-bar w-[200px] hidden sm:flex" title="ค้นหา (Ctrl+K)">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                <span class="text-[13px]">ค้นหา...</span>
                                <kbd class="ml-auto text-[10px] text-erms-muted bg-white border border-erms-border rounded px-1 py-0.5 font-mono">⌘K</kbd>
                            </button>

                            {{-- Quick Add Button --}}
                            <button class="btn-primary !py-1.5 !px-3 !text-[13px] hidden sm:inline-flex" onclick="Livewire.dispatch('open-quick-create')">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                                <span>สร้าง</span>
                            </button>

                            <div class="w-px h-5 bg-erms-border mx-1 hidden sm:block"></div>

                            {{-- Notifications --}}
                            <livewire:notification-bell />

                            {{-- User Avatar (quick menu) --}}
                            <div x-data="{ open: false }" class="relative">
                                <button @click="open = !open" class="btn-icon" aria-label="เมนูผู้ใช้">
                                    <img src="{{ auth()->user()->avatar_url }}" alt="" class="w-7 h-7 rounded-full">
                                </button>
                                <div x-show="open" @click.away="open = false" x-transition
                                     class="absolute right-0 top-full mt-1 w-48 bg-white rounded-lg border border-erms-border shadow-asana-lg py-1 z-50" x-cloak>
                                    <div class="px-3 py-2 border-b border-erms-border-light">
                                        <p class="text-sm font-medium text-erms-text truncate">{{ auth()->user()->name }}</p>
                                        <p class="text-xs text-erms-muted truncate">{{ auth()->user()->email }}</p>
                                    </div>
                                    <a href="{{ route('profile') }}" class="flex items-center gap-2 px-3 py-2 text-sm text-erms-text-secondary hover:bg-erms-surface-2 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                        โปรไฟล์
                                    </a>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="flex items-center gap-2 px-3 py-2 text-sm text-erms-red hover:bg-erms-red-light transition w-full text-left cursor-pointer">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                            ออกจากระบบ
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </header>

                {{-- Page Content --}}
                <main class="flex-1 overflow-y-auto">
                    <div class="p-4 lg:p-6 animate-fade-in">
                        {{ $slot }}
                    </div>
                </main>
            </div>

            {{-- ═══════ TASK DETAIL SLIDE-IN PANEL ═══════ --}}
            <div x-data="{ open: false, taskId: null }"
                 @open-task-panel.window="taskId = $event.detail.taskId; open = true"
                 @close-task-panel.window="open = false"
                 @keydown.escape.window="if(open) { open = false }" x-cloak>
                {{-- Overlay --}}
                <div x-show="open"
                     x-transition:enter="transition-opacity duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                     x-transition:leave="transition-opacity duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                     class="fixed inset-0 z-40 bg-black/20" @click="open = false"></div>
                {{-- Panel --}}
                <div x-show="open"
                     x-transition:enter="transition-transform duration-250 ease-out" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
                     x-transition:leave="transition-transform duration-200 ease-in" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full"
                     class="slide-panel" >
                    <div class="sticky top-0 bg-white z-10 px-5 py-3 border-b border-erms-border flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <button @click="open = false" class="btn-icon" aria-label="ปิด">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                            <span class="text-sm text-erms-muted">รายละเอียดงาน</span>
                        </div>
                        <a :href="'/tasks/' + taskId" class="btn-ghost !text-xs" aria-label="เปิดเต็มหน้า">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            เปิดเต็มหน้า
                        </a>
                    </div>
                    <div class="p-5">
                        <p class="text-[13px] text-erms-muted text-center py-8">
                            <a :href="'/tasks/' + taskId" class="btn-primary" x-show="taskId">เปิดรายละเอียดงาน</a>
                        </p>
                    </div>
                </div>
            </div>

            {{-- ═══════ GLOBAL SEARCH ═══════ --}}
            <livewire:global-search />

            {{-- ═══════ QUICK CREATE TASK MODAL ═══════ --}}
            <livewire:quick-create-task />

            {{-- ═══════ QUICK ADD FAB (Mobile) ═══════ --}}
            <button class="quick-add-fab sm:hidden" onclick="Livewire.dispatch('open-quick-create')" aria-label="สร้างงานใหม่">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            </button>
        </div>

        @livewireScripts

        <script>
            function appShell() {
                return {
                    sidebarOpen: false,
                    handleKeyboardShortcut(e) {
                        // Ctrl/Cmd + K = Search
                        if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
                            e.preventDefault();
                            this.$dispatch('open-search');
                        }
                        // Q = Quick create (when not in input)
                        if (e.key === 'q' && !['INPUT', 'TEXTAREA', 'SELECT'].includes(e.target.tagName) && !e.target.isContentEditable) {
                            e.preventDefault();
                            Livewire.dispatch('open-quick-create');
                        }
                    }
                }
            }

            // @Mention autocomplete for comments
            function commentMention() {
                return {
                    showSuggestions: false,
                    suggestions: [],
                    highlightedIndex: 0,
                    allUsers: @json(\App\Models\User::select('id', 'name', 'avatar')->get()->map(fn($u) => ['id' => $u->id, 'name' => $u->name, 'avatar' => $u->avatar_url])),

                    onInput(e) {
                        const val = e.target.value;
                        const cursorPos = e.target.selectionStart;
                        const textBeforeCursor = val.substring(0, cursorPos);
                        const mentionMatch = textBeforeCursor.match(/@(\S*)$/);

                        if (mentionMatch) {
                            const search = mentionMatch[1].toLowerCase();
                            this.suggestions = this.allUsers.filter(u =>
                                u.name.toLowerCase().includes(search)
                            ).slice(0, 5);
                            this.showSuggestions = this.suggestions.length > 0;
                            this.highlightedIndex = 0;
                        } else {
                            this.showSuggestions = false;
                        }
                    },

                    selectUser(user) {
                        const input = this.$refs.commentInput;
                        const val = input.value;
                        const cursorPos = input.selectionStart;
                        const textBeforeCursor = val.substring(0, cursorPos);
                        const mentionStart = textBeforeCursor.lastIndexOf('@');
                        const newVal = val.substring(0, mentionStart) + '@' + user.name + ' ' + val.substring(cursorPos);
                        input.value = newVal;
                        input.dispatchEvent(new Event('input', { bubbles: true }));
                        this.showSuggestions = false;
                        input.focus();
                        const newPos = mentionStart + user.name.length + 2;
                        input.setSelectionRange(newPos, newPos);
                    },

                    selectSuggestion() {
                        if (this.suggestions[this.highlightedIndex]) {
                            this.selectUser(this.suggestions[this.highlightedIndex]);
                        }
                    },

                    highlightNext() {
                        this.highlightedIndex = (this.highlightedIndex + 1) % this.suggestions.length;
                    },

                    highlightPrev() {
                        this.highlightedIndex = (this.highlightedIndex - 1 + this.suggestions.length) % this.suggestions.length;
                    }
                }
            }
        </script>
    </body>
</html>
