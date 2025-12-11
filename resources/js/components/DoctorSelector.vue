<script setup lang="ts">
import { ref, watch, computed } from 'vue';
import type { Doctor } from '@/types/Doctor';

const props = defineProps({
    modelValue: Object,
    date: [String, null],
    period: [String, null],
    hour: [String, null],
    staticDoctors: Array as () => Doctor[] | undefined,
    validation: String
});

const emit = defineEmits(['update:modelValue']);

const internalValue = ref(props.modelValue ?? null);
watch(() => props.modelValue, v => internalValue.value = v);
watch(internalValue, v => emit('update:modelValue', v));

const availableDoctors = ref<Doctor[]>(props.staticDoctors ?? []);
const loadingDoctors = ref(false);

watch(() => props.staticDoctors, (v) => {
    if (!v || v.length === 0) return;
    if (!availableDoctors.value.length) availableDoctors.value = v;
});

const shouldFetch = computed(() => {
    return !!props.date && !!props.period && !!props.hour;
});

const fetchAvailableDoctors = async () => {
    if (!shouldFetch.value) {
        availableDoctors.value = props.staticDoctors ?? [];
        internalValue.value = null;
        return;
    }

    loadingDoctors.value = true;
    try {
        const resp = await fetch(
            `/api/doctors/?period=${encodeURIComponent(props.period as string)}&hour=${encodeURIComponent(props.hour as string)}&date=${encodeURIComponent(props.date as string)}`
        );
        const json = await resp.json();
        availableDoctors.value = json.data ?? [];
        // se o doctor atualmente selecionado deixou de estar na lista, limpar
        if (internalValue.value) {
            const stillExists = availableDoctors.value.some(d => d.id === internalValue.value.id);
            if (!stillExists) internalValue.value = null;
        }
    } catch (e) {
        console.error('Erro ao buscar doctors:', e);
        availableDoctors.value = props.staticDoctors ?? [];
        internalValue.value = null;
    } finally {
        loadingDoctors.value = false;
    }
};

watch([() => props.date, () => props.period, () => props.hour], fetchAvailableDoctors, { immediate: true });
</script>

<template>
    <div class="form-row">
        <label class="form-label">Veterinário</label>
        <v-select
            v-model="internalValue"
            :items="availableDoctors"
            item-title="name"
            item-value="id"
            return-object
            variant="outlined"
            hide-details="auto"
            density="comfortable"
            class="form-input"
            :disabled="!shouldFetch || loadingDoctors"
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
