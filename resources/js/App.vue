<template>
    <div class="min-h-screen bg-gray-100">
        <header class="bg-black text-white py-6 px-4">
            <div class="container mx-auto flex justify-between items-center">
                <h1 class="text-2xl font-bold">Artickets</h1>
                <nav>
                    <router-link to="/" class="mr-4">Events</router-link>
                </nav>
            </div>
        </header>

        <main class="container mx-auto py-8 px-4">
            <router-view></router-view>
        </main>
    </div>
</template>

<script lang="ts">
import { defineComponent } from 'vue';

export default defineComponent({
    name: 'App',
});
</script>

<style scoped>
header {
    padding: 1.5rem;
}
</style>
