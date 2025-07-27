<template>
    <div class="px-4 py-6">
        <h1 class="text-2xl font-bold mb-6">{{ event.title }}</h1>

        <div class="mb-8">
            <div v-for="section in sections" :key="section.id" class="mb-8">
                <h2 class="text-xl font-semibold mb-2">{{ section.name }} - ${{ section.price }}</h2>
                <div class="flex flex-wrap gap-2">
                    <div v-for="seat in section.seats" :key="seat.id" :class="[
                        seat.payment_id ? 'bg-gray-300 opacity-50 cursor-not-allowed' : `bg-${section.color}-200 cursor-pointer`,
                        selectedSeats.includes(seat.id) ? 'ring-4 ring-blue-500' : '',
                        'w-10 h-10 rounded-md flex items-center justify-center'
                    ]" @click="() => toggleSeat(seat)">
                        {{ seat.row }}-{{ seat.column }}
                    </div>
                </div>
            </div>
        </div>

        <button :disabled="selectedSeats.length === 0" :class="[
            selectedSeats.length === 0 ? 'bg-gray-400 cursor-not-allowed' : 'bg-blue-600 hover:bg-blue-700',
            'px-4 py-2 rounded-md text-white font-medium'
        ]" @click="proceedToPayment">
            Checkout ({{ selectedSeats.length }} seats)
        </button>
    </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';

// Declare route function globally for TypeScript
declare function route(name: string, params?: any): string;

// Define interfaces for type safety
interface Seat {
    id: number;
    row: string;
    column: string;
    payment_id: number | null;
}

interface Section {
    id: number;
    name: string;
    price: number;
    color: string;
    seats: Seat[];
}

interface Event {
    id: number;
    title: string;
    // Add other event properties as needed
}

// Define props with TypeScript
const props = defineProps<{
    event: Event;
    sections: Section[];
}>();

// Use Composition API
const selectedSeats = ref<number[]>([]);

function toggleSeat(seat: Seat) {
    if (seat.payment_id) return;

    const index = selectedSeats.value.indexOf(seat.id);
    if (index > -1) {
        selectedSeats.value.splice(index, 1);
    } else {
        selectedSeats.value.push(seat.id);
    }
}

function proceedToPayment() {
    if (selectedSeats.value.length === 0) return;

    router.post(route('payments.create'), {
        seat_ids: selectedSeats.value
    });
}
</script>
