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
import Marcado from './vue/controllers/colaborador/marcado.vue';
import EEEmpleados from './vue/controllers/empresa/empleados/empleados.vue';
import EHHorario from './vue/controllers/empresa/horario/horario.vue';

const eosede = createApp(EOSede);
const eeempleados = createApp(EEEmpleados);
const ehhorario = createApp(EHHorario);

eosede.use(pinia);
eosede.mount('#eosede');

const colaborador = createApp(Marcado);
colaborador.use(pinia);
colaborador.mount('#colaborador')
eeempleados.use(pinia);
eeempleados.mount('#eeempleados');

ehhorario.use(pinia);
ehhorario.mount('#ehhorario');

