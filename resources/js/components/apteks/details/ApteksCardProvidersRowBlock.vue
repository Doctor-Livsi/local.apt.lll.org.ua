<script setup>
defineProps({
    aptekaId: { type: Number, required: true },
})
</script>

<template>
    <h2 class="small-title text-uppercase"><b>Провайдери</b> ID аптеки: {{ aptekaId }}</h2>
    <div class="card h-100 card-primary mb-5">
        <div class="card-body">
            <!--
              Тут у майбутньому буде збірка з БД:
              - провайдери (1..2)
              - LTE/3G (0..1)
              Якщо чогось немає — карточка не рендериться.
            -->

            <div class="row g-4">
                <!-- Провайдер 1 -->
                <div class="col-12 col-lg-4">
                    <div class="card h-100 border">
                        <div class="card-body">
                            <div class="fw-bold text-uppercase mb-2">Провайдер 1</div>
                            <div class="text-muted">Заглушка</div>
                        </div>
                    </div>
                </div>

                <!-- Провайдер 2 -->
                <div class="col-12 col-lg-4">
                    <div class="card h-100 border">
                        <div class="card-body">
                            <div class="fw-bold text-uppercase mb-2">Провайдер 2</div>
                            <div class="text-muted">Заглушка</div>
                        </div>
                    </div>
                </div>

                <!-- LTE / 3G -->
                <div class="col-12 col-lg-4">
                    <div class="card h-100 border">
                        <div class="card-body">
                            <div class="fw-bold text-uppercase mb-2">LTE / 3G</div>
                            <div class="text-muted">Заглушка</div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</template>
