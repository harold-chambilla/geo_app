import { registerVueControllerComponents } from '@symfony/ux-vue';
import './bootstrap.js';
import './styles/app.scss';

import { createApp } from 'vue';
import { createPinia } from 'pinia';

const $ = require('jquery');
require('bootstrap');

registerVueControllerComponents(require.context('./vue/controllers', true, /\.vue$/));

const pinia = createPinia();

const googleMapsApiKey = 'AIzaSyBKG625KcwDUXUIvO0x22JMGYMV7DMqd7Q';
const googleMapsScript = document.createElement('script');
googleMapsScript.src = `https://maps.googleapis.com/maps/api/js?key=${googleMapsApiKey}&libraries=geometry&callback=initMap`;
googleMapsScript.async = true;
googleMapsScript.defer = true;

document.body.appendChild(googleMapsScript);
window.initMap = () => {}

// Importamos nuestras secciones y componentes

import App from "../assets/vue/controllers/test/App.vue";
import Resultado from "../assets/vue/controllers/colaborador/asistencia/resultado/Resultado.vue";
import Marcado from "../assets/vue/controllers/colaborador/asistencia/marcado/Marcado.vue";
import Map from "../assets/vue/controllers/colaborador/asistencia/marcado/Map.vue";
import Administracion from "../assets/vue/controllers/empresa/Administracion.vue";
import Horario from "../assets/vue/controllers/empresa/horario/secciones/Horario.vue";

const marcado = createApp(Marcado);
const app = createApp(App);
const resultado = createApp(Resultado);
const map = createApp(Map);
const administracion = createApp(Administracion);
const horario = createApp(Horario);

app.use(pinia);
app.mount('#app');

resultado.use(pinia);
resultado.mount('#resultado');

marcado.use(pinia);
marcado.mount('#marcado');

map.use(pinia);
map.mount('#mapa');

administracion.use(pinia);
administracion.mount('#administracion');

horario.mount('#horario');

