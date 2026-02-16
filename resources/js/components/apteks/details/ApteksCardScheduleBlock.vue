<script setup>
import { ref, computed, watch } from 'vue'
import axios from 'axios'

const props = defineProps({
    aptekaId: { type: Number, required: true },
})

const loading = ref(true)
const error = ref('')
const payload = ref(null)

const endpoint = computed(() => `/api/apteks/${props.aptekaId}/schedule/week/current`)

const load = async () => {
    if (!props.aptekaId) {
        loading.value = false
        return
    }

    loading.value = true
    error.value = ''
    payload.value = null

    try {
        const { data } = await axios.get(endpoint.value, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            withCredentials: true,
        })
        payload.value = data
    } catch (e) {
        const status = e?.response?.status
        error.value =
            e?.response?.data?.message ||
            (status === 401 || status === 419
                ? 'Сесія завершилась. Оновіть сторінку та увійдіть знову.'
                : status === 403
                    ? 'Немає доступу.'
                    : 'Не вдалося завантажити графік.')
    } finally {
        loading.value = false
    }
}

watch(
    () => props.aptekaId,
    (v) => {
        if (v) load()
        else loading.value = false
    },
    { immediate: true }
)

const rows = computed(() => payload.value?.rows ?? [])
const isEmpty = computed(() =>
    !loading.value && !error.value && rows.value.length === 0
)
const toIsoDate = (val) => {
    if (!val) return ''
    const s = String(val).trim()
    // берём YYYY-MM-DD даже если дальше " 00:00:00..."
    const iso = s.slice(0, 10)
    return /^\d{4}-\d{2}-\d{2}$/.test(iso) ? iso : ''
}

const formatDateUA = (val) => {
    const iso = toIsoDate(val)
    if (!iso) return ''
    const [y, m, d] = iso.split('-')
    return `${d}.${m}.${y}`
}

const todayRow = computed(() => rows.value.find(r => r.is_today) || null)
const todayDateUA = computed(() => (todayRow.value ? formatDateUA(todayRow.value.date_at) : ''))

</script>

<template>
    <h2 class="small-title text-uppercase"><b>Графік роботи</b></h2>
    <div class="card mb-2 card-primary">
        <div class="card-body h-100">
            <!-- Loading -->
            <div v-if="loading" class="p-3 text-muted">
                Завантаження…
            </div>

            <!-- Error -->
            <div v-else-if="error" class="p-3">
                <div class="alert alert-danger mb-0">
                    {{ error }}
                </div>
            </div>

            <!-- Empty -->
            <div v-else-if="isEmpty" class="p-3 text-muted">
                Дані за поточний тиждень відсутні
            </div>

            <!-- Passport-like schedule -->
            <div v-else>

                <!-- Сьогодні (единый стиль, одна строка) -->
                <div
                    v-if="todayRow"
                    class="d-flex justify-content-between align-items-center fw-semibold text-primary border-bottom mb-0 h5"
                >
                    <div>
                        {{ formatDateUA(todayRow.date_at) }}
                    </div>
                </div>
                <!-- Всі дні тижня -->
                <div
                    v-for="(r, idx) in rows"
                    :key="r.week_num_day"
                    class="d-flex justify-content-between align-items-center px-0 py-0 mb-0 h5"
                    :class="[
                        r.is_today ? 'fw-semibold text-primary' : '',
                        idx < rows.length - 1 ? 'border-bottom' : ''
                      ]"
                    >
                    <!-- label -->
                    <div>
                        {{ r.day_name }}:
                    </div>
                    <!-- value -->
                    <div>
                        {{ r.time || '—' }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
