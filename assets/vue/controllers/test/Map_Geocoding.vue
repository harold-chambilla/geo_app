<template>
  <div>
    <div>Holla</div>
    <!-- Campo de autocompletado para la dirección -->
    <input id="autocomplete" type="text" placeholder="Ingresa una dirección" />

    <!-- Mapa de Google Maps -->
    <div id="map2" style="width: 100%; height: 400px;"></div>

    <!-- Mostrar las coordenadas de la dirección seleccionada -->
    <div v-if="latitude && longitude">
      <p><strong>Latitud:</strong> {{ latitude }}</p>
      <p><strong>Longitud:</strong> {{ longitude }}</p>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';

// Variables reactivas para almacenar las coordenadas
const latitude = ref(null);
const longitude = ref(null);
let map;
let marker;

// Función que inicializa el mapa y el autocompletado
const initAutocomplete = () => {
  const input = document.getElementById('autocomplete');
  
  // Inicializamos el autocompletado de Google Places
  const autocomplete = new google.maps.places.Autocomplete(input, {
    types: ['geocode'], // Limitar el autocompletado solo a direcciones
  });

  // Escuchar cuando el usuario selecciona una opción del autocompletado
  autocomplete.addListener('place_changed', () => {
    const place = autocomplete.getPlace();
    
    if (!place.geometry) {
      console.error('No se pudo obtener la ubicación.');
      return;
    }

    // Actualizamos las coordenadas con la ubicación seleccionada
    latitude.value = place.geometry.location.lat();
    longitude.value = place.geometry.location.lng();

    // Mover el marcador y centrar el mapa en la ubicación seleccionada
    const location = place.geometry.location;
    map.setCenter(location);
    marker.setPosition(location);
  });
};

// Función que inicializa el mapa
const initMap = (lat, lng) => {
  const mapOptions = {
    center: { lat, lng }, // Inicializar con la ubicación proporcionada
    zoom: 15,
  };
  
  // Crear el mapa y agregar el marcador
  map = new google.maps.Map(document.getElementById('map2'), mapOptions);
  marker = new google.maps.Marker({
    position: { lat, lng },
    map: map,
  });
};

// Obtener la ubicación actual del usuario
const setUserLocation = () => {
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition((position) => {
      const userLat = position.coords.latitude;
      const userLng = position.coords.longitude;
      latitude.value = userLat;
      longitude.value = userLng;

      // Inicializar el mapa con la ubicación del usuario
      initMap(userLat, userLng);
    }, (error) => {
      console.error('Error al obtener la ubicación del usuario', error);
      // Si hay un error, inicializar el mapa en una ubicación predeterminada
      initMap(-34.397, 150.644); // Ubicación por defecto
    });
  } else {
    console.error('Geolocalización no soportada por este navegador.');
    // Si la geolocalización no está disponible, inicializar en una ubicación predeterminada
    initMap(-34.397, 150.644); // Ubicación por defecto
  }
};

// Cargar el script de Google Maps y la API de Places
const loadGoogleMapsScript = () => {
  return new Promise((resolve, reject) => {
    const existingScript = document.getElementById('googleMaps');

    if (!existingScript) {
      const script = document.createElement('script');
      script.src = `https://maps.googleapis.com/maps/api/js?key=AIzaSyBKG625KcwDUXUIvO0x22JMGYMV7DMqd7Q&libraries=places`;
      script.id = 'googleMaps';
      script.onload = resolve;
      script.onerror = reject;
      document.body.appendChild(script);
    } else {
      resolve();
    }
  });
};

// Ejecutar la carga del script y el autocompletado al montar el componente
onMounted(() => {
  loadGoogleMapsScript().then(() => {
    setUserLocation(); // Obtener la ubicación actual del usuario
    initAutocomplete(); // Iniciar el autocompletado
  }).catch((error) => {
    console.error('Error al cargar Google Maps', error);
  });
});
</script>

<style scoped>
/* Estilos básicos para mejorar la presentación */
#autocomplete {
  width: 100%;
  padding: 10px;
  font-size: 16px;
}
</style>
