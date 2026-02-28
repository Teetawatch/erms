<x-app-layout>
    <x-slot name="header">งาน</x-slot>

    <div x-data="{ view: '{{ request('view', 'board') }}' }">
        {{-- View Tabs --}}
        <div class="flex items-center gap-1 mb-5 bg-erms-surface border border-erms-border rounded-lg p-1 w-fit">
            <button @click="view = 'board'; history.replaceState(null, '', '?view=board{{ $projectId ? '&project_id='.$projectId : '' }}')"
                    :class="view === 'board' ? 'bg-erms-blue text-white shadow-sm' : 'text-erms-muted hover:text-erms-text hover:bg-erms-surface-2'"
                    class="flex items-center gap-1.5 px-3 py-1.5 rounded-md text-sm font-medium transition cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"/></svg>
                Board
            </button>
            <button @click="view = 'list'; history.replaceState(null, '', '?view=list{{ $projectId ? '&project_id='.$projectId : '' }}')"
                    :class="view === 'list' ? 'bg-erms-blue text-white shadow-sm' : 'text-erms-muted hover:text-erms-text hover:bg-erms-surface-2'"
                    class="flex items-center gap-1.5 px-3 py-1.5 rounded-md text-sm font-medium transition cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                List
            </button>
            <button @click="view = 'timeline'; history.replaceState(null, '', '?view=timeline{{ $projectId ? '&project_id='.$projectId : '' }}')"
                    :class="view === 'timeline' ? 'bg-erms-blue text-white shadow-sm' : 'text-erms-muted hover:text-erms-text hover:bg-erms-surface-2'"
                    class="flex items-center gap-1.5 px-3 py-1.5 rounded-md text-sm font-medium transition cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                Timeline
            </button>
            <a href="{{ route('calendar') }}"
               class="flex items-center gap-1.5 px-3 py-1.5 rounded-md text-sm font-medium transition text-erms-muted hover:text-erms-text hover:bg-erms-surface-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                Calendar
            </a>
        </div>

        {{-- Board View --}}
        <div x-show="view === 'board'" x-cloak>
            <livewire:kanban-board :project-id="$projectId" />
        </div>

        {{-- List View --}}
        <div x-show="view === 'list'" x-cloak>
            <livewire:task-list-view :project-id="$projectId" />
        </div>

        {{-- Timeline View --}}
        <div x-show="view === 'timeline'" x-cloak>
            <livewire:timeline-view :project-id="$projectId" />
        </div>
    </div>
</x-app-layout>
