import { createApp } from 'vue'

import ApteksCounterWidget from '@/components/widgets/ApteksCounterWidget.vue'
import ApteksCounterChatBotWidget from '@/components/widgets/ApteksCounterChatBotWidget.vue'
import ApteksCardEmployeesBlock from '@/components/apteks/details/ApteksCardEmployeesBlock.vue'
function mountIfExists(selector, component) {
    const el = document.querySelector(selector)
    if (!el) return

    const aptekaId = Number(el.dataset.aptekaId || 0)

    if (el.dataset.vMounted === '1') return
    el.dataset.vMounted = '1'

    createApp(component, { aptekaId }).mount(el)
}

export function mountGlobalWidgets() {
    mountIfExists('#apteksCounterWidget', ApteksCounterWidget)
    mountIfExists('#apteksCounterChatBotWidget', ApteksCounterChatBotWidget)
    mountIfExists('#apteksCardEmployeesBlock', ApteksCardEmployeesBlock)
}
