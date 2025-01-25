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
const distanciaSede = computed(() => { return marcadoStore.distanciaSede; });
const exactitudRadio = ref(null);

// Funciones de marcar
const marcarEntrada = () => {
    console.log("Entrada marcada");
};

const marcarSalida = () => {
    console.log("Salida marcada");
};

onMounted(async () => {
  try {
    console.log("Distancia", distanciaSede.value);

  } catch (error) {
    console.error('Error al obtener el horario:', error);
  }
});

watch(ubicacion.exactitud, (newValue) => {
  if (newValue >= 50 && newValue <= 200) {
    exactitudRadio.value = 'Exactitud baja.';
  } else if (newValue >= 1 && newValue <= 50) {
    exactitudRadio.value = 'Exactitud alta.';
  } else {
    exactitudRadio.value = 'Exactitud inaceptable. Por favor, actualiza la ubicación.';
  }
});
</script>