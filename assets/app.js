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

const eosede = createApp(EOSede)

eosede.use(pinia);
eosede.mount('#eosede');
