<template>
  <div class="container-fluid mt-4">
    <div class="card">
      <!-- Cabecera -->
      <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Configuración de Horarios</h4>
        <button class="btn btn-light" @click="resetearFormulario">Restablecer</button>
      </div>

      <!-- Contenido -->
      <div class="card-body">
        <!-- Filtros principales -->
        <div class="row mb-4">
          <div class="col-md-3">
            <label class="form-label">Selecciona el Área:</label>
            <select class="form-select" v-model="areaSeleccionada" @change="actualizarPuestos">
              <option value="" disabled>Seleccionar</option>
              <option v-for="area in areas" :key="area" :value="area">{{ area }}</option>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label">Selecciona el Puesto:</label>
            <select class="form-select" v-model="puestoSeleccionado" @change="actualizarEmpleados">
              <option value="" disabled>Seleccionar</option>
              <option v-for="puesto in puestosFiltrados" :key="puesto" :value="puesto">{{ puesto }}</option>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label">Selecciona la Sede:</label>
            <select class="form-select" v-model="sedeSeleccionada">
              <option value="" disabled>Seleccionar</option>
              <option v-for="sede in sedes" :key="sede" :value="sede">{{ sede }}</option>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label">Selecciona un Empleado:</label>
            <select class="form-select" v-model="empleadoSeleccionado">
              <option value="" disabled>Seleccionar</option>
              <option v-for="empleado in empleadosFiltrados" :key="empleado.id" :value="empleado.id">
                {{ empleado.nombre }}
              </option>
            </select>
          </div>
        </div>

        <!-- Opciones de configuración -->
        <div class="accordion" id="accordionHorario">
          <!-- Configuración por Mes -->
          <div class="accordion-item">
            <h2 class="accordion-header" id="headingMes">
              <button
                class="accordion-button"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#collapseMes"
                aria-expanded="true"
                aria-controls="collapseMes"
              >
                Configurar horario para todo el mes
              </button>
            </h2>
            <div
              id="collapseMes"
              class="accordion-collapse collapse show"
              aria-labelledby="headingMes"
              data-bs-parent="#accordionHorario"
            >
              <div class="accordion-body">
                <form @submit.prevent="asignarHorario('mes')">
                  <div class="row mb-3">
                    <div class="col-md-4">
                      <label class="form-label">Mes:</label>
                      <select class="form-select" v-model="mesSeleccionado">
                        <option value="" disabled>Seleccionar</option>
                        <option v-for="(mes, index) in meses" :key="index" :value="index">
                          {{ mes }}
                        </option>
                      </select>
                    </div>
                    <div class="col-md-4">
                      <label class="form-label">Hora de Ingreso:</label>
                      <input type="time" class="form-control" v-model="horaIngreso" required />
                    </div>
                    <div class="col-md-4">
                      <label class="form-label">Hora de Salida:</label>
                      <input type="time" class="form-control" v-model="horaSalida" required />
                    </div>
                  </div>
                  <div class="text-end">
                    <button type="submit" class="btn btn-primary">Asignar</button>
                  </div>
                </form>
              </div>
            </div>
          </div>

          <!-- Configuración por Semana -->
          <div class="accordion-item">
            <h2 class="accordion-header" id="headingSemana">
              <button
                class="accordion-button collapsed"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#collapseSemana"
                aria-expanded="false"
                aria-controls="collapseSemana"
              >
                Configurar horario por semana
              </button>
            </h2>
            <div
              id="collapseSemana"
              class="accordion-collapse collapse"
              aria-labelledby="headingSemana"
              data-bs-parent="#accordionHorario"
            >
              <div class="accordion-body">
                <form @submit.prevent="asignarHorario('semana')">
                  <div class="row mb-3">
                    <div class="col-md-6">
                      <label class="form-label">Selecciona la Semana:</label>
                      <select class="form-select" v-model="semanaSeleccionada">
                        <option value="" disabled>Seleccionar</option>
                        <option v-for="semana in semanas" :key="semana" :value="semana">
                          Semana {{ semana }}
                        </option>
                      </select>
                    </div>
                    <div class="col-md-3">
                      <label class="form-label">Hora de Ingreso:</label>
                      <input type="time" class="form-control" v-model="horaIngreso" required />
                    </div>
                    <div class="col-md-3">
                      <label class="form-label">Hora de Salida:</label>
                      <input type="time" class="form-control" v-model="horaSalida" required />
                    </div>
                  </div>
                  <div class="text-end">
                    <button type="submit" class="btn btn-primary">Asignar</button>
                  </div>
                </form>
              </div>
            </div>
          </div>

          <!-- Configuración por Día -->
          <div class="accordion-item">
            <h2 class="accordion-header" id="headingDia">
              <button
                class="accordion-button collapsed"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#collapseDia"
                aria-expanded="false"
                aria-controls="collapseDia"
              >
                Configurar horario por día
              </button>
            </h2>
            <div
              id="collapseDia"
              class="accordion-collapse collapse"
              aria-labelledby="headingDia"
              data-bs-parent="#accordionHorario"
            >
              <div class="accordion-body">
                <form @submit.prevent="asignarHorario('dia')">
                  <div class="row mb-3">
                    <div class="col-md-4">
                      <label class="form-label">Fecha:</label>
                      <input type="date" class="form-control" v-model="fechaSeleccionada" required />
                    </div>
                    <div class="col-md-4">
                      <label class="form-label">Hora de Ingreso:</label>
                      <input type="time" class="form-control" v-model="horaIngreso" required />
                    </div>
                    <div class="col-md-4">
                      <label class="form-label">Hora de Salida:</label>
                      <input type="time" class="form-control" v-model="horaSalida" required />
                    </div>
                  </div>
                  <div class="text-end">
                    <button type="submit" class="btn btn-primary">Asignar</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue";

// Datos iniciales
const areas = ["Administración", "Recursos Humanos", "Tecnología"];
const sedes = ["Sede Central", "Sucursal 1", "Sucursal 2"];
const puestos = {
  Administración: ["Analista", "Gerente"],
  "Recursos Humanos": ["Coordinador", "Asistente"],
  Tecnología: ["Desarrollador", "Soporte Técnico"],
};
const empleados = {
  Analista: [{ id: 1, nombre: "Empleado 1" }, { id: 2, nombre: "Empleado 2" }],
  Gerente: [{ id: 3, nombre: "Gerente 1" }],
  Coordinador: [{ id: 4, nombre: "Coordinador 1" }],
  Asistente: [{ id: 5, nombre: "Asistente 1" }],
  Desarrollador: [{ id: 6, nombre: "Dev 1" }, { id: 7, nombre: "Dev 2" }],
  "Soporte Técnico": [{ id: 8, nombre: "Soporte 1" }],
};

const meses = [
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
const semanas = [1, 2, 3, 4, 5];

// Variables reactivas
const areaSeleccionada = ref("");
const puestoSeleccionado = ref("");
const sedeSeleccionada = ref("");
const empleadoSeleccionado = ref("");

const mesSeleccionado = ref(new Date().getMonth());
const semanaSeleccionada = ref("");
const fechaSeleccionada = ref("");

const horaIngreso = ref("");
const horaSalida = ref("");

const puestosFiltrados = ref([]);
const empleadosFiltrados = ref([]);

// Métodos
const actualizarPuestos = () => {
  puestosFiltrados.value = puestos[areaSeleccionada.value] || [];
  puestoSeleccionado.value = "";
  empleadosFiltrados.value = [];
};

const actualizarEmpleados = () => {
  empleadosFiltrados.value = empleados[puestoSeleccionado.value] || [];
};

const asignarHorario = (modo) => {
  console.log(`Asignando horario para ${modo}`);
  console.log({
    area: areaSeleccionada.value,
    puesto: puestoSeleccionado.value,
    sede: sedeSeleccionada.value,
    empleado: empleadoSeleccionado.value,
    horaIngreso: horaIngreso.value,
    horaSalida: horaSalida.value,
    mes: mesSeleccionado.value,
    semana: semanaSeleccionada.value,
    fecha: fechaSeleccionada.value,
  });
  alert(`Horario asignado para ${modo}`);
};

const resetearFormulario = () => {
  areaSeleccionada.value = "";
  puestoSeleccionado.value = "";
  sedeSeleccionada.value = "";
  empleadoSeleccionado.value = "";
  horaIngreso.value = "";
  horaSalida.value = "";
  mesSeleccionado.value = new Date().getMonth();
  semanaSeleccionada.value = "";
  fechaSeleccionada.value = "";
};
</script>
