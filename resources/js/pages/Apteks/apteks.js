import { createApp } from 'vue'
import ApteksTableStatus from "@/components/Apteks/ApteksTableStatus.vue";

document.addEventListener('DOMContentLoaded', () => {
    const el = document.getElementById('apteks-table-wrapper')
    if (!el) return

    const { status, variant } = el.dataset

    createApp(ApteksTableStatus, {
        status,
        variant: variant || status, // подстраховка
    }).mount(el)
})
