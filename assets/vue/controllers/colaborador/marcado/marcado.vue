<template>
    <div class="container vh-100 d-flex flex-column align-items-center justify-content-center">
      <reloj />

      <!-- Mapa -->
      <div class="card shadow" style="width: 300px; border-radius: 20px; overflow: hidden;">
        <div id="map" style="height: 300px;"></div>
        <div class="d-flex justify-content-around p-3">
          <!-- Botón de entrada -->
          <button class="btn btn-success btn-lg px-4 d-flex align-items-center" @click="marcarEntrada">
            <span>ENTRADA</span>
            <span class="badge bg-purple ms-2">Y</span>
          </button>
  
          <!-- Botón de salida -->
          <button class="btn btn-danger btn-lg px-4 d-flex align-items-center" @click="marcarSalida">
            <span>SALIDA</span>
            <span class="badge bg-purple ms-2">Y</span>
          </button>
        </div>
      </div>
  
      <!-- Horario -->
      <div class="mt-4 text-center">
        <i class="bi bi-calendar-event" style="font-size: 2rem; color: #ffc107;"></i>
        <h5 class="fw-bold text-primary">HORARIO</h5>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref, onMounted } from "vue";
  import reloj from "./reloj.vue";
  
  // Funciones de marcar
  const marcarEntrada = () => {
    console.log("Entrada marcada");
  };
  
  const marcarSalida = () => {
    console.log("Salida marcada");
  };
  
  // Mapa
  let map;
  
  const initMap = () => {
    const mapOptions = {
      center: { lat: -12.0464, lng: -77.0428 }, // Centro predeterminado (Lima, Perú)
      zoom: 15,
    };
  
    map = new google.maps.Map(document.getElementById("map"), mapOptions);
  
    // Agregar un marcador
    new google.maps.Marker({
      position: mapOptions.center,
      map,
      title: "Tu posición",
    });
  };
  
  onMounted(() => {
    // Cargar el mapa
    const googleMapsScript = document.createElement("script");
    googleMapsScript.src = `https://maps.googleapis.com/maps/api/js?key=YOUR_GOOGLE_MAPS_API_KEY&callback=initMap`;
    googleMapsScript.async = true;
    googleMapsScript.defer = true;
    window.initMap = initMap; // Vincular la función initMap
    document.head.appendChild(googleMapsScript);
  });
  </script>
  
  <style scoped>
  .card {
    border-radius: 20px;
  }
  
  .btn-success {
    background-color: #28a745;
    border: none;
  }
  
  .btn-danger {
    background-color: #dc3545;
    border: none;
  }
  
  .badge.bg-purple {
    background-color: #6f42c1;
  }
  
  #map {
    border-radius: 20px 20px 0 0;
  }
  </style>
  