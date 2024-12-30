<template>
    <div class="container vh-100 d-flex flex-column align-items-center justify-content-center">
      <reloj />
      <mapa />
      <horario />
    </div>
  </template>
  
<script setup>
  import reloj from "./reloj.vue";
  import horario from "./horario.vue";
  import mapa from "./mapa.vue";
</script>
  