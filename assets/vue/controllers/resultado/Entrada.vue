<template>
    <div>
      <h3>ENTRADA</h3>
      <p>Fecha: {{ entrada.fecha }}</p>
      <p>Hora: {{ entrada.hora }}</p>    
      <p>Usuario: {{ entrada.usuario }}</p>
      <p>DNI: {{ entrada.dni }}</p>
      <p>Modalidad: {{ entrada.modalidad }}</p>
      <p>Estado: {{ entrada.estado }}</p>
      <p></p>
    </div>
  </template>
  
  <script setup>
  import { computed } from 'vue';
  import { asistenciaStore } from '../../../store/asistencia.js';
  
  const store = asistenciaStore();
  const entrada = computed(() => store.ENTRADA);

  </script>
  