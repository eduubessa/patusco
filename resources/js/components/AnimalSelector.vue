<script setup lang="ts">
import { ref, watch, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

const props = defineProps({
    modelValue: Object,
    customer: Object,
    validation: String
});
const emit = defineEmits(['update:modelValue']);

const internalValue = ref(props.modelValue);
watch(() => props.modelValue, v => internalValue.value = v);
watch(internalValue, v => emit('update:modelValue', v));

const user = usePage().props.auth.user;
const isCustomer = computed(() => user.role === 'customer');

const animals = ref([]);
const loading = ref(false);

const loadAnimals = async (customerUsername: string) => {
    loading.value = true;
    try {
        const res = await fetch(`/api/customers/${customerUsername}`);
        const json = await res.json();
        animals.value = json.data.animals ?? [];
    } finally {
        loading.value = false;
    }
};

if (isCustomer.value) {
    loadAnimals(user.username);
}

watch(() => props.customer, customer => {
    if (!customer) {
        animals.value = [];
        internalValue.value = null;
        return;
    }
    loadAnimals(customer.username);
});
</script>

<template>
    <div class="form-row">
        <label class="form-label">Animal</label>
        <v-select
            v-model="internalValue"
            :items="animals"
            item-title="name"
            item-value="slug"
            return-object
            :loading="loading"
            @update:model-value="val => emit('update:modelValue', val)"
            :disabled="(!isCustomer.value && !props.customer) || animals.length === 0"
            variant="outlined"
            density="comfortable"
            class="form-input"
            :error="props.validation != null && props.validation.length > 0"
            :error-messages="props.validation"
        />
    </div>
</template>

<style scoped>
.form-row {
    display: flex;
    align-items: center;
    gap: 16px;
    margin-bottom: 20px;
}
.form-label {
    width: 180px;
    font-weight: 600;
    color: #333;
}
.form-input {
    flex: 1;
}
</style>
