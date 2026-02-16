<script setup>
defineProps({
    aptekId: { type: Number, required: true },
    providerIndex: { type: Number, default: 1 } // 1 / 2
})
</script>

<template>
    <div class="card card-primary h-100">
        <div class="card-header d-flex justify-content-between">
      <span class="small-title text-uppercase">
        <b>Провайдер {{ providerIndex }}</b>
      </span>
        </div>

        <div class="card-body">
            <div class="text-muted">
                Назва провайдера<br>
                Тип підключення<br>
                Протокол<br>
                Контакти
            </div>
        </div>
    </div>
</template>
