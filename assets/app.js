import { registerVueControllerComponents } from '@symfony/ux-vue';
import './bootstrap.js';
import './styles/app.scss';

import { createApp } from 'vue';
import { createPinia } from 'pinia';
import piniaPersist from 'pinia-plugin-persistedstate';

const $ = require('jquery');
require('bootstrap');

registerVueControllerComponents(require.context('./vue/controllers', true, /\.vue$/));

const pinia = createPinia();
pinia.use(piniaPersist);

// Importar los componentes de Vue - Empresa
import EOSede from './vue/controllers/empresa/opciones/sede.vue';
import EEEmpleados from './vue/controllers/empresa/empleados/empleados.vue';
import EHHorario from './vue/controllers/empresa/horario/horario.vue';

const eosede = createApp(EOSede);
const eeempleados = createApp(EEEmpleados);
const ehhorario = createApp(EHHorario);

eosede.use(pinia);
eeempleados.use(pinia);
ehhorario.use(pinia);

eosede.mount('#eosede');
eeempleados.mount('#eeempleados');
ehhorario.mount('#ehhorario');

// Importar los componentes de Vue - Colaborador

import CMMarcado from './vue/controllers/colaborador/marcado/marcado.vue';

const cmmarcado = createApp(CMMarcado);

cmmarcado.use(pinia);

cmmarcado.mount('#cmmarcado')



