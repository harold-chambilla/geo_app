<template>
  <div id="map" class="maps" loading="lazy"></div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { funcionesStore } from '../../../../../store/colaborador/funciones';
import { asistenciaStore } from '../../../../../store/colaborador/asistencia';
import edificioImg from '../../../../../img/colaborador/edificio-svg.png';
import hombreImg from '../../../../../img/colaborador/hombre-svg.png';
import { opcionesStore } from '../../../../../store/empresa/opciones';

const opcionesStorage = opcionesStore();

const sedes = computed(() => {
  return asisStore.SEDES;  // Acceder a las sedes almacenadas en el store
});

// Variables de ubicación
const latitude = ref(0);
const longitude = ref(0);
const exactitud = ref(0);
const mapInitialized = ref(false);
const watchId = ref(null);
const verId = ref(null);

// Llamado al Store
const functStore = funcionesStore();
const asisStore = asistenciaStore();

const mapReady = ref(false);

// Obtener la geolocalización del usuario
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

// Función para inicializar el mapa con las sedes y la ubicación actual
const initMap = (latitud, longitud, exact, sedes) => {
  if (!mapInitialized.value) {
    const ubiActual = { lat: latitud, lng: longitud };  // Ubicación actual del usuario

    const map = new google.maps.Map(document.getElementById('map'), {
      zoom: 10,
    });

    // Añadir el marcador de la ubicación actual del usuario
    const usuarioMarker = new google.maps.Marker({
      position: ubiActual,
      map: map,
      title: "Aquí estoy!",
      icon: { url: hombreImg, scaledSize: new google.maps.Size(50, 50) }
    });

    // Dibujar un círculo alrededor de la ubicación actual
    new google.maps.Circle({
      strokeColor: '#8CD0FF',
      strokeOpacity: 0.8,
      strokeWeight: 2,
      fillColor: '#8CD0FF',
      fillOpacity: 0.35,
      map: map,
      center: ubiActual,
      radius: 300  // Utilizar la exactitud como radio del círculo
    });

    // Crear los límites (bounds) para ajustar el mapa a todas las ubicaciones
    const bounds = new google.maps.LatLngBounds();
    bounds.extend(ubiActual);  // Incluir la ubicación del usuario en los límites

    // Añadir las sedes al mapa
    sedes.forEach((sede) => {
      const ubicacionSede = { lat: parseFloat(sede.latitud), lng: parseFloat(sede.longitud) };

      // Verificar si las coordenadas de la sede son válidas
      if (!isNaN(ubicacionSede.lat) && !isNaN(ubicacionSede.lng)) {
        // Crear un marcador para cada sede
        new google.maps.Marker({
          position: ubicacionSede,
          map: map,
          title: sede.nombre,
          icon: { url: edificioImg, scaledSize: new google.maps.Size(50, 50) }
        });

        // Dibujar un círculo para cada sede con su respectivo radio
        new google.maps.Circle({
          strokeColor: '#FF5733',
          strokeOpacity: 0.8,
          strokeWeight: 2,
          fillColor: '#FFC300',
          fillOpacity: 0.35,
          map: map,
          center: ubicacionSede,
          radius: sede.radio  // Radio por defecto de la sede
        });

        // Extender los límites del mapa para incluir la ubicación de la sede
        bounds.extend(ubicacionSede);
      }
    });

    // Ajustar el mapa para que todas las ubicaciones (usuario y sedes) queden dentro de los límites
    map.fitBounds(bounds);

    // Añadir el listener de tilesloaded
    const tiles = new google.maps.event.addListenerOnce(map, 'tilesloaded', () => {
      if (!mapInitialized.value) {
        mapInitialized.value = true;
        navigator.geolocation.clearWatch(watchId.value);
      }
    });
  }
};

// Llamar a la API y cargar las sedes en el mapa cuando el componente se monte
onMounted(() => {
  if ('geolocation' in navigator) {
    setTimeout(() => {
      const coordenadasInitMap = async () => {
        try {
          const coords = await watchCoordinates();  // Obtener las coordenadas del usuario
          latitude.value = coords.latitude;
          longitude.value = coords.longitude;
          exactitud.value = coords.accuracy;

          console.log("Initializing map with coordinates:", latitude.value, longitude.value);
          // Obtener el listado de sedes desde la API y luego inicializar el mapa
          await asisStore.fetchSedesMarcado();  // Llamada a la API para obtener las sedes

          console.log('sedes: ', sedes.value.length)
          if (!mapInitialized.value && sedes.value.length > 0) {
            initMap(latitude.value, longitude.value, exactitud.value, sedes.value);  // Inicializar el mapa con las sedes y la ubicación del usuario
          }
        } catch (error) {
          console.error("Error al obtener las coordenadas o sedes:", error);
        }
      };
      coordenadasInitMap();
    }, 500);
  }
});

// Cargar el script de Google Maps
window.initMap = function () {
  mapReady.value = true;
};
</script>

<style scoped>
.maps {
  height: 400px;
  width: 100%;
}
</style>
