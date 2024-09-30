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
  </div>
</template>

<script setup>
import { ref } from 'vue';

const data = ref({
  area: '',
  puesto: '',
  modalidadTrabajo: '',
  predeterminarHorario: '',
});

const guardarDatos = () => {
  const resultado = {
    area: data.value.area,
    puesto: data.value.puesto,
    modalidadTrabajo: data.value.modalidadTrabajo,
    predeterminarHorario: data.value.predeterminarHorario,
  };

  console.log("Datos guardados:", resultado);
};
</script>





