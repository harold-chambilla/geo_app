<template>
    <!-- Botones -->
    <div class="d-flex flex-column gap-3 p-3 text-center">
        <button class="btn btn-success fw-bold py-2" @click="marcarEntrada" :disabled="isDisabledEntrada">
            ENTRADA
        </button>
        <button class="btn btn-danger fw-bold py-2" @click="marcarSalida" :disabled="isDisabledSalida">
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
const distanciaSede = computed(() => { return marcadoStore.distanciaSede; })
const asistencias = computed(() => { return marcadoStore.asistencias; })
const horaActual = computed(() => { return marcadoStore.horaActual; })
const exactitudRadio = ref(null);

// Funciones de marcar
const marcarEntrada = () => {
  console.log("Intentando marcar entrada...", asistencias.value);

  if (
    marcadoStore.getAsistencias.length === 0 ||
    (marcadoStore.getAsistencias.length === 1 && marcadoStore.getAsistencias[0].error)
  ) 
  {
    console.log("No se encontraron asistencias, procediendo con el registro...");
    const asistenciaData = {
      colaborador_id: 1,
      asi_fechaentrada: null,
      asi_fechasalida: null,
      asi_horaentrada: horaActual,
      asi_horasalida: null,
      asi_fotoentrada: null,
      asi_fotosalida: null,
      asi_ubicacionentrada: null,
      asi_ubicacionsalida: null,
      asi_estadoentrada: null,
      asi_estadosalida: null,
      asi_notas: null,
      asi_eliminado: false,
    };

    try {
      const response = marcadoStore.crearAsistencia(asistenciaData);
      console.log("Entrada registrada exitosamente:", response);
    } catch (error) {
      console.error("Error al registrar la asistencia:", error);
    }
  } else {
    console.log("Ya existe una asistencia registrada para hoy.");
  }
};

const marcarSalida = async () => {
    
};

const isDisabledEntrada = computed(() => {
  if (marcadoStore.getAsistencias.length === 0) {
    return false;
  }
  if (marcadoStore.getAsistencias.length === 1 && marcadoStore.getAsistencias[0].error) {
    return false;
  }
  return true;
});

const isDisabledSalida = computed(() => {
  const asistenciasValidas = marcadoStore.getAsistencias.filter(asistencia => !asistencia.error);
  return asistenciasValidas.length === 0 || asistenciasValidas[0].asi_fechasalida !== null;
});

onMounted(async () => {
  try {
    await marcadoStore.obtenerAsistencia({
      colaborador_id: 1,
      fecha: horarioColaborador.value.horario.hot_fecha
    });
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