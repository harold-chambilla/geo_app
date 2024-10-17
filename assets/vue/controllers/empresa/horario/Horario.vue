<template>
  <div class="CTN-principal">
    <div class="calendar-header">
      <button class="button-blanco" @click="prevMonth">Anterior</button>
      <span class="mes clickable" @click="openMonthAccordion">{{ capitalizedMonthYear }}</span>
      <button class="button-blanco" @click="nextMonth">Siguiente</button>
    </div>

    <div class="container-submenu-left">
      <div class="dropdown">
        <select v-model="selectedArea" @change="updatePuestos">
          <option value="">Selecciona un área</option>
          <option v-for="area in areas" :key="area.id" :value="area.id">
            {{ area.nombre }}
          </option>
        </select>
      </div>
      <div class="dropdown">
        <select v-model="selectedPuesto" @change="updateColaboradores">
          <option value="">Selecciona un puesto</option>
          <option v-for="puesto in puestos" :key="puesto.id" :value="puesto.id">
            {{ puesto.nombre }}
          </option>
        </select>
      </div>
    </div>

    <div class="calendar-body">
      <div class="table-container">
        <table class="calendar-table">
          <thead>
            <tr>
              <th class="header" colspan="2"></th>
              <th class="header semana domingo">Dom</th>
              <th class="header semana lunes">Lun</th>
              <th class="header semana martes">Mar</th>
              <th class="header semana miercoles">Mié</th>
              <th class="header semana jueves">Jue</th>
              <th class="header semana viernes">Vie</th>
              <th class="header semana sabado">Sáb</th>
            </tr>
          </thead>
          <tbody>
            <template v-for="(semana, semanaIndex) in calendarData" :key="'semana-'+semanaIndex">
              <tr>
                <td class="semana-header" colspan="2">Semana {{ semanaIndex + 1 }}</td>
                <td v-for="(dia, diaIndex) in semana" :key="'dia-'+diaIndex" class="dia-cell">
                  {{ dia || '' }}
                </td>
              </tr>
              <tr v-for="colaborador in filteredColaboradores" :key="'colaborador-'+colaborador.id+'-semana-'+semanaIndex">
                <td class="colaborador-cell" colspan="2">{{ colaborador.nombre }} {{ colaborador.apellidos }}</td>
                <td v-for="(dia, diaIndex) in semana" :key="'colaborador-'+colaborador.id+'-dia-'+diaIndex" class="libre clickable" @click="dia ? openDayAccordion(colaborador, dia) : null">
                  <span v-if="dia">Libre</span>
                </td>
              </tr>
            </template>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Accordion para edición mensual o diaria -->
    <div class="accordion mt-4" id="horarioAccordion" v-if="showAccordion">
      <div class="accordion-item">
        <h2 class="accordion-header" id="headingOne">
          <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
            {{ modalHeader }}
          </button>
        </h2>
        <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#horarioAccordion">
          <div class="accordion-body">
            <p>{{ modalSubheader }}</p>
            <div class="mb-3">
              <label for="ingreso" class="form-label">Hora de Ingreso:</label>
              <input type="time" v-model="ingreso" class="form-control" id="ingreso">
            </div>
            <div class="mb-3">
              <label for="salida" class="form-label">Hora de Salida:</label>
              <input type="time" v-model="salida" class="form-control" id="salida">
            </div>
            <div class="mb-3">
              <label for="modalidad" class="form-label">Modalidad de Trabajo:</label>
              <select v-model="modalidad" class="form-select" id="modalidad">
                <option value="Presencial">Presencial</option>
                <option value="Remoto">Remoto</option>
              </select>
            </div>
            <div class="form-check" v-if="isDayAccordion">
              <input type="checkbox" class="form-check-input" v-model="applyToAll" id="applyToAll">
              <label class="form-check-label" for="applyToAll">Aplicar a todo el personal</label>
            </div>
            <button class="btn btn-primary mt-3" @click="saveData">Aceptar</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useHorarioStore } from '../../../../store/empresa/horario';

const currentDate = ref(new Date());
const showAccordion = ref(false);
const modalHeader = ref('');
const modalSubheader = ref('');
const ingreso = ref('');
const salida = ref('');
const modalidad = ref('Presencial');
const applyToAll = ref(false);
const isDayAccordion = ref(false);
const selectedArea = ref('');
const selectedPuesto = ref('');
const areas = ref([]);
const puestos = ref([]);
const colaboradores = ref([]);

const horarioStore = useHorarioStore();

const capitalizedMonthYear = computed(() => {
  const options = { year: 'numeric', month: 'long' };
  const monthYear = currentDate.value.toLocaleDateString('es-ES', options);
  return monthYear.charAt(0).toUpperCase() + monthYear.slice(1);
});

const filteredColaboradores = computed(() => {
  if (!selectedArea.value) return colaboradores.value;
  const area = areas.value.find(area => area.id === selectedArea.value);
  if (!selectedPuesto.value) {
    return area ? area.puestos.flatMap(puesto => puesto.colaboradores) : [];
  }
  const puesto = area ? area.puestos.find(puesto => puesto.id === selectedPuesto.value) : null;
  return puesto ? puesto.colaboradores : [];
});

const calendarData = ref([]);
const generateCalendar = () => {
  const month = currentDate.value.getMonth();
  const year = currentDate.value.getFullYear();
  const firstDayOfMonth = new Date(year, month, 1).getDay();
  const daysInMonth = new Date(year, month + 1, 0).getDate();

  calendarData.value = [];
  let week = new Array(7).fill(null);

  for (let i = 1; i <= daysInMonth; i++) {
    const dayOfWeek = (firstDayOfMonth + i - 1) % 7;
    week[dayOfWeek] = i;

    if (dayOfWeek === 6 || i === daysInMonth) {
      calendarData.value.push(week);
      week = new Array(7).fill(null);
    }
  }
};

const prevMonth = () => {
  currentDate.value.setMonth(currentDate.value.getMonth() - 1);
  currentDate.value = new Date(currentDate.value);
  generateCalendar();
};

const nextMonth = () => {
  currentDate.value.setMonth(currentDate.value.getMonth() + 1);
  currentDate.value = new Date(currentDate.value);
  generateCalendar();
};

const scrollToAccordion = () => {
  const accordionElement = document.getElementById('horarioAccordion');
  if (accordionElement) {
    accordionElement.scrollIntoView({ behavior: 'smooth' });
  }
};

const openMonthAccordion = () => {
  isDayAccordion.value = false;
  modalHeader.value = `Ingresar horario para todos los empleados en ${capitalizedMonthYear.value}`;
  modalSubheader.value = capitalizedMonthYear.value;
  showAccordion.value = true;
  scrollToAccordion();
};

const openDayAccordion = (colaborador, dia) => {
  if (!dia) return;
  isDayAccordion.value = true;
  modalHeader.value = `Ingresar horario para ${colaborador.nombre} ${colaborador.apellidos} el día ${dia}`;
  modalSubheader.value = capitalizedMonthYear.value;
  showAccordion.value = true;
  scrollToAccordion();
};

const saveData = () => {
  console.log('Datos guardados:', { ingreso, salida, modalidad, applyToAll });
  showAccordion.value = false;
};

const updatePuestos = () => {
  const areaSeleccionada = areas.value.find(area => area.id === selectedArea.value);
  puestos.value = areaSeleccionada ? areaSeleccionada.puestos.filter(puesto => !puesto.eliminado) : [];
  colaboradores.value = [];
};

const updateColaboradores = () => {
  const puestoSeleccionado = puestos.value.find(puesto => puesto.id === selectedPuesto.value);
  colaboradores.value = puestoSeleccionado ? puestoSeleccionado.colaboradores : [];
};

onMounted(async () => {
  await horarioStore.fetchAreasEmpleados();
  areas.value = horarioStore.areas.filter(area => !area.eliminado);
  generateCalendar();
});
</script>

<style scoped>
.clickable {
  cursor: pointer;
  user-select: none;
}

.CTN-principal {
  width: 90%;
  max-width: 1200px;
  background-color: #fff;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
  border-radius: 10px;
  overflow: hidden;
  padding: 20px;
  margin: 20px auto;
}

.calendar-header {
  background-color: #007bff;
  color: #fff;
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 10px 20px;
}

.button-blanco {
  background-color: #f4f4f4;
  color: #8692A6;
  border: none;
  font-size: 16px;
  padding: 5px 10px;
  border-radius: 5px;
  cursor: pointer;
}

.button-blanco:hover {
  background-color: #f0f0f0;
}

.mes {
  color: #111111;
  font-size: 16px;
  padding: 5px 10px;
  font-weight: bold;
}

.container-submenu-left {
  display: flex;
  justify-content: flex-start;
  gap: 10px;
  margin-bottom: 20px;
  margin-top: 20px;
}

.dropdown select {
  padding: 5px 25px;
  border: 1px solid #ccd1d9;
  border-radius: 15px;
  background-color: #f5f7fa;
  cursor: pointer;
  font-weight: bold;
}

.calendar-body {
  margin-top: 20px;
}

.calendar-table {
  width: 100%;
  border-collapse: collapse;
  table-layout: fixed;
}

.calendar-table th,
.calendar-table td {
  padding: 10px;
  border: 1px solid #ddd;
  text-align: center;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.header {
  background-color: #007bff;
  color: #fff;
}

.semana-header {
  background-color: #f4f4f4;
  font-weight: bold;
}

.colaborador-cell {
  background-color: #fafafa;
  text-align: left;
  padding-left: 20px;
}

.dia-cell, .libre {
  min-width: 60px;
}

.libre {
  background-color: #f9f9f9;
}
</style>



