<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
import axios from 'axios'

/**
 * Стан завантаження та помилки
 */
const loading = ref(true)
const error = ref('')

/**
 * Поточні дані лічильника аптек
 */
const counter = ref(null)

/**
 * Назва WebSocket-каналу
 */
const channelName = 'apteks.counter'

/**
 * Рядки таблиці статусу звʼязку
 */
const rows = computed(() => {
    const c = counter.value || {}

    const safe = v => Number.isFinite(Number(v)) ? Number(v) : 0

    const over3h = safe(c.disconnected_lvl3) + safe(c.disconnected_max)

    return [
        { label: 'До 30 хв',    value: safe(c.disconnected_min)  },
        { label: 'До 1 год',    value: safe(c.disconnected_lvl1) },
        { label: 'До 3 год',    value: safe(c.disconnected_lvl2) },
        { label: 'Понад 3 год', value: over3h                    },
    ]
})

/**
 * Текст у футері картки
 */
const footerText = computed(() => {
    const c = counter.value || {}
    const total = c.total_apteks ?? '—'
    const connected = c.connected_apteks ?? '—'
    return `Всього: ${total} · На звʼязку: ${connected}`
})

/**
 * Форматування дати/часу у вигляді:
 * HH:mm:ss DD.MM.YYYY
 */
const formattedDateTime = computed(() => {
    const raw = counter.value?.created_at
    if (!raw) return ''

    const d = new Date(raw)
    if (isNaN(d)) return ''

    const pad = (v) => String(v).padStart(2, '0')

    const time =
        `${pad(d.getHours())}:${pad(d.getMinutes())}:${pad(d.getSeconds())}`

    const date =
        `${pad(d.getDate())}.${pad(d.getMonth() + 1)}.${d.getFullYear()}`

    return `${time} ${date}`
})

/**
 * Початкове завантаження даних (initial state)
 */
async function loadInitial() {
    loading.value = true
    error.value = ''

    try {
        const { data } = await axios.get('/api/apteks/counter', {
            headers: { Accept: 'application/json' },
            withCredentials: true,
        })
        counter.value = data
    } catch (e) {
        error.value =
            e?.response?.data?.message ||
            e?.message ||
            'Не вдалося завантажити дані'
    } finally {
        loading.value = false
    }
}

/**
 * Підключення до WebSocket та підписка на оновлення
 */
function connectWs() {
    if (!window.Echo) return

    window.Echo
        .channel(channelName)
        .listen('.updated', (e) => {
            // Очікуємо формат { payload: {...} }
            if (e?.payload) {
                counter.value = e.payload
            }
        })
}

/**
 * Відключення від WebSocket-каналу
 */
function disconnectWs() {
    window.Echo?.leave(channelName)
}

onMounted(async () => {
    await loadInitial()
    connectWs()
})

onBeforeUnmount(() => {
    disconnectWs()
})
</script>

<template>
    <div class="card w-100 h-100 mb-2 shadow-sm card-success">
        <div class="card-body">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="fw-semibold text-primary">
                    Статус звʼязку аптек
                </div>
            </div>

            <div v-if="loading" class="text-muted">
                Завантаження…
            </div>

            <div v-else-if="error" class="alert alert-danger py-2 mb-0">
                <div class="fw-semibold">Помилка</div>
                <div class="small">{{ error }}</div>
            </div>

            <div v-else>
                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-2">
                        <tbody>
                        <tr v-for="r in rows" :key="r.label">
                            <td class="text-primary">
                                {{ r.label }}
                            </td>
                            <td class="text-end fw-semibold">
                                {{ r.value }}
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted small fw-semibold">
                        {{ footerText }}
                    </div>
                    <div class="text-muted small fw-semibold">
                        {{ formattedDateTime }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
