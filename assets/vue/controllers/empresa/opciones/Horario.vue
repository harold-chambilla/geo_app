<template>
  <div class="container mt-4">
    <div class="Ttl1 mb-1">HORARIO</div>
    <div class="linea-azul mb-4"></div>

    <div class="row">
      <div class="col-md-6">
        <div class="mb-3">
          <h6>Área</h6>
          <select v-model="data.area" class="form-select">
            <option value="" disabled>Selecciona una opción</option>
            <option value="si">Sí</option>
            <option value="no">No</option>
          </select>
        </div>
      </div>

      <div class="col-md-6">
        <div class="mb-3">
          <h6>Puesto</h6>
          <select v-model="data.puesto" class="form-select">
            <option value="" disabled>Selecciona una opción</option>
            <option value="si">Sí</option>
            <option value="no">No</option>
          </select>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-6">
        <div class="mb-3">
          <h6>Modalidad de trabajo total</h6>
          <select v-model="data.modalidadTrabajo" class="form-select">
            <option value="" disabled>Selecciona una opción</option>
            <option value="presencial">Presencial</option>
            <option value="remoto">Remoto</option>
          </select>
        </div>
      </div>

      <div class="col-md-6">
        <div class="mb-3">
          <h6>Predeterminar horario</h6>
          <select v-model="data.predeterminarHorario" class="form-select">
            <option value="" disabled>Selecciona una opción</option>
            <option value="si">Sí</option>
            <option value="no">No</option>
          </select>
        </div>
      </div>
    </div>

    <div class="d-flex justify-content-center">
      <button @click="guardarDatos" class="btn btn-primary btn-lg mt-3">Guardar</button>
    </div>

    <!-- Mostrar mensaje de éxito o error -->
    <div v-if="registroExitosoConfiguracionTrabajo" class="alert alert-success mt-3">
      {{ registroExitosoConfiguracionTrabajo }}
    </div>
    <div v-if="errorRegistroConfiguracionTrabajo" class="alert alert-danger mt-3">
      {{ errorRegistroConfiguracionTrabajo }}
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import { opcionesStore } from '../../../../store/empresa/opciones';

// Inicializar el store y el estado local de los datos
const store = opcionesStore();
const data = ref({
  area: '',
  puesto: '',
  modalidadTrabajo: '',
  predeterminarHorario: '',
});

const registroExitosoConfiguracionTrabajo = ref(null);
const errorRegistroConfiguracionTrabajo = ref(null);

// Función para cargar la configuración de trabajo al montar el componente
const cargarConfiguracionTrabajo = async () => {
  await store.fetchConfiguracionTrabajo();
  if (store.configuracionTrabajo) {
    data.value = {
      area: store.configuracionTrabajo.area ? 'si' : 'no',
      puesto: store.configuracionTrabajo.puesto ? 'si' : 'no',
      modalidadTrabajo: store.configuracionTrabajo.modalidadTrabajo,
      predeterminarHorario: store.configuracionTrabajo.predeterminarHorario ? 'si' : 'no',
    };
  } else if (store.errorConfiguracionTrabajo) {
    errorRegistroConfiguracionTrabajo.value = store.errorConfiguracionTrabajo;
  }
};

// Función para guardar los datos actualizados
const guardarDatos = async () => {
  const configuracion = {
    area: data.value.area === 'si',
    puesto: data.value.puesto === 'si',
    modalidadTrabajo: data.value.modalidadTrabajo,
    predeterminarHorario: data.value.predeterminarHorario === 'si',
  };

  await store.actualizarConfiguracionTrabajo(configuracion);

  if (store.registroExitosoConfiguracionTrabajo) {
    registroExitosoConfiguracionTrabajo.value = store.registroExitosoConfiguracionTrabajo;
    errorRegistroConfiguracionTrabajo.value = null;
  } else {
    errorRegistroConfiguracionTrabajo.value = store.errorRegistroConfiguracionTrabajo;
    registroExitosoConfiguracionTrabajo.value = null;
  }
};

// Cargar configuración de trabajo al montar el componente
onMounted(() => {
  cargarConfiguracionTrabajo();
});
</script>

<style scoped>
/* Sin cambios adicionales en los estilos, todo se basa en Bootstrap */
</style>





