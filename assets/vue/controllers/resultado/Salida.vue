<template>
    <div>
      <h3>SALIDA</h3>
      <p>Salida: {{ salida.hora }}</p>
      <p>Fecha: {{ salida.fecha }}</p>
      <p>Estado: {{ salida.estado }}</p>
    </div>
  </template>
  
  <script setup>
  import { computed } from 'vue';
  import { asistenciaStore } from '../../../store/asistencia.js';
  
  const store = asistenciaStore();
  const salida = computed(() => store.SALIDA);
  
  </script>
  