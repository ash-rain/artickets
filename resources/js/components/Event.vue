<template>
    <div v-if="event">
        <header class="mb-6">
            <img :src="event.image" alt="Event Image" class="w-full h-64 object-cover rounded-lg mb-4">
        </header>

        <div class="my-6">
            <router-link to="/" class="bg-gray-500 text-white px-4 py-2 rounded mr-2 hover:bg-gray-600 transition">
                Back to Events
            </router-link>
            <router-link :to="`/payment/${event.id}`"
                class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                Buy Tickets
            </router-link>
        </div>

        <h2 class="text-2xl font-bold mb-4">{{ event.title }}</h2>

        <div class="mb-4">
            <p class="text-gray-700"><span class="font-semibold">Date:</span> {{ event.date }}</p>
            <p class="text-gray-700"><span class="font-semibold">Description:</span> {{ event.description }}</p>
        </div>

        <EventSeating :event="event" />
    </div>
    <div v-else-if="loading" class="text-center py-8">
        <p>Loading event details...</p>
    </div>
    <div v-else class="text-center py-8">
        <p>Event not found</p>
    </div>
</template>

<script lang="ts">
import { defineComponent, ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import axios from 'axios'

export default defineComponent({
    name: 'Event',
    setup() {
        const route = useRoute()
        const event = ref(null)
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
            loading
        }
    }
})
</script>
