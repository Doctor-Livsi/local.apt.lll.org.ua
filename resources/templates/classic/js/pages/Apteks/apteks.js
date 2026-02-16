import { createApp } from 'vue'
import ApteksTable from '@/components/apteks/ApteksTable.vue'

document.addEventListener('DOMContentLoaded', () => {
    const el = document.getElementById('apteks-table-wrapper')
    if (el) {
        const status = el.dataset.status
        const app = createApp(ApteksTable, { status })
        app.mount(el)
    }
})
