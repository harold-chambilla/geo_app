<template>
    <div>
      <Entrada v-if="entrada !== null && salida === null" />
      <Salida v-if="salida !== null && entrada !== null" />
    </div>
</template>
<script setup>
  import Entrada from './Entrada.vue';
  import Salida from './Salida.vue';
  import { onMounted, computed } from 'vue';
  import { asistenciaStore } from '../../../../../store/colaborador/asistencia';
  
  const store = asistenciaStore();
  const entrada = computed(() => store.ENTRADA);
  const salida = computed(() => store.SALIDA);
  
  onMounted(() => {
    store.GET_ASISTENCIA();
  });
</script>
  