<template>

      <!-- Botón para abrir el modal -->
      <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#sedeModal">Agregar Sede</button>
  
      <!-- Modal de Bootstrap -->
      <div class="modal fade" id="sedeModal" tabindex="-1" aria-labelledby="sedeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="sedeModalLabel">Registrar Nueva Sede</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <div class="container">
                <div class="row">
                  <!-- Columna con el mapa de Google Maps -->
                  <div class="col-md-7 mb-3">
                    <div id="map2" style="width: 100%; height: 300px;"></div>
                  </div>
                  <!-- Columna con los campos de entrada -->
                  <div class="col-md-5">
                    <div class="mb-3">
                      <label for="nombreEmpresa" class="form-label">Nombre de la Empresa:</label>
                      <input type="text" v-model="nombreEmpresa" id="nombreEmpresa" placeholder="Nombre de la Empresa" class="form-control">
                    </div>
                    <div class="mb-3">
                      <label for="latInput" class="form-label">Latitud:</label>
                      <input v-model="latitude" type="text" class="form-control" id="latInput" placeholder="Ingresa la latitud">
                    </div>
                    <div class="mb-3">
                      <label for="lngInput" class="form-label">Longitud:</label>
                      <input v-model="longitude" type="text" class="form-control" id="lngInput" placeholder="Ingresa la longitud">
                    </div>
                    <button class="btn btn-info" @click="actualizarMapa">Actualizar Mapa</button>
                    <div class="mt-3">
                      <p><strong>País:</strong> {{ country }}</p>
                      <p><strong>Dirección:</strong> {{ direccion }}</p>
                      <p><strong>Departamento:</strong> {{ departamento }}</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
              <button type="button" class="btn btn-primary" @click="saveCoordinates">Guardar Sede</button>
            </div>
          </div>
        </div>

    </div>
  </template>
  
  <script setup>
  import { ref, onMounted } from 'vue';
import { useOpcionesStore } from '../../../../store/empresa/opciones';
  
  const nombreEmpresa = ref('');
  const latitude = ref('');
  const longitude = ref('');
  const direccion = ref('');
  const country = ref('');
  const departamento = ref('');
  let map;
  let marker;
  
  // Instancia del store de Pinia
  const opcionesStorage = useOpcionesStore();
  
  // Inicializar el mapa
  const initMap = () => {
    const defaultLocation = { lat: -12.046374, lng: -77.0427934 }; // Lima, Perú
    map = new google.maps.Map(document.getElementById('map2'), {
      center: defaultLocation,
      zoom: 15
    });
    marker = new google.maps.Marker({
      position: defaultLocation,
      map: map
    });
  };
  
  // Actualizar el mapa con las coordenadas introducidas
  const actualizarMapa = async () => {
    const lat = parseFloat(latitude.value);
    const lng = parseFloat(longitude.value);
  
    if (isNaN(lat) || isNaN(lng)) {
      alert('Por favor, ingrese coordenadas válidas');
      return;
    }
  
    const nuevaUbicacion = { lat, lng };
    map.setCenter(nuevaUbicacion);
    marker.setPosition(nuevaUbicacion);
  
    // Obtener la dirección, país y departamento con Reverse Geocoding
    const geocoder = new google.maps.Geocoder();
    const response = await geocoder.geocode({ location: nuevaUbicacion });
    if (response.results[0]) {
      direccion.value = response.results[0].formatted_address;
      const addressComponents = response.results[0].address_components;
      country.value = obtenerComponenteDireccion(addressComponents, 'country');
      departamento.value = obtenerComponenteDireccion(addressComponents, 'administrative_area_level_1');
      console.log('Dirección:', direccion.value);
      console.log('País:', country.value);
      console.log('Departamento:', departamento.value);
    }
  };
  
  // Función auxiliar para extraer el país o departamento de los resultados de Geocoding
  const obtenerComponenteDireccion = (components, type) => {
    const result = components.find(component => component.types.includes(type));
    return result ? result.long_name : '';
  };
  
  // Guardar las coordenadas en el store de Pinia
  const saveCoordinates = () => {
    opcionesStorage.saveCoordinates({
      empresaNombre: nombreEmpresa.value,
      latitude: latitude.value,
      longitude: longitude.value,
      direccion: direccion.value,
      pais: country.value
    });
  
    // Limpiar campos
    nombreEmpresa.value = '';
    latitude.value = '';
    longitude.value = '';
    direccion.value = '';
    country.value = '';
    departamento.value = '';
  };
  
  // Cargar el script de Google Maps
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
  
  // Ejecutar al montar el componente
  onMounted(() => {
    loadGoogleMapsScript().then(() => {
      initMap(); // Inicializar el mapa
    }).catch((error) => {
      console.error('Error al cargar Google Maps', error);
    });
  });
  </script>
  
  <style scoped>
  .maps {
    height: 300px;
    width: 100%;
  }
  </style>
  