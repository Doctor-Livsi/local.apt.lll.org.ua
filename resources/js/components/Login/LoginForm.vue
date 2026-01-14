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
    props: {
        // Убрал csrfToken — теперь фетчим динамически
    },
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
                // Шаг 1: Фетч CSRF-куки (set XSRF-TOKEN и laravel_session, если нужно)
                console.log('Фетч /sanctum/csrf-cookie...');
                await fetch('/sanctum/csrf-cookie', {
                    method: 'GET',
                    credentials: 'include',  // Куки
                });
                console.log('CSRF-куки set');

                const redirectPath = window.location.pathname + window.location.search;
                const requestData = {
                    ...this.form,
                    redirect: redirectPath,
                };

                console.log('Дані для /api/login:', JSON.stringify(requestData));

                // Шаг 2: POST на логин
                const response = await fetch('/api/login', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        // X-CSRF-TOKEN берётся из куки автоматически (fetch + credentials)
                    },
                    body: JSON.stringify(requestData),
                    credentials: 'include',  // Передаёт/получает куки (laravel_session)
                });

                const data = await response.json();

                console.log('Response от /api/login:', data);

                if (response.status === 419) {
                    this.errors.message = 'Сторінка застаріла. Оновіть і спробуйте знову.';
                    return;
                }

                if (!data.success) {
                    this.errors = {
                        message: data.message || 'Помилка входу',
                        email: data.errors?.email || [],
                        password: data.errors?.password || [],
                    };
                    return;
                }

                // Шаг 3: После успеха — set Bearer-токен для API (если возвращается)
                if (data.token) {
                    localStorage.setItem('auth_token', data.token);  // Или в axios.defaults
                    // Если используешь axios глобально:
                    // axios.defaults.headers.common['Authorization'] = `Bearer ${data.token}`;
                    console.log('Bearer-токен збережено:', data.token);
                }

                // Шаг 4: Редирект — используй router, если есть (в App.vue или router/index.js)
                const redirectUrl = data.redirect || '/';
                if (this.$router) {
                    this.$router.push(redirectUrl);  // SPA-редирект, сессия держится
                } else {
                    window.location.href = redirectUrl;  // Fallback
                }

            } catch (error) {
                console.error('Помилка в submitForm:', error);
                this.errors.message = error.message || 'Невідома помилка';
            } finally {
                this.loading = false;
            }
        },
    },
};
</script>
