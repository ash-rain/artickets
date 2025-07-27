<template>
    <div class="max-w-lg mx-auto">
        <h2 class="text-2xl font-bold mb-6">Checkout</h2>

        <div v-if="loading" class="text-center py-8">
            <p>Loading seat information...</p>
        </div>

        <div v-else>
            <div v-if="seats.length === 0" class="text-center py-8">
                <p>No seats selected. Please go back and select seats.</p>
                <router-link to="/" class="text-blue-500 hover:underline mt-4 inline-block">Back to events</router-link>
            </div>

            <div v-else>
                <div class="mb-8">
                    <h3 class="text-xl font-semibold mb-4">Selected Seats</h3>
                    <ul class="grid grid-cols-3 gap-2">
                        <li v-for="seat in seats" :key="seat.id" class="bg-gray-100 p-3 rounded">
                            Section {{ seat.section.name }}, Row {{ seat.row }}, Seat {{ seat.column }} - ${{
                            seat.section.price }}
                        </li>
                    </ul>
                    <p class="text-xl font-bold mt-4">Total: ${{ totalPrice }}</p>
                </div>

                <form @submit.prevent="submitPayment">
                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2" for="name">Full Name</label>
                        <input v-model="form.name" type="text" id="name" class="w-full px-3 py-2 border rounded-md"
                            :class="{ 'border-red-500': errors.name }">
                        <p v-if="errors.name" class="text-red-500 text-sm mt-1">{{ errors.name }}</p>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2" for="email">Email</label>
                        <input v-model="form.email" type="email" id="email" class="w-full px-3 py-2 border rounded-md"
                            :class="{ 'border-red-500': errors.email }">
                        <p v-if="errors.email" class="text-red-500 text-sm mt-1">{{ errors.email }}</p>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2" for="phone">Phone</label>
                        <input v-model="form.phone" type="tel" id="phone" class="w-full px-3 py-2 border rounded-md"
                            :class="{ 'border-red-500': errors.phone }">
                        <p v-if="errors.phone" class="text-red-500 text-sm mt-1">{{ errors.phone }}</p>
                    </div>

                    <div class="mb-6">
                        <label class="flex items-center">
                            <input v-model="form.acceptedTerms" type="checkbox" class="form-checkbox"
                                :class="{ 'border-red-500': errors.acceptedTerms }">
                            <span class="ml-2">I accept the terms and conditions</span>
                        </label>
                        <p v-if="errors.acceptedTerms" class="text-red-500 text-sm mt-1">{{ errors.acceptedTerms }}</p>
                    </div>

                    <button type="submit"
                        class="bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-6 rounded-lg w-full transition duration-200"
                        :disabled="processing">
                        <span v-if="processing">Processing...</span>
                        <span v-else>Pay Now</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { defineComponent, ref, computed, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';

export default defineComponent({
    name: 'Payment',
    setup() {
        const route = useRoute();
        const router = useRouter();
        const seats = ref<any[]>([]);
        const loading = ref(true);
        const processing = ref(false);

        const form = ref({
            name: '',
            email: '',
            phone: '',
            acceptedTerms: false
        });

        const errors = ref({
            name: '',
            email: '',
            phone: '',
            acceptedTerms: ''
        });

        const totalPrice = computed(() => {
            return seats.value.reduce((sum, seat) => sum + seat.section.price, 0);
        });

        const validateForm = () => {
            let valid = true;
            errors.value = { name: '', email: '', phone: '', acceptedTerms: '' };

            if (!form.value.name.trim()) {
                errors.value.name = 'Name is required';
                valid = false;
            }

            if (!form.value.email.trim()) {
                errors.value.email = 'Email is required';
                valid = false;
            } else if (!/^\S+@\S+\.\S+$/.test(form.value.email)) {
                errors.value.email = 'Invalid email format';
                valid = false;
            }

            if (!form.value.phone.trim()) {
                errors.value.phone = 'Phone is required';
                valid = false;
            }

            if (!form.value.acceptedTerms) {
                errors.value.acceptedTerms = 'You must accept the terms and conditions';
                valid = false;
            }

            return valid;
        };

        const submitPayment = async () => {
            if (!validateForm()) return;

            processing.value = true;

            try {
                const payload = {
                    name: form.value.name,
                    email: form.value.email,
                    phone: form.value.phone,
                    seat_ids: seats.value.map(seat => seat.id)
                };

                const response = await axios.post('/api/payments', payload);

                if (response.status === 201) {
                    alert('Payment successful! Your tickets have been booked.');
                    router.push({ name: 'Events' });
                }
            } catch (error: any) {
                if (error.response) {
                    if (error.response.status === 409) {
                        alert('One or more seats have already been taken. Please select different seats.');
                        router.push({ name: 'Events' });
                    } else {
                        alert('Payment failed: ' + (error.response.data.error || 'Please try again later'));
                    }
                } else {
                    alert('Network error. Please check your connection and try again.');
                }
            } finally {
                processing.value = false;
            }
        };

        onMounted(async () => {
            const seatIds = route.query.seatIds ? route.query.seatIds.toString().split(',').map(Number) : [];

            if (seatIds.length === 0) {
                loading.value = false;
                return;
            }

            try {
                // Fetch seat details with section information
                const response = await axios.get('/api/seats', {
                    params: { ids: seatIds.join(',') }
                });
                seats.value = response.data;
            } catch (error) {
                console.error('Error fetching seat details:', error);
                alert('Failed to load seat information. Please try again.');
            } finally {
                loading.value = false;
            }
        });

        return {
            seats,
            loading,
            processing,
            form,
            errors,
            totalPrice,
            submitPayment
        };
    }
});
</script>
