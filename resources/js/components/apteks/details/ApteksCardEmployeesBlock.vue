<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'
import axios from 'axios'

const props = defineProps({
    aptekaId: { type: Number, required: true },
})

const loading = ref(true)
const error = ref('')
const employees = ref([])

const channelName = `apteks.${props.aptekaId}.employees`

async function loadEmployees() {
    loading.value = true
    error.value = ''

    try {
        const { data } = await axios.get(
            `/api/apteks/${props.aptekaId}/employees`,
            { withCredentials: true }
        )

        employees.value = data?.data ?? []
    } catch (e) {
        error.value =
            e?.response?.data?.message ||
            e?.message ||
            'Помилка завантаження'
    } finally {
        loading.value = false
    }
}

/**
 * Оновлюємо on_working для конкретного співробітника
 */
function updateEmployeeWorking(code, onWorking) {
    const emp = employees.value.find(x => Number(x.code) === Number(code))
    if (!emp) return
    emp.on_working = !!onWorking
}

/**
 * WS connect (як у лічильниках: без падіння, якщо Echo ще не підключений)
 */
function connectWs() {
    if (!window.Echo) return

    window.Echo
        .channel(channelName)
        .listen('.employee.working.updated', (e) => {
            updateEmployeeWorking(e?.code, e?.on_working)
        })
}

function disconnectWs() {
    if (!window.Echo) return
    window.Echo.leave(channelName)
}

onMounted(async () => {
    await loadEmployees()
    connectWs()
})

onBeforeUnmount(() => {
    disconnectWs()
})
</script>

<template>
    <h2 class="small-title text-uppercase"><b>Співробітники</b></h2>
    <div class="card card-primary mb-2">
        <div class="card-body h-100">

            <!-- Loading -->
            <div v-if="loading" class="text-muted">
                Завантаження...
            </div>

            <!-- Error -->
            <div v-else-if="error" class="alert alert-danger py-2">
                {{ error }}
            </div>

            <!-- Empty -->
            <div v-else-if="employees.length === 0" class="text-muted">
                Дані відсутні
            </div>

            <!-- List -->
            <div v-else class="h5 mb-0">
                <div
                    v-for="(emp, index) in employees"
                    :key="emp.code ?? emp.id"
                    class="d-flex justify-content-between align-items-center"
                    :class="[
                        index !== employees.length - 1 ? 'border-bottom' : '',
                        emp.on_working ? 'employee-working' : ''
                    ]"
                >
                    <!-- left -->
                    <div class="me-3">
                        <template v-if="emp.on_working">
                            <b class="text-primary">
                                {{ emp.position }}:
                            </b>
                            {{ emp.fio_ua || emp.fio_ru }}
                        </template>
                        <template v-else>
                            {{ emp.position }}: {{ emp.fio_ua || emp.fio_ru }}
                        </template>
                    </div>

                    <!-- right -->
                    <div
                        class="text-end text-nowrap"
                        :class="{ 'text-primary fw-semibold': emp.on_working }"
                    >
                        {{ emp.phone || '—' }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
