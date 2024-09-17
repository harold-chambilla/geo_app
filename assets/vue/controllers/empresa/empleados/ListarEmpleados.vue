<template>
  <div class="container mt-4">
    <div v-if="error" class="alert alert-danger">
      {{ error }}
    </div>

    <div v-if="empleados.length === 0 && !error" class="alert alert-info">
      No hay empleados registrados.
    </div>

    <div v-if="empleados.length > 0" class="log-container">
      <p v-for="empleado in empleados" :key="empleado.id" class="log-entry">
        Empleado ID: {{ empleado.id }} - {{ empleado.nombres }} {{ empleado.apellidos }} creado exitosamente.
      </p>
    </div>
  </div>
</template>

<script setup>
import { onMounted, toRefs } from 'vue';
import { empleadosStore } from '../../../../store/empresa/empleados'; // Ruta corregida

const store = empleadosStore();
const { empleados, error } = toRefs(store);

onMounted(() => {
  store.fetchEmpleados();
});
</script>

<style scoped>
.log-container {
  border: 1px solid #ddd;
  padding: 15px;
  background-color: #f9f9f9;
  border-radius: 5px;
  max-height: 400px;
  overflow-y: auto;
}

.log-entry {
  font-family: 'Courier New', Courier, monospace;
  margin-bottom: 10px;
  padding: 10px;
  background-color: #e9ecef;
  border: 1px solid #ced4da;
  border-radius: 4px;
}
</style>



