<template>
  <div>
    <div class="Ttl1 fw-bold">PERMISOS</div>
    <div class="linea-azul mb-3"></div>
    <div class="row mb-4">
      <div class="col-md-6 d-flex justify-content-center align-items-center">
        <div class="border p-3 w-100" style="height: 150px; overflow-y: auto;" id="nuevoTextoGuardado">
          <!-- Mostrar los motivos agregados -->
          <p v-if="storeMotivos.length === 0" class="text-muted">No hay motivos agregados.</p>
          <p v-for="(motivo, index) in storeMotivos" :key="index">{{ motivo }}</p>
        </div>
      </div>

      <div class="col-md-6">
        <div class="mb-3">
          <p class="fw-bold">Motivo</p>
          <input type="text" v-model="nuevoMotivo" placeholder="Escribe el motivo" class="form-control" />
        </div>
        <button @click="agregarMotivo" class="btn btn-primary">Agregar</button>
      </div>
    </div>

    <!-- Mostrar mensaje de éxito o error -->
    <div v-if="store.registroExitosoMotivos" class="alert alert-success mt-3">{{ store.registroExitosoMotivos }}</div>
    <div v-if="store.errorRegistroMotivos" class="alert alert-danger mt-3">{{ store.errorRegistroMotivos }}</div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { opcionesStore } from '../../../../store/empresa/opciones'; // Ruta del store de Pinia

const store = opcionesStore();

// Variables reactivas
const nuevoMotivo = ref(''); // Para el motivo que se ingresa

// Computed para obtener los motivos desde el store
const storeMotivos = computed(() => store.motivos);

// Función para agregar el motivo localmente y enviarlo al servidor
const agregarMotivo = async () => {
  if (nuevoMotivo.value.trim() !== '') {
    // Agregar el motivo localmente para que se muestre inmediatamente en el cuadro
    storeMotivos.value.push(nuevoMotivo.value);

    // Enviar el motivo al servidor para registrarlo en la base de datos
    await store.registrarMotivos([nuevoMotivo.value]);

    // Limpiar el campo de entrada
    nuevoMotivo.value = '';
  }
};

// Llamar a la API para obtener los motivos cuando el componente se monta
onMounted(() => {
  store.fetchMotivos(); // Obtener los motivos desde el servidor
});
</script>

<style scoped>
/* Puedes agregar estilos personalizados si es necesario */
</style>






