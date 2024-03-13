import { registerVueControllerComponents } from '@symfony/ux-vue';
import './bootstrap.js';

import { createApp } from 'vue';
import { createPinia } from 'pinia';
import App from "../assets/vue/controllers/App.vue";
// import Salida from "../assets/vue/controllers/Salida.vue"
import Salida from "../assets/vue/controllers/resultado/Resultado.vue";
import Marcado from "../assets/vue/controllers/marcado/Marcado.vue";
import Map from "../assets/vue/controllers/marcado/Map.vue";
const googleMapsApiKey = 'AIzaSyBKG625KcwDUXUIvO0x22JMGYMV7DMqd7Q';
const googleMapsScript = document.createElement('script');
googleMapsScript.src = `https://maps.googleapis.com/maps/api/js?key=${googleMapsApiKey}&libraries=geometry&callback=initMap`;
googleMapsScript.async = true;
googleMapsScript.defer = true;

// Agregar el script al final del cuerpo del documento
document.body.appendChild(googleMapsScript);


const pinia = createPinia();
const marcado = createApp(Marcado);
const app = createApp(App);
const salida = createApp(Salida);
const map = createApp(Map);

app.use(pinia);
app.mount('#app');

salida.use(pinia);
salida.mount('#salida');

marcado.use(pinia);
marcado.mount('#marcado');

map.use(pinia);
map.mount('#mapa')
/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.scss';

const $ = require('jquery');
// this "modifies" the jquery module: adding behavior to it
// the bootstrap module doesn't export/return anything
require('bootstrap');

registerVueControllerComponents(require.context('./vue/controllers', true, /\.vue$/));