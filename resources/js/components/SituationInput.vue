<script setup lang="ts">
import { ref, watch } from 'vue';

const props = defineProps<{
    modelValue: string;
}>();
const emit = defineEmits(['update:modelValue']);

const localValue = ref(props.modelValue ?? '');

watch(localValue, (val) => emit('update:modelValue', val));
watch(() => props.modelValue, (val) => {
    if (val !== localValue.value) localValue.value = val ?? '';
});
</script>

<template>
    <div class="form-row">
        <label class="form-label">Situação</label>
        <v-textarea
            v-model="localValue"
            rows="3"
            auto-grow
            variant="outlined"
            hide-details
            density="comfortable"
            class="form-input"
            placeholder="Descreva a situação"
        />
    </div>
</template>

<style scoped>
.form-row {
    display: flex;
    align-items: flex-start;
    gap: 16px;
    margin-bottom: 20px;
}

.form-label {
    width: 180px;
    font-weight: 600;
    color: #333;
    margin-top: 8px;
}

.form-input {
    flex: 1;
}
</style>
