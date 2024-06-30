<template>
  <div class="container mt-5 text-center">
    <div class="row">
      <div id="map" style="height: 400px;"></div>
      <button class="B-Verde border-0" @click="turnoEntrada">Entrada</button>
      <button class="B-ROJO  border-0" @click="turnoSalida">Salida</button>
    </div>
  </div>
</template>

<script setup>
// Dato a enviar a otro formulario se le pondra (*), los datos que no van a estar son estos (-), Datos que iran en infoWindows(+)
// Datos que se van a quedar en la misma vista (/)
import { ref, onMounted, onUnmounted, watch, computed } from 'vue';
import { funcionesStore } from '../../../store/colaborador/funciones';
import { asistenciaStore } from '../../../store/colaborador/asistencia';
import edificioImg from '../../../img/colaborador/edificio-svg.png';
import hombreImg from '../../../img/colaborador/hombre-svg.png';

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

// Errores
const errorMessage = computed(() => asisStore.errorMessage);
//Tiempo y fecha
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

    const mapOptions = {
      // center: ubiDestino,
      // zoom: 4
    };

    const map = new google.maps.Map(document.getElementById('map'), mapOptions);

    const radio = 800;
    const circle = new google.maps.Circle({
      strokeColor: '#8CD0FF',
      strokeOpacity: 0.8,
      strokeWeight: 2,
      fillColor: '#8CD0FF',
      fillOpacity: 0.35,
      map: map,
      center: ubiDestino,
      radius: radio
    });
    const dist = google.maps.geometry.spherical.computeDistanceBetween(ubiActual, ubiDestino);

    const infoWindow = new google.maps.InfoWindow({
      content: '<p>Latitud: ' + latitud + '</p>' +
        '<p>Longitud: ' + longitud + '</p>' +
        '<p>Exactitud: ' + exact + '</p>' +
        '<p>Distancia: ' + dist + '</p>' +
        '<a href="https://www.google.com/maps/search/' + latitud + ',' + longitud + '">Ver en google maps</a>',
      map: map,
      arialabel: "Uluru",
      // pixelOffset: new google.maps.Size(0, -50)
    })

    const marker = [
      new google.maps.Marker({ position: ubiActual, map: map, title: "Aqui estoy!", icon: { url: hombreImg, scaledSize: new google.maps.Size(50, 50) } }),
      new google.maps.Marker({ position: ubiDestino, map: map, title: "Mi empresa", icon: { url: edificioImg, scaledSize: new google.maps.Size(50, 50) } })
    ];

    const bounds = new google.maps.LatLngBounds();
    bounds.extend(ubiActual);
    bounds.extend(ubiDestino);
    // const padding = 50;
    // map.fitBounds(bounds, padding);
    map.fitBounds(bounds)

    //const lat = new google.maps.LatLng(-12.033077753513151, -76.99137861370299)

    const tiles = new google.maps.event.addListenerOnce(map, 'tilesloaded', () => {
      if (!mapInitialized.value) { // El mapa ya esta inicializado, por lo que no lo que el watch se limpiara y no se volvera a actualizar
        mapInitialized.value = true;
        // Limpiar el observador de ubicación una vez que el usuario esté satisfecho con la ubicación  
        navigator.geolocation.clearWatch(watchId.value);
      }
    });

    

    // infoWindow.open(map, marker[0]);
    marker[0].addListener("click", () => { //debido al emergente, el mapa se centra automaticamente en este, por eso tome como opcion cerrarlo para abrirlo.
      infoWindow.open({
        anchor: marker[0],
        map: map,
      });
    });



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

    // if (accuracy <= estimado) {
    //   const text = "Exactitud aceptable";
    //   exactitudRadio.value = text;
    // } else {
    //   const text = "Exactitud baja";
    //   exactitudRadio.value = text;
    // }

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