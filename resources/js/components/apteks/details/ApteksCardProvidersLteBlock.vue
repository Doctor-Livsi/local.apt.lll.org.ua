<script setup>
defineProps({
    aptekId: { type: Number, required: true }
})
</script>

<template>
    <div class="card card-primary h-100">
        <div class="card-header">
            <span class="small-title text-uppercase"><b>Мобільний інтернет</b></span>
        </div>

        <div class="card-body">
            <div class="text-muted">
                • Оператор<br>
                • Номер SIM<br>
                • Статус звʼязку
            </div>
        </div>
    </div>
</template>
