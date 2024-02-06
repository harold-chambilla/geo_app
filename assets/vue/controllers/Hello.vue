<template>
  <div>
    <div class="container mt-5">
      <div class="row justify-content-center">
        <div class="col-md-8">
          <div class="card">
            <div class="card-header">Mapa y Coordenadas</div>
            <div class="card-body">
              <p v-if="errorMessage" class="alert alert-danger">{{ errorMessage }}</p>
              <p class="mb-1">Latitud: {{ latitude }}</p>
              <p class="mb-4">Longitud: {{ longitude }}</p>
              <div id="map" class="mb-4"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
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

    navigator.geolocation.getCurrentPosition((position) => {
        latitude.value = position.coords.latitude;
        longitude.value = position.coords.longitude;
        console.log('Ubicación del usuario obtenida: ', position);

        const script = document.createElement('script');
        script.src = `https://maps.googleapis.com/maps/api/js?key=AIzaSyBKG625KcwDUXUIvO0x22JMGYMV7DMqd7Q&callback=initMap`;
        script.defer = true;
        script.async = true;
        script.onerror = () => {
          console.error("Error al cargar la API de Google Maps");
        };
        document.head.appendChild(script);
      },
      (error) => {
        errorMessage.value = `Error al obtener la ubicación: ${error.message}`;
        console.error('Geolocation error:', error);
      }
    );
  
    window.initMap = () => {
      console.log('latitud:', latitude.value);
      console.log('longitud:', longitude.value);
      const ubiActual = { lat: latitude.value, lng: longitude.value }
      const mapOptions = {
        center: ubiActual,
        zoom: 18
      };
      const map = new google.maps.Map(document.getElementById('map'), mapOptions);
      const marker = new google.maps.Marker({ position: ubiActual, map: map, title: "Aqui estoy!" })
      console.log("MARKER:", marker.title)
    };

  });

  
  </script>
  