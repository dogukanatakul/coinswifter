import {createRouter, createWebHistory} from "vue-router";
import authRoutes from './auth';
import pagesRoutes from './pages';

let routes = [];

routes = routes.concat(authRoutes);
routes = routes.concat(pagesRoutes);
const router = createRouter({
    mode: 'history',
    history: createWebHistory(process.env.BASE_URL),
    routes,
});

export default router;
