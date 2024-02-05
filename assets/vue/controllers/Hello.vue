<template>
    <div>
      <p v-if="errorMessage">{{ errorMessage }}</p>
      <p>Latitud: {{ latitude }}</p>
      <p>Longitud: {{ longitude }}</p>
    </div>
    <div>
      <div id="map" style="height: 400px;"></div>
    </div>
  </template>
  
  <script setup>
  import { ref, onMounted, computed } from 'vue';
  
  const latitude = ref(null);
  const longitude = ref(null);
  const errorMessage = ref(null);
  
  onMounted(() => {
    if (!navigator.geolocation) {
      errorMessage.value = 'Geolocalización no es soportada por tu navegador.';
      return;
    }
  
    navigator.geolocation.getCurrentPosition(
      position => {
        latitude.value = position.coords.latitude;
        longitude.value = position.coords.longitude;
      },
      error => {
        errorMessage.value = `Error al obtener la ubicación: ${error.message}`;
        console.error('Geolocation error:', error);
      }
    );
  });

  window.initMap = () => {
    const mapOptions = {
      center: { lat: latitude.value, lng: longitude.value },
      zoom: 13
    };
    const map = new google.maps.Map(document.getElementById('map'), mapOptions);
  };
  </script>
  