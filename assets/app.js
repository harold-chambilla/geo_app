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
googleMapsScript.src = `https://maps.googleapis.com/maps/api/js?key=${googleMapsApiKey}&libraries=places,geometry`;
// googleMapsScript.src = `https://maps.googleapis.com/maps/api/js?key=${googleMapsApiKey}&libraries=places`;
googleMapsScript.async = true;
googleMapsScript.defer = true;

document.body.appendChild(googleMapsScript);
// window.initMap = () => {}


    // assets/app.js

// export const loadGoogleMapsScript = () => {
//     return new Promise((resolve, reject) => {
//       // Verificamos si el script ya fue cargado
//       const existingScript = document.getElementById('googleMaps');
  
//       if (!existingScript) {
//         // Si no está cargado, creamos el script dinámicamente
//         const script = document.createElement('script');
//         script.src = `https://maps.googleapis.com/maps/api/js?key=AIzaSyBKG625KcwDUXUIvO0x22JMGYMV7DMqd7Q&libraries=places,geometry`;
//         script.id = 'googleMaps';
//         script.onload = resolve;
//         script.onerror = reject;
//         document.body.appendChild(script);
//       } else {
//         // Si ya está cargado, resolvemos la promesa directamente
//         resolve();
//       }
//     });
//   };
  
// Importamos nuestras secciones y componentes

import App from "../assets/vue/controllers/test/App.vue";
import Resultado from "../assets/vue/controllers/colaborador/asistencia/resultado/Resultado.vue";
import Marcado from "../assets/vue/controllers/colaborador/asistencia/marcado/Marcado.vue";
import Map from "../assets/vue/controllers/colaborador/asistencia/marcado/Map.vue";
import Administracion from "../assets/vue/controllers/empresa/Administracion.vue";
import Horario from "../assets/vue/controllers/empresa/horario/secciones/Horario.vue";
import APITester from "../assets/vue/controllers/empresa/APITester.vue";
import CrearEmpleado from './vue/controllers/empresa/empleados/CrearEmpleado.vue';
import CrearEmpleadoLocal from './vue/controllers/empresa/empleados/CrearEmpleadoLocal.vue';
import ListarEmpleados from './vue/controllers/empresa/empleados/ListarEmpleados.vue';
import VerEmpresa from './vue/controllers/empresa/opciones/VerEmpresa.vue';
import Map_Geocoding from './vue/controllers/test/Map_Geocoding.vue';
import Area from './vue/controllers/empresa/opciones/Area.vue';
import Permisos from './vue/controllers/empresa/opciones/Permisos.vue';

const marcado = createApp(Marcado);
const app = createApp(App);
const resultado = createApp(Resultado);
const map = createApp(Map);
const administracion = createApp(Administracion);
const horario = createApp(Horario);
const api_tester = createApp(APITester);
const crearEmpleado = createApp(CrearEmpleado);
const crear_empleado_local = createApp(CrearEmpleadoLocal);
const listar_empleados = createApp(ListarEmpleados);
const ver_empresa = createApp(VerEmpresa);
const area = createApp(Area);
const permisos = createApp(Permisos);

const mapGeo = createApp(Map_Geocoding);

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

crearEmpleado.use(pinia);
crearEmpleado.mount('#crear-empleado');

horario.mount('#horario');

api_tester.use(pinia);
api_tester.mount('#api_tester');

crear_empleado_local.use(pinia);
crear_empleado_local.mount('#crear_empleado_local');

listar_empleados.use(pinia);
listar_empleados.mount('#listar_empleados');

ver_empresa.use(pinia);
ver_empresa.mount('#ver_empresa');

mapGeo.use(pinia);
mapGeo.mount('#mapGeo');
area.use(pinia);
area.mount("#area");

permisos.use(pinia);
permisos.mount('#permisos');
