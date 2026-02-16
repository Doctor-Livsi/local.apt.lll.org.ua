<template>
    <div>
        <form @submit.prevent="submitForm" class="tooltip-end-bottom" novalidate>
            <div v-if="errors.message" class="text-danger mb-3">
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
                <div v-if="errors.email.length" class="text-danger">
                    {{ errors.email[0] }}
                </div>
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
                <div v-if="errors.password.length" class="text-danger">
                    {{ errors.password[0] }}
                </div>
            </div>

            <button type="submit" class="btn btn-lg btn-primary w-100" :disabled="loading">
                {{ loading ? 'Вхід...' : 'Вхід' }}
            </button>
        </form>
    </div>
</template>

<script>
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap';

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
        getCsrfTokenFromMeta() {
            const el = document.querySelector('meta[name="csrf-token"]');
            return el ? el.getAttribute('content') : null;
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
                const csrf = this.getCsrfTokenFromMeta();
                if (!csrf) {
                    throw new Error('CSRF token не знайдено. Додай <meta name="csrf-token" content="..."> в шаблон.');
                }

                const requestData = {
                    email: this.form.email,
                    password: this.form.password,
                    remember: this.form.remember,
                    redirect: window.location.pathname + window.location.search,
                };

                const response = await fetch('/login', {
                    method: 'POST',
                    credentials: 'include',
                    headers: {
                        Accept: 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                    },
                    body: JSON.stringify(requestData),
                });

                // 419 — CSRF истёк/не совпал
                if (response.status === 419) {
                    this.errors.message = 'Сторінка застаріла. Оновіть і спробуйте знову.';
                    return;
                }

                // Даже если не ok — Laravel часто возвращает json, попробуем прочитать
                let data = null;
                try {
                    data = await response.json();
                } catch (e) {
                    data = null;
                }

                if (!response.ok || !data || !data.success) {
                    this.errors.message = (data && data.message) ? data.message : 'Помилка входу';
                    this.errors.email = (data && data.errors && data.errors.email) ? data.errors.email : [];
                    this.errors.password = (data && data.errors && data.errors.password) ? data.errors.password : [];
                    return;
                }

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
