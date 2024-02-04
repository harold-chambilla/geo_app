<template>
    <div>
      <div ref="map" style="height: 400px;"></div>
      <p v-if="latitude !== null && longitude !== null">Latitud: {{ latitude }}</p>
      <p v-if="latitude !== null && longitude !== null">Longitud: {{ longitude }}</p>
    </div>
</template>
  
<script setup>
  import { ref, onMounted } from 'vue';
  import L from 'leaflet';
  import 'leaflet/dist/leaflet.css';
  
  const map = ref(null);
  const latitude = ref(null);
  const longitude = ref(null);
  
  onMounted(() => {
    map.value = L.map('map').setView([0, 0], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map.value);
  
    // Obtiene la ubicación actual del usuario
    navigator.geolocation.getCurrentPosition((position) => {
      const { latitude: lat, longitude: lon } = position.coords;
      latitude.value = lat;
      longitude.value = lon;
  
      // Centra el mapa en la ubicación actual del usuario
      map.value.setView([lat, lon], 13); // Funcional en openstreetMap
  
      // Agrega un marcador en la ubicación actual del usuario
      L.marker([lat, lon]).addTo(map.value)
        .bindPopup('¡Estás aquí!')
        .openPopup();
    }, (error) => {
      console.error('Error al obtener la ubicación:', error);
      //Si tu url es de tipo http te dara este error, para solucionarlo aplica este comando "symfony server:ca:install"
      //Esto generará una certificacion ssl y la web te solicitara que permitas la ubicación.
    });
  });
</script>