<template>
    <div class="payment-container container mx-auto pt-4">
        <h2 class="text-2xl font-bold mb-6">{{ t('payment.title') }}</h2>

        <div v-if="selectedSeats.length > 0" class="mb-8">
            <h3 class="text-lg font-semibold mb-4">{{ t('payment.selected_seats') }}</h3>
            <table class="mb-6 outline-dashed outline-2 outline-black table-auto w-full">
                <tr v-for="seat in selectedSeats" :key="seat.id"
                    class="bg-gray-100 p-4 rounded border-b border-dashed border-black">
                    <td class="p-4">{{ seat.section?.name || t('payment.general') }}
                        ({{ seat.section?.price.toFixed(2) }} &euro;)
                    </td>
                    <td class="text-right p-4">
                        {{ t('payment.row') }} {{ seat.row }}
                    </td>
                    <td class="text-right p-4">
                        {{ t('payment.seat') }} {{ seat.column }}
                    </td>
                </tr>
            </table>
            <p class="text-xl font-semibold">{{ t('payment.total') }} {{ totalPrice.toFixed(2) }} &euro;</p>
        </div>
        <div v-else class="mb-8">
            <p>{{ t('payment.no_seats') }}</p>
        </div>

        <form @submit.prevent="handleCheckout" class="mb-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div>
                    <label for="name" class="block mb-2">{{ t('payment.form.name') }}</label>
                    <input v-model="form.name" type="text" id="name" required class="w-full p-2 border rounded">
                </div>
                <div>
                    <label for="email" class="block mb-2">{{ t('payment.form.email') }}</label>
                    <input v-model="form.email" type="email" id="email" required class="w-full p-2 border rounded">
                </div>
                <div>
                    <label for="phone" class="block mb-2">{{ t('payment.form.phone') }}</label>
                    <input v-model="form.phone" type="tel" id="phone" required class="w-full p-2 border rounded">
                </div>
            </div>

            <div class="mb-6">
                <div class="flex items-center">
                    <input v-model="form.termsAccepted" type="checkbox" id="terms" required
                        class="mr-2 h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <label for="terms" class="text-sm">{{ t('payment.form.terms') }}</label>
                </div>
            </div>

            <div class="flex gap-4">
                <button type="button" @click="router.go(-1)"
                    class="bg-gray-500 text-white px-6 py-3 rounded hover:bg-gray-600 transition">
                    {{ t('payment.buttons.back') }}
                </button>
                <button type="submit" :disabled="selectedSeats.length === 0"
                    class="bg-green-600 text-white px-6 py-3 rounded hover:bg-green-500 transition disabled:bg-gray-400 disabled:cursor-not-allowed">
                    {{ t('payment.buttons.checkout') }}
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
import { useI18n } from '../utils/i18n'

const { t } = useI18n()

const route = useRoute()
const router = useRouter()
const seatStore = useSeatSelectionStore()
const selectedSeats = ref<Seat[]>([])
const totalPrice = ref(0)
const loading = ref(true)
const form = ref({
    name: '',
    email: '',
    phone: '',
    termsAccepted: false
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
    if (!form.value.termsAccepted) {
        alert(t('payment.errors.terms'))
        return
    }

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
        alert(t('payment.errors.payment_failed') + (error instanceof Error ? error.message : ''))
    }
}
</script>
