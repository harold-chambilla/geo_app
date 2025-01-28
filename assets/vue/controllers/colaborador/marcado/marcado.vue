<template>
  <div class="container vh-100 d-flex flex-column align-items-center justify-content-center">
    <reloj />

    <div v-if="horarioDisponible" class="card shadow border-0 rounded-3" style="width: 300px; overflow: hidden;">
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

    <div v-else class="card shadow border-0 rounded-3 p-4 text-center" style="width: 300px;">
      <div class="d-flex justify-content-center">
        <img src="@img/colaborador/descanso.png" alt="Día libre" class="img-fluid mb-3" style="max-width: 80px;">
      </div>
      <p class="fw-bold text-danger">¡Hoy no trabajas! 🎉</p>
      <p class="text-muted">Aprovecha el día, relájate o haz algo divertido. 😎☕</p>
      <button class="btn btn-primary mt-2" @click="mostrarSugerencia">
        ¿Qué puedo hacer hoy? 🤔
      </button>
      <p v-if="sugerencia" class="mt-3 text-muted">{{ sugerencia }}</p>
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

const horarioColaborador = computed(() => marcadoStore.horario);

// Verifica si el colaborador tiene horario asignado
const horarioDisponible = computed(() => {
  return horarioColaborador.value &&
         horarioColaborador.value.horario &&
         horarioColaborador.value.horario.hot_fecha;
});

// Determina si es presencial o remoto
const isPresencial = computed(() => {
  if (!horarioDisponible.value) return false;

  if (horarioColaborador.value.horario.hot_tipojornada) {
    return horarioColaborador.value.horario.hot_tipojornada.toLowerCase() === 'presencial';
  }

  if (horarioColaborador.value.configuracion_asistencia?.cas_modalidad) {
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

const sugerencia = ref("");

const mostrarSugerencia = () => {
  const opciones = [
    "¡Maratón de tu serie favorita! 📺🍿",
    "Un paseo al parque suena bien. 🌳🚶",
    "Hora de probar una nueva receta. 🍔👨‍🍳",
    "Día perfecto para un poco de ejercicio. 🏋️‍♂️🏃",
    "Un libro y un café, el plan ideal. 📖☕",
    "Llama a ese amigo que no ves hace tiempo. 📞👫",
    "¡Haz nada y disfruta de tu descanso! 😆💆‍♂️"
  ];
  sugerencia.value = opciones[Math.floor(Math.random() * opciones.length)];
};

onMounted(async () => {
  try {
    await marcadoStore.fetchHorario(1);
  } catch (error) {
    console.error('Error al obtener el horario:', error);
  }
});
</script>