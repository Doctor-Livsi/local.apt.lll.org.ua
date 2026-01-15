import axios from 'axios';

// Налаштування CSRF-токена (з meta, як на test)
const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
if (token) {
    axios.defaults.headers.common['X-CSRF-TOKEN'] = token;
}
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.withCredentials = true;  // Куки (laravel_session, XSRF-TOKEN)

export default axios;
