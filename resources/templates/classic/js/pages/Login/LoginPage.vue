<template>
  <div class="h-100">
    <!-- Background -->
    <div class="fixed-background"></div>

    <div class="container-fluid p-0 min-h-100 position-relative">
      <div class="row g-0 h-100">
        <!-- Left Side -->
        <div class="offset-0 col-12 d-none d-lg-flex offset-md-1 col-lg h-lg-100">
          <div class="min-h-100 d-flex align-items-center">
            <div class="w-100 w-lg-75 w-xxl-50">
            </div>
          </div>
        </div>
        <!-- Right Side -->
        <div class="col-12 col-lg-auto h-100 pb-4 px-4 pt-0 p-lg-0">
          <div class="sw-lg-70 min-h-100 bg-foreground d-flex justify-content-center align-items-center shadow-deep py-5 full-page-content-right-border">
            <div class="sw-lg-50 px-5">
              <div class="mb-5">
                <h2 class="cta-1 mb-0 text-primary">Ласкаво просимо,</h2>
                <h2 class="cta-1 text-primary">Давайте почнемо!</h2>
              </div>
              <div class="mb-5">
                <p class="h6">Будь ласка,</p>
                <p class="h6">Використовуйте свої облікові дані для входу.</p>
              </div>
              <div>
                <login-form :csrf-token="csrfToken"></login-form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import LoginForm from '../../components/Login/LoginForm.vue';

export default {
  name: 'LoginPage',
  components: {
    LoginForm,
  },
  props: {
    csrfToken: {
      type: String,
      required: true,
    },
  },
};
</script>
