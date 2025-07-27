import { createRouter, createWebHistory, RouteRecordRaw } from 'vue-router';
import Events from '../components/Events.vue';
import Event from '../components/Event.vue';
import Payment from '../components/Payment.vue';

const routes: Array<RouteRecordRaw> = [
    {
        path: '/',
        name: 'events',
        component: Events
    },
    {
        path: '/event/:id',
        name: 'event',
        component: Event,
        props: true
    },
    {
        path: '/payment/:id',
        name: 'payment',
        component: Payment,
        props: true
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

export default router;
