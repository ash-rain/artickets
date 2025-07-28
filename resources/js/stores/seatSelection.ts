import { defineStore } from 'pinia'
import { ref, computed, watch } from 'vue'
import type { Seat } from '../types/Seat'

const STORAGE_KEY = 'seatSelection'

export const useSeatSelectionStore = defineStore('seatSelection', () => {
    const selectedSeats = ref<Seat[]>(JSON.parse(localStorage.getItem(STORAGE_KEY) || '[]'))
    const eventId = ref<number | null>(JSON.parse(localStorage.getItem(`${STORAGE_KEY}:eventId`) || 'null'))

    const totalPrice = computed(() => selectedSeats.value.reduce((sum, seat) => sum + seat.section.price, 0))

    function setSeats(seats: Seat[], id: number) {
        selectedSeats.value = seats
        eventId.value = id
    }

    function clearSeats() {
        selectedSeats.value = []
        eventId.value = null
    }

    // Persist to localStorage on changes
    watch([selectedSeats, eventId], () => {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(selectedSeats.value))
        localStorage.setItem(`${STORAGE_KEY}:eventId`, JSON.stringify(eventId.value))
    }, { deep: true })

    return {
        selectedSeats,
        eventId,
        totalPrice,
        setSeats,
        clearSeats
    }
})

