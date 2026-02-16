<script setup>
defineProps({
    aptekaId: { type: Number, required: true }
})
</script>

<template>
    <h2 class="small-title text-uppercase"><b>Відвідування аптеки</b></h2>
    <div class="card card-primary mb-2">
        <div class="card-body">
            <div class="text-muted">
                Логи, події, історія активності
            </div>
        </div>
    </div>
</template>
