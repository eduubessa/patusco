<script setup lang="ts">
import { ref, watch } from 'vue';

const props = defineProps({
    modelValue: Object,
});
const emit = defineEmits(['update:modelValue']);

const searchCustomers = ref('');
const customers = ref([]);
const loading = ref(false);

let timer: number | null = null;

const fetchCustomers = async (query: string) => {
    loading.value = true;
    try {
        const res = await fetch(`/api/customers?search=${encodeURIComponent(query)}`);
        const json = await res.json();
        customers.value = json.data;
    } catch (error) {
        console.error('fetch customers error', error);
    } finally {
        loading.value = false;
    }
};

watch(searchCustomers, (val) => {
    if (timer) clearTimeout(timer);
    timer = setTimeout(() => fetchCustomers(val), 300);
});
</script>

<template>
    <div class="form-row">
        <label class="form-label">Cliente</label>
        <v-autocomplete
            v-model="props.modelValue"
            @update:model-value="val => emit('update:modelValue', val)"
            v-model:search="searchCustomers"
            :items="customers"
            item-title="name"
            item-value="username"
            return-object
            :loading="loading"
            variant="outlined"
            density="comfortable"
            class="form-input"
        />
    </div>
</template>

<style scoped>
.form-row {
    display: flex;
    align-items: center; /* alinhamento vertical */
    gap: 16px;           /* espaço entre label e input */
    margin-bottom: 20px;
}

.form-label {
    width: 180px;        /* coluna fixa para label */
    font-weight: 600;
    color: #333;
}

.form-input {
    flex: 1;             /* input ocupa restante espaço */
}
</style>
