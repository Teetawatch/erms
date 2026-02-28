import './bootstrap';
import Sortable from 'sortablejs';
import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import interactionPlugin from '@fullcalendar/interaction';

window.Sortable = Sortable;

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
    });
    calendar.render();
    return calendar;
};
