<template>
  <div>
    <h2>Buscar Asistencias</h2>
    <form @submit.prevent="buscarAsistencias" :disabled="loading">

      

      <label>Buscar:</label>
      <input type="text" v-model="searchQuery" placeholder="Buscar...">
      
      <label>Fecha:</label>
      <input type="date" v-model="fecha">

      <label>Estado Entrada:</label>
      <input type="text" v-model="estadoEntrada">

      <label>Estado Salida:</label>
      <input type="text" v-model="estadoSalida">

      <label>Puesto:</label>
      <input type="text" v-model="puesto">

      <label>Área:</label>
      <input type="text" v-model="area">

      <label>Modalidad:</label>
      <input type="text" v-model="modalidad">

      <button type="submit">Buscar</button>
    </form>

    <h2>Resultados de Búsqueda</h2>
    <table>
      <thead>
        <tr>
          <th>Nombre</th>
          <th>Puesto</th>
          <th>Área</th>
          <th>Fecha</th>
          <th>Hora Entrada</th>
          <th>Hora Salida</th>
          <th>Modalidad</th>
          <th>Hora Extra</th>
        </tr>
      </thead>
      <tbody>
        <div v-if="loading">Cargando...</div>
        <div v-if="error">{{ error }}</div>

        <tr v-if="!loading && !error" v-for="(asistencia, index) in asistencias" :key="index">
          <td>{{ asistencia.nombre }}</td>
          <td>{{ asistencia.puesto }}</td>
          <td>{{ asistencia.area }}</td>
          <td>{{ asistencia.fecha }}</td>
          <td>{{ asistencia.ingreso }}</td>
          <td>{{ asistencia.salida }}</td>
          <td>{{ asistencia.modalidad }}</td>
          <td>{{ asistencia.hora_extra }}</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>
  
  <script setup>
  import { computed, onMounted, ref } from 'vue';
  import { administracionStore } from '../../../store/administracion';
  
  const asisStore = administracionStore();
  const loading = ref(false);
const error = ref(null);
const fechaSeleccionada = ref('');

const searchQuery = ref(null);
const fecha = ref(null);
const estadoEntrada = ref(null);
const estadoSalida = ref(null);
const puesto = ref(null);
const area = ref(null);
const modalidad = ref(null);

const opcionesEstado = ['marcado', 'no marcado', 'pendiente', 'permiso', 'vacaciones'];
const opcionesArea = ['contabilidad', 'sistemas'];
const opcionesPuesto = ['Asistente de contabilidad', 'Jefe de Sistemas'];
const opcionesModalidad = ['Presencial', 'Virtual'];

// Funcion par que el listado GET tenga un carga con loading y sus condicionales.
const getData = async (data) => {
    loading.value = true;
    error.value = null;

    try {
        await data();
    } catch (err) {
        console.error('Error al obtener los datos:', err);
        error.value = 'Hubo un error al obtener los datos.';
    } finally {
        loading.value = false;
    }
}


// const asistencias = computed(() => {return asisStore.ASISTENCIAS});
const asistencias = computed(() => {return asisStore.ASISTENCIAS_FECHA});

const buscarAsistencias = async () => {
    // getData(await asisStore.GET_ASISTENCIAS)
    const params = {
      query: searchQuery.value,
      fecha: fecha.value,
      estado_entrada: estadoEntrada.value,
      estado_salida: estadoSalida.value,
      puesto: puesto.value,
      area: area.value,
      modalidad: modalidad.value    
    }
    getData(async () => { await asisStore.GET_ASISTENCIAS_FECHA(params) })
}

  </script>
  