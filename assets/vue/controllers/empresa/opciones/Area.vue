<template>
  <div class="container">
    <div class="area">
      <!-- Título del área -->
      <div class="Ttl1 fw-bold">ÁREA Y PUESTO</div>
      <div class="linea-azul mb-3"></div>

      <!-- Agregar Área -->
      <div class="row mb-4">
        <div class="col-md-6">
          <div class="mb-3">
            <p class="fw-bold">Área</p>
            <input type="text" id="textoArea" v-model="nuevaArea" class="form-control" placeholder="Escribe el área..." />
          </div>
          <button @click="agregarArea" class="btn btn-primary">Agregar</button>
        </div>
        <div class="col-md-6 d-flex justify-content-center align-items-center">
          <div class="border p-3" style="width: 100%; height: 150px; overflow-y: auto;">
            <p v-if="storeAreas.length === 0" class="text-muted">No hay áreas agregadas.</p>
            <p v-for="(area, index) in storeAreas" :key="index" class="mb-1">{{ area.nombre }}</p>
          </div>
        </div>
      </div>

      <!-- Seleccionar área y agregar puesto -->
      <div class="row mb-4">
        <div class="col-md-6">
          <div class="mb-3">
            <p class="fw-bold">Seleccionar Área</p>
            <select id="selectOpciones" v-model="areaSeleccionada" class="form-select">
              <option value="">Selecciona una opción</option>
              <option v-for="area in storeAreas" :key="area.nombre" :value="area">{{ area.nombre }}</option>
            </select>
          </div>

          <div class="mb-3">
            <p class="fw-bold">Puesto</p>
            <input v-model="nuevoPuesto" class="form-control" type="text" id="inputPuesto" placeholder="Escribe el puesto..." />
          </div>

          <button @click="guardarPuesto" class="btn btn-primary">Agregar</button>
        </div>

        <div class="col-md-6 d-flex justify-content-center align-items-center">
          <div class="border p-3" style="width: 100%; height: 150px; overflow-y: auto;">
            <p v-if="!areaSeleccionada" class="text-muted">Selecciona un área para ver los puestos.</p>
            <p v-else-if="selectedAreaPuestos.length === 0" class="text-muted">No hay puestos agregados para esta área.</p>
            <p v-for="(puesto, index) in selectedAreaPuestos" :key="index" class="mb-1">{{ puesto.nombre }}</p>
          </div>
        </div>
      </div>

      <!-- Botón Guardar Empresa centrado y más sombrío -->
      <div class="d-flex justify-content-center">
        <button @click="guardarEmpresa" class="btn btn-primary btn-lg shadow-lg">Guardar</button>
      </div>

      <!-- Mensajes de éxito o error -->
      <div v-if="store.registroExitosoAreas" class="alert alert-success mt-3">{{ store.registroExitosoAreas }}</div>
      <div v-if="store.errorRegistroAreas" class="alert alert-danger mt-3">{{ store.errorRegistroAreas }}</div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { opcionesStore } from '../../../../store/empresa/opciones'; // Importar el store de Pinia

const store = opcionesStore();

// Variables reactivas
const nuevaArea = ref(''); // Input para nueva área
const areaSeleccionada = ref(null); // Área seleccionada para el puesto
const nuevoPuesto = ref(''); // Input para nuevo puesto

// Llamar a la API para obtener las áreas cuando el componente se monta
onMounted(() => {
  store.fetchAreas(); // Llamar a la acción del store que obtiene las áreas
});

// Computed para obtener las áreas desde el store
const storeAreas = computed(() => store.areas);

// Computed para obtener los puestos del área seleccionada
const selectedAreaPuestos = computed(() => {
  if (!areaSeleccionada.value) return [];
  return areaSeleccionada.value.puestos || [];
});

// Función para agregar un área
const agregarArea = () => {
  if (nuevaArea.value && !storeAreas.value.some(area => area.nombre === nuevaArea.value)) {
    storeAreas.value.push({ nombre: nuevaArea.value, puestos: [] });
    nuevaArea.value = ''; // Limpiar input
  }
};

// Función para agregar un puesto a un área seleccionada
const guardarPuesto = () => {
  if (areaSeleccionada.value && nuevoPuesto.value) {
    areaSeleccionada.value.puestos.push({ nombre: nuevoPuesto.value });
    nuevoPuesto.value = ''; // Limpiar input de puesto
  }
};

// Función para guardar todas las áreas y puestos
const guardarEmpresa = () => {
  store.registrarAreas(storeAreas.value); // Llamar a la acción del store para registrar las áreas y puestos
};
</script>

<style scoped>
/* Sin modificaciones adicionales de estilo, todo está basado en Bootstrap */
</style>








