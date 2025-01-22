<template>
    <div id="map" class="w-100" style="height: 300px;"></div>
</template>
<script setup>
import { ref, onMounted } from "vue";
import { useMarcadoStore } from '@/store/colaborador/marcado';

const marcadoStore = useMarcadoStore();

const latitude = ref(0);
const longitude = ref(0);
const exactitud = ref(0);

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
    if ('geolocation' in navigator) {
      setTimeout(() => {
        const cordenadasInitMap = async () => {
          try {
            const coords = await watchCoordinates();
            latitude.value = coords.latitude;
            latitude.value = coords.longitude;
            exactitud.value = coords.accuracy;
            console.log("Initializing map with coordinates:", latitude.value, longitude.value);
          } catch (error) {
            console.error('Error al obtener las coordenadas o sedes:', error);
          }
        };
      }, 500);
    }

    // Cargar el mapa
    const googleMapsScript = document.createElement("script");
    googleMapsScript.src = `https://maps.googleapis.com/maps/api/js?key=YOUR_GOOGLE_MAPS_API_KEY&callback=initMap`;
    googleMapsScript.async = true;
    googleMapsScript.defer = true;
    window.initMap = initMap; // Vincular la función initMap
    document.head.appendChild(googleMapsScript);
});
</script>