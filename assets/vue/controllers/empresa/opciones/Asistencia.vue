<template>
  <div class="container">
    <div class="Ttl1 mb-1">ASISTENCIA</div>
    <div class="linea-azul mb-4"></div>
    <div class="row g-3">
      <div class="col-md-6">
        <div class="conten-asis">
          <div class="d-flex align-items-center mb-3">
            <span class="col-4 text-end pe-3">Tmp. de falta</span>
            <input type="number" class="form-control col-4" min="1" max="8" style="width: auto;"> horas
          </div>
          <div class="d-flex align-items-center mb-3">
            <span class="col-4 text-end pe-3">Tolerancia de ingreso</span>
            <input type="number" class="form-control col-4" min="10" max="120" style="width: auto;"> min.
          </div>
          <div class="d-flex align-items-center mb-3">
            <span class="col-4 text-end pe-3">Permitir foto</span>
            <div class="form-check form-check-inline">
              <input type="checkbox" class="form-check-input me-1">
              <span>Sí</span>
            </div>
            <div class="form-check form-check-inline">
              <input type="checkbox" class="form-check-input me-1">
              <span>No</span>
            </div>
          </div>
        </div>
      </div>

      <div class="col-md-6">
        <div class="conten mb-3" style="padding:10px">
          <span class="d-block mb-2">Permitir horas extra</span>
          <!-- Selector dinámico de áreas de la empresa -->
          <select v-model="selectedArea" class="form-select">
            <option value="" disabled>Selecciona un área</option>
            <option v-for="area in areas" :key="area.id" :value="area.nombre">
              {{ area.nombre }}
            </option>
          </select>
        </div>

        <!-- Botón "Agregar" centrado debajo de "Permitir horas extra" -->
        <div class="d-flex justify-content-center mb-3">
          <button @click="agregarArea" class="btn btn-primary">Agregar</button>
        </div>

        <!-- Rellenar recuadro con las áreas seleccionadas -->
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

    <!-- Centrar el botón "Guardar" al final -->
    <div class="row">
      <div class="col-12 d-flex justify-content-center mt-4">
        <button @click="guardarAreas" class="btn btn-primary btn-lg">Guardar</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import { opcionesStore } from '../../../../store/empresa/opciones';

// Usamos la tienda Pinia para manejar el estado de las áreas
const store = opcionesStore();
const areas = ref([]);
const selectedArea = ref('');
const areasSeleccionadas = ref([]);

// Función para agregar el área seleccionada a la lista de áreas seleccionadas
const agregarArea = () => {
  if (selectedArea.value && !areasSeleccionadas.value.includes(selectedArea.value)) {
    areasSeleccionadas.value.push(selectedArea.value);
    selectedArea.value = ''; // Resetear el select después de agregar
  }
};

// Función para eliminar un área de la lista de seleccionadas
const eliminarArea = (index) => {
  areasSeleccionadas.value.splice(index, 1);
};

// Función para guardar las áreas seleccionadas
const guardarAreas = () => {
  // Imprimir el array de áreas seleccionadas en la consola
  console.log("Áreas seleccionadas:", areasSeleccionadas.value);

  // Aquí puedes enviar las áreas seleccionadas a una API, almacenarlas en el localStorage, etc.
  // Ejemplo: enviar a una API con Axios
  /*
  axios.post('/api/guardar-areas', {
    areas: areasSeleccionadas.value
  }).then(response => {
    console.log("Áreas guardadas correctamente:", response.data);
  }).catch(error => {
    console.error("Error al guardar las áreas:", error);
  });
  */
};

// Cuando se monta el componente, obtenemos las áreas
onMounted(async () => {
  await store.fetchAreas();
  areas.value = store.areas;
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
</style>

