<template>
    <button class="Verde E-boton border-0" @click="turnoEntrada">Entrada</button>
    <button class="Rojo  E-boton border-0" @click="turnoSalida">Salida</button>
</template>
  
<script setup>
  // Dato a enviar a otro formulario se le pondra (*), los datos que no van a estar son estos (-), Datos que iran en infoWindows(+)
  // Datos que se van a quedar en la misma vista (/)
  import { ref, onMounted, onUnmounted, watch, computed } from 'vue';
  import { funcionesStore } from '../../../../../store/colaborador/funciones';
  import { asistenciaStore } from '../../../../../store/colaborador/asistencia';
  import { opcionesStore } from '../../../../../store/empresa/opciones';
  
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
  const dentroDeSede = ref(false);
  
  // Llamado al Store
  const functStore = funcionesStore();
  const asisStore = asistenciaStore();

  const opcionesStorage = opcionesStore();
  
  const sedes = computed(() => {
    return opcionesStorage.SEDES;  // Acceder a las sedes almacenadas en el store
  });

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

const validarSedes = async () => {
  await asisStore.fetchSedesMarcado();  // Asegúrate de obtener las sedes antes de validar

  dentroDeSede.value = sedes.value.some((sede) => {
    const ubicacionSede = new google.maps.LatLng(parseFloat(sede.latitud), parseFloat(sede.longitud));
    const ubicacionUsuario = new google.maps.LatLng(latitude.value, longitude.value);
    const distancia = google.maps.geometry.spherical.computeDistanceBetween(ubicacionUsuario, ubicacionSede);
    return distancia <= sede.radio;
  });

  if (dentroDeSede.value) {
    estadoEntrada.value = "Dentro del radio de alguna sede.";
  } else {
    estadoEntrada.value = "Fuera del radio de todas las sedes.";
  }
};

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
  
  onMounted(() => {
    if ('geolocation' in navigator) {
      setTimeout(() => {    
        const coordenadasInitMap = async () => {
          try {
            const coords = await watchCoordinates();
            // coords es un objeto con latitude y longitude
            latitude.value = coords.latitude;
            longitude.value = coords.longitude;
            exactitud.value = coords.accuracy;
            if (!mapInitialized.value) { // Función a aparecer al actualizar la web
              await initMap(latitude.value, longitude.value, exactitud.value);
              // console.log("no creo: ", latitude.value);
            }
          } catch (error) {
            console.error("Error al obtener coordenadas:", error);
          }
        };
        coordenadasInitMap();
      }, 500);
    }
  });

  // Esta función se ejecutará una vez que la API de Google Maps esté cargada y lista
  const initMap = (latitud, longitud, exact) => {
    if (!mapInitialized.value) {
      const ubiActual = { lat: latitud, lng: longitud };
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

      console.log('dissss: ', radio);
  
      const horaEntrada = '08:30:00'
      if (currentTiempo.value >= horaEntrada && dist <= radio) {
        // const text = "Tardanza y dentro de radio";
        const text = "Tardanza";
        estadoEntrada.value = text;
      } else if (currentTiempo.value <= horaEntrada && dist <= radio) {
        // const text = "Puntual y dentro de radio";
        const text = "Puntual";
        estadoEntrada.value = text;
      } 
      // else if (currentTiempo.value <= horaEntrada && dist >= radio) {
      //   const text = "Puntual y fuera de radio";
      //   estadoEntrada.value = text;
      // } else if (currentTiempo.value <= horaEntrada && dist >= radio) {
      //   const text = "Tardanza y fuera de radio";
      //   estadoEntrada.value = text;
      // } 
      else {
        console.log('Esta fuera del radio');
        const text = "No se encuentra en la empresa";
        estadoEntrada.value = text;
      }
      // const estadoAsistencia = estadoEntrada.value;
      
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
  console.log('distanciaaa: ', distancia.value);

  const turnoEntrada = async () => {
    try {
      const coords = await watchCoordinates();
      await validarSedes();
      const newAsis = {
        fechaEntrada: currentFecha.value, //Lo que pasa es que la fecha esta asi / / y debe estar asi - -
        horaEntrada: currentTiempo.value,
        fotoEntrada: null,
        estadoEntrada: estadoEntrada.value,
        latitud: coords.latitude,
        longitud: coords.longitude
      };
      // if (distancia.value > 800) {
      //   console.log("Su solicitud ha sido denegada, no se encuentra dentro de la empresa.");
      //   return; // Salir de la función sin enviar el POST_ENTRADA
      // }
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
      await validarSedes();
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
</script>