// login.js
import { createApp } from 'vue';
import LoginPage from './LoginPage.vue';

const loginEl = document.getElementById('login');

if (loginEl) {
    createApp(LoginPage, {
        csrfToken: document.head.querySelector('meta[name="csrf-token"]').content
    }).mount(loginEl);
}
