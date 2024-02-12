import { registerVueControllerComponents } from '@symfony/ux-vue';
import './bootstrap.js';

import { createApp } from 'vue';
import App from "../assets/vue/controllers/App.vue";

const app = createApp(App);

app.mount('#app');
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