import './bootstrap';

import * as bootstrap from 'bootstrap';
window.bootstrap = bootstrap;

import { createApp } from 'vue';
import router from './router';
import VueSweetalert2 from 'vue-sweetalert2';
import { BApp } from 'bootstrap-vue-next';

// My Components
import Transactions from './components/Transactions.vue';
import Categories from './components/Categories.vue';
import { Helpers } from './methods/helpers.js';

import 'sweetalert2/dist/sweetalert2.min.css';
import 'bootstrap/dist/css/bootstrap.css';
import 'bootstrap-vue-next/dist/bootstrap-vue-next.css';

const app = createApp({});

app.use(VueSweetalert2);
app.mixin(Helpers);

app.component('transactions', Transactions);
app.component('categories', Categories);

app.use(router);
app.mount('#app');
