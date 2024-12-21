<template>
  <div class="container-fluid px-4 mt-4">
    <div class="card shadow-sm w-100">
      <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <button class="btn btn-light btn-sm d-flex align-items-center gap-1" @click="changeMonth(-1)">
          <i class="bi bi-arrow-bar-left"></i>
          <span>Anterior</span>
        </button>
        <h5 class="m-0 text-center cursor-pointer" @click="openMassiveModal('month', { monthName })">{{ monthName }} de {{ currentYear }}</h5>
        <button class="btn btn-light btn-sm d-flex align-items-center gap-1" @click="changeMonth(1)">
          <span>Siguiente</span>
          <i class="bi bi-arrow-bar-right"></i>
        </button>
      </div>
      <div class="card-body">
        <div class="d-flex gap-2 mb-3">
            <!-- Selector de Áreas -->
            <select class="form-select form-select-sm w-auto" v-model="selectedArea" @change="updatePuestos">
              <option value="">Área</option>
              <option v-for="area in filteredAreas" :key="area.ara_id" :value="area.ara_nombre">{{ area.ara_nombre }}</option>
            </select>
  
            <!-- Selector de Puestos -->
            <select class="form-select form-select-sm w-auto" v-model="selectedPuesto">
              <option value="">Puesto</option>
              <option v-for="puesto in filteredPuestos" :key="puesto.pst_id" :value="puesto.pst_nombre">{{ puesto.pst_nombre }}</option>
            </select>
        </div>
        <div class="table-responsive">
          <table class="table table-bordered text-center table-sm w-100">
            <thead class="table-primary">
              <tr>
                <th class="align-middle">SEMANA</th>
                <th v-for="(day, index) in daysOfWeek" :key="'day-header-' + index" class="cursor-pointer" @click="openMassiveModal('day', { day })">{{ day }}</th>
              </tr>
            </thead>
            <tbody>
              <template v-for="(week, weekIndex) in calendar" :key="'week-' + weekIndex">
                <tr>
                  <td class="align-middle bg-light fw-bold cursor-pointer" @click="openMassiveModal('week', { weekIndex })">Semana {{ weekIndex + 1 }}</td>
                  <td v-for="(day, dayIndex) in week" :key="'day-' + dayIndex" class="align-middle bg-white cursor-pointer" @click="openMassiveModal('date', { day, weekIndex, monthName })">
                    <div :class="{ 'text-dark fw-bold': day.isCurrentMonth, 'text-secondary': !day.isCurrentMonth, }">{{ day.date }}</div>
                  </td>
                </tr>
                <tr v-for="employee in filteredEmployees" :key="'employee-' + employee.name + weekIndex">
                  <td class="align-middle bg-light text-center text-secondary small cursor-pointer" @click="openMassiveModal('employeeWeek', { weekIndex, employee })">{{ employee.nombres }} {{ employee.apellidos }}</td>
                  <td v-for="(day, dayIndex) in week" :key="'employee-day-' + dayIndex" class="align-middle bg-white cursor-pointer" @click="openMassiveModal('employeeDay', { day, weekIndex, employee, monthName })">
                    <div v-if="hasSchedule(currentYear, day.isCurrentMonth ? monthName : day.date < calendar[0][0].date ? monthNames[currentMonth - 1 < 0 ? 11 : currentMonth - 1] : monthNames[currentMonth + 1 > 11 ? 0 : currentMonth + 1], day.date, employee.nombres + ' ' + employee.apellidos)">
                      <div 
                        :class="{
                          'badge bg-primary text-white': hasSchedule(currentYear, day.isCurrentMonth ? monthName : day.date < calendar[0][0].date ? monthNames[currentMonth - 1 < 0 ? 11 : currentMonth - 1] : monthNames[currentMonth + 1 > 11 ? 0 : currentMonth + 1], day.date, employee.nombres + ' ' + employee.apellidos).jornada !== 'remoto',
                          'badge bg-warning text-dark': hasSchedule(currentYear, day.isCurrentMonth ? monthName : day.date < calendar[0][0].date ? monthNames[currentMonth - 1 < 0 ? 11 : currentMonth - 1] : monthNames[currentMonth + 1 > 11 ? 0 : currentMonth + 1], day.date, employee.nombres + ' ' + employee.apellidos).jornada === 'remoto'
                        }"
                      >
                        {{ hasSchedule(currentYear, day.isCurrentMonth ? monthName : day.date < calendar[0][0].date ? monthNames[currentMonth - 1 < 0 ? 11 : currentMonth - 1] : monthNames[currentMonth + 1 > 11 ? 0 : currentMonth + 1], day.date, employee.nombres + ' ' + employee.apellidos).hora_entrada }} - {{ hasSchedule(currentYear, day.isCurrentMonth ? monthName : day.date < calendar[0][0].date ? monthNames[currentMonth - 1 < 0 ? 11 : currentMonth - 1] : monthNames[currentMonth + 1 > 11 ? 0 : currentMonth + 1], day.date, employee.nombres + ' ' + employee.apellidos).hora_salida }}
                      </div>
                    </div>
                    <div v-else class="badge text-bg-light"></div>
                  </td>
                </tr>
              </template>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="scheduleModal" tabindex="-1" aria-labelledby="scheduleModalLabel" aria-hidden="true" ref="scheduleModal">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header d-flex flex-column align-items-start">
            <h5 class="modal-title text-primary" id="scheduleModalLabel">{{ modalTitle }}</h5>
            <small class="text-secondary">
              Área: {{ selectedArea || "Todos" }}, Puesto: {{ selectedPuesto || "Todos" }}, Personal: {{ currentEmployee || "Todos" }}
            </small>
            <button type="button" class="btn-close position-absolute top-0 end-0 me-3 mt-3" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div>
              <div class="row mb-3">
                <div class="col-md-6">
                  <label class="fw-bold">Ingreso:</label>
                  <input type="time" v-model="startTime" class="form-control" @input="calculateWorkHours" />
                </div>
                <div class="col-md-6">
                  <label class="fw-bold">Salida:</label>
                  <input type="time" v-model="endTime" class="form-control" @input="calculateWorkHours" />
                </div>
              </div>
              <div class="mb-3">
                <label class="fw-bold">Horas laborales:</label>
                <input type="text" v-model="workHours" class="form-control" readonly />
              </div>
              <div class="mb-3">
                <label class="fw-bold">Modalidad:</label>
                <div class="d-flex gap-3">
                  <div class="form-check">
                    <input class="form-check-input" type="radio" id="presencial" value="Presencial" v-model="workMode" />
                    <label class="form-check-label" for="presencial">Presencial</label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="radio" id="remoto" value="Remoto" v-model="workMode" />
                    <label class="form-check-label" for="remoto">Remoto</label>
                  </div>
                </div>
              </div>
              <div v-if="showRestDay" class="mb-3">
                <label class="fw-bold">Descanso:</label>
                <select v-model="restDay" class="form-select">
                  <option value="">Seleccionar...</option>
                  <option v-for="day in daysOfWeekOptions" :key="day" :value="day">{{ day }}</option>
                </select>
              </div>
              <div v-if="showApplyAll" class="form-check">
                <input type="checkbox" id="applyToAll" v-model="applyToAll" class="form-check-input" />
                <label for="applyToAll" class="form-check-label">Aplicar a todo</label>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button v-if="idHorarioSelected !== null && idHorarioSelected.length > 0" type="button" class="btn btn-danger me-auto" @click="abrirModalEliminar(idHorarioSelected)" title="Eliminar Horario"><i class="bi bi-trash"></i></button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <!--<button type="button" class="btn btn-primary" @click="saveSchedule">Aceptar</button>-->
            <button type="button" class="btn btn-primary" @click="idHorarioSelected !== null && idHorarioSelected.length > 0 ? abrirModalModificar() : saveSchedule()">Guardar</button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true" ref="confirmDeleteModal">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title text-danger" id="confirmDeleteModalLabel">Confirmar Eliminación</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          ¿Estás seguro de que deseas eliminar este horario? Esta acción no se puede deshacer.
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="button" class="btn btn-danger" @click="confirmarEliminacion">Eliminar</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="warningModal" tabindex="-1" aria-labelledby="warningModalLabel" aria-hidden="true" ref="warningModal">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title text-danger" id="warningModalLabel">Advertencia</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p>
            <strong>Advertencia:</strong> Los cambios realizados afectarán a todos los horarios seleccionados en el grupo.
            ¿Está seguro de continuar?
          </p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="button" class="btn btn-danger" @click="confirmarModificacion">Confirmar y Guardar</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useHorarioStore } from '@/store/empresa/horario';
import { ref, shallowRef,  computed, onMounted, watch } from "vue";
import { Modal } from "bootstrap";

const horarioStore = useHorarioStore();

const currentDate = ref(new Date());
const currentYear = ref(currentDate.value.getFullYear());
const currentMonth = ref(currentDate.value.getMonth());
const selectedArea = ref("");
const selectedPuesto = ref("");
const currentEmployee = ref("");
const schedules = ref({});
const employees = ref([]);
const empresaId = ref(1);

const filteredAreas = computed(() => {
  return horarioStore.areas.filter((area) => area.ara_nombre.toLowerCase() !== "sistema");
});

const filteredPuestos = computed(() => {
  const area = horarioStore.areas.find((area) => area.ara_nombre == selectedArea.value);
  return area
    ? area.puestos.filter((puesto) => puesto.pst_nombre.toLowerCase() !== "sistema")
    : [];
});

const updatePuestos = () => {
  selectedPuesto.value = "";
};

const filteredEmployees = computed(() => {
  const filtered = employees.value.filter((employee) => {
    const matchesArea = selectedArea.value ? employee.area === selectedArea.value : true; // Comparando con el nombre del área
    const matchesPuesto = selectedPuesto.value ? employee.puesto === selectedPuesto.value : true; // Comparando con el nombre del puesto
    return matchesArea && matchesPuesto;
  });

  return filtered;
});

const fetchHorarios = async () => {
  const data = {
    empresa_id: empresaId.value,
    colaborador_ids: filteredEmployees.value.map((e) => e.colaborador_id),
  };

  try {
    await horarioStore.obtenerHorarios(data);

    schedules.value = horarioStore.horarios.reduce((acc, horario) => {
      // Ajustar la fecha utilizando Intl.DateTimeFormat con la zona horaria 'America/Lima'
      const date = new Date(horario.fecha + 'T00:00:00'); // Asegura que la fecha esté en el formato adecuado
      const dateInLimaTimezone = new Intl.DateTimeFormat('es-PE', {
        timeZone: 'America/Lima',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
      }).formatToParts(date);

      // Extraer año, mes y día de la fecha formateada
      const yearIndex = dateInLimaTimezone.find((part) => part.type === 'year').value;
      const monthName = dateInLimaTimezone.find((part) => part.type === 'month').value;
      const dayOfMonth = dateInLimaTimezone.find((part) => part.type === 'day').value;

      const capitalizedMonthName = monthName.charAt(0).toUpperCase() + monthName.slice(1).toLowerCase();

      // Buscar el empleado correspondiente por colaborador_id
      const employee = filteredEmployees.value.find(
        (e) => e.colaborador_id === horario.colaborador_id
      );

      if (!employee) {
        console.warn(`Empleado no encontrado para colaborador_id: ${horario.colaborador_id}`);
        return acc; // Si no se encuentra el empleado, continuar
      }

      // Crear la clave con yearIndex, monthName, dayOfMonth, y el nombre completo del empleado
      const key = `${yearIndex}-${capitalizedMonthName}-${dayOfMonth}-${employee.nombres} ${employee.apellidos}`;

      // Guardar los datos del horario en schedules.value
      acc[key] = {
        id: horario.id,
        hora_entrada: horario.hora_entrada,
        hora_salida: horario.hora_salida,
        jornada: horario.tipo_jornada,
        descanso: horario.descanso,
      };

      return acc;
    }, {});

    console.log("Horarios obtenidos:", schedules.value);
  } catch (error) {
    console.error("Error al obtener horarios:", error);
  }
};

const hasSchedule = (yearIndex, monthName, dayOfMonth, employeeName) => {
  const key = `${yearIndex}-${monthName}-${dayOfMonth}-${employeeName}`;
  return schedules.value[key];
};

const modalInstance = ref(null);
const modalTitle = ref("");
const showRestDay = ref(true);
const showApplyAll = ref(false);
const startTime = ref("");
const endTime = ref("");
const idHorarioSelected = ref([]);
const horariosSeleccionados = ref([]);
const workHours = ref("");
const workMode = ref("");
const restDay = ref("");
const applyToAll = ref(false);
let currentScheduleKey = "";
let scheduleKey = "";

const daysOfWeek = ["Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado", "Domingo"];
const daysOfWeekOptions = [...daysOfWeek, "Sábado y Domingo"];

const monthNames = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
const monthName = computed(() => monthNames[currentMonth.value]);

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

const openMassiveModal = (type, context) => {
  const { day, weekIndex, employee } = context || {};

  // Verificar si schedules está vacío y cargarlo
  if (!schedules.value || Object.keys(schedules.value).length === 0) {
    console.warn("Schedules está vacío. Cargando datos...");
    fetchHorarios();
  }

  // Ajustar el mes para casos donde el día no pertenece al mes actual
  let adjustedMonthIndex = currentMonth.value; // Inicialmente asignar al mes actual
  let adjustedMonthName = monthNames[currentMonth.value]; // Nombre del mes actual

  if ((type === "date" || type === "day" || type === "employeeDay") && day?.date) {
    if (!day.isCurrentMonth) {
      if (weekIndex === 0 && day.date > 15) {
        // Si está en la primera semana y el día es mayor a 15, pertenece al mes anterior
        adjustedMonthIndex = currentMonth.value - 1 < 0 ? 11 : currentMonth.value - 1;
        adjustedMonthName = monthNames[adjustedMonthIndex];
      } else if (weekIndex > 0 && day.date < 7) {
        // Si no está en la primera semana y el día es menor a 7, pertenece al mes siguiente
        adjustedMonthIndex = currentMonth.value + 1 > 11 ? 0 : currentMonth.value + 1;
        adjustedMonthName = monthNames[adjustedMonthIndex];
      }
    }
  }

  // Mapeo para días de la semana (Lunes, Martes, etc.)
  if (type === "day") {
    const dayIndex = daysOfWeek.indexOf(day); // Obtener el índice del día
    if (dayIndex === -1) {
      console.error("Día de la semana inválido:", day);
      return;
    }
    modalTitle.value = `Día: ${daysOfWeek[dayIndex]}`;
    currentScheduleKey = `day-${daysOfWeek[dayIndex]}`;
  } else if (type === "month") {
    modalTitle.value = `Mes: ${monthNames[currentMonth.value]}`;
    currentScheduleKey = "month";
  } else if (type === "date") {
    modalTitle.value = `Día: ${day.date} de ${adjustedMonthName}`;
    currentScheduleKey = `date-${day.date}-${adjustedMonthName}`;
  } else if (type === "week") {
    modalTitle.value = `Semana ${weekIndex + 1}`;
    currentScheduleKey = `week-${weekIndex}`;
  } else if (type === "employeeDay") {
    const dayOfMonth = String(day.date); // Día con formato 01, 02, etc.
    const normalizedMonthName = adjustedMonthName.charAt(0).toUpperCase() + adjustedMonthName.slice(1).toLowerCase(); // Capitaliza el nombre del mes
    const employeeName = `${employee.nombres} ${employee.apellidos}`; // Nombre completo del empleado
    const year = currentYear.value; // Año actual
    
    //modalTitle.value = `Día: ${day.date} de ${normalizedMonthName} - ${employeeName}`;
    scheduleKey = `${year}-${normalizedMonthName}-${dayOfMonth}-${employeeName}`;
    
    modalTitle.value = `Día: ${day.date} de ${adjustedMonthName} - ${employee.nombres} ${employee.apellidos}`;
    currentScheduleKey = `employeeDay-${day.date}-${adjustedMonthName}-${employee.colaborador_id}`;
  } else if (type === "employeeWeek") {
    modalTitle.value = `Semana ${weekIndex + 1} - ${employee.nombres} ${employee.apellidos}`;
    currentScheduleKey = `employeeWeek-${weekIndex}-${employee.colaborador_id}`;
  } else {
    console.error("Tipo no válido para abrir el modal:", type);
    return;
  }

  currentEmployee.value = employee?.nombres && employee?.apellidos
    ? `${employee.nombres} ${employee.apellidos}`
    : "";

  // Solo mostrar el campo "Descanso" en acciones específicas
  showRestDay.value = !["day", "date", "employeeDay"].includes(type);

// Identificar los IDs de horarios para rangos de fechas y empleados seleccionados
let dateRanges = [];
let employees = [];

// Determinar los rangos de fechas y empleados según el tipo
if (type === "employeeWeek") {
  // Rango de fechas basado en la semana seleccionada
  const selectedWeek = generateWeeksInMonth(currentYear.value, currentMonth.value)[weekIndex];
  dateRanges = selectedWeek.map((day) => day.date);
  employees = [employee]
} else if (type === "employeeDay") {
  const dayOfWeek = new Date(
    currentYear.value,
    adjustedMonthIndex,
    day.date
  ).toLocaleDateString("es-PE", { weekday: "long" });

  dateRanges = generateWeeksInMonth(currentYear.value, currentMonth.value)
    .flat()
    .filter((d) => d.dayOfWeek === dayOfWeek)
    .map((d) => d.date);

  employees = [employee];
} else if (type === "date") {
  // Ajustar el mes para casos donde el día no pertenece al mes actual
  let adjustedMonthIndex = currentMonth.value; // Inicialmente asignar al mes actual
  let adjustedMonthName = monthNames[currentMonth.value]; // Nombre del mes actual

  if (day?.date) {
    if (!day.isCurrentMonth) {
      if (weekIndex === 0 && day.date > 15) {
        // Si está en la primera semana y el día es mayor a 15, pertenece al mes anterior
        adjustedMonthIndex = currentMonth.value - 1 < 0 ? 11 : currentMonth.value - 1;
        adjustedMonthName = monthNames[adjustedMonthIndex];
      } else if (weekIndex > 0 && day.date < 7) {
        // Si no está en la primera semana y el día es menor a 7, pertenece al mes siguiente
        adjustedMonthIndex = currentMonth.value + 1 > 11 ? 0 : currentMonth.value + 1;
        adjustedMonthName = monthNames[adjustedMonthIndex];
      }
    }
  }

  // Crear fecha completa en formato 'YYYY-MM-DD'
  const formattedDate = `${currentYear.value}-${String(adjustedMonthIndex + 1).padStart(2, '0')}-${String(day.date).padStart(2, '0')}`;
  dateRanges = [formattedDate];

  //console.log("Rangos de fechas seleccionados (date):", dateRanges);

  employees = filteredEmployees.value;
} else if (type === "day") {
  // Todos los días de la semana seleccionada en el mes
  const dayIndex = daysOfWeek.indexOf(day);
  dateRanges = generateWeeksInMonth(currentYear.value, currentMonth.value)
        .flat()
        .filter((day) => day.dayOfWeek.toLowerCase() === daysOfWeek[dayIndex].toLowerCase())
        .map((day) => day.date);

  employees = filteredEmployees.value;
} else if (type === "week") {
  // Rango completo de una semana específica
  const selectedWeek = generateWeeksInMonth(currentYear.value, currentMonth.value)[weekIndex];
  dateRanges = selectedWeek.map((day) => day.date);
  employees = filteredEmployees.value;
} else if (type === "month") {
  // Todo el mes
  dateRanges = generateWeeksInMonth(currentYear.value, currentMonth.value)
    .flat()
    .map((day) => day.date);
  employees = filteredEmployees.value;
}

  // Obtener los IDs de horarios para las fechas y empleados
  horariosSeleccionados.value = [];

  dateRanges.forEach((date) => {
    employees.forEach((emp) => {
      const fecha = new Date(date + 'T00:00:00'); // Asegura que la fecha esté en el formato adecuado
      const dateInLimaTimezone = new Intl.DateTimeFormat('es-PE', {
        timeZone: 'America/Lima',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
      }).formatToParts(fecha);

      // Extraer las partes de la fecha
      const day = parseInt(dateInLimaTimezone.find((part) => part.type === "day").value, 10);
      const monthName = capitalize(dateInLimaTimezone.find((part) => part.type === "month").value);
      const year = dateInLimaTimezone.find((part) => part.type === "year").value;

      // Construir la clave del horario
      const key = `${year}-${monthName}-${day}-${emp.nombres} ${emp.apellidos}`;
      //console.log(key);

      // Verificar si existe el horario y agregarlo
      if (schedules.value[key]?.id) {
        horariosSeleccionados.value.push(schedules.value[key].id);
      }
    });
  });

  // Si hay horarios seleccionados, se muestran
  //console.log("Horarios seleccionados:", horariosSeleccionados.value);

  // Cargar un horario existente si está disponible
  const existingSchedule = schedules.value[scheduleKey];
  if (existingSchedule) {
    idHorarioSelected.value = horariosSeleccionados.value;
    startTime.value = existingSchedule.hora_entrada;
    endTime.value = existingSchedule.hora_salida;
    workMode.value = capitalize(existingSchedule.jornada);
    restDay.value = existingSchedule.restDay || "";
  } else {
    idHorarioSelected.value = horariosSeleccionados.value;
    startTime.value = "08:00";
    endTime.value = "18:00";
    workMode.value = "Presencial";
    restDay.value = "";
  }

  calculateWorkHours();

  // Determinar si se muestra la opción de "Aplicar a todo"
  showApplyAll.value = ["employeeWeek", "employeeDay"].includes(type);

  // Mostrar el modal
  if (!modalInstance.value) {
    modalInstance.value = new Modal(document.getElementById("scheduleModal"));
  }
  modalInstance.value.show();
};

const capitalize = (str) => {
  if (!str) return "";
  return str.charAt(0).toUpperCase() + str.slice(1).toLowerCase();
};

const generateWeeksInMonth = (year, month) => {
  const weeks = [];
  const firstDayOfMonth = new Date(year, month, 1);
  const lastDayOfMonth = new Date(year, month + 1, 0);

  // Ajustar el primer día del mes para que comience el lunes
  const startDate = new Date(firstDayOfMonth);
  startDate.setDate(startDate.getDate() - ((startDate.getDay() + 6) % 7)); // Retroceder al lunes de la semana

  let currentDay = new Date(startDate);

  while (currentDay <= lastDayOfMonth) {
    // Calcular el índice de la semana
    const weekIndex = Math.floor(
      (currentDay - startDate) / (7 * 24 * 60 * 60 * 1000)
    );

    if (!weeks[weekIndex]) weeks[weekIndex] = [];

    if (currentDay.getMonth() === month) {
      weeks[weekIndex].push({
        date: currentDay.toISOString().split("T")[0], // Formato 'YYYY-MM-DD'
        dayOfWeek: currentDay.toLocaleDateString("es-PE", { weekday: "long" }), // Día de la semana en español
      });
    }

    currentDay.setDate(currentDay.getDate() + 1);
  }

  return weeks;
};

const saveSchedule = () => {
  if (!startTime.value || !endTime.value) {
    console.error("Faltan datos obligatorios para guardar el horario.");
    return;
  }

  calculateWorkHours();

  let horarioData;
  const [keyType, ...keyDetails] = currentScheduleKey.split("-");
  const workModeLower = workMode.value.toLowerCase();

  const weeksInMonth = generateWeeksInMonth(currentYear.value, currentMonth.value);

  switch (keyType) {
    case "month":
      horarioData = {
        tipo_registro: "mes",
        empresa_id: empresaId.value,
        colaborador_ids: filteredEmployees.value.map((e) => e.colaborador_id),
        dia_inicio: `${currentYear.value}-${String(currentMonth.value + 1).padStart(2, "0")}-01`,
        dia_fin: `${currentYear.value}-${String(currentMonth.value + 1).padStart(2, "0")}-${new Date(currentYear.value, currentMonth.value + 1, 0).getDate()}`,
        hora_entrada: startTime.value,
        hora_salida: endTime.value,
        descanso: restDay.value || "no establecido",
        tipo_jornada: workModeLower || "presencial",
      };
      break;

    case "week": {
      const weekIndex = parseInt(keyDetails[0]);
      const selectedWeek = weeksInMonth[weekIndex];
      const startDate = selectedWeek[0].date;
      const endDate = selectedWeek[selectedWeek.length - 1].date;

      horarioData = {
        tipo_registro: "semana",
        empresa_id: empresaId.value,
        colaborador_ids: filteredEmployees.value.map((e) => e.colaborador_id),
        dia_inicio: startDate,
        dia_fin: endDate,
        hora_entrada: startTime.value,
        hora_salida: endTime.value,
        descanso: restDay.value || "no establecido",
        tipo_jornada: workModeLower || "presencial",
      };
      break;
    }

    case "employeeWeek": {
      const weekIndex = parseInt(keyDetails[0]);
      const employeeId = parseInt(keyDetails[1]);

      // Obtener todas las semanas del mes
      const weeksInMonth = generateWeeksInMonth(currentYear.value, currentMonth.value);

      let dateRanges;

      if (applyToAll.value) {
        // Caso: aplicar a todas las semanas del mes
        dateRanges = weeksInMonth.map(week => [week[0].date, week[week.length - 1].date]);
      } else {
        // Caso: aplicar solo a la semana seleccionada
        const selectedWeek = weeksInMonth[weekIndex];
        dateRanges = [[selectedWeek[0].date, selectedWeek[selectedWeek.length - 1].date]];
      }

      horarioData = {
        tipo_registro: "colaborador",
        empresa_id: empresaId.value,
        colaborador_ids: [employeeId],
        fechas: dateRanges, // Enviar solo el rango seleccionado o múltiples rangos según applyToAll.value
        hora_entrada: startTime.value,
        hora_salida: endTime.value,
        descanso: restDay.value || "no establecido",
        tipo_jornada: workModeLower || "presencial",
        aplicar_a_todo: applyToAll.value || false,
      };

      break;
    }

    case "day": {
      const dayIndex = daysOfWeek.indexOf(keyDetails[0]);
      if (dayIndex === -1) {
        console.error("Día de la semana inválido:", keyDetails[0]);
        return;
      }

      // Filtrar todas las fechas del mes que coincidan con el día de la semana indicado
      const datesInMonth = weeksInMonth
        .flat()
        .filter((day) => day.dayOfWeek.toLowerCase() === daysOfWeek[dayIndex].toLowerCase())
        .map((day) => day.date);

      if (datesInMonth.length === 0) {
        console.error("No se encontraron fechas en el mes para el día indicado:", keyDetails[0]);
        return;
      }

      // Construir los datos de horario
      horarioData = {
        tipo_registro: "dia",
        empresa_id: empresaId.value,
        colaborador_ids: filteredEmployees.value.map((e) => e.colaborador_id),
        fechas: datesInMonth,
        hora_entrada: startTime.value,
        hora_salida: endTime.value,
        descanso: restDay.value || "no establecido",
        tipo_jornada: workModeLower || "presencial",
      };
      break;
    }

    case "date": {
      const adjustedMonthIndex = monthNames.indexOf(keyDetails[1]);
      const specificDate = `${currentYear.value}-${String(adjustedMonthIndex + 1).padStart(2, "0")}-${String(keyDetails[0]).padStart(2, "0")}`;

      horarioData = {
        tipo_registro: "dia",
        empresa_id: empresaId.value,
        colaborador_ids: filteredEmployees.value.map((e) => e.colaborador_id),
        fechas: [specificDate], // Se devuelve un array que contiene la fecha específica
        hora_entrada: startTime.value,
        hora_salida: endTime.value,
        descanso: restDay.value || "no establecido",
        tipo_jornada: workModeLower || "presencial",
      };
      break;
    }

    case "employeeDay": {
      const adjustedMonthIndex = monthNames.indexOf(keyDetails[1]);
      const employeeId = parseInt(keyDetails[2]);
      const selectedDay = parseInt(keyDetails[0]);

      // Obtener todas las semanas del mes
      const weeksInMonth = generateWeeksInMonth(currentYear.value, currentMonth.value);

      // Identificar el día de la semana del día seleccionado
      const dayOfWeek = new Date(
        currentYear.value,
        adjustedMonthIndex,
        selectedDay
      ).toLocaleDateString("es-PE", { weekday: "long" });

      let dateRanges;

      if (applyToAll.value) {
        // Caso: aplicar a todos los días de la semana correspondientes
        const matchingDays = weeksInMonth
          .flat()
          .filter((day) => day.dayOfWeek === dayOfWeek) // Buscar todos los días de la semana que coincidan
          .map((day) => day.date);

        dateRanges = matchingDays; // Lista de fechas de todos los días de la semana
      } else {
        // Caso: solo un día específico
        dateRanges = [`${currentYear.value}-${String(adjustedMonthIndex + 1).padStart(2, "0")}-${String(selectedDay).padStart(2, "0")}`];
      }

      horarioData = {
        tipo_registro: "colaborador",
        empresa_id: empresaId.value,
        colaborador_ids: [employeeId],
        fechas: dateRanges, // Lista de fechas (un día o todos los días de la semana)
        hora_entrada: startTime.value,
        hora_salida: endTime.value,
        descanso: restDay.value || "no establecido",
        tipo_jornada: workModeLower || "presencial",
        aplicar_a_todo: applyToAll.value || false,
      };

      break;
    }

    default:
      console.error("Tipo de guardado no reconocido.");
      return;
  }

  try {
    horarioStore.registrarHorario(horarioData);
    console.log("Horario registrado exitosamente.");

    modalInstance.value.hide();

    fetchHorarios();
  } catch (error) {
    console.error("Error al registrar el horario:", error);
  }
};

const calculateWorkHours = () => {
  if (startTime.value && endTime.value) {
    const [startHour, startMinute] = startTime.value.split(":").map(Number);
    const [endHour, endMinute] = endTime.value.split(":").map(Number);
    const totalMinutes = (endHour * 60 + endMinute) - (startHour * 60 + startMinute);

    if (totalMinutes > 0) {
      workHours.value = `${Math.floor(totalMinutes / 60)} horas ${totalMinutes % 60} minutos`;
    } else {
      workHours.value = "Horas inválidas";
    }
  } else {
    workHours.value = "";
  }
};

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

const confirmDeleteModal = ref(null);
const currentHorarioId = ref([]);

const abrirModalEliminar = (horarioId) => {
  // Verificar si el modal principal está activo y ocultarlo
  if (modalInstance.value) {
    modalInstance.value.hide();
  }

  // Inicializar el modal de confirmación si no está inicializado
  if (confirmDeleteModal.value) {
    confirmDeleteModal.value = new Modal(document.getElementById("confirmDeleteModal"), {
      backdrop: 'static', // Hacer que el backdrop sea estático
      keyboard: false,    // Evitar que se cierre con el teclado
    });
  }

  // Guardar el horario seleccionado y mostrar el modal de confirmación
  currentHorarioId.value = horarioId;
  confirmDeleteModal.value.show();
};

// Función para confirmar y eliminar
const confirmarEliminacion = async () => {
  if (!currentHorarioId.value || currentHorarioId.value.length === 0) {
    console.error("No se han seleccionado horarios para eliminar.");
    return;
  }

  try {
    for (const horarioId of currentHorarioId.value) {
      await horarioStore.eliminarHorario(horarioId); // Eliminar cada ID
      console.log(`Horario ${horarioId} eliminado exitosamente.`);
    }

    confirmDeleteModal.value.hide();
    fetchHorarios(); // Recargar los horarios
  } catch (error) {
    console.error("Error al eliminar los horarios:", error);
  }
};

const warningModal = ref(null);

const abrirModalModificar = () => {
  // Verificar si el modal principal está activo y ocultarlo
  if (modalInstance.value) {
    modalInstance.value.hide();
  }

  // Inicializar y mostrar el modal de advertencia
  if (warningModal.value) {
    warningModal.value = new Modal(document.getElementById("warningModal"), {
      backdrop: "static",
      keyboard: false,
    });
  }
  warningModal.value.show();
};

const confirmarModificacion = () => {
  // Llamar a saveSchedule y cerrar el modal de advertencia
  saveSchedule();
  warningModal.value.hide();
};

onMounted(() => {
  // Modal principal
  const scheduleModalElement = document.getElementById("scheduleModal");
  if (scheduleModalElement) {
    modalInstance.value = new Modal(scheduleModalElement);
  }

  // Modal de confirmación
  const confirmDeleteModalElement = document.getElementById("confirmDeleteModal");
  if (confirmDeleteModalElement) {
    confirmDeleteModal.value = new Modal(confirmDeleteModalElement);
  }

  const warningModalElement = document.getElementById("warningModal");
  if (warningModalElement) {
    warningModal.value = new Modal(warningModalElement);
  }

  horarioStore.fetchAreas(empresaId.value); 
  horarioStore.fetchColaboradores(empresaId.value)
    .then(() => {
      employees.value = horarioStore.colaboradores;
      fetchHorarios();
    })
    .catch((error) => {
      console.error("Error al obtener los colaboradores:", error);
    });
});

watch([selectedArea, selectedPuesto], () => {
  fetchHorarios();
});
</script>

<style scoped>
.cursor-pointer {
  cursor: pointer;
}
</style>

