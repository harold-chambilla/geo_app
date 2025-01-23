<template>
    <!-- Botones -->
    <div class="d-flex flex-column gap-3 p-3 text-center">
        <button class="btn btn-success fw-bold py-2" @click="marcarEntrada">
            ENTRADA
        </button>
        <button class="btn btn-danger fw-bold py-2" @click="marcarSalida">
            SALIDA
        </button>
    </div>
</template>
<script setup>
import { useMarcadoStore } from '@/store/colaborador/marcado';
import { ref, onMounted, computed } from "vue";

const marcadoStore = useMarcadoStore();

const horarioColaborador = computed(() => {
  return marcadoStore.horario;
});

// Funciones de marcar
const marcarEntrada = () => {
    console.log("Entrada marcada");
};

const marcarSalida = () => {
    console.log("Salida marcada");
};

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