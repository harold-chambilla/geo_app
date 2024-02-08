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
              <p class="">Distancia: {{ distancia }}</p>
              <p class="">Asistencia: {{ asistencia }}</p>
              <div id="map" class="mb-4"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
  
  <script setup>
  import { ref, onMounted } from 'vue';
  
  const latitude = ref(0);
  const longitude = ref(0);
  const errorMessage = ref(null);
  const distancia = ref(null)
  const asistencia = ref("");
  
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
        script.src = `https://maps.googleapis.com/maps/api/js?key=AIzaSyBKG625KcwDUXUIvO0x22JMGYMV7DMqd7Q&callback=initMap&libraries=geometry`;
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
      const ubiActual = { lat: latitude.value, lng: longitude.value };
      // const ubiDestino = { lat: -12.033055274005628, lng: -76.99143343148506 };
      // const ubiDestino = { lat: -12.058597, lng: -76.964071 };
      const ubiDestino = { lat: -12.085184, lng: -76.976101};
      const mapOptions = {
        center: ubiDestino,
        zoom: 16
      };
      const map = new google.maps.Map(document.getElementById('map'), mapOptions);
      const marker = [ 
        new google.maps.Marker({ position: ubiActual, map: map, title: "Aqui estoy!" }),
        new google.maps.Marker({ position: ubiDestino, map: map })
      ]
      const radio = 800;
      const circle = new google.maps.Circle({
            strokeColor: '#8CD0FF',
            strokeOpacity: 0.8,
            strokeWeight: 2,
            fillColor: '#8CD0FF',
            fillOpacity: 0.35,
            map: map,
            center: ubiDestino,
            radius: radio
        });
      const dist = google.maps.geometry.spherical.computeDistanceBetween(ubiActual, ubiDestino);

      
      if (dist <= radio) {
        alert("Si llego");
        const text = "Si esta en la empresa";
        asistencia.value = text;
      } else {
        alert("No llego");
        const text = "No esta en la empresa";
        asistencia.value = text;
      }

      distancia.value = dist;
      console.log("MARKER:", marker.title)
    };

  });

  
  </script>
  