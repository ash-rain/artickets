<template>
    <div>
        <h2 class="text-2xl font-bold mb-6">{{ event.title }}</h2>
        <p class="text-gray-600 mb-6">Venue: {{ event.venue.name }}</p>

        <div v-for="section in event.venue.sections" :key="section.id" class="mb-8">
            <h3 class="text-xl font-semibold mb-4">{{ section.name }} - ${{ section.price }}</h3>

            <div class="grid grid-cols-10 gap-2">
                <button v-for="seat in section.seats" :key="seat.id" :class="[
                    'w-10 h-10 rounded flex items-center justify-center',
                    getSectionColor(section),
                    seat.payment ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer',
                    selectedSeats.includes(seat.id) ? 'border-4 border-yellow-400' : 'border border-gray-300'
                ]" :disabled="seat.payment" @click="toggleSeat(seat.id)">
                    {{ seat.row }}-{{ seat.column }}
                </button>
            </div>
        </div>

        <button :disabled="selectedSeats.length === 0" :class="[
            'bg-green-500 text-white font-bold py-3 px-6 rounded-lg transition duration-200',
            selectedSeats.length === 0 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-green-600'
        ]" @click="goToCheckout">
            Checkout ({{ selectedSeats.length }} seats selected)
        </button>
    </div>
</template>

<script lang="ts">
import { defineComponent, ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';

export default defineComponent({
    name: 'Event',
    setup() {
        const route = useRoute();
        const router = useRouter();
        const event = ref<any>({});
        const loading = ref(true);
        const selectedSeats = ref<number[]>([]);

        const sectionColors = [
            'bg-blue-500',
            'bg-purple-500',
            'bg-red-500',
            'bg-orange-500',
            'bg-teal-500'
        ];

        const getSectionColor = (section: any) => {
            return sectionColors[section.id % sectionColors.length];
        };

        const toggleSeat = (seatId: number) => {
            const index = selectedSeats.value.indexOf(seatId);
            if (index === -1) {
                selectedSeats.value.push(seatId);
            } else {
                selectedSeats.value.splice(index, 1);
            }
        };

        const goToCheckout = () => {
            router.push({
                name: 'Payment',
                query: { seatIds: selectedSeats.value.join(',') }
            });
        };

        onMounted(async () => {
            try {
                const response = await axios.get(`/api/events/${route.params.id}`);
                event.value = response.data;
            } catch (error) {
                console.error('Error fetching event:', error);
            } finally {
                loading.value = false;
            }
        });

        return {
            event,
            loading,
            selectedSeats,
            getSectionColor,
            toggleSeat,
            goToCheckout
        };
    }
});
</script>
