import { createRouter, createWebHistory, RouteRecordRaw } from 'vue-router';
import Events from '../components/Events.vue';
import Event from '../components/Event.vue';
import Payment from '../components/Payment.vue';

const routes: Array<RouteRecordRaw> = [
    {
        path: '/',
        name: 'Events',
        component: Events
    },
    {
        path: '/events/:id',
        name: 'Event',
        component: Event,
        props: true
    },
    {
        path: '/payment',
        name: 'Payment',
        component: Payment,
        props: (route) => ({ seatIds: route.query.seatIds })
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

export default router;
