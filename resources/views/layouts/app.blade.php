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

        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

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
                        <i class="fa-solid fa-clipboard-check text-white text-xs"></i>
                    </div>
                    <span class="font-semibold text-[15px] text-white tracking-tight">ERMS</span>
                </div>

                {{-- Navigation --}}
                <nav class="flex-1 px-3 py-2 space-y-0.5 overflow-y-auto">
                    {{-- Main Navigation --}}
                    <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="fa-solid fa-house sidebar-icon"></i>
                        <span>หน้าหลัก</span>
                    </a>
                    <a href="{{ route('tasks.index') }}" class="sidebar-link {{ request()->routeIs('tasks.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-circle-check sidebar-icon"></i>
                        <span>งานของฉัน</span>
                    </a>
                    <a href="{{ route('calendar') }}" class="sidebar-link {{ request()->routeIs('calendar') ? 'active' : '' }}">
                        <i class="fa-solid fa-calendar-days sidebar-icon"></i>
                        <span>ปฏิทิน</span>
                    </a>
                    <a href="{{ route('reports.index') }}" class="sidebar-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-chart-column sidebar-icon"></i>
                        <span>รายงาน</span>
                    </a>

                    {{-- Divider --}}
                    <div class="!my-3 border-t border-white/10"></div>

                    {{-- Projects Section --}}
                    <div class="px-3 mb-1.5 flex items-center justify-between">
                        <span class="text-[11px] font-semibold text-erms-sidebar-text uppercase tracking-widest">โครงการ</span>
                        @can('manage-all-projects')
                        <a href="{{ route('projects.create') }}" class="text-erms-sidebar-text hover:text-white transition" aria-label="สร้างโครงการ">
                            <i class="fa-solid fa-plus text-xs"></i>
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
                        <i class="fa-solid fa-folder-open sidebar-icon"></i>
                        <span>ดูโครงการทั้งหมด</span>
                    </a>

                    {{-- Tools Section --}}
                    <div class="!my-3 border-t border-white/10"></div>
                    <a href="{{ route('templates.index') }}" class="sidebar-link {{ request()->routeIs('templates.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-file-lines sidebar-icon"></i>
                        <span>เทมเพลต</span>
                    </a>

                    @role('admin')
                    <div class="!my-3 border-t border-white/10"></div>
                    <div class="px-3 mb-1.5">
                        <span class="text-[11px] font-semibold text-erms-sidebar-text uppercase tracking-widest">จัดการระบบ</span>
                    </div>
                    <a href="{{ route('admin.users.index') }}" class="sidebar-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-users sidebar-icon"></i>
                        <span>ผู้ใช้งาน</span>
                    </a>
                    <a href="{{ route('admin.departments.index') }}" class="sidebar-link {{ request()->routeIs('admin.departments.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-building sidebar-icon"></i>
                        <span>ฝ่าย</span>
                    </a>
                    <a href="{{ route('admin.audit-log') }}" class="sidebar-link {{ request()->routeIs('admin.audit-log') ? 'active' : '' }}">
                        <i class="fa-solid fa-clock-rotate-left sidebar-icon"></i>
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
                        <i class="fa-solid fa-gear text-erms-sidebar-text group-hover:text-white transition opacity-0 group-hover:opacity-100 text-sm"></i>
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
                                <i class="fa-solid fa-bars text-lg"></i>
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
                                <i class="fa-solid fa-magnifying-glass text-sm flex-shrink-0"></i>
                                <span class="text-[13px]">ค้นหา...</span>
                                <kbd class="ml-auto text-[10px] text-erms-muted bg-white border border-erms-border rounded px-1 py-0.5 font-mono">⌘K</kbd>
                            </button>

                            {{-- Quick Add Button --}}
                            <button class="btn-primary !py-1.5 !px-3 !text-[13px] hidden sm:inline-flex" onclick="Livewire.dispatch('open-quick-create')">
                                <i class="fa-solid fa-plus"></i>
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
                                        <i class="fa-solid fa-user w-4"></i>
                                        โปรไฟล์
                                    </a>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="flex items-center gap-2 px-3 py-2 text-sm text-erms-red hover:bg-erms-red-light transition w-full text-left cursor-pointer">
                                            <i class="fa-solid fa-right-from-bracket w-4"></i>
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
                                <i class="fa-solid fa-xmark text-lg"></i>
                            </button>
                            <span class="text-sm text-erms-muted">รายละเอียดงาน</span>
                        </div>
                        <a :href="'/tasks/' + taskId" class="btn-ghost !text-xs" aria-label="เปิดเต็มหน้า">
                            <i class="fa-solid fa-up-right-from-square"></i>
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
                <i class="fa-solid fa-plus text-xl"></i>
            </button>

            {{-- ═══════ TOAST NOTIFICATIONS ═══════ --}}
            <div x-data="toastManager()" @toast.window="addToast($event.detail)"
                 class="fixed bottom-5 right-5 z-[100] flex flex-col gap-2 pointer-events-none">
                <template x-for="toast in toasts" :key="toast.id">
                    <div x-show="toast.show"
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                         x-transition:leave="transition ease-in duration-200"
                         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                         x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                         class="pointer-events-auto flex items-center gap-3 px-4 py-3 rounded-xl shadow-asana-lg border max-w-sm"
                         :class="{
                             'bg-white border-erms-green/30': toast.type === 'success',
                             'bg-white border-erms-red/30': toast.type === 'error',
                             'bg-white border-erms-blue/30': toast.type === 'info',
                         }">
                        <div class="flex-shrink-0">
                            <template x-if="toast.type === 'success'">
                                <i class="fa-solid fa-circle-check text-erms-green text-lg"></i>
                            </template>
                            <template x-if="toast.type === 'error'">
                                <i class="fa-solid fa-circle-exclamation text-erms-red text-lg"></i>
                            </template>
                            <template x-if="toast.type === 'info'">
                                <i class="fa-solid fa-circle-info text-erms-blue text-lg"></i>
                            </template>
                        </div>
                        <p class="text-[13px] font-medium text-erms-text" x-text="toast.message"></p>
                        <button @click="removeToast(toast.id)" class="ml-auto text-erms-muted hover:text-erms-text transition flex-shrink-0">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                </template>
            </div>
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

            // Search keyboard navigation
            function searchKeyboard(urls) {
                return {
                    activeIndex: -1,
                    urls: urls,
                    moveDown() {
                        if (this.urls.length === 0) return;
                        this.activeIndex = (this.activeIndex + 1) % this.urls.length;
                        this.scrollToActive();
                    },
                    moveUp() {
                        if (this.urls.length === 0) return;
                        this.activeIndex = this.activeIndex <= 0 ? this.urls.length - 1 : this.activeIndex - 1;
                        this.scrollToActive();
                    },
                    go() {
                        if (this.activeIndex >= 0 && this.urls[this.activeIndex]) {
                            $wire.navigateTo(this.urls[this.activeIndex]);
                        }
                    },
                    scrollToActive() {
                        this.$nextTick(() => {
                            const el = this.$refs.resultsList?.querySelector(`[data-search-idx="${this.activeIndex}"]`);
                            if (el) el.scrollIntoView({ block: 'nearest' });
                        });
                    }
                }
            }

            // Toast notification manager
            function toastManager() {
                return {
                    toasts: [],
                    nextId: 0,
                    addToast(detail) {
                        const id = this.nextId++;
                        this.toasts.push({ id, message: detail.message, type: detail.type || 'info', show: true });
                        setTimeout(() => this.removeToast(id), 4000);
                    },
                    removeToast(id) {
                        const toast = this.toasts.find(t => t.id === id);
                        if (toast) toast.show = false;
                        setTimeout(() => { this.toasts = this.toasts.filter(t => t.id !== id); }, 300);
                    }
                }
            }

            // @Mention autocomplete for comments
            function commentMention() {
                return {
                    showSuggestions: false,
                    suggestions: [],
                    highlightedIndex: 0,
                    allUsers: {!! \App\Models\User::select('id', 'name', 'avatar')->get()->map(fn($u) => ['id' => $u->id, 'name' => $u->name, 'avatar' => $u->avatar_url])->toJson() !!},

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
