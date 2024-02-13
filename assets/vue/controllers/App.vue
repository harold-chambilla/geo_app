<template>
  <div class="container mt-5">
    <div class="row">
      <div class="col-md-6">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Información de Ubicación</h5>
            <p class="card-text">Latitude: {{ latitude }}(+)</p>
            <p class="card-text">Longitude: {{ longitude }}(+)</p>
            <p class="card-text">Exactitud: {{ accuracy }} | {{ exactitudRadio }}(+)</p>
            <p class="card-text">Asistencia: {{ asistencia }}(*)</p>
            <p class="card-text">Distancia: {{ distancia }}(+)</p>
            <p class="card-text">Estado: Falta casuisticas(*)</p>
            <p class="card-text">Hora: {{ currentTime }}(*)(/)</p>
            <p class="card-text">Fecha: {{ currentDate }}(*)</p>
            <p class="card-text">Si deseas más información de tu posición selecciona tu muñeco en el mapa</p>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div id="map" style="height: 400px;"></div>
      </div>
    </div>
  </div>
</template>

  
  <script setup>
  // Dato a enviar a otro formulario se le pondra (*), los datos que no van a estar son estos (-), Datos que iran en infoWindows(+)
  // Datos que se van a quedar en la misma vista (/)
import { ref, onMounted, onUnmounted, watch } from 'vue';
import icono from '../../img/icon.svg'
import human from '../../img/human.svg'

const latitude = ref(0);
const longitude = ref(0);
const accuracy = ref(null);
const mapInitialized = ref(false);
const watchId = ref(null);
const longitude1 = ref(0);

const userLocation = ref(null);

  const distancia = ref(null);
  const asistencia = ref("");
  const exactitudRadio = ref(null);

  const currentTime = ref(getCurrentTime());


  const currentDate = ref(getCurrentDate());

// Función para obtener la fecha actual
function getCurrentDate() {
  const now = new Date();
  const day = now.getDate().toString().padStart(2, '0');
  const month = (now.getMonth() + 1).toString().padStart(2, '0'); // Sumamos 1 porque los meses empiezan en 0
  const year = now.getFullYear().toString();
  return `${day}/${month}/${year}`;
}
// Función para obtener la hora actual
function getCurrentTime() {
  const now = new Date();
  const hours = now.getHours().toString().padStart(2, '0');
  const minutes = now.getMinutes().toString().padStart(2, '0');
  const seconds = now.getSeconds().toString().padStart(2, '0');
  return `${hours}:${minutes}:${seconds}`;
}

// Actualizar la hora cada segundo
const timerId = setInterval(() => {
  currentTime.value = getCurrentTime();
}, 1000);

// Limpiar el intervalo cuando el componente se desmonte
onUnmounted(() => {
  clearInterval(timerId);
});

onMounted(() => {

  if ('geolocation' in navigator) {
    setTimeout(() => {
    watchId.value = navigator.geolocation.watchPosition((position) => {
        latitude.value = position.coords.latitude;
        longitude.value = position.coords.longitude;
        accuracy.value = position.coords.accuracy;
        if (!mapInitialized.value) {
        initMap(latitude.value, longitude.value, accuracy.value);
      }
      },
      (error) => {
        errorMessage.value = `Error al obtener la ubicación: ${error.message}`;
        console.error('Geolocation error:', error);
      }
    )}, 500);
  }
});

// Esta función se ejecutará una vez que la API de Google Maps esté cargada y lista
function initMap(latitud, longitud, exact) {
  if (!mapInitialized.value) {
    const ubiActual = { lat: latitud, lng: longitud };
    const ubiDestino = { lat: -12.085184, lng: -76.976101 }; // Ubicación de la empresa

    const centerLocation = {
      lat: (latitud + ubiDestino.lat) / 2,
      lng: (longitud + ubiDestino.lng) / 2
    };
    
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
      content: '<p>Latitud: '+latitud+'</p>' + 
      '<p>Longitud: '+longitud+'</p>' + 
      '<p>Exactitud: '+ exact +'</p>' +
      '<p>Distancia: '+ dist +'</p>' +
      '<a href="https://www.google.com/maps/search/'+ latitud +','+ longitud +'">Ver en google maps</a>',
      map:map,
      arialabel: "Uluru",
      // pixelOffset: new google.maps.Size(0, -50)
    })

    const marker = [
      new google.maps.Marker({ position: ubiActual, map: map, title: "Aqui estoy!",icon: { url: human, scaledSize: new google.maps.Size(50, 50) } }),
      new google.maps.Marker({ position: ubiDestino, map: map, title: "Mi empresa", icon: { url: icono, scaledSize: new google.maps.Size(50, 50) } })
    ];

    const bounds = new google.maps.LatLngBounds();
  bounds.extend(ubiActual);
  bounds.extend(ubiDestino);
  // const padding = 50;
  // map.fitBounds(bounds, padding);
  map.fitBounds(bounds)

    //const lat = new google.maps.LatLng(-12.033077753513151, -76.99137861370299)
    
    const tiles = new google.maps.event.addListenerOnce(map, 'tilesloaded', () => {
      if (!mapInitialized.value) {
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

watch(accuracy, (newValue) => {
        if (newValue >= 50 && newValue <= 200) {
          exactitudRadio.value = 'Exactitud baja.';
        } else if (newValue >= 1 && newValue <= 50) {
          exactitudRadio.value = 'Exactitud alta.';
        } else {
          exactitudRadio.value = 'Exactitud inaceptable. Por favor, actualiza la ubicación.';
        }
      });
console.log('exact: ', accuracy)
// window.initMap = initMap;
</script>