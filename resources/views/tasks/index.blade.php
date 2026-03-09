<x-app-layout>
    <x-slot name="header">งานของฉัน</x-slot>

    <div x-data="{ view: '{{ request('view', 'board') }}' }">
        {{-- ═══ Asana-style View Tabs ═══ --}}
        <div class="flex items-center gap-0.5 mb-5 border-b border-erms-border-light -mt-2">
            <button @click="view = 'board'; history.replaceState(null, '', '?view=board{{ $projectId ? '&project_id='.$projectId : '' }}')"
                    :class="view === 'board' ? 'text-erms-text border-erms-text' : 'text-erms-text-secondary border-transparent hover:text-erms-text'"
                    class="flex items-center gap-1.5 px-3 py-2.5 text-[13px] font-medium border-b-2 -mb-px transition-colors duration-100 cursor-pointer">
                <i class="fa-solid fa-table-columns"></i>
                Board
            </button>
            <button @click="view = 'list'; history.replaceState(null, '', '?view=list{{ $projectId ? '&project_id='.$projectId : '' }}')"
                    :class="view === 'list' ? 'text-erms-text border-erms-text' : 'text-erms-text-secondary border-transparent hover:text-erms-text'"
                    class="flex items-center gap-1.5 px-3 py-2.5 text-[13px] font-medium border-b-2 -mb-px transition-colors duration-100 cursor-pointer">
                <i class="fa-solid fa-list"></i>
                List
            </button>
            <button @click="view = 'timeline'; history.replaceState(null, '', '?view=timeline{{ $projectId ? '&project_id='.$projectId : '' }}')"
                    :class="view === 'timeline' ? 'text-erms-text border-erms-text' : 'text-erms-text-secondary border-transparent hover:text-erms-text'"
                    class="flex items-center gap-1.5 px-3 py-2.5 text-[13px] font-medium border-b-2 -mb-px transition-colors duration-100 cursor-pointer">
                <i class="fa-solid fa-chart-gantt"></i>
                Timeline
            </button>
            <a href="{{ route('calendar') }}"
               class="flex items-center gap-1.5 px-3 py-2.5 text-[13px] font-medium text-erms-text-secondary border-b-2 border-transparent -mb-px hover:text-erms-text transition-colors duration-100">
                <i class="fa-solid fa-calendar-days"></i>
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
