import './bootstrap';
import Sortable from 'sortablejs';
import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import interactionPlugin from '@fullcalendar/interaction';

window.Sortable = Sortable;

// ═══════════════════════════════════════
// FullCalendar (Asana-styled)
// ═══════════════════════════════════════
window.initFullCalendar = function(el, eventsUrl) {
    const calendar = new Calendar(el, {
        plugins: [dayGridPlugin, interactionPlugin],
        initialView: 'dayGridMonth',
        locale: 'th',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,dayGridWeek',
        },
        buttonText: {
            today: 'วันนี้',
            month: 'เดือน',
            week: 'สัปดาห์',
        },
        events: eventsUrl,
        eventClick: function(info) {
            if (info.event.url) {
                info.jsEvent.preventDefault();
                window.location.href = info.event.url;
            }
        },
        height: 'auto',
        dayMaxEventRows: 3,
        eventDisplay: 'block',
    });
    calendar.render();
    return calendar;
};

// ═══════════════════════════════════════
// Livewire re-init Sortable after DOM updates
// ═══════════════════════════════════════
document.addEventListener('livewire:navigated', () => {
    requestAnimationFrame(() => {
        document.querySelectorAll('.kanban-column').forEach(column => {
            if (column._sortable) return;
            column._sortable = new Sortable(column, {
                group: 'kanban',
                animation: 200,
                easing: 'cubic-bezier(0.4, 0, 0.2, 1)',
                ghostClass: 'sortable-ghost',
                dragClass: 'sortable-drag',
                chosenClass: 'sortable-chosen',
                delay: 50,
                delayOnTouchOnly: true,
                fallbackTolerance: 3,
                onEnd: (evt) => {
                    const taskId = evt.item.dataset.taskId;
                    const newStatus = evt.to.dataset.status;
                    const newIndex = evt.newIndex;
                    if (window.Livewire) {
                        Livewire.dispatch('taskMoved', { taskId: parseInt(taskId), newStatus, newIndex });
                    }
                }
            });
        });
    });
});

// ═══════════════════════════════════════
// Toast Notifications (Asana-style)
// ═══════════════════════════════════════
window.showToast = function(message, type = 'success') {
    const toast = document.createElement('div');
    const colors = {
        success: 'bg-emerald-600',
        error: 'bg-red-600',
        info: 'bg-blue-600',
        warning: 'bg-amber-600',
    };
    toast.className = `fixed bottom-6 left-1/2 -translate-x-1/2 z-[9999] px-4 py-2.5 rounded-lg text-white text-sm font-medium shadow-lg ${colors[type] || colors.info} transition-all duration-300 opacity-0 translate-y-2`;
    toast.textContent = message;
    document.body.appendChild(toast);

    requestAnimationFrame(() => {
        toast.classList.remove('opacity-0', 'translate-y-2');
        toast.classList.add('opacity-100', 'translate-y-0');
    });

    setTimeout(() => {
        toast.classList.remove('opacity-100', 'translate-y-0');
        toast.classList.add('opacity-0', 'translate-y-2');
        setTimeout(() => toast.remove(), 300);
    }, 3000);
};

// Listen for Livewire toast events
document.addEventListener('livewire:init', () => {
    Livewire.on('toast', (data) => {
        window.showToast(data.message || data[0]?.message, data.type || data[0]?.type || 'success');
    });
});

// ═══════════════════════════════════════
// Smooth page transitions
// ═══════════════════════════════════════
document.addEventListener('livewire:navigating', () => {
    document.querySelector('main')?.classList.add('opacity-50', 'transition-opacity', 'duration-100');
});

document.addEventListener('livewire:navigated', () => {
    const main = document.querySelector('main');
    if (main) {
        main.classList.remove('opacity-50');
        main.classList.add('opacity-100');
    }
});
