<script>
import FullCalendar from '@fullcalendar/vue3';
import dayGridPlugin from '@fullcalendar/daygrid';
import interactionPlugin from '@fullcalendar/interaction';
import bootstrapPlugin from '@fullcalendar/bootstrap5';

export default {
  name: 'CalendarComponent',
  components: {
    FullCalendar
  },
  data() {
    return {
      calendarOptions: {
        plugins: [dayGridPlugin, interactionPlugin, bootstrapPlugin],
        themeSystem: 'bootstrap5',
        initialView: 'dayGridMonth',
        editable: true,
        headerToolbar: {
          left: 'prev,next today',
          center: 'title',
          right: 'dayGridMonth,dayGridWeek'
        },
        events: [
          { title: 'Evento 1', start: '2024-11-10' },
          { title: 'Evento 2', start: '2024-11-12', end: '2024-11-15' }
        ]
      }
    };
  }
};
</script>

<template>
  <div class="container mt-4">
    <FullCalendar :options="calendarOptions" />
  </div>
</template>
