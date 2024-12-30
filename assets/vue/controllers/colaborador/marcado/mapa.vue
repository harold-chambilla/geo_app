<template>
    <div class="card shadow border-0 rounded-3" style="width: 300px; overflow: hidden;">
        <!-- Mapa -->
        <div id="map" class="w-100" style="height: 300px;"></div>

        <!-- Botones -->
        <div class="d-flex flex-column gap-3 p-3 text-center">
            <button class="btn btn-success fw-bold py-2" @click="marcarEntrada">
            ENTRADA
            </button>
            <button class="btn btn-danger fw-bold py-2" @click="marcarSalida">
            SALIDA
            </button>
        </div>
    </div>
</template>
<script setup>
import { onMounted } from "vue";

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