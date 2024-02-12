<template>
    <div>
      <p>Latitude: {{ latitude }}</p>
      <p>Longitude: {{ longitude }}</p>
      <p>Exactitud: {{ accuracy }}</p>
      <p>Longitude1: {{ longitude1 }}</p>
      <p class="">Asistencia: {{ asistencia }}</p>
      <div id="map"></div>
    </div>
  </template>
  
  <script setup>
import { ref, onMounted } from 'vue';

const latitude = ref(0);
const longitude = ref(0);
const accuracy = ref(null);
const mapInitialized = ref(false);
const watchId = ref(null);
const longitude1 = ref(0);

const userLocation = ref(null);

  const distancia = ref(null)
  const asistencia = ref("");

onMounted(() => {

  if ('geolocation' in navigator) {
    setTimeout(() => {
    watchId.value = navigator.geolocation.watchPosition((position) => {
        latitude.value = position.coords.latitude;
        longitude.value = position.coords.longitude;
        accuracy.value = position.coords.accuracy;
        if (!mapInitialized.value) {
        initMap(latitude.value, longitude.value);
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
function initMap(latitud, longitud) {
  if (!mapInitialized.value) {
    const ubiActual = { lat: latitud, lng: longitud };
    longitude1.value = latitud;
    const ubiDestino = { lat: -12.085184, lng: -76.976101 }; // Ubicación de la empresa

    const mapOptions = {
      center: ubiDestino,
      zoom: 16
    };

    const map = new google.maps.Map(document.getElementById('map'), mapOptions);
    const marker = [
      new google.maps.Marker({ position: ubiActual, map: map, title: "Aqui estoy!" }),
      new google.maps.Marker({ position: ubiDestino, map: map })
    ];

    const tiles = new google.maps.event.addListenerOnce(map, 'tilesloaded', () => {
      if (!mapInitialized.value) {
        mapInitialized.value = true;
        // Limpiar el observador de ubicación una vez que el usuario esté satisfecho con la ubicación  
        navigator.geolocation.clearWatch(watchId.value);
        console.log("Mensaje")
      }
    });

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

      
      if (dist <= radio) {
        const text = "Si esta en la empresa";
        asistencia.value = text;
      } else {
        const text = "No esta en la empresa";
        asistencia.value = text;
      }

      distancia.value = dist;
  }
};

// window.initMap = initMap;
</script>