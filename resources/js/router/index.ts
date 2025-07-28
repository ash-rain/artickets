import { createRouter, createWebHistory, RouteRecordRaw } from 'vue-router';
import Events from '../components/Events.vue';
import Event from '../components/Event.vue';
import Payment from '../components/Payment.vue';

const routes: Array<RouteRecordRaw> = [
    {
        path: '/:lang(en|bg)',
        name: 'events',
        component: Events,
        props: true
    },
    {
        path: '/:lang(en|bg)/event/:id',
        name: 'event',
        component: Event,
        props: true
    },
    {
        path: '/:lang(en|bg)/payment/:id',
        name: 'payment',
        component: Payment,
        props: true
    },
    {
        path: '/',
        redirect: '/bg'
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

// Set language from route param
router.beforeEach((to, from, next) => {
    const lang = to.params.lang as string || 'bg';
    document.documentElement.lang = lang;
    next();
});

export default router;
