import './bootstrap';

import * as bootstrap from 'bootstrap';
window.bootstrap = bootstrap;

import { createApp } from 'vue';
import router from './router';
import VueSweetalert2 from 'vue-sweetalert2';
import { BApp } from 'bootstrap-vue-next';

import { Helpers } from './methods/helpers.js';

import 'sweetalert2/dist/sweetalert2.min.css';
import 'bootstrap/dist/css/bootstrap.css';
import 'bootstrap-vue-next/dist/bootstrap-vue-next.css';

const app = createApp({});

app.use(VueSweetalert2);
app.mixin(Helpers);

app.use(router);
app.mount('#app');
