<template>
    <div>
      <h3>SALIDA</h3>
      <p>Fecha: {{ salida.fecha }}</p>
      <p>Hora: {{ salida.hora }}</p>    
      <p>Usuario: {{ salida.usuario }}</p>
      <p>DNI: {{ salida.dni }}</p>
      <p>Modalidad: {{ salida.modalidad }}</p>
      <p>Estado: {{ salida.estado }}</p>
    </div>
  </template>
  
  <script setup>
  import { computed } from 'vue';
  import { asistenciaStore } from '../../../store/asistencia.js';
  
  const store = asistenciaStore();
  const salida = computed(() => store.SALIDA);
  
  </script>
  