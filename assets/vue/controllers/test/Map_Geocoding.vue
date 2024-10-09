<template>

  <div id="direccionDiv" style="justify-content: center; align-items: center;">
    <div class="form-row d-flex input-amarillo justify-content-center align-items-center">

      <p class="Plbra3 " for="direccionInput">UBICACION</p>
      <input id="autocomplete" type="text" class="inputContainer1" placeholder="Ingresa una dirección" />
    </div>

    <div class="form-row d-flex justify-content-center align-items-center min-vh-40">
      <div id="map2" class="col-md-7" style="padding=20px; width: 100%; height: 200px;"></div>
      <!-- <div id="map2" style="width: 100%; height: 400px;"></div> -->

      <div class="col-md-5 justify-content-center align-items" style="padding=20px; height=100px">
        <div class="linea_separadora"></div>
        <div class="form-row d-flex justify-content-center align-items-center min-vh-40">
          <div style="float: left;" id="Nom_Empresa">
            <div for="nombreEmpresa" id="Nom_Empresa" class="Plbra3" style="width:200px">Nombre de la Empresa:</div>
            <input type="text" v-model="nombreEmpresa" id="nombreEmpresa" placeholder="Nombre de la Empresa"
              class="inputContainer1 ">
            <button class="Btn2" @click="saveCoordinates">Guardar</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { opcionesStore } from '../../../store/empresa/opciones';

// Variables reactivas para almacenar las coordenadas y el país
const nombreEmpresa = ref('');
const latitude = ref(null);
const longitude = ref(null);
const direccion = ref('');
const country = ref(null);  // Nueva variable para almacenar el país

let map;
let marker;

// Instancia del store de Pinia
const opcionesStorage = opcionesStore();

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

    // Actualizamos las coordenadas y dirección con la ubicación seleccionada
    latitude.value = place.geometry.location.lat();
    longitude.value = place.geometry.location.lng();
    direccion.value = input.value;

    // Extraer el país de los address_components
    const addressComponents = place.address_components;
    country.value = getCountryFromAddress(addressComponents);

    // Mover el marcador y centrar el mapa en la ubicación seleccionada
    const location = place.geometry.location;
    map.setCenter(location);
    marker.setPosition(location);
  });
};

// Función para obtener el país de los componentes de la dirección
const getCountryFromAddress = (addressComponents) => {
  for (const component of addressComponents) {
    if (component.types.includes("country")) {
      return component.long_name;
    }
  }
  return null; // Si no se encuentra el   país, devolver null
};

// Función para inicializar el mapa
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

// Función para guardar las coordenadas en el backend al hacer clic en el botón
const saveCoordinates = () => {
  // Guardar las coordenadas, dirección y país en el store de Pinia
  opcionesStorage.saveCoordinates({
    empresaNombre: nombreEmpresa.value,
    latitude: latitude.value,
    longitude: longitude.value,
    direccion: direccion.value,
    pais: country.value
  });

  nombreEmpresa.value = '';
  document.getElementById('autocomplete').value = '';
  direccion.value = '';
  country.value= '';

  

  // Llamar al método que envía los datos al backend
  // opcionesStorage.createSede();
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
