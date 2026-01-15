<template>
    <div>
        <form @submit.prevent="submitForm" class="tooltip-end-bottom" novalidate>
            <div v-if="errors.message && errors.message.length > 0" class="text-danger mb-3">
                {{ errors.message }}
            </div>
            <div class="mb-3 filled form-group tooltip-end-bottom">
                <i data-acorn-icon="email"></i>
                <input
                    id="email"
                    v-model="form.email"
                    type="email"
                    class="form-control"
                    placeholder="Email"
                    autocomplete="username"
                    required
                />
                <div v-if="errors.email.length" class="text-danger">{{ errors.email[0] }}</div>
            </div>
            <div class="mb-3 filled form-group tooltip-end-bottom">
                <i data-acorn-icon="lock-off"></i>
                <input
                    id="password"
                    v-model="form.password"
                    type="password"
                    class="form-control pe-7"
                    placeholder="Password"
                    autocomplete="current-password"
                    required
                />
                <div v-if="errors.password.length" class="text-danger">{{ errors.password[0] }}</div>
            </div>
            <button type="submit" class="btn btn-lg btn-primary w-100" :disabled="loading">
                {{ loading ? 'Вхід...' : 'Вхід' }}
            </button>
        </form>
    </div>
</template>

<script>
import 'bootstrap/dist/css/bootstrap.min.css'
import 'bootstrap'

export default {
    name: 'LoginForm',

    data() {
        return {
            form: {
                email: '',
                password: '',
                remember: false,
            },
            errors: {
                message: '',
                email: [],
                password: [],
            },
            loading: false,
        };
    },

    methods: {
        getCookie(name) {
            const value = `; ${document.cookie}`;
            const parts = value.split(`; ${name}=`);
            if (parts.length === 2) {
                return parts.pop().split(';').shift();
            }
            return null;
        },

        async submitForm() {
            this.errors = { message: '', email: [], password: [] };
            this.loading = true;

            if (!this.form.email) {
                this.errors.email = ['Електронна пошта обов’язкова.'];
                this.loading = false;
                return;
            }

            if (!this.form.password) {
                this.errors.password = ['Пароль обов’язковий.'];
                this.loading = false;
                return;
            }

            try {
                console.log('Поточні куки ДО csrf:', document.cookie);

                // 1️⃣ Получаем CSRF-cookie
                await fetch('/sanctum/csrf-cookie', {
                    credentials: 'include',
                });

                console.log('Куки ПІСЛЯ csrf:', document.cookie);

                // 2️⃣ Достаём XSRF-TOKEN и ДЕКОДИРУЕМ
                const rawToken = this.getCookie('XSRF-TOKEN');

                if (!rawToken) {
                    throw new Error('XSRF-TOKEN не знайдено в cookie');
                }

                const xsrfToken = decodeURIComponent(rawToken);
                console.log('XSRF-TOKEN (decoded):', xsrfToken);

                const requestData = {
                    email: this.form.email,
                    password: this.form.password,
                    remember: this.form.remember,
                    redirect: window.location.pathname + window.location.search,
                };

                // 3️⃣ ЛОГИН ЧЕРЕЗ /login (НЕ /api/login)
                const response = await fetch('/login', {
                    method: 'POST',
                    credentials: 'include',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-XSRF-TOKEN': xsrfToken,
                    },
                    body: JSON.stringify(requestData),
                });

                console.log('HTTP status:', response.status);

                const data = await response.json();
                console.log('Response data:', data);

                if (response.status === 419) {
                    this.errors.message = 'Сторінка застаріла. Оновіть і спробуйте знову.';
                    return;
                }

                if (!response.ok || !data.success) {
                    this.errors.message = data.message || 'Помилка входу';
                    this.errors.email = data.errors?.email || [];
                    this.errors.password = data.errors?.password || [];
                    return;
                }

                // 4️⃣ Сохраняем Bearer-токен (если используешь)
                if (data.token) {
                    localStorage.setItem('auth_token', data.token);
                    console.log('Bearer-токен збережено');
                }

                // 5️⃣ Редирект
                const redirectUrl = data.redirect || '/';
                window.location.href = redirectUrl;

            } catch (error) {
                console.error('Помилка submitForm:', error);
                this.errors.message = error.message || 'Невідома помилка';
            } finally {
                this.loading = false;
            }
        },
    },
};

</script>
