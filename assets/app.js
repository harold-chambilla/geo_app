import { registerVueControllerComponents } from '@symfony/ux-vue';
import './bootstrap.js';
import './styles/app.scss';

import { createApp } from 'vue';
import { createPinia } from 'pinia';

const $ = require('jquery');
require('bootstrap');

registerVueControllerComponents(require.context('./vue/controllers', true, /\.vue$/));

const pinia = createPinia();

import EOEmpresa from "../assets/vue/controllers/empresa/opciones/empresa.vue";
import EOArea from "../assets/vue/controllers/empresa/opciones/area.vue";
import EOSede from './vue/controllers/empresa/opciones/sede.vue';

const eoempresa = createApp(EOEmpresa);
const eoarea = createApp(EOArea);
const esede = createApp(EOSede)

eoempresa.use(pinia);
eoempresa.mount('#eoempresa');

eoarea.use(pinia);
eoarea.mount('#eoarea');

esede.use(pinia);
esede.mount('#eosede');
