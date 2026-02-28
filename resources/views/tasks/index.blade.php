<x-app-layout>
    <x-slot name="header">งานของฉัน</x-slot>

    <div x-data="{ view: '{{ request('view', 'board') }}' }">
        {{-- ═══ Asana-style View Tabs ═══ --}}
        <div class="flex items-center gap-0.5 mb-5 border-b border-erms-border-light -mt-2">
            <button @click="view = 'board'; history.replaceState(null, '', '?view=board{{ $projectId ? '&project_id='.$projectId : '' }}')"
                    :class="view === 'board' ? 'text-erms-text border-erms-text' : 'text-erms-text-secondary border-transparent hover:text-erms-text'"
                    class="flex items-center gap-1.5 px-3 py-2.5 text-[13px] font-medium border-b-2 -mb-px transition-colors duration-100 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"/></svg>
                Board
            </button>
            <button @click="view = 'list'; history.replaceState(null, '', '?view=list{{ $projectId ? '&project_id='.$projectId : '' }}')"
                    :class="view === 'list' ? 'text-erms-text border-erms-text' : 'text-erms-text-secondary border-transparent hover:text-erms-text'"
                    class="flex items-center gap-1.5 px-3 py-2.5 text-[13px] font-medium border-b-2 -mb-px transition-colors duration-100 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                List
            </button>
            <button @click="view = 'timeline'; history.replaceState(null, '', '?view=timeline{{ $projectId ? '&project_id='.$projectId : '' }}')"
                    :class="view === 'timeline' ? 'text-erms-text border-erms-text' : 'text-erms-text-secondary border-transparent hover:text-erms-text'"
                    class="flex items-center gap-1.5 px-3 py-2.5 text-[13px] font-medium border-b-2 -mb-px transition-colors duration-100 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/></svg>
                Timeline
            </button>
            <a href="{{ route('calendar') }}"
               class="flex items-center gap-1.5 px-3 py-2.5 text-[13px] font-medium text-erms-text-secondary border-b-2 border-transparent -mb-px hover:text-erms-text transition-colors duration-100">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
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
