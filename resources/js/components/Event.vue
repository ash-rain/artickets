<template>
    <div v-if="event" class="p-8 relative bg-cover bg-center rounded-lg shadow-lg">
        <div class="z-0">
            <div class="mb-6">
                <router-link to="/" class="bg-gray-500 text-white px-4 py-2 rounded mr-2 hover:bg-gray-600 transition">
                    &laquo;
                    {{ t('payment.event.back') }}
                </router-link>
            </div>

            <header class="mb-6">
                <img :src="event.image" :alt="event.title" class="w-full h-auto object-cover rounded-lg mb-4">
            </header>

            <h2 class="text-2xl font-bold mb-4">{{ event.title }}</h2>

            <div class="mb-4">
                <p class="text-gray-700"><span class="font-semibold">{{ t('payment.event.date') }}</span> {{ event.date
                    }}
                </p>
                <p class="text-gray-700"><span class="font-semibold">{{ t('payment.event.description') }}</span> {{
                    event.description }}</p>
            </div>

            <SeatsMatrix :event="event" />
        </div>
    </div>
    <div v-else-if="loading" class="text-center py-8">
        <p>{{ t('event.loading') }}</p>
    </div>
    <div v-else class="text-center py-8">
        <p>{{ t('event.not_found') }}</p>
    </div>
</template>

<script lang="ts">
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import axios from 'axios'
import SeatsMatrix from './SeatsMatrix.vue'
import { Event } from '../types/Event'
import { useI18n } from '../utils/i18n'

export default {
    name: 'Event',
    components: {
        SeatsMatrix
    },
    setup() {
        const route = useRoute()
        const { t } = useI18n()
        const event = ref<Event | null>(null)
        const loading = ref(true)

        onMounted(async () => {
            try {
                const response = await axios.get(`/api/events/${route.params.id}`)
                event.value = response.data
            } catch (error) {
                console.error('Error fetching event:', error)
            } finally {
                loading.value = false
            }
        })

        return {
            event,
            loading,
            t
        }
    }
}
</script>
