<template>
  <div>
    <h2>Datos de Asistencia</h2>
    <div v-if="entrada !== null && salida === null">
      <p>ENTRADA</p>
      <p>Entrada: {{ store.entrada.hora }}</p>
      <p>Entrada: {{ store.entrada.fecha }}</p>
      <p>Entrada: {{ store.entrada.estado }}</p>
    </div>
    <div v-if="salida !== null && entrada !== null">
      <p>SALIDA</p>
      <p>Salida: {{ store.salida.hora }}</p>
      <p>Salida: {{ store.salida.fecha }}</p>
      <p>Salida: {{ store.salida.estado }}</p>
    </div>
  </div>
</template>

<script setup>
import { onMounted, computed } from 'vue';
import { asistenciaStore } from '../../store/asistencia';

const store = asistenciaStore();

const entrada = computed(() => { return store.ENTRADA });
const salida = computed(() => { return store.SALIDA });
// Al montar el componente, obtener la asistencia
onMounted(() => {
  store.GET_ASISTENCIA();
});
</script>
