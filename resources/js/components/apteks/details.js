import { createApp } from 'vue'

import ApteksCardPassportBlock from '@/components/apteks/details/ApteksCardPassportBlock.vue'
import ApteksCardScheduleBlock from '@/components/apteks/details/ApteksCardScheduleBlock.vue'
import ApteksCardProvidersRowBlock from '@/components/apteks/details/ApteksCardProvidersRowBlock.vue'
import ApteksCardEmployeesBlock from '@/components/apteks/details/ApteksCardEmployeesBlock.vue'
import ApteksCardTechniqueBlock from '@/components/apteks/details/ApteksCardTechniqueBlock.vue'
import ApteksCardVisitsBlock from '@/components/apteks/details/ApteksCardVisitsBlock.vue'

function mount(selector, component) {
    const el = document.querySelector(selector)
    if (!el) return
    if (el.dataset.vMounted === '1') return
    el.dataset.vMounted = '1'

    const aptekaId = Number(el.dataset.aptekaId || 0)
    createApp(component, { aptekaId }).mount(el)
}

mount('#apteksPassportBlock', ApteksCardPassportBlock)
mount('#apteksScheduleBlock', ApteksCardScheduleBlock)
mount('#apteksProvidersRowBlock', ApteksCardProvidersRowBlock)
mount('#apteksEmployeesBlock', ApteksCardEmployeesBlock)
mount('#apteksTechniqueBlock', ApteksCardTechniqueBlock)
mount('#apteksVisitsBlock', ApteksCardVisitsBlock)
