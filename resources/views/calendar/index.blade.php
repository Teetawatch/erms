<x-app-layout>
    <x-slot name="header">ปฏิทิน</x-slot>

    <style>
        .fc {
            --fc-border-color: #e2e6ef;
            --fc-button-bg-color: #ffffff;
            --fc-button-border-color: #e2e6ef;
            --fc-button-text-color: #1e293b;
            --fc-button-hover-bg-color: #f0f2f7;
            --fc-button-hover-border-color: #4f8ef7;
            --fc-button-active-bg-color: #4f8ef7;
            --fc-button-active-border-color: #4f8ef7;
            --fc-today-bg-color: rgba(79,142,247,0.06);
            --fc-event-border-color: transparent;
            --fc-page-bg-color: transparent;
            --fc-neutral-bg-color: #f4f6fb;
            --fc-list-event-hover-bg-color: #f0f2f7;
        }
        .fc .fc-col-header-cell-cushion,
        .fc .fc-daygrid-day-number { color: #1e293b; }
        .fc .fc-day-other .fc-daygrid-day-number { color: #94a3b8; }
        .fc-theme-standard td, .fc-theme-standard th { border-color: #e2e6ef; }
        .fc .fc-event { border-radius: 4px; padding: 2px 4px; font-size: 12px; }
    </style>

    <div class="card p-6">
        <div id="erms-calendar"
             x-data
             x-init="
                $nextTick(() => {
                    if (window.initFullCalendar) {
                        window.initFullCalendar($el, '{{ route('calendar.events') }}');
                    }
                });
             ">
        </div>
    </div>
</x-app-layout>
