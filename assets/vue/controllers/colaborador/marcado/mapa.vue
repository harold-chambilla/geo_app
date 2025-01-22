<template>
    <div id="map" class="w-100" style="height: 300px;"></div>
</template>
<script setup>
import { ref, onMounted, computed } from "vue";
import { useMarcadoStore } from '@/store/colaborador/marcado';

const marcadoStore = useMarcadoStore();

const sede = computed(() => {
  return marcadoStore.sede;
});

const latitude = ref(0);
const longitude = ref(0);
const exactitud = ref(0);
const mapInitialized = ref(false);
const verId = ref(null);
const watchId = ref(null);

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

const initMap = (latitud, longitud, exact, sedes) => {
  if (!mapInitialized.value){
    const ubiActual = { lat: latitud, lng: longitud };
    const map = new google.maps.Map(document.getElementById("map"), {
      zoom: 10,
    });
  
    const usuarioMarker = new google.maps.Marker({
      position: ubiActual,
      map: map,
      title: "Aqui estoy!"
    });

    new google.maps.Circle({
      strokeColor: '#8CD0FF',
      strokeOpacity: 0.8,
      strokeWeight: 2,
      fillColor: '#8CD0FF',
      fillOpacity: 0.35,
      map: map,
      center: ubiActual,
      radius: 300
    });

    const bounds = new google.maps.LatLngBounds();
    bounds.extend(ubiActual);

    sedes.forEach((sede) => {
      const ubicacionSede = { lat: parseFloat(sede.latitud), lng: parseFloat(sede.longitud) };

      if (!isNaN(ubicacionSede.lat) && !isNaN(ubicacionSede.lng)) {
        new google.maps.Marker({
          position: ubicacionSede,
          map: map,
          title: sede.nombre
        });

        new google.maps.Circle({
          strokeColor: '#FF5733',
          strokeOpacity: 0.8,
          strokeWeight: 2,
          fillColor: '#FFC300',
          fillOpacity: 0.35,
          map: map,
          center: ubicacionSede,
          radius: sede.radio
        });

        bounds.extend(ubicacionSede);
      }
    });

    map.fitBounds(bounds);

    const tiles = new google.maps.event.addListenerOnce(map, 'tilesloaded', () => {
      if (!mapInitialized.value) {
        mapInitialized.value = true;
        navigator.geolocation.clearWatch(watchId.value);
      }
    });
  }
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

            await marcadoStore.fetchSede(1); // Id de colaborador
            console.log('sede: ', sede.value);
          } catch (error) {
            console.error('Error al obtener las coordenadas o sedes:', error);
          }
        };
        cordenadasInitMap();
      }, 500);
    }
});
</script>