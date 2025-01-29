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

    <!-- Modal de Confirmación -->
    <div class="modal fade" id="confirmacionModal" tabindex="-1" aria-labelledby="confirmacionModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center p-4">

          <!-- Sección del Icono -->
          <div class="d-flex justify-content-center">
            <img :src="iconoEstado" :alt="tituloModal" class="img-fluid rounded-circle border p-2 bg-light" style="width: 100px;">
          </div>

          <!-- Estado de Asistencia -->
          <h5 class="fw-bold mt-3" :class="colorEstado">{{ tituloModal }}</h5>
          <p class="text-muted">{{ mensajeModal }}</p>

          <!-- Información de Entrada y Salida -->
          <div class="list-group list-group-flush border rounded-3 shadow-sm">
            <div class="list-group-item">
              <i class="bi bi-calendar-event text-primary"></i> <strong>Fecha:</strong> {{ asistenciaData.asi_fechaentrada || asistenciaData.asi_fechasalida }}
            </div>
            <div class="list-group-item">
              <i class="bi bi-clock text-primary"></i> <strong>Hora:</strong> {{ asistenciaData.asi_horaentrada || asistenciaData.asi_horasalida }}
            </div>
            <div v-if="(textoUbicacion && textoUbicacion !== 'No corresponde') || 
                        (asistenciaData.asi_ubicacionentrada && asistenciaData.asi_ubicacionentrada !== 'No corresponde') || 
                        (asistenciaData.asi_ubicacionsalida && asistenciaData.asi_ubicacionsalida !== 'No corresponde')"
                class="list-group-item text-wrap text-break">
              <i class="bi bi-geo-alt text-danger"></i> <strong>Ubicación:</strong> 
              <span class="d-block text-justify">{{ textoUbicacion }}</span>
            </div>
          </div>

          <!-- Botón de Cierre -->
          <div class="modal-footer border-0 d-flex justify-content-center">
            <button type="button" class="btn btn-primary px-4 py-2" data-bs-dismiss="modal">
              <i class="bi bi-check-circle"></i> Aceptar
            </button>
          </div>

        </div>
      </div>
    </div>
</template>
<script setup>
import { Modal } from "bootstrap";
import { useMarcadoStore } from '@/store/colaborador/marcado';
import { ref, onMounted, computed, watch, onUnmounted } from "vue";
import puntualidad from "@img/colaborador/ico_puntualidad.png";
import tardanza from "@img/colaborador/ico_tardanza.png";
import accesoDenegado from "@img/colaborador/ico_denegado.png";

const marcadoStore = useMarcadoStore();

const horarioColaborador = computed(() => { return marcadoStore.horario; });
const sede = computed(() => { return marcadoStore.sede; });
const ubicacion = computed(() => { return marcadoStore.ubicacion; });
const distanciaSede = computed(() => { return marcadoStore.distanciaSede; })
const asistencias = computed(() => { return marcadoStore.asistencias; })
const horaActual = computed(() => { return marcadoStore.horaActual; })
const exactitudRadio = ref(null);

const tituloModal = ref("");
const iconoEstado = ref("");
const colorEstado = ref("");
const textoUbicacion = ref("");

const asistenciaData = ref({
  colaborador_id: null,
  asi_fechaentrada: null,
  asi_horaentrada: null,
  asi_fechasalida: null,
  asi_horasalida: null,
  asi_fotoentrada: null,
  asi_fotosalida: null,
  asi_ubicacionentrada: null,
  asi_ubicacionsalida: null,
  asi_estadoentrada: null,
  asi_estadosalida: null,
  asi_notas: '',
  asi_eliminado: false
});

const convertirHoraA24 = (hora) => {
  // Normaliza los espacios y convierte a minúsculas
  const normalizedHora = hora.replace(/\s+/g, ' ').trim().toLowerCase();

  // Detecta si es AM o PM
  let isPM = normalizedHora.includes("p. m.") || normalizedHora.includes("p.m.");
  let isAM = normalizedHora.includes("a. m.") || normalizedHora.includes("a.m.");

  // Extrae la parte de la hora sin el AM/PM
  let timePart = normalizedHora.replace("p. m.", "").replace("p.m.", "").replace("a. m.", "").replace("a.m.", "").trim();
  
  // Separa horas y minutos
  let [hours, minutes] = timePart.split(':').map(Number);

  // Conversión a formato 24 horas
  if (isPM && hours < 12) {
    hours += 12;
  } else if (isAM && hours === 12) {
    hours = 0;
  }

  return `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:00`;
};

const marcarEntrada = async () => {
  try {
    asistenciaData.value.colaborador_id = 1;
    asistenciaData.value.asi_fechaentrada = horarioColaborador.value.horario.hot_fecha;
    asistenciaData.value.asi_horaentrada = convertirHoraA24(horaActual.value);

    const horaEntrada = new Date(`1970-01-01T${horarioColaborador.value.horario.hot_entrada}:00`);
    const horaActualAux = new Date(`1970-01-01T${asistenciaData.value.asi_horaentrada}:00`);
    const toleranciaMinutos = horarioColaborador.value.configuracion_asistencia.cas_tolerancia_ingreso_minutos;
    const horaTolerancia = new Date(horaEntrada.getTime() + toleranciaMinutos * 60 * 1000);
    textoUbicacion.value = ubicacion.value.latitud && ubicacion.value.longitud ? `Lat: ${ubicacion.value.latitud}, Lng: ${ubicacion.value.longitud}` : 'Ubicación no disponible.';

    if (horaActualAux <= horaTolerancia) {
      asistenciaData.value.asi_estadoentrada = 'puntual';
      tituloModal.value = "Puntual";
      colorEstado.value = "text-success";
      iconoEstado.value = puntualidad;
    } else {
      asistenciaData.value.asi_estadoentrada = 'tardanza';
      tituloModal.value = "Tardanza";
      colorEstado.value = "text-warning";
      iconoEstado.value = tardanza;
    }

    if (horarioColaborador.value.horario.hot_tipojornada === 'presencial') {
      if (!distanciaSede.value.dentroRadio) {
        tituloModal.value = "Acceso Denegado";
        colorEstado.value = "text-danger";
        textoUbicacion.value = "No estás en la ubicación requerida 📍❌";
        iconoEstado.value = accesoDenegado;
        console.error('El colaborador no está dentro del lugar requerido para jornada presencial.');
        const modal = new Modal(document.getElementById("confirmacionModal"));
        modal.show();
        return;
      }
      asistenciaData.value.asi_fotoentrada = 'No configurado';
      asistenciaData.value.asi_ubicacionentrada = JSON.stringify(ubicacion.value);
    } else if (horarioColaborador.value.horario.hot_tipojornada === 'remoto') {
      asistenciaData.value.asi_fotoentrada = 'No corresponde';
      asistenciaData.value.asi_ubicacionentrada = 'No corresponde';
    }

    asistenciaData.value.asi_notas = 'Entrada registrada correctamente';

    await marcadoStore.crearAsistencia(asistenciaData.value);

    const modal = new Modal(document.getElementById("confirmacionModal"));
    modal.show();
  } catch (err) {
    tituloModal.value = "Error";
    colorEstado.value = "text-danger";
    iconoEstado.value = accesoDenegado;
    textoUbicacion.value = "Hubo un problema al registrar la asistencia ❌";
    console.error('Error al registrar la asistencia:', err.response?.data || err.message);
    const modal = new Modal(document.getElementById("confirmacionModal"));
    modal.show();
  }
};

const marcarSalida = async () => {
  try {
    if (!marcadoStore.asistencias.length) {
      console.error("No hay registros de entrada para actualizar.");
      return;
    }

    // Obtener la última asistencia registrada
    const asistencia = asistencias.value[marcadoStore.asistencias.length - 1];

    if (!asistencia.id) {
      console.error("El registro de asistencia no tiene un ID válido.");
      return;
    }

    if (horarioColaborador.value.horario.hot_tipojornada === 'presencial' && !distanciaSede.value.dentroRadio) {
      tituloModal.value = "Acceso Denegado";
      colorEstado.value = "text-danger";
      textoUbicacion.value = "No estás en la ubicación requerida 📍❌";
      iconoEstado.value = accesoDenegado;
      console.error("El colaborador no está dentro del lugar requerido para cerrar su asistencia en una jornada presencial.");
      const modal = new Modal(document.getElementById("confirmacionModal"));
      modal.show();
      return;
    }

    const ahora = new Date();
    const horaSalida = convertirHoraA24(horaActual.value);
    const horaSalidaPermitida = new Date(`1970-01-01T${horarioColaborador.value.horario.hot_salida}:00`);
    const estadoSalida = ahora <= horaSalidaPermitida ? 'a_tiempo' : 'sobre_hora';
    textoUbicacion.value = ubicacion.value.latitud && ubicacion.value.longitud
      ? `Lat: ${ubicacion.value.latitud}, Lng: ${ubicacion.value.longitud}`
      : 'Ubicación no disponible.';

    const asistenciaUpdate = {  
      asi_fechasalida: horarioColaborador.value.horario.hot_fecha,
      asi_horasalida: horaSalida,
      asi_estadosalida: estadoSalida,
    };

    Object.assign(asistenciaData.value, {  
      asi_fechasalida: horarioColaborador.value.horario.hot_fecha,
      asi_horasalida: horaSalida,
      asi_estadosalida: estadoSalida,
    });

    if (estadoSalida === 'a_tiempo') {
      tituloModal.value = "Salida a Tiempo";
      colorEstado.value = "text-primary";
      iconoEstado.value = puntualidad;
    } else {
      tituloModal.value = "Salida Sobre la Hora";
      colorEstado.value = "text-warning";
      iconoEstado.value = tardanza;
    }

    if (horarioColaborador.value.horario.hot_tipojornada === 'presencial') {
      asistenciaUpdate.asi_fotosalida = 'No configurado';
      asistenciaUpdate.asi_ubicacionsalida = JSON.stringify(ubicacion.value);
      Object.assign(asistenciaData.value, {
        asi_fotosalida: 'No configurado',
        asi_ubicacionsalida: JSON.stringify(ubicacion.value),
      });
    } else if (horarioColaborador.value.horario.hot_tipojornada === 'remoto') {
      asistenciaUpdate.asi_fotosalida = 'No corresponde';
      asistenciaUpdate.asi_ubicacionsalida = 'No corresponde';
      Object.assign(asistenciaData.value, {
        asi_fotosalida: 'No corresponde',
        asi_ubicacionsalida: 'No corresponde',
      });
    }

    await marcadoStore.actualizarAsistencia(asistencia.id, asistenciaUpdate);

    const modal = new Modal(document.getElementById("confirmacionModal"));
    modal.show();
  } catch (err) {
    console.error('Error al registrar la salida:', err.response?.data || err.message);
  }
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
    console.log(asistencias.value);
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