<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'

/**
 * ID аптеки передаётся из blade:
 * <div id="apteksPassportBlock" data-apteka-id="482"></div>
 */
const props = defineProps({
    aptekaId: {type: Number, required: true, },
})

const loading = ref(true)
const error = ref('')
const apteka = ref(null)

/**
 * Формат дати (дд.мм.рррр)
 */
function formatDateUA(value) {
    if (!value) return ''
    const d = new Date(value)
    if (Number.isNaN(d.getTime())) return String(value)
    return new Intl.DateTimeFormat('uk-UA', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
    }).format(d)
}

/**
 * Рядки паспорта (ПЛОСКИЙ список, без group)
 * Логіка статусів:
 *
 * 1. Якщо closed = true → "Зачинена"
 *    - якщо closed_at є → показуємо його
 *    - ігноруємо open / plan_open
 *
 * 2. Якщо НЕ closed:
 *    2.1 Якщо є plan_open_at:
 *        - якщо є open_at → "Відкрита"
 *        - інакше → "Планується до відкриття"
 *    2.2 Якщо є plan_closed_at → ОКРЕМИЙ рядок нижче
 *
 * 3. Якщо plan_open_at і open_at пусті → статус відкриття не показуємо
 */
const rows = computed(() => {
    const a = apteka.value || {}
    const list = []

// ===== СТАТУСИ =====

// 1. Якщо аптека зачинена (closed = true)
// → завжди показуємо ФАКТ зачинення
    if (a.closed === true) {
        list.push({
            label: 'Зачинена:',
            value: a.closed_at
                ? `${formatDateUA(a.closed_at)}`
                : 'Дата невідома',
        })
    } else {
        // 2. Аптека НЕ зачинена

        /**
         * ПЕРША СТРОКА: "Статус"
         */

        // (plan_open_at && !open_at && !plan_closed_at && !closed_at)
        if (a.plan_open_at && !a.open_at && !a.plan_closed_at && !a.closed_at) {
            list.push({
                label: 'Планується до відкриття:',
                value: `${formatDateUA(a.plan_open_at)}`,
            })
        }

        // (plan_open_at && open_at && !plan_closed_at && !closed_at)
        else if (a.open_at && !a.plan_closed_at && !a.closed_at) {
            list.push({
                label: 'Відкрита:',
                value: `${formatDateUA(a.open_at)}`,
            })
        }

        // (plan_closed_at && open_at && !closed_at)
        else if (a.plan_closed_at && a.open_at && !a.closed_at) {
            list.push({
                label: 'Планується закриття',
                value: `${formatDateUA(a.plan_closed_at)}`,
            })
        }

        // (plan_closed_at && closed_at && !closed)
        else if (a.closed_at) {
            list.push({
                label: 'Зачинена:',
                value: a.closed_at
                    ? formatDateUA(a.closed_at)
                    : 'Дата невідома',
            })
        }

        /**
         * ДРУГА СТРОКА (ОКРЕМА):
         * "Планується зачинення"
         *
         * Умова:
         * plan_closed_at є
         * і аптека ще не зачинена
         */
        if (a.plan_closed_at && !a.closed && !a.closed_at) {
            list.push({
                label: 'Планується зачинення',
                value: formatDateUA(a.plan_closed_at),
            })
        }
    }

    // ===== ОСНОВНІ ДАНІ =====

    const addrDetails = [
        a.address_type ? `${a.address_type}.` : '',
        a.address_street || '',
        a.address_house_number || '',
    ].filter(Boolean).join(' ').trim()

    list.push(
        { label: 'Ліцензія', value: a.license || '' },
        { label: 'Фірма', value: a.firma || '' },
        { label: 'Бренд', value: a.brand || '' },
        { label: 'Телефон', value: a.phone || '' },

        { label: 'Повна адреса', value: a.address_full || '' },
        { label: 'Область', value: a.address_region || '' },
        { label: 'Місто', value: a.address_town || '' },
        { label: 'Адрес', value: addrDetails || '' },

        { label: 'IP сервера', value: a.apteka_ip || '' },
        { label: 'IP роутера', value: a.router_ip || '' },

        { label: 'Примітка', value: a.description || '' },
    )

    // Прибираємо порожні
    return list.filter(r => String(r.value || '').trim() !== '')
})

const coords = computed(() => {
    const a = apteka.value || {}
    if (!a.google_x || !a.google_y) return null
    return { x: a.google_x, y: a.google_y }
})

const mapsLinks = computed(() => {
    if (!coords.value) return null
    const { x, y } = coords.value
    return {
        google: `https://www.google.com/maps/search/?api=1&query=${x},${y}`,
        osm: `https://www.openstreetmap.org/?mlat=${x}&mlon=${y}#map=15/${x}/${y}`,
        waze: `https://www.waze.com/ru/live-map/directions?navigate=yes&to=ll.${x}%2C${y}`,
    }
})

async function load() {
    loading.value = true
    error.value = ''

    try {
        const { data } = await axios.get(`/api/apteks/${props.aptekaId}/passport`)
        apteka.value = data?.data ?? data
    } catch (e) {
        error.value = e?.response?.data?.message || 'Помилка завантаження паспорта'
    } finally {
        loading.value = false
    }
}

onMounted(load)
</script>

<template>
    <h2 class="small-title text-uppercase"><b>Картка аптеки</b></h2>
    <div class="card h-100 card-primary mb-3">
            <div class="card-body">
            <div v-if="loading" class="text-muted">
                Завантаження...
            </div>

            <div v-else-if="error" class="alert alert-danger mb-0">
                {{ error }}
            </div>

            <div v-else>
                <h2 class="small-title text-uppercase"><b>{{ apteka?.name }}</b></h2>
                <div class="list-group list-group-flush">
                    <div
                        v-for="(row, idx) in rows"
                        :key="idx"
                        class="list-group-item px-0 py-0 d-flex justify-content-between gap-3"
                    >
                        <div class="mb-0 h6">
                            {{ row.label }}
                        </div>
                        <div class="text-end fw-semibold text-break" style="max-width: 60%;">
                            {{ row.value }}
                        </div>
                    </div>
                </div>

                <div v-if="mapsLinks" class="mt-3 pt-3 border-top">
                    <div class="text-muted fw-semibold small">
                        Геолокація
                    </div>
                    <div class="d-flex flex-column gap-2">
                        <a :href="mapsLinks.google" target="_blank">Відкрити в Google Maps</a>
                        <a :href="mapsLinks.osm" target="_blank">Відкрити в OpenStreetMap</a>
                        <a :href="mapsLinks.waze" target="_blank">Відкрити в Waze</a>
                        <div class="text-muted small">
                            Координати: {{ coords.x }}, {{ coords.y }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
