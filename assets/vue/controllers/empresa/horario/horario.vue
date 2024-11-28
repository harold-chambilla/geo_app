<template>
  <div class="container-fluid px-4 mt-4">
    <div class="card shadow-sm w-100">
      <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <button class="btn btn-light btn-sm" @click="changeMonth(-1)">
          &lt; Antes
        </button>
        <h4 class="m-0 text-center">{{ monthName }} de {{ currentYear }}</h4>
        <button class="btn btn-light btn-sm" @click="changeMonth(1)">
          Después &gt;
        </button>
      </div>
      <div class="card-body">
        <div class="row mb-3">
          <div class="col-md-6">
            <select class="form-select form-select-sm" v-model="selectedArea">
              <option value="">Área</option>
              <option value="1">Área 1</option>
              <option value="2">Área 2</option>
            </select>
          </div>
          <div class="col-md-6">
            <select class="form-select form-select-sm" v-model="selectedPuesto">
              <option value="">Puesto</option>
              <option value="1">Puesto 1</option>
              <option value="2">Puesto 2</option>
            </select>
          </div>
        </div>
        <!-- Tabla -->
        <div class="table-responsive">
          <table class="table table-bordered text-center table-sm w-100">
            <thead class="table-primary">
              <tr>
                <th class="align-middle">SEMANA</th>
                <th>LUNES</th>
                <th>MARTES</th>
                <th>MIÉRCOLES</th>
                <th>JUEVES</th>
                <th>VIERNES</th>
                <th>SÁBADO</th>
                <th>DOMINGO</th>
              </tr>
            </thead>
            <tbody>
              <template v-for="(week, weekIndex) in calendar" :key="'week-' + weekIndex">
                <tr>
                  <td class="align-middle bg-light fw-bold">
                    Semana {{ weekIndex + 1 }}
                  </td>
                  <td
                    v-for="(day, dayIndex) in week"
                    :key="'day-' + dayIndex"
                    class="align-middle bg-white"
                  >
                    <div :class="day.isCurrentMonth ? 'text-dark' : 'text-secondary'">
                      {{ day.date }}
                    </div>
                  </td>
                </tr>
                <tr
                  v-for="employee in filteredEmployees"
                  :key="'employee-' + employee.name + weekIndex"
                >
                  <td class="align-middle bg-light text-start">
                    {{ employee.name }}
                  </td>
                  <td
                    v-for="(day, dayIndex) in week"
                    :key="'employee-day-' + dayIndex"
                    class="align-middle bg-white"
                  >
                    <div
                      v-if="hasSchedule(weekIndex, dayIndex, employee.name)"
                      class="badge bg-primary text-white"
                    >
                      09:00 - 18:30
                    </div>
                    <div v-else class="text-secondary small">Sin horario</div>
                  </td>
                </tr>
              </template>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from "vue";

const currentDate = ref(new Date());
const currentYear = ref(currentDate.value.getFullYear());
const currentMonth = ref(currentDate.value.getMonth());
const selectedArea = ref("");
const selectedPuesto = ref("");
const employees = ref([
  { name: "Angel Benavides", area: "1", puesto: "1" },
  { name: "Fiorela Ruiz", area: "1", puesto: "2" },
  { name: "Carlos Vega", area: "2", puesto: "1" },
  { name: "Lucía Gómez", area: "2", puesto: "2" },
]);

const monthNames = [
  "Enero",
  "Febrero",
  "Marzo",
  "Abril",
  "Mayo",
  "Junio",
  "Julio",
  "Agosto",
  "Septiembre",
  "Octubre",
  "Noviembre",
  "Diciembre",
];

const monthName = computed(() => monthNames[currentMonth.value]);

const filteredEmployees = computed(() => {
  return employees.value.filter((employee) => {
    const matchesArea = selectedArea.value ? employee.area === selectedArea.value : true;
    const matchesPuesto = selectedPuesto.value ? employee.puesto === selectedPuesto.value : true;
    return matchesArea && matchesPuesto;
  });
});

const getCalendarDays = (year, month) => {
  const firstDay = new Date(year, month, 1).getDay();
  const daysInMonth = new Date(year, month + 1, 0).getDate();
  const previousMonthDays = new Date(year, month, 0).getDate();
  const days = [];
  let week = [];
  let startDay = (firstDay + 6) % 7;

  for (let i = 0; i < startDay; i++) {
    week.push({
      date: previousMonthDays - startDay + i + 1,
      isCurrentMonth: false,
    });
  }

  for (let day = 1; day <= daysInMonth; day++) {
    week.push({ date: day, isCurrentMonth: true });
    if (week.length === 7) {
      days.push(week);
      week = [];
    }
  }

  let nextDay = 1;
  while (week.length < 7) {
    week.push({ date: nextDay, isCurrentMonth: false });
    nextDay++;
  }

  if (week.length > 0) {
    days.push(week);
  }

  return days;
};

const calendar = computed(() => getCalendarDays(currentYear.value, currentMonth.value));

const changeMonth = (offset) => {
  currentMonth.value += offset;
  if (currentMonth.value > 11) {
    currentMonth.value = 0;
    currentYear.value++;
  } else if (currentMonth.value < 0) {
    currentMonth.value = 11;
    currentYear.value--;
  }
};

const hasSchedule = (weekIndex, dayIndex, employee) => {
  if (!calendar.value[weekIndex] || !calendar.value[weekIndex][dayIndex]) {
    return false;
  }

  const day = calendar.value[weekIndex][dayIndex];
  if (day.isCurrentMonth) {
    if (weekIndex === 1 && dayIndex >= 2 && dayIndex <= 6 && employee === "Angel Benavides") return true;
    if (weekIndex === 0 && dayIndex === 6 && employee === "Fiorela Ruiz") return true;
  }
  return false;
};
</script>
