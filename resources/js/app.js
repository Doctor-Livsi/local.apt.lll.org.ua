import { createApp } from 'vue';

import 'bootstrap/dist/css/bootstrap.min.css';
import * as bootstrap from 'bootstrap';
window.bootstrap = bootstrap;
//
// DataTables CSS — раніше
import 'datatables.net-dt/css/dataTables.dataTables.css';
import 'datatables.net-responsive-dt/css/responsive.dataTables.css';

// import 'datatables.net-dt/css/jquery.dataTables.css'
import 'datatables.net-buttons-dt/css/buttons.dataTables.css'
import 'datatables.net-select-dt/css/select.dataTables.css'
import 'datatables.net-keytable-dt/css/keyTable.dataTables.css'
import 'datatables.net-colreorder-dt/css/colReorder.dataTables.css'
import 'datatables.net-searchpanes-dt/css/searchPanes.dataTables.css'


import '../css/styles.css';
import '../css/custom.css';
import '../css/main.css';

import './echo';

// import '../css/custom/acorn-datatables.css'
import $ from 'jquery';

// Робимо jQuery глобально доступним
window.$ = $;
window.jQuery = $;

const loginEl = document.getElementById('login');
if (loginEl) {
    import('./pages/Login/LoginPage.vue').then(({ default: LoginPage }) => {
        createApp(LoginPage).mount(loginEl);
    });
}
const apteksCounterEl = document.getElementById('apteksCounterWidget');
if (apteksCounterEl) {
    import('./components/Widget/ApteksCounterConnected.vue').then(({ default: ApteksCounter }) => {
        createApp(ApteksCounter).mount(apteksCounterEl);
    });
}

const apteksChatBotWidgetEl = document.getElementById('apteksCounterChatBotWidget');
if (apteksChatBotWidgetEl) {
    import('./components/Widget/ApteksCounterChatBotWidget.vue').then(
        ({ default: ApteksCounterChatBotWidget }) => {
            createApp(ApteksCounterChatBotWidget).mount(apteksChatBotWidgetEl);
        }
    );
}
