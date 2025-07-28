<template>
    <div class="payment-container">
        <h2 class="text-2xl font-bold mb-6">Payment</h2>

        <div v-if="selectedSeats.length > 0" class="mb-8">
            <h3 class="text-lg font-semibold mb-4">Selected Seats</h3>
            <ul class="grid grid-cols-4 gap-2 mb-6">
                <li v-for="seat in selectedSeats" :key="seat.id" class="bg-gray-100 p-2 rounded">
                    {{ seat.section }} - Row {{ seat.row }}, Seat {{ seat.column }}
                </li>
            </ul>
            <p class="text-xl font-semibold">Total: ${{ totalPrice.toFixed(2) }}</p>
        </div>
        <div v-else class="mb-8">
            <p>No seats selected. Please go back and select seats.</p>
        </div>

        <button @click="handleCheckout" :disabled="selectedSeats.length === 0"
            class="bg-blue-600 text-white px-6 py-3 rounded hover:bg-blue-700 transition disabled:bg-gray-400 disabled:cursor-not-allowed">
            Checkout
        </button>
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

onMounted(async () => {
    try {
        if (seatStore.eventId === Number(route.params.id)) {
            selectedSeats.value = seatStore.selectedSeats
            totalPrice.value = seatStore.totalPrice
        } else if (route.query.seats) {
            const seatIds = route.query.seats.toString().split(',').map(Number)
            if (seatIds.length > 0) {
                const response = await axios.get(`/api/events/${route.params.id}/seats`, {
                    params: { ids: seatIds.join(',') }
                })
                selectedSeats.value = response.data
                totalPrice.value = selectedSeats.value.length * 25 // $25 per seat
            }
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
            seat_ids: selectedSeats.value.map(seat => seat.id)
        })
        seatStore.clearSeats()
        router.push({ name: 'events' })
    } catch (error) {
        console.error('Payment failed:', error)
        alert('Payment failed. Please try again.')
    }
}
</script>

<style scoped>
.payment-container {
    max-width: 800px;
    margin: 0 auto;
    padding: 2rem;
}
</style>
