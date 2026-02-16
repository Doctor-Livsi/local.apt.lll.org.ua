import { createApp } from 'vue'
import ApteksTable from '@/components/apteks/ApteksTable.vue'

document.addEventListener('DOMContentLoaded', () => {
    const el = document.getElementById('apteks-wrapper')
    if (el) {
        const status = el.dataset.status
        createApp(ApteksTable, { status }).mount(el)
    }
})
