<template>
  <div class="container vh-100 d-flex flex-column align-items-center justify-content-center">
    <reloj />
    <div class="card shadow border-0 rounded-3" style="width: 300px; overflow: hidden;">
      <template v-if="isPresencial">
        <mapa />
      </template>
      <template v-else>
        <div class="d-flex flex-column align-items-center">
          <img :src="remoto" alt="Registro de entrada" class="img-fluid mb-2 mt-4" style="max-width: 100px;">
          <p class="fw-bold text-primary">
            ¿Ya registraste tu entrada o salida?
          </p>
          <p class="text-muted mx-2 text-center">
            En remoto, la puntualidad es la esencia del compromiso.
          </p>
        </div>
      </template>
      <botones />
    </div>
  </div>
</template>

<script setup>
import { useMarcadoStore } from '@/store/colaborador/marcado';
import { ref, onMounted, computed } from "vue";
import reloj from "./reloj.vue";
import mapa from "./mapa.vue";
import botones from "./botones.vue";
import remoto from "@img/colaborador/ico_remoto.png";

const marcadoStore = useMarcadoStore();

const horarioColaborador = computed(() => {
  return marcadoStore.horario;
});

const isPresencial = computed(() => {
  if (!horarioColaborador.value) return false;

  if (horarioColaborador.value.horario.hot_tipojornada && horarioColaborador.value.horario.hot_tipojornada.toLowerCase() === 'presencial') {
    return true;
  } else if (horarioColaborador.value.horario.hot_tipojornada && horarioColaborador.value.horario.hot_tipojornada.toLowerCase() === 'remoto') {
    return false;
  }

  if (horarioColaborador.value.configuracion_asistencia && horarioColaborador.value.configuracion_asistencia.cas_modalidad) {
    try {
      const modalidad = JSON.parse(horarioColaborador.value.configuracion_asistencia.cas_modalidad.replace(/'/g, '"'));
      return modalidad.includes("MOD_PRESENCIAL");
    } catch (error) {
      console.error("Error procesando la modalidad:", error);
      return false;
    }
  }

  return false;
});

onMounted(async () => {
  try {
    await marcadoStore.fetchHorario(1);
  } catch (error) {
    console.error('Error al obtener el horario:', error);
  }
});
</script>
  