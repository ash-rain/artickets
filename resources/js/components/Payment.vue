<template>
    <div v-if="event">
        <h2 class="text-2xl font-bold mb-4">Purchase Tickets for {{ event.title }}</h2>

        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2">Select Seats</label>
                <div class="grid grid-cols-5 gap-2">
                    <div v-for="seat in event.venue.sections[0].seats" :key="seat.id"
                        class="p-2 border rounded text-center cursor-pointer hover:bg-gray-100" :class="{
                            'bg-green-200': selectedSeats.includes(seat.id),
                            'bg-red-200': !seat.available
                        }" @click="toggleSeat(seat)">
                        Seat {{ seat.row }}{{ seat.number }}
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2">Card Number</label>
                <input type="text" class="w-full px-3 py-2 border rounded" placeholder="1234 5678 9012 3456"
                    v-model="cardNumber" />
            </div>

            <div class="flex space-x-4 mb-6">
                <div class="w-1/2">
                    <label class="block text-gray-700 font-semibold mb-2">Expiration</label>
                    <input type="text" class="w-full px-3 py-2 border rounded" placeholder="MM/YY"
                        v-model="expiration" />
                </div>
                <div class="w-1/2">
                    <label class="block text-gray-700 font-semibold mb-2">CVV</label>
                    <input type="text" class="w-full px-3 py-2 border rounded" placeholder="123" v-model="cvv" />
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 font-semibold mb-2">Email</label>
                <input type="email" class="w-full px-3 py-2 border rounded" placeholder="you@example.com"
                    v-model="email" />
            </div>

            <div class="flex justify-between items-center">
                <router-link :to="`/event/${event.id}`"
                    class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition">
                    Back to Event
                </router-link>
                <button class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700 transition"
                    @click="processPayment" :disabled="processing">
                    <span v-if="processing">Processing...</span>
                    <span v-else>Complete Purchase (${{ totalPrice }})</span>
                </button>
            </div>
        </div>

        <div v-if="error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            {{ error }}
        </div>

        <div v-if="success" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
            Payment successful! Your tickets have been reserved.
        </div>
    </div>
    <div v-else-if="loading" class="text-center py-8">
        <p>Loading payment details...</p>
    </div>
    <div v-else class="text-center py-8">
        <p>Event not found</p>
    </div>
</template>

<script lang="ts">
import { defineComponent, ref, onMounted, computed } from 'vue'
import { useRoute } from 'vue-router'
import axios from 'axios'

interface Seat {
    id: number
    row: string
    number: number
    available: boolean
    price: number
}

interface Section {
    id: number
    name: string
    seats: Seat[]
}

interface Venue {
    id: number
    name: string
    sections: Section[]
}

interface Event {
    id: number
    title: string
    venue: Venue
}

export default defineComponent({
    name: 'Payment',
    setup() {
        const route = useRoute()
        const event = ref<Event | null>(null)
        const loading = ref(true)
        const selectedSeats = ref<number[]>([])
        const cardNumber = ref('')
        const expiration = ref('')
        const cvv = ref('')
        const email = ref('')
        const processing = ref(false)
        const error = ref('')
        const success = ref(false)

        const totalPrice = computed(() => {
            if (!event.value) return 0
            return selectedSeats.value.reduce((total, seatId) => {
                const seat = event.value!.venue.sections
                    .flatMap(s => s.seats)
                    .find(s => s.id === seatId)
                return total + (seat?.price || 0)
            }, 0)
        })

        const toggleSeat = (seat: Seat) => {
            if (!seat.available) return

            const index = selectedSeats.value.indexOf(seat.id)
            if (index > -1) {
                selectedSeats.value.splice(index, 1)
            } else {
                selectedSeats.value.push(seat.id)
            }
        }

        const processPayment = async () => {
            if (selectedSeats.value.length === 0) {
                error.value = 'Please select at least one seat'
                return
            }

            if (!cardNumber.value || !expiration.value || !cvv.value || !email.value) {
                error.value = 'Please fill all payment details'
                return
            }

            processing.value = true
            error.value = ''

            try {
                const response = await axios.post('/api/payments', {
                    event_id: event.value?.id,
                    seat_ids: selectedSeats.value,
                    card_number: cardNumber.value,
                    expiration: expiration.value,
                    cvv: cvv.value,
                    email: email.value
                })

                if (response.data.success) {
                    success.value = true
                    // Reset form
                    selectedSeats.value = []
                    cardNumber.value = ''
                    expiration.value = ''
                    cvv.value = ''
                    email.value = ''
                } else {
                    error.value = response.data.message || 'Payment failed'
                }
            } catch (err) {
                console.error('Payment error:', err)
                error.value = 'An error occurred during payment processing'
            } finally {
                processing.value = false
            }
        }

        onMounted(async () => {
            try {
                const response = await axios.get(`/api/events/${route.params.id}`)
                event.value = response.data
            } catch (err) {
                console.error('Error loading event:', err)
            } finally {
                loading.value = false
            }
        })

        return {
            event,
            loading,
            selectedSeats,
            cardNumber,
            expiration,
            cvv,
            email,
            processing,
            error,
            success,
            totalPrice,
            toggleSeat,
            processPayment
        }
    }
})
</script>
