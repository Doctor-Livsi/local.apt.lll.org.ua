<script setup>
import { ref, computed, onMounted, onBeforeUnmount, watch, nextTick } from 'vue'
import axios from 'axios'

// DataTables 2 + styling
import DataTable from 'datatables.net-dt'
import 'datatables.net-dt/css/dataTables.dataTables.css'

// Buttons (side-effect)
import 'datatables.net-buttons-dt'
import 'datatables.net-buttons-dt/css/buttons.dataTables.css'
// import '../css/custom/acorn-datatables.css'
import 'datatables.net-buttons/js/buttons.html5'
import 'datatables.net-buttons/js/buttons.print'

// Export deps
import JSZip from 'jszip'
import pdfMake from 'pdfmake/build/pdfmake'
import pdfFonts from 'pdfmake/build/vfs_fonts'

// Language
import uk from 'datatables.net-plugins/i18n/uk.mjs'

// Підключаємо залежності для Buttons (Excel/PDF)
pdfMake.addVirtualFileSystem(pdfFonts)
window.JSZip = JSZip
window.pdfMake = pdfMake

import ApteksFilters from '@/components/apteks/ApteksFilters.vue'

/*
|--------------------------------------------------------------------------
| Props
|--------------------------------------------------------------------------
*/
const props = defineProps({
    status: { type: String, required: true },
    variant: { type: String, required: true },
})

/*
|--------------------------------------------------------------------------
| Presets для статусів
|--------------------------------------------------------------------------
| УСІ відмінності між сторінками — ТУТ
*/
const APTEK_CARD_URL = (id) => `/apteks/${id}`

// безопасное экранирование, чтобы не словить XSS, если в названии будет кавычка
const escapeHtml = (v) =>
    String(v ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;')

/**
 * Колонка "Назва" с опциональной ссылкой.
 * field: какое поле брать из row (name / name_full / любое другое)
 * link: true/false
 */
const colAptekName = (field = 'name', link = true) => ({
    data: field,
    title: 'Назва',
    render: (data, type, row) => {
        if (type !== 'display') return data

        const text = escapeHtml(data)

        if (!link) return text

        const href = APTEK_CARD_URL(row.id)
        return `<a href="${href}" class="text-decoration-none">${text}</a>`
    },
})

/**
 * Формат дати YYYY-MM-DD → DD.MM.YYYY
 * Якщо значення пусте / null — повертаємо порожньо
 */
const formatDateUA = (field, title = 'Дата') => ({
    data: field,
    title,
    render: (data, type) => {
        if (type !== 'display') return data
        if (!data) return ''

        // очікуємо YYYY-MM-DD
        const [y, m, d] = String(data).split('-')
        if (!y || !m || !d) return data

        return `${d}.${m}.${y}`
    },
})

const formatDateTimeUA = (
    field,
    title = 'Дата/час',
    options = {}
) => ({
    data: field,
    title,
    render: (data, type, row) => {
        if (type !== 'display') return data
        if (!data) return ''

        // YYYY-MM-DD HH:MM:SS(.ms)
        const [datePart, timePart] = String(data).split(' ')
        if (!datePart || !timePart) return data

        const [y, m, d] = datePart.split('-')
        const [h, min, s] = timePart.split('.')[0].split(':')

        if (!y || !m || !d || !h || !min || !s) return data

        const formatted = `${h}:${min}:${s} ${d}.${m}.${y}`

        // ---- СРАВНЕНИЕ БЕЗ МИЛЛИСЕКУНД ----
        let highlight = false

        if (options.highlightIfEqualTo) {
            const a = String(row[field] ?? '').split('.')[0]
            const b = String(row[options.highlightIfEqualTo] ?? '').split('.')[0]
            highlight = a && b && a !== b
        }

        if (highlight) {
            return `
                <div style="
                    background-color:#ffe5e5;
                    color:#842029;
                    font-weight:600;
                    padding:4px 6px;
                    border-radius:4px;
                ">
                    ${formatted}
                </div>
            `
        }

        return formatted
    },
})

const PRESETS = {
    working: {
        title: 'Діючі аптеки',
        columns: [
            colAptekName('name', true),
            { data: 'brand', title: 'brand' },
            { data: 'apteka_ip', title: 'ip' },
            { data: 'address_full', title: 'Адреса' },
        ],
    },
    projected: {
        title: 'Заплановані аптеки',
        columns: [
            colAptekName('name', true),
            { data: 'apteka_ip', title: 'ip' },
            { data: 'address_full', title: 'Адреса' },
            { data: 'brand', title: 'brand' },
        ],
    },
    closed: {
        title: 'Зачинені аптеки',
        columns: [
            colAptekName('name', true),
            { data: 'address_full', title: 'Адреса' },
            formatDateUA('closed_at', 'Дата закриття'),
        ],
    },
    connected: {
        title: "Аптеки без звʼязку",
        columns: [
            colAptekName('name', true),
            { data: 'apteka_ip', title: 'ip' },
            formatDateTimeUA('loss_of_services', 'Служба', {
                highlightIfEqualTo: 'loss_of_server',
            }),
            formatDateTimeUA('loss_of_server', 'Сервер'),
            { data: 'address_full', title: 'Адреса' },
        ],
        defaultOrder: [[2, 'desc']],
    },
}

const preset = computed(() => PRESETS[props.variant] ?? PRESETS[props.status] ?? PRESETS.working)

/*
|--------------------------------------------------------------------------
| Refs
|--------------------------------------------------------------------------
*/
const tableRef = ref(null)
const filtersRef = ref(null)
let dt = null

/*
|--------------------------------------------------------------------------
| Фільтри
|--------------------------------------------------------------------------
*/
const filters = ref({
    region_id: '',
    city_id: '',
    company_id: '',
    brand_id: '',
})

const regions = ref([])
const cities = ref([])
const companies = ref([])
const brands = ref([])

const isCitiesDisabled = computed(() => !filters.value.region_id)

/*
|--------------------------------------------------------------------------
| Helpers
|--------------------------------------------------------------------------
*/
const getCsrf = () =>
    document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? ''

const reloadTable = () => {
    if (dt) dt.ajax.reload(null, true)
}

const resetFilters = () => {
    filters.value = { region_id: '', city_id: '', company_id: '', brand_id: '' }
    cities.value = []
    reloadTable()
}

// Мої кнопки → тригеримо Buttons всередині DataTables
const dtButton = (name) => {
    if (!dt) return
    const btn = dt.buttons().container().find(`button.buttons-${name}`)
    if (btn.length) btn.trigger('click')
}

/*
|--------------------------------------------------------------------------
| API: довідники фільтрів
|--------------------------------------------------------------------------
*/
const loadRegions = async () => {
    const { data } = await axios.get('/api/apteks/filters/regions')
    regions.value = Array.isArray(data) ? data : (data?.data ?? [])
}

const loadCities = async (regionId) => {
    if (!regionId) {
        cities.value = []
        filters.value.city_id = ''
        return
    }
    const { data } = await axios.get('/api/apteks/filters/cities', {
        params: { region_id: regionId },
    })
    cities.value = Array.isArray(data) ? data : (data?.data ?? [])
}

const loadCompanies = async () => {
    const { data } = await axios.get('/api/apteks/filters/companies')
    companies.value = Array.isArray(data) ? data : (data?.data ?? [])
}

const loadBrands = async () => {
    const { data } = await axios.get('/api/apteks/filters/brands')
    brands.value = Array.isArray(data) ? data : (data?.data ?? [])
}

/*
|--------------------------------------------------------------------------
| DataTables init
|--------------------------------------------------------------------------
*/
const initDataTable = async () => {
    if (!tableRef.value) return

    if (dt) {
        dt.destroy()
        dt = null
    }

    dt = new DataTable(tableRef.value, {
        serverSide: true,
        processing: true,
        searching: true,
        paging: true,
        pageLength: 25,
        lengthMenu: [10, 25, 50, 100, -1],

        ajax: {
            url: `/api/apteks/status/${props.status}`,
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': getCsrf(),
                'X-Requested-With': 'XMLHttpRequest',
            },
            data: (d) => {
                d.filters = { ...filters.value }
                return d
            },
            error: (xhr) => {
                if (xhr?.status === 401 || xhr?.status === 419) {
                    window.location.href = '/login'
                }
            },
        },
        columns: preset.value.columns,
        headerCallback: function (thead) {
            const ths = thead.querySelectorAll('th')

            ths.forEach(th => {
                // центрируем заголовок
                th.style.textAlign = 'center'

                // берём ТОЛЬКО текст заголовка, не трогая стрелки
                const title = th.querySelector('.dt-column-title')
                if (title) {
                    title.textContent = title.textContent.toUpperCase()
                }
            })
        },
        order: preset.value.defaultOrder ?? [[0, 'asc']],
        language: uk,

        // Не показуємо стандартні buttons у layout (ми малюємо свої)
        layout: {
            topStart: ['pageLength'],
            topEnd: ['search'],
            bottomStart: ['info'],
            bottomEnd: ['paging'],
        },

        // Buttons залишаємо, щоб тригерити програмно
        buttons: [
            { extend: 'print' },
            { extend: 'copy' },
            { extend: 'pdf' },
            { extend: 'excel' },
            { extend: 'csv' },
        ],
    })

    // Переміщаємо блок фільтрів у верхню панель DataTables (під стандартні контролі)
    await nextTick()
    const wrapper =
        tableRef.value.closest('.dt-container') ||
        tableRef.value.closest('.dataTables_wrapper')

    if (wrapper && filtersRef.value) {
        const topRow = wrapper.querySelector('.dt-layout-row') // верхняя строка (pageLength + search)
        if (topRow) {
            // создаём отдельный ряд под topRow
            let filtersRow = wrapper.querySelector('.apteks-filters-row')
            if (!filtersRow) {
                filtersRow = document.createElement('div')
                filtersRow.className = 'dt-layout-row apteks-filters-row'
                topRow.insertAdjacentElement('afterend', filtersRow)
            }
            // filtersRow.appendChild(filtersRef.value)
        } else {
            // fallback
            wrapper.prepend(filtersRef.value)
        }
    }
}

/*
|--------------------------------------------------------------------------
| Watchers
|--------------------------------------------------------------------------
*/
watch(
    () => filters.value.region_id,
    async (newVal) => {
        await loadCities(newVal)
        reloadTable()
    }
)

watch(() => filters.value.city_id, () => reloadTable())
watch(() => filters.value.company_id, () => reloadTable())
watch(() => filters.value.brand_id, () => reloadTable())

/*
|--------------------------------------------------------------------------
| Lifecycle
|--------------------------------------------------------------------------
*/
onMounted(async () => {
    axios.defaults.withCredentials = true
    axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'
    const csrf = getCsrf()
    if (csrf) axios.defaults.headers.common['X-CSRF-TOKEN'] = csrf

    await Promise.all([loadRegions(), loadCompanies(), loadBrands()])
    await initDataTable()
})

onBeforeUnmount(() => {
    if (dt) {
        dt.destroy()
        dt = null
    }
})
</script>

<template>
    <div ref="rootRef" class="apteks-table-status">
        <h2 class="mb-3">{{ preset?.title }}</h2>

        <!-- Мої кнопки експорту (повністю під твій стиль) -->
        <div class="d-flex flex-wrap gap-2 mb-2 justify-content-end">
            <button class="btn btn-sm btn-outline-primary" @click="dtButton('print')">Print</button>
            <button class="btn btn-sm btn-outline-primary" @click="dtButton('copy')">Copy</button>
            <button class="btn btn-sm btn-outline-primary" @click="dtButton('pdf')">PDF</button>
            <button class="btn btn-sm btn-outline-primary" @click="dtButton('excel')">Excel</button>
            <button class="btn btn-sm btn-outline-primary" @click="dtButton('csv')">CSV</button>
        </div>

        <!-- Фільтри (цей блок буде переміщено під стандартні контролі DataTables) -->

        <ApteksFilters
            ref="filtersRef"
            v-model="filters"
            :regions="regions"
            :cities="cities"
            :companies="companies"
            :brands="brands"
            :is-cities-disabled="isCitiesDisabled"
        />

        <table ref="tableRef" class="table table-striped display w-100"></table>
    </div>
</template>

<style scoped>
.apteks-table-status {
    width: 100%;
}
.apteks-filters select {
    min-width: 180px;
}
</style>
