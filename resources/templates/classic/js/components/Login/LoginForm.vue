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
      <button type="submit" class="btn btn-lg btn-primary w-100">Вхід</button>
    </form>
  </div>
</template>

<script>
import 'bootstrap/dist/css/bootstrap.min.css'
import 'bootstrap'
export default {
  name: 'LoginForm',
  props: {
    csrfToken: {
      type: String,
      required: true,
    },
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
    };
  },
  methods: {
    async submitForm() {
      this.errors = {
        message: '',
        email: [],
        password: [],
      };

      if (!this.form.email) {
        this.errors.email = ['Електронна пошта обов’язкова.'];
        return;
      }
      if (!this.form.password) {
        this.errors.password = ['Пароль обов’язковий.'];
        return;
      }

      try {
        const headers = {
          'Accept': 'application/json',
          'X-CSRF-TOKEN': this.csrfToken,
          'Content-Type': 'application/json',
        };

        const redirectPath = window.location.pathname + window.location.search;
        const requestData = {
          ...this.form,
          redirect: redirectPath,
        };

        // console.log('Дані, які передаємо:', JSON.stringify(requestData));
        // await new Promise(resolve => setTimeout(resolve, 5000));

        const response = await fetch('/api/login', {
          method: 'POST',
          headers: headers,
          body: JSON.stringify(requestData),
          credentials: 'include',
        });

        const data = await response.json();

        if (!data.success) {
          this.errors = {
            message: data.message || 'Помилка входу',
            email: data.errors?.email || [],
            password: data.errors?.password || [],
          };
          return;
        }

        const sessionCookie = document.cookie.split('; ').find(row => row.startsWith('laravel_session='));

        const redirectUrl = data.redirect || '/';

        window.location.href = redirectUrl === '/' ? '/' : redirectUrl;
      } catch (error) {
        console.error('Помилка:', error.message);
        this.errors.message = error.message || 'Невідома помилка';
      }
    },
  },
};
</script>