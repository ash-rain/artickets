<template>
    <div class="payment-container">
        <h2 class="text-2xl font-bold mb-6">Payment</h2>

        <div v-if="selectedSeats.length > 0" class="mb-8">
            <h3 class="text-lg font-semibold mb-4">Selected Seats</h3>
            <ul class="flex flex-col gap-2 mb-6">
                <li v-for="seat in selectedSeats" :key="seat.id" class="bg-gray-100 p-2 rounded">
                    {{ seat.section?.name || 'General' }} ({{ seat.section.price.toFixed(2) }}) - Row {{ seat.row }},
                    Seat {{
                        seat.column
                    }}
                </li>
            </ul>
            <p class="text-xl font-semibold">Total: ${{ totalPrice.toFixed(2) }}</p>
        </div>
        <div v-else class="mb-8">
            <p>No seats selected. Please go back and select seats.</p>
        </div>

        <form @submit.prevent="handleCheckout" class="mb-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div>
                    <label for="name" class="block mb-2">Full Name</label>
                    <input v-model="form.name" type="text" id="name" required class="w-full p-2 border rounded">
                </div>
                <div>
                    <label for="email" class="block mb-2">Email</label>
                    <input v-model="form.email" type="email" id="email" required class="w-full p-2 border rounded">
                </div>
                <div>
                    <label for="phone" class="block mb-2">Phone</label>
                    <input v-model="form.phone" type="tel" id="phone" required class="w-full p-2 border rounded">
                </div>
            </div>

            <div class="flex gap-4">
                <button type="button" @click="router.go(-1)"
                    class="bg-gray-500 text-white px-6 py-3 rounded hover:bg-gray-600 transition">
                    Back to Event
                </button>
                <button type="submit" :disabled="selectedSeats.length === 0"
                    class="bg-blue-600 text-white px-6 py-3 rounded hover:bg-blue-700 transition disabled:bg-gray-400 disabled:cursor-not-allowed">
                    Checkout
                </button>
            </div>
        </form>
    </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import axios from 'axios'
import { useSeatSelectionStore } from '../stores/seatSelection'
import { Seat } from '../types/Seat'

const route = useRoute()
const router = useRouter()
const seatStore = useSeatSelectionStore()
const selectedSeats = ref<Seat[]>([])
const totalPrice = ref(0)
const loading = ref(true)
const form = ref({
    name: '',
    email: '',
    phone: ''
})

onMounted(async () => {
    try {
        if (seatStore.eventId === Number(route.params.id)) {
            selectedSeats.value = seatStore.selectedSeats
            totalPrice.value = seatStore.totalPrice
        }
    } catch (error) {
        console.error('Error loading seats:', error)
    } finally {
        loading.value = false
    }
})

const handleCheckout = async () => {
    try {
        const response = await axios.post('/api/payments', {
            event_id: route.params.id,
            seat_ids: selectedSeats.value.map(seat => seat.id),
            name: form.value.name,
            email: form.value.email,
            phone: form.value.phone
        })
        seatStore.clearSeats()
        router.push({ name: 'events' })
    } catch (error) {
        console.error('Payment failed:', error)
        alert('Payment failed. Please try again. ' + error.message)
    }
}
</script>
