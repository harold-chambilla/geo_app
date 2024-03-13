<template>
    <button class="B-Verde border-0" @click="turnoEntrada">Entrada</button>
    <button class="B-ROJO  border-0" @click="turnoSalida">Salida</button>
  </template>
  
  <script setup>
  // Dato a enviar a otro formulario se le pondra (*), los datos que no van a estar son estos (-), Datos que iran en infoWindows(+)
  // Datos que se van a quedar en la misma vista (/)
  import { ref, onMounted, onUnmounted, watch, computed } from 'vue';
  import { funcionesStore } from '../../../store/funciones.js';
  import { asistenciaStore } from '../../../store/asistencia';
  import edificioImg from '../../../img/edificio-svg.png';
  import hombreImg from '../../../img/hombre-svg.png';
  
  // Variables de ubicación
  const latitude = ref(0);
  const longitude = ref(0);
  const exactitud = ref(0);
  const mapInitialized = ref(false);
  const watchId = ref(null);
  const verId = ref(null);
  const fileInput = ref(null);
  
  // Variables adicionales
  const distancia = ref(null);
  const asistencia = ref("");
  const exactitudRadio = ref(null);
  const estadoEntrada = ref("");
  
  // Llamado al Store
  const functStore = funcionesStore();
  const asisStore = asistenciaStore();
  
  const mapReady = ref(false);
  
  onMounted(() => { //Utilizado para inicializar todo cuando ya esta cargado
  // Iniciar la actualización de la hora
  functStore.iniciarTiempo();
});
const currentTiempo = computed(() => functStore.getCurrentTiempo);
const currentFecha = computed(() => functStore.getCurrentFecha);
onUnmounted(() => { //Utilizado para no utilizar componentes al desmontar
  // Limpiar el intervalo cuando el componente se desmonte y no consuma memoria mientras navegas
  clearInterval(currentTiempo);
});

  const watchCoordinates = () => {
    return new Promise((resolve, reject) => {
      verId.value = navigator.geolocation.getCurrentPosition(
        (position) => {
          latitude.value = position.coords.latitude;
          longitude.value = position.coords.longitude;
          exactitud.value = position.coords.accuracy;
          resolve({ latitude: latitude.value, longitude: longitude.value, accuracy: exactitud.value });
        },
        (error) => {
          console.error('Geolocation error:', error);
          reject(error);
        }
      );
    });
  };
  
  const turnoEntrada = async () => {
    try {
      const coords = await watchCoordinates();
      const newAsis = {
        fechaEntrada: currentFecha.value, //Lo que pasa es que la fecha esta asi / / y debe estar asi - -
        horaEntrada: currentTiempo.value,
        fotoEntrada: null,
        estadoEntrada: estadoEntrada.value,
        latitud: coords.latitude,
        longitud: coords.longitude
      };
      console.log("CREAR: Latitude: " + coords.latitude + ", Longitude: " + coords.longitude);
      await asisStore.POST_ENTRADA(newAsis);
      // window.location.href = '/resultado';
    } catch (error) {
      console.error("Error al enviar los datos de entrada: ", error);
    }
  }
  
  const turnoSalida = async () => {
    try {
      const coords = await watchCoordinates();
      const updAsis = {
        fechaSalida: currentFecha.value,
        horaSalida: currentTiempo.value,
        fotoSalida: null,
        estadoSalida: estadoEntrada.value,
        latitud: coords.latitude,
        longitud: coords.longitude
      };
      // const nuevoAsistenciaId = asisStore.asistencia.id;
      console.log("ACTUALIZAR: Latitude: " + coords.latitude + ", Longitude: " + coords.longitude);
      await asisStore.POST_SALIDA(updAsis);
      // window.location.href = '/resultado';
    } catch (error) {
      console.error("Error al enviar los datos de salida: ", error);
    }
  };
  
  // Esta función se ejecutará una vez que la API de Google Maps esté cargada y lista
  const initMap = () => {
    if (!mapInitialized.value) {
    const coords = watchCoordinates();

      const ubiActual = { lat: coords.latitude, lng: coords.longitude };
      const ubiDestino = { lat: -12.085184, lng: -76.976101 }; // Ubicación de la empresa
  
      const radio = 800;

      const dist = google.maps.geometry.spherical.computeDistanceBetween(ubiActual, ubiDestino);
  
      if (dist <= radio) {
        const text = "Si esta en la empresa";
        asistencia.value = text;
      } else {
        const text = "No esta en la empresa";
        asistencia.value = text;
      }
  
      const horaEntrada = '08:30:00'
      if (currentTiempo.value >= horaEntrada && dist <= radio) {
        const text = "Tardanza y dentro de radio";
        estadoEntrada.value = text;
      } else if (currentTiempo.value <= horaEntrada && dist <= radio) {
        const text = "Puntual y dentro de radio";
        estadoEntrada.value = text;
      } else if (currentTiempo.value <= horaEntrada && dist >= radio) {
        const text = "Puntual y fuera de radio";
        estadoEntrada.value = text;
      } else if (currentTiempo.value <= horaEntrada && dist >= radio) {
        const text = "Tardanza y fuera de radio";
        estadoEntrada.value = text;
      } else {
        console.log('Esta fuera del radio');
      }
  
      const estadoAsistencia = estadoEntrada.value;
  
      distancia.value = dist;
    }
  };
  
  watch(exactitud, (newValue) => {
    if (newValue >= 50 && newValue <= 200) {
      exactitudRadio.value = 'Exactitud baja.';
    } else if (newValue >= 1 && newValue <= 50) {
      exactitudRadio.value = 'Exactitud alta.';
    } else {
      exactitudRadio.value = 'Exactitud inaceptable. Por favor, actualiza la ubicación.';
    }
  });
  
  window.initMap = function () {
    mapReady.value = true;
  };
  </script>