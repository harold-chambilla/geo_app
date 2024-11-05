import { registerVueControllerComponents } from '@symfony/ux-vue';
import './bootstrap.js';
import './styles/app.scss';

import { createApp } from 'vue';
import { createPinia } from 'pinia';

const $ = require('jquery');
require('bootstrap');

registerVueControllerComponents(require.context('./vue/controllers', true, /\.vue$/));

const pinia = createPinia();


import EOSede from './vue/controllers/empresa/opciones/sede.vue';
import EEEmpleados from './vue/controllers/empresa/empleados/empleados.vue';

const eosede = createApp(EOSede);
const eeempleados = createApp(EEEmpleados);

eosede.use(pinia);
eosede.mount('#eosede');

eeempleados.use(pinia);
eeempleados.mount('#eeempleados');
