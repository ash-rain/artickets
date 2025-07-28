<template>
    <div class="min-h-screen bg-gray-100">
        <header class="bg-black text-white py-6 px-4">
            <div class="container text-shadow-md mx-auto flex justify-between items-center">
                <h1 class="text-2xl font-bold">
                    <router-link to="/" class="hover:bg-black/50 p-2">
                        {{ t('app.title') }}
                    </router-link>
                </h1>
                <nav class="flex items-center gap-4">
                    <router-link to="/" class="mr-4 p-2 hover:bg-black/50">{{ t('app.events') }}</router-link>
                    <select :value="currentLanguage" @change="changeLanguage"
                        class="bg-black/50 p-2 text-white border-none">
                        <option value="en">English</option>
                        <option value="bg">Български</option>
                    </select>
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
import { useI18n } from './utils/i18n';
import { useRoute } from 'vue-router';

export default defineComponent({
    name: 'App',
    setup() {
        const route = useRoute()
        const { currentLanguage, loadTranslations, t } = useI18n()

        const changeLanguage = async (event: Event) => {
            const target = event.target as HTMLSelectElement;
            const lang = target.value as 'en' | 'bg';
            await loadTranslations(lang)
            // Update URL without reload
            window.history.replaceState({}, '', `/${lang}${route.fullPath.replace(/^\/[a-z]{2}/, '')}`)
        }

        // Load translations based on URL
        const lang = (route.params.lang as 'en' | 'bg') || 'bg'
        loadTranslations(lang)

        return {
            currentLanguage,
            changeLanguage,
            t
        }
    }
});
</script>

<style scoped>
header {
    padding: 2.5rem;
    background-image: url('/storage/app/public/assets/hero.png');
    background-size: cover;
}
</style>
