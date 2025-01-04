<template>
  <div class="container vh-100 d-flex flex-column align-items-center justify-content-center">
    <reloj />
    <div class="card shadow border-0 rounded-3" style="width: 300px; overflow: hidden;">
      <mapa />
      <botones />
    </div>
    <horario />
  </div>
</template>
  
<script setup>
  import reloj from "./reloj.vue";
  import horario from "./horario.vue";
  import mapa from "./mapa.vue";
  import botones from "./botones.vue";
</script>
  