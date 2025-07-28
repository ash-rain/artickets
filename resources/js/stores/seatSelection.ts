import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import type { Seat } from '../types/Seat'

export const useSeatSelectionStore = defineStore('seatSelection', () => {
    const selectedSeats = ref<Seat[]>([])
    const eventId = ref<number | null>(null)

    const totalPrice = computed(() => selectedSeats.value.length * 25) // $25 per seat

    function setSeats(seats: Seat[], id: number) {
        selectedSeats.value = seats
        eventId.value = id
    }

    function clearSeats() {
        selectedSeats.value = []
        eventId.value = null
    }

    return {
        selectedSeats,
        eventId,
        totalPrice,
        setSeats,
        clearSeats
    }
})
