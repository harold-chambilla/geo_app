<template>
  <div class="notificaciones">
    <div class="Ttl1 fw-bold">NOTIFICACIONES</div>
    <div class="linea-azul mb-3"></div>

    <div class="opcion">
      <div class="d-flex align-items-center" style="padding: 20px;">
        <input type="checkbox" v-model="notificaciones.faltasTardanzas" id="faltasTardanzas" class="me-2">
        <p class="mb-0">Envío por correo de faltas y tardanzas diaria</p>
      </div>
    </div>

    <div class="opcion">
      <div class="d-flex align-items-center" style="padding: 20px;">
        <input type="checkbox" v-model="notificaciones.permisosCreados" id="permisosCreados" class="me-2">
        <p class="mb-0">Envío al colaborador por correo de permisos creados</p>
      </div>
    </div>

    <div class="opcion">
      <div class="d-flex align-items-center" style="padding: 20px;">
        <input type="checkbox" v-model="notificaciones.vacacionesCreadas" id="vacacionesCreadas" class="me-2">
        <p class="mb-0">Envío al colaborador por correo de vacaciones creadas</p>
      </div>
    </div>

    <div class="opcion">
      <div class="d-flex align-items-center" style="padding: 20px;">
        <input type="checkbox" v-model="notificaciones.marcacionAsistencia" id="marcacionAsistencia" class="me-2">
        <p class="mb-0">Envío al colaborador por correo el registro de su marcación de asistencia</p>
      </div>
    </div>

    <!-- Centrar el botón "Guardar" -->
    <div class="d-flex justify-content-center">
      <button @click="guardarNotificaciones" class="btn btn-primary btn-lg mt-3">Guardar</button>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';

// Estado reactivo para las notificaciones
const notificaciones = ref({
  faltasTardanzas: false,
  permisosCreados: false,
  vacacionesCreadas: false,
  marcacionAsistencia: false
});

// Función para guardar las notificaciones (aquí puedes hacer una llamada a una API)
const guardarNotificaciones = () => {
  console.log('Notificaciones guardadas:', notificaciones.value);
  // Aquí puedes hacer la lógica para enviar las notificaciones a tu backend
};
</script>

<style scoped>
/* Sin cambios adicionales en los estilos, todo se basa en Bootstrap */
</style>






