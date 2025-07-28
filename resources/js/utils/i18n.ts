import { ref } from 'vue'
import { useRoute } from 'vue-router'

type Translations = Record<string, any>
type Language = 'en' | 'bg'

const route = useRoute()
const urlLang = typeof route?.params?.lang === 'string'
    ? route?.params?.lang
    : null
const defaultLang: Language = urlLang && ['en', 'bg'].includes(urlLang) ? urlLang as Language : 'bg'
const currentLanguage = ref<Language>(defaultLang)
const translations = ref<Translations>({})

async function loadTranslations(lang: Language) {
    try {
        const response = await import(`../../lang/${lang}.json`)
        translations.value = response.default
        currentLanguage.value = lang
    } catch (error) {
        console.error(`Failed to load ${lang} translations:`, error)
    }
}

function t(key: string, params: Record<string, any> = {}): string {
    const keys = key.split('.')
    let value: any = translations.value

    for (const k of keys) {
        if (!value[k]) {
            console.warn(`Translation key not found: ${key}`)
            return key
        }
        value = value[k]
    }

    // Replace placeholders
    let result = value
    for (const [param, val] of Object.entries(params)) {
        result = result.replace(`{{${param}}}`, val)
    }

    return result
}

export function useI18n() {
    return {
        currentLanguage,
        loadTranslations,
        t
    }
}
