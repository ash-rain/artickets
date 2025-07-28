<template>
    <div>
        <h2 class="text-2xl font-bold mb-6">{{ t('events.title') }}</h2>

        <div v-if="loading" class="text-center py-8">
            <p>{{ t('events.loading') }}</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3" v-else>
            <router-link v-for="event in events" :key="event.id" :to="{ name: 'event', params: { id: event.id } }">
                <div class="mb-8 p-6 bg-gray-300 hover:bg-green-300 rounded-lg shadow-md">
                    <div>
                        <img :src="event.image" alt="Event Image" class="w-full h-64 object-cover rounded-lg mb-4">
                    </div>

                    <h3 class="text-xl font-semibold mb-2">{{ event.title }}</h3>
                    <p class="text-gray-700 mb-2">
                        <span class="font-semibold">{{ t('events.date') }}</span>
                        {{ event.date }}
                    </p>
                </div>
            </router-link>
        </div>
    </div>
</template>

<script lang="ts">
import { defineComponent, ref, onMounted } from 'vue';
import axios from 'axios';
import { useI18n } from '../utils/i18n';

interface Event {
    id: number;
    title: string;
    date: string;
    image: string;
}

export default defineComponent({
    name: 'Events',
    setup() {
        const events = ref<Event[]>([]);
        const loading = ref(true);
        const { t } = useI18n();

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
            loading,
            t
        };
    }
});
</script>
