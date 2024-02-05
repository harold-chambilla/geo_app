<template>
    <div>
      <p v-if="errorMessage">{{ errorMessage }}</p>
      <p>Latitud: {{ latitude }}</p>
      <p>Longitud: {{ longitude }}</p>
    </div>
    <div>
      <div ref="map" id="map" style="height: 400px;"></div>
    </div>
  </template>
  
  <script setup>
  import { ref, onMounted, computed } from 'vue';
  
  const latitude = ref(0);
  const longitude = ref(0);
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
    const ubiActual = { lat: latitude.value, lng: longitude.value }
    const mapOptions = {
      center: ubiActual,
      zoom: 16
    };
    const map = new google.maps.Map(document.getElementById('map'), mapOptions);
    const marker = new google.maps.Marker({ position: ubiActual, map: map })
  };
  </script>
  