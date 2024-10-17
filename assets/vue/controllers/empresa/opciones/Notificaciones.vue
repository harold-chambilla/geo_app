<template>
  <div class="notificaciones">
    <div class="Ttl1 fw-bold">NOTIFICACIONES</div>
    <div class="linea-azul mb-3"></div>

    <div class="opcion">
      <div class="d-flex align-items-center" style="padding: 20px;">
        <input type="checkbox" v-model="notificaciones.faltas_tardanzas" id="faltasTardanzas" class="me-2">
        <p class="mb-0">Envío por correo de faltas y tardanzas diaria</p>
      </div>
    </div>

    <div class="opcion">
      <div class="d-flex align-items-center" style="padding: 20px;">
        <input type="checkbox" v-model="notificaciones.permisos" id="permisosCreados" class="me-2">
        <p class="mb-0">Envío al colaborador por correo de permisos creados</p>
      </div>
    </div>

    <div class="opcion">
      <div class="d-flex align-items-center" style="padding: 20px;">
        <input type="checkbox" v-model="notificaciones.vacaciones" id="vacacionesCreadas" class="me-2">
        <p class="mb-0">Envío al colaborador por correo de vacaciones creadas</p>
      </div>
    </div>

    <div class="opcion">
      <div class="d-flex align-items-center" style="padding: 20px;">
        <input type="checkbox" v-model="notificaciones.marcacion" id="marcacionAsistencia" class="me-2">
        <p class="mb-0">Envío al colaborador por correo el registro de su marcación de asistencia</p>
      </div>
    </div>

    <!-- Centrar el botón "Guardar" -->
    <div class="d-flex justify-content-center">
      <button @click="guardarNotificaciones" class="btn btn-primary btn-lg mt-3">Guardar</button>
    </div>

    <!-- Mostrar mensaje de éxito o error -->
    <div v-if="registroExitosoNotificaciones" class="alert alert-success mt-3">
      {{ registroExitosoNotificaciones }}
    </div>
    <div v-if="errorRegistroNotificaciones" class="alert alert-danger mt-3">
      {{ errorRegistroNotificaciones }}
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import { opcionesStore } from '../../../../store/empresa/opciones';

// Estado reactivo para almacenar las notificaciones y el store
const store = opcionesStore();
const notificaciones = ref({
  faltas_tardanzas: false,
  permisos: false,
  vacaciones: false,
  marcacion: false
});

// Refs para los mensajes de éxito y error
const registroExitosoNotificaciones = ref(null);
const errorRegistroNotificaciones = ref(null);

// Función para cargar las notificaciones activas desde el store
const cargarNotificaciones = async () => {
  await store.fetchNotificacionesActivas();
  if (store.notificacionesActivas) {
    notificaciones.value = { ...store.notificacionesActivas };
  } else if (store.errorNotificacionesActivas) {
    errorRegistroNotificaciones.value = store.errorNotificacionesActivas;
  }
};

// Función para guardar las notificaciones activas a través del store
const guardarNotificaciones = async () => {
  await store.actualizarNotificacionesActivas(notificaciones.value);
  if (store.registroExitosoNotificaciones) {
    registroExitosoNotificaciones.value = store.registroExitosoNotificaciones;
    errorRegistroNotificaciones.value = null;
  } else {
    errorRegistroNotificaciones.value = store.errorRegistroNotificaciones;
    registroExitosoNotificaciones.value = null;
  }
};

// Cargar las notificaciones al montar el componente
onMounted(() => {
  cargarNotificaciones();
});
</script>

<style scoped>
/* Estilos basados en Bootstrap */
</style>







