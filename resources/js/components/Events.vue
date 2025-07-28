<template>
    <div>
        <h2 class="text-2xl font-bold mb-6">Events</h2>

        <div v-if="loading" class="text-center py-8">
            <p>Loading events...</p>
        </div>

        <div v-else>
            <router-link v-for="event in events" :key="event.id" :to="{ name: 'event', params: { id: event.id } }">
                <div class="mb-8 p-6 bg-grey-300 rounded-lg shadow-md">
                    <div>
                        <img :src="event.image" alt="Event Image" class="w-full h-64 object-cover rounded-lg mb-4">
                    </div>

                    <h3 class="text-xl font-semibold mb-2">{{ event.title }}</h3>
                    <p class="text-gray-700 mb-2"><span class="font-semibold">Date:</span> {{ event.date }}</p>

                </div>
            </router-link>
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
