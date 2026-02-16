<template>
    <div class="apteks-filters mb-2">
        <div class="d-grid gap-2 apteks-filters-grid">
            <select class="form-select" v-model="model.region_id">
                <option value="">Область: всі</option>
                <option v-for="r in regions" :key="r.id" :value="r.id">{{ r.name }}</option>
            </select>

            <select class="form-select" v-model="model.city_id" :disabled="isCitiesDisabled">
                <option value="">Місто: всі</option>
                <option v-for="c in cities" :key="c.id" :value="c.id">{{ c.name }}</option>
            </select>

            <select class="form-select" v-model="model.company_id">
                <option value="">Фірма: всі</option>
                <option v-for="c in companies" :key="c.id" :value="c.id">{{ c.name }}</option>
            </select>

            <select class="form-select" v-model="model.brand_id">
                <option value="">Бренд: всі</option>
                <option v-for="b in brands" :key="b.id" :value="b.id">{{ b.name }}</option>
            </select>
        </div>
    </div>
</template>

<script setup>
const model = defineModel({ type: Object, required: true })

defineProps({
    regions: { type: Array, default: () => [] },
    cities: { type: Array, default: () => [] },
    companies: { type: Array, default: () => [] },
    brands: { type: Array, default: () => [] },
    isCitiesDisabled: { type: Boolean, default: false },
})
</script>

<style scoped>
.apteks-filters-grid {
    grid-template-columns: 1fr;           /* mobile */
}

@media (min-width: 768px) {             /* md */
    .apteks-filters-grid {
        grid-template-columns: 1fr 1fr;     /* 2 в ряд */
    }
}

@media (min-width: 992px) {             /* lg */
    .apteks-filters-grid {
        grid-template-columns: repeat(4, 1fr); /* 4 в ряд */
    }
}
</style>
