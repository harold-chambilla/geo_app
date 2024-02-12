<template>
    <div>
      <p>Latitude: {{ latitude }}</p>
      <p>Longitude: {{ longitude }}</p>
      <p>Exactitud: {{ accuracy }}</p>
      <div id="map"></div>
    </div>
  </template>
  
  <script setup>
import { ref, onMounted } from 'vue';

const latitude = ref(null);
const longitude = ref(null);
const accuracy = ref(null);
const mapInitialized = ref(false);
const watchId = ref(null);

  const distancia = ref(null)
  const asistencia = ref("");

// const handleLocationUpdate = (position) => {
//   latitude.value = position.coords.latitude;
//   longitude.value = position.coords.longitude;
// };

// const handleLocationError = (error) => {
//   console.error('Error getting location:', error);
// };

onMounted(() => {
  if ('geolocation' in navigator) {
    watchId.value = navigator.geolocation.watchPosition((position) => {
        latitude.value = position.coords.latitude;
        longitude.value = position.coords.longitude;
        accuracy.value = position.coords.accuracy;
        console.log('Ubicación del usuario obtenida: ', position);
        console.log('metros: ', position.coords.accuracy);

        // const script = document.createElement('script');
        // script.src = `https://maps.googleapis.com/maps/api/js?key=AIzaSyBKG625KcwDUXUIvO0x22JMGYMV7DMqd7Q&callback=initMap&libraries=geometry`;
        // script.defer = true;
        // script.async = true;
        // script.onerror = () => {
        //   console.error("Error al cargar la API de Google Maps");
        // };
        // document.head.appendChild(script);
      },
      (error) => {
        errorMessage.value = `Error al obtener la ubicación: ${error.message}`;
        console.error('Geolocation error:', error);
      }
    );
  }
});

// Esta función se ejecutará una vez que la API de Google Maps esté cargada y lista
window.initMap = () => {
  if (!mapInitialized.value) {
    console.log('latitud:', latitude.value);
    console.log('longitud:', longitude.value);
    const ubiActual = { lat: latitude.value, lng: longitude.value };
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
        console.log('latitud tilesloaded:', latitude.value);
        console.log('longitud tilesloaded:', longitude.value);
        navigator.geolocation.clearWatch(watchId.value);
        alert('Mapa cargado!');
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
        alert("Si llego");
        const text = "Si esta en la empresa";
        asistencia.value = text;
      } else {
        alert("No llego");
        const text = "No esta en la empresa";
        asistencia.value = text;
      }

      distancia.value = dist;
      console.log("MARKER:", marker.title)
  }
};
</script>
