import { createApp } from 'vue'

// Bootstrap
import 'bootstrap/dist/css/bootstrap.min.css'
import * as bootstrap from 'bootstrap'
window.bootstrap = bootstrap

// DataTables CSS
import 'datatables.net-dt/css/dataTables.dataTables.css'
import 'datatables.net-responsive-dt/css/responsive.dataTables.css'
import 'datatables.net-buttons-dt/css/buttons.dataTables.css'
import 'datatables.net-select-dt/css/select.dataTables.css'
import 'datatables.net-keytable-dt/css/keyTable.dataTables.css'
import 'datatables.net-colreorder-dt/css/colReorder.dataTables.css'
import 'datatables.net-searchpanes-dt/css/searchPanes.dataTables.css'

// Project styles
import '../css/styles.css'
import '../css/custom.css'
import '../css/main.css'

// Echo / WS
import './echo'

// jQuery global
import $ from 'jquery'
window.$ = $
window.jQuery = $

/**
 * Глобальний mount для віджетів (WS), які можуть бути присутні на будь-якій сторінці.
 * ВАЖЛИВО: віджети лежать у components/apteks/details/
 */
import { mountGlobalWidgets } from '@/mount/widgets'

// ---------------------------
// Mount: Login page
// ---------------------------
const loginEl = document.getElementById('login')
if (loginEl) {
    import('./pages/login/LoginPage.vue').then(({ default: LoginPage }) => {
        createApp(LoginPage).mount(loginEl)
    })
}

// ---------------------------
// Mount: Global WS widgets
// ---------------------------
const runMountGlobalWidgets = () => {
    try {
        mountGlobalWidgets()
    } catch (e) {
        console.error('[mountGlobalWidgets] error:', e)
    }
}

// 1) Звичайне завантаження
document.addEventListener('DOMContentLoaded', runMountGlobalWidgets)

// 2) Back/Forward cache (bfcache) — DOMContentLoaded НЕ спрацює, але сторінка відновиться
window.addEventListener('pageshow', () => {
    runMountGlobalWidgets()
})

// 3) Якщо в тебе Turbo/Turbolinks або інша “розумна” навігація
document.addEventListener('turbo:load', runMountGlobalWidgets)
document.addEventListener('turbolinks:load', runMountGlobalWidgets)

// 4) На всяк випадок — зміна history без повного перезавантаження
window.addEventListener('popstate', runMountGlobalWidgets)
