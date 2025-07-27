<template>
    <div>
        <h2 class="text-2xl font-bold mb-6">Events</h2>

        <div v-if="loading" class="text-center py-8">
            <p>Loading events...</p>
        </div>

        <div v-else>
            <div v-for="event in events" :key="event.id" class="mb-8 p-6 bg-white rounded-lg shadow-md">
                <h3 class="text-xl font-semibold mb-2">{{ event.title }}</h3>
                <p class="text-gray-600 mb-4">Venue: {{ event.venue.name }}</p>

                <router-link :to="{ name: 'Event', params: { id: event.id } }"
                    class="bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-6 rounded-lg transition duration-200">
                    View Seats
                </router-link>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { defineComponent, ref, onMounted } from 'vue';
import axios from 'axios';

export default defineComponent({
    name: 'Events',
    setup() {
        const events = ref([]);
        const loading = ref(true);

        onMounted(async () => {
            try {
                const response = await axios.get('/api/events');
                events.value = response.data;
            } catch (error) {
                console.error('Error fetching events:', error);
            } finally {
                loading.value = false;
            }
        });

        return {
            events,
            loading
        };
    }
});
</script>
