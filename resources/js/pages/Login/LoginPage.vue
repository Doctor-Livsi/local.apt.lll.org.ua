<template>
    <div class="h-100">
        <div class="fixed-background-logon"></div>

        <div class="container-fluid p-0 vh-100 position-relative">
            <div class="row g-0 h-100">
                <!-- Ліва частина -->
                <div class="d-none d-lg-flex col-lg-7 offset-xl-1 align-items-center">
                    <div class="w-100 w-lg-75 w-xxl-50">
                        <!-- Ліва частина container -->
                    </div>
                </div>

                <!-- Права частина: ТЕПЕР це bootstrap-колонка -->
                <div class="col-12 col-lg d-flex flex-column justify-content-center align-items-end py-5 full-page-content-right-border px-4 full-page-content-right-border ">
                    <div class="bg-foreground shadow-deep py-5 px-4 px-lg-5 w-100" style="max-width: 480px; min-height: 380px;">
                        <div class="mb-5 text-lg-start">
                            <h2 class="cta-1 mb-1 text-primary">Ласкаво просимо</h2>
                        </div>
                        <login-form :csrf-token="csrfToken" />
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
