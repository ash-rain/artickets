<template>
    <div class="seats-matrix">
        <button @click="handleBuyTickets" :disabled="selectedSeats.length === 0"
            class="mb-4 w-full bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition disabled:bg-gray-400 disabled:cursor-not-allowed">
            {{ t('seats_matrix.buy_tickets') }} ({{ t('seats_matrix.selected_count', { count: selectedSeats.length })
            }})
        </button>
        <div v-for="section in event.venue.sections" :key="section.id" class="mb-8">
            <h3 class="text-lg font-semibold mb-4">
                {{ t('seats_matrix.section') }}: {{ section.name }}
                <p class="text-xl font-semibold text-green-600">{{ section.price.toFixed(2) }} &euro;</p>
            </h3>
            <div class="grid grid-cols-10 gap-1"
                :style="{ gridTemplateColumns: `repeat(${section.columns}, minmax(0, 1fr))` }">
                <button v-for="seat in section.seats" :key="seat.id" @click="toggleSeat(seat)"
                    :disabled="!!seat.payment" :class="[
                        'py-2 rounded text-center',
                        selectedSeats.includes(seat.id) ? 'bg-green-500 text-black outline-2 outline-black' : 'bg-gray-200',
                        seat.payment ? 'bg-red-200 cursor-not-allowed' : 'hover:bg-green-300 cursor-pointer'
                    ]">
                    {{ seat.row }}/{{ seat.column }}
                </button>
            </div>
        </div>
        <button @click="handleBuyTickets" :disabled="selectedSeats.length === 0"
            class="mt-4 w-full bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition disabled:bg-gray-400 disabled:cursor-not-allowed">
            Buy Tickets ({{ selectedSeats.length }} selected)
        </button>
    </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useSeatSelectionStore } from '../stores/seatSelection'
import { Seat } from '../types/Seat'
import { useI18n } from '../utils/i18n'

const { t } = useI18n()
import { Section } from '../types/Section'
import { Venue } from '../types/Venue'
import { Event } from '../types/Event'

const props = defineProps<{
    event: Event
}>()

const router = useRouter()
const seatStore = useSeatSelectionStore()
const selectedSeats = ref<number[]>([])

onMounted(() => {
    if (seatStore.eventId === props.event.id) {
        selectedSeats.value = seatStore.selectedSeats.map(seat => seat.id)
    }
})

const toggleSeat = (seat: Seat) => {
    if (seat.payment_id) return

    const index = selectedSeats.value.indexOf(seat.id)
    if (index === -1) {
        selectedSeats.value.push(seat.id)
    } else {
        selectedSeats.value.splice(index, 1)
    }

    const seats = props.event.venue.sections
        .flatMap((section: Section) => section.seats)
        .filter((seat: Seat) => selectedSeats.value.includes(seat.id))
    seatStore.setSeats(seats, props.event.id)
}

const handleBuyTickets = () => {
    if (selectedSeats.value.length > 0) {
        router.push(`/${router.currentRoute.value.params.lang}/payment/${props.event.id}`)
    }
}
</script>
