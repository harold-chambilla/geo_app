<template>
    <div>
      <h3>ENTRADA</h3>
      <p>Entrada: {{ entrada.hora }}</p>
      <p>Fecha: {{ entrada.fecha }}</p>
      <p>Estado: {{ entrada.estado }}</p>
    </div>
  </template>
  
  <script setup>
  import { computed } from 'vue';
  import { asistenciaStore } from '../../../store/asistencia.js';
  
  const store = asistenciaStore();
  const entrada = computed(() => store.ENTRADA);

  </script>
  