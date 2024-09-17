<template>
  <div class="container">
    <!-- Campo RUC -->
    <div class="mb-3">
      <p class="fw-bold">RUC</p> <!-- Usamos un parágrafo con fuente negrita para el título -->
      <div class="d-flex">
        <input 
          type="text" 
          class="form-control me-2" 
          :value="ruc || 'Cargando...'" 
          readonly
        />
      </div>
    </div>

    <!-- Campo Razón Social -->
    <div class="mb-3">
      <p class="fw-bold">Razón social</p> <!-- Usamos un parágrafo con fuente negrita para el título -->
      <div class="d-flex">
        <input 
          type="text" 
          class="form-control me-2" 
          :value="razonSocial || 'Cargando...'" 
          readonly
        />
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, toRefs } from 'vue';
import { opcionesStore } from '../../../../store/empresa/opciones'; // Ajustar la ruta según sea necesario

const store = opcionesStore();
const { ruc, razonSocial } = toRefs(store);

// Al montar el componente, cargar los datos
onMounted(() => {
  store.fetchRuc();
  store.fetchRazonSocial();
});
</script>

<style scoped>
/* Sin modificaciones adicionales de estilo, todo está basado en Bootstrap */
</style>







