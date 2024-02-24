<template>
    <div class="container mt-5">
        <div class="col-md-6">
          <div id="map" style="height: 400px;"></div>
        </div>
        <div>
        <p>Latitud: {{ latitude }}</p>
        <p>Longitud: {{ longitude }}</p>
        <p>Exactitud: {{ exactitud }}</p>
      </div>
    </div>
  </template>
  
  <script setup>
  // Dato a enviar a otro formulario se le pondra (*), los datos que no van a estar son estos (-), Datos que iran en infoWindows(+)
  // Datos que se van a quedar en la misma vista (/)
  import { ref, onMounted } from 'vue';
  
  // Variables de ubicación
  const latitude = ref(0);
  const longitude = ref(0);
  const exactitud = ref(0);
  const mapInitialized = ref(false);
  const watchId = ref(null);
  // Variables adicionales
  const marker = ref(null);
  const map = ref(null);
  const distancia = ref(null);
  
  onMounted(() => {
    if ('geolocation' in navigator) {
      setTimeout(() => {
        watchId.value = navigator.geolocation.watchPosition((position) => {
          latitude.value = position.coords.latitude;
          longitude.value = position.coords.longitude;
          exactitud.value = position.coords.accuracy;
          if (!mapInitialized.value) {
            initMap();      
          } else {
            marker.value.setPosition({ lat: latitude.value, lng: longitude.value});
            // map.value.setCenter({ lat: latitude.value, lng: longitude.value})
            // console.log('map: ', marker);
          }
          // 
        },
        (error) => {
          errorMessage.value = `Error al obtener la ubicación: ${error.message}`;
          console.error('Geolocation error:', error);
        },
        { enableHighAccuracy: true, timeout:5000, maximumAge: 0}
        )
      }, 500);
    }
  });
  
  // Esta función se ejecutará una vez que la API de Google Maps esté cargada y lista
  const initMap = () => {
  // console.log(latitude)
    if (!mapInitialized.value) {
  console.log("latitud:", latitude.value)
  
      const ubiActual = { lat: latitude.value, lng: longitude.value };
      const ubiAleatoria = { lat: -12.058624, lng: -76.9654784 };
  
      const mapOptions = {
        center: ubiActual,
        zoom: 18
      };
  
      const map = new google.maps.Map(document.getElementById('map'), mapOptions);
      
      marker.value = new google.maps.Marker({ position: ubiActual, map: map, title: "Aqui estoy!" });
      // console.log("markaddorr: ", marker.value.position)
      // marker.setPosition(ubiActual);
      // map.setCenter(ubiActual);
      //const lat = new google.maps.LatLng(-12.033077753513151, -76.99137861370299)
  
      // const tiles = new google.maps.event.addListenerOnce(map, 'tilesloaded', () => {
      //   if (!mapInitialized.value) {
      mapInitialized.value = true;
      //     // Limpiar el observador de ubicación una vez que el usuario esté satisfecho con la ubicación  
      //     navigator.geolocation.clearWatch(watchId.value);
      //   }
      // });
  
    }
  };
  // window.initMap = initMap;
  </script>