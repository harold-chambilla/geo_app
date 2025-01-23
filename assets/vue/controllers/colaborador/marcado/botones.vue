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
import { ref, onMounted, computed, watch, onUnmounted } from "vue";

const marcadoStore = useMarcadoStore();

const horarioColaborador = computed(() => { return marcadoStore.horario; });

const sede = computed(() => { return marcadoStore.sede; });

const ubicacion = computed(() => { return marcadoStore.ubicacion; });

// Funciones de marcar
const marcarEntrada = () => {
    console.log("Entrada marcada");
};

const marcarSalida = () => {
    console.log("Salida marcada");
};

onMounted(async () => {
  try {
    console.log("Ubicacion", ubicacion.value);

  } catch (error) {
    console.error('Error al obtener el horario:', error);
  }
});
</script>