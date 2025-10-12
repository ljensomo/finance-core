import { createRouter, createWebHistory } from 'vue-router';
import Dashboard from './components/Dashboard.vue';
import Transactions from './components/Transactions.vue';
import Categories from './components/Categories.vue';
import SubCategories from './components/SubCategories/SubCategories.vue';
import Wishlists from './components/Wishlists.vue';
import Reports from './components/Reports.vue';
import Settings from './components/Settings.vue';

const routes = [
  { path: '/dashboard', component: Dashboard },
  { path: '/transactions', component: Transactions },
  { path: '/categories', component: Categories },
  { path: '/wishlists', component: Wishlists },
  { path: '/reports', component: Reports },
  { path: '/settings', component: Settings },
  { path: '/sub-categories', component: SubCategories },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;