<template>
  <div class="container">
    <div class="Ttl1 mb-1">ASISTENCIA</div>
    <div class="linea-azul mb-4"></div>
    <div class="row g-3">
      <div class="col-md-6">
        <!-- Inputs para la configuración de asistencia -->
        <div class="conten-asis">
          <div class="d-flex align-items-center mb-3">
            <span class="col-4 text-end pe-3">Tmp. de falta</span>
            <input v-model="configuracionAsistencia.tmpFalta" type="number" class="form-control col-4" min="1" max="8" style="width: auto;"> horas
          </div>
          <div class="d-flex align-items-center mb-3">
            <span class="col-4 text-end pe-3">Tolerancia de ingreso</span>
            <input v-model="configuracionAsistencia.toleranciaIngreso" type="number" class="form-control col-4" min="10" max="120" style="width: auto;"> min.
          </div>
          <div class="d-flex align-items-center mb-3">
            <span class="col-4 text-end pe-3">Permitir foto</span>
            <label class="switch">
              <input type="checkbox" v-model="configuracionAsistencia.permitirFoto">
              <span class="slider round"></span>
            </label>
          </div>
        </div>
      </div>

      <div class="col-md-6">
        <!-- Selector de áreas de la empresa -->
        <div class="conten mb-3" style="padding:10px">
          <span class="d-block mb-2">Permitir horas extra</span>
          <select v-model="selectedArea" class="form-select">
            <option value="" disabled>Selecciona un área</option>
            <option v-for="area in areas" :key="area.id" :value="area.nombre">
              {{ area.nombre }}
            </option>
          </select>
        </div>

        <!-- Botón "Agregar" para áreas seleccionadas -->
        <div class="d-flex justify-content-center mb-3">
          <button @click="agregarArea" class="btn btn-primary">Agregar</button>
        </div>

        <!-- Recuadro con áreas seleccionadas -->
        <div class="p-3" id="nuevoTextoGuardado" style="border: 1px solid #ccc; border-radius: 10px; width: 100%; max-width: 400px; background-color: #f9f9f9;">
          <p v-if="areasSeleccionadas.length === 0" class="text-center">No hay áreas seleccionadas</p>
          <div v-else>
            <div v-for="(area, index) in areasSeleccionadas" :key="index" class="area-item d-flex justify-content-between align-items-center mb-2">
              <span>{{ area }}</span>
              <button @click="eliminarArea(index)" class="btn-close" style="position: relative; right: 5px;"></button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Botón "Guardar" -->
    <div class="row">
      <div class="col-12 d-flex justify-content-center mt-4">
        <button @click="guardarTodo" class="btn btn-primary btn-lg">Guardar</button>
      </div>
    </div>

    <!-- Mensajes de éxito o error -->
    <div v-if="registroExitosoConfiguracion" class="alert alert-success mt-3">
      {{ registroExitosoConfiguracion }}
    </div>
    <div v-if="store.errorRegistroConfiguracion" class="alert alert-danger mt-3">
      {{ store.errorRegistroConfiguracion }}
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import { opcionesStore } from '../../../../store/empresa/opciones';

const store = opcionesStore();
const areas = ref([]);
const selectedArea = ref('');
const areasSeleccionadas = ref([]);
const configuracionAsistencia = ref({
  tmpFalta: null,
  toleranciaIngreso: null,
  permitirFoto: false,
});
const registroExitosoConfiguracion = ref(null);

// Función para obtener la configuración de asistencia
const fetchConfiguracionAsistencia = async () => {
  await store.fetchConfiguracionAsistencia();
  configuracionAsistencia.value = {
    tmpFalta: store.configuracionAsistencia.tiempo_falta_horas,
    toleranciaIngreso: store.configuracionAsistencia.tolerancia_ingreso_minutos,
    permitirFoto: store.configuracionAsistencia.permitir_foto,
  };
};

// Función para guardar la configuración de asistencia
const registrarConfiguracionAsistencia = async () => {
  const configuracion = {
    tiempo_falta_horas: configuracionAsistencia.value.tmpFalta,
    tolerancia_ingreso_minutos: configuracionAsistencia.value.toleranciaIngreso,
    permitir_foto: configuracionAsistencia.value.permitirFoto,
  };
  
  await store.registrarConfiguracionAsistencia(configuracion);
  registroExitosoConfiguracion.value = store.registroExitosoConfiguracion;
};

// Función para agregar un área a la lista de seleccionadas
const agregarArea = () => {
  if (selectedArea.value && !areasSeleccionadas.value.includes(selectedArea.value)) {
    areasSeleccionadas.value.push(selectedArea.value);
    selectedArea.value = '';
  }
};

// Función para eliminar un área de la lista de seleccionadas
const eliminarArea = (index) => {
  areasSeleccionadas.value.splice(index, 1);
};

// Función para guardar todo
const guardarTodo = async () => {
  // Guardar áreas seleccionadas
  console.log("Áreas seleccionadas:", areasSeleccionadas.value);

  // Guardar configuración de asistencia
  await registrarConfiguracionAsistencia();
};

// Obtener datos al montar el componente
onMounted(async () => {
  await store.fetchAreas();
  areas.value = store.areas;
  fetchConfiguracionAsistencia();
});
</script>

<style scoped>
.area-item {
  padding: 5px;
  border-bottom: 1px solid #ccc;
}

.area-item:last-child {
  border-bottom: none;
}

/* Estilos para el toggle switch */
.switch {
  position: relative;
  display: inline-block;
  width: 40px;
  height: 20px;
}

.switch input { 
  opacity: 0;
  width: 0;
  height: 0;
}

.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  transition: .4s;
  border-radius: 20px;
}

.slider:before {
  position: absolute;
  content: "";
  height: 14px;
  width: 14px;
  left: 3px;
  bottom: 3px;
  background-color: white;
  transition: .4s;
  border-radius: 50%;
}

input:checked + .slider {
  background-color: #2196F3;
}

input:checked + .slider:before {
  transform: translateX(20px);
}
</style>



