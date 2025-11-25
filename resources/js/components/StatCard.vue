<script setup lang="ts">
import { computed, onMounted, ref, watch } from 'vue';
import { VCard, VIcon } from 'vuetify/components';

const props = defineProps<{
    title: string;
    value: number;
    variationValue: string; // Ex: "4.2%"
    variationUnit: string; // Ex: "%"
    isPositive: boolean; // true se for aumento, false se for descida
}>();

const formattedValue = ref(props.value.toLocaleString());
const isAnimating = ref(true);

watch(
    () => props.value,
    (newValue, oldValue) => {
        isAnimating.value = true;
        // Simula um "re-render" com delay para animação fade-in
        setTimeout(() => {
            formattedValue.value = newValue.toLocaleString();
            isAnimating.value = false;
        }, 100);
    },
    { immediate: true },
);

onMounted(() => {
    setTimeout(() => {
        isAnimating.value = false;
    }, 100);
});

const variationIcon = computed(() => {
    return props.isPositive ? 'mdi-arrow-up-right' : 'mdi-arrow-down-right';
});

const variationColorClasses = computed(() => {
    return props.isPositive
        ? 'bg-green-100 text-green-700'
        : 'bg-red-100 text-red-700';
});
</script>
<template>
    <v-card
        class="pa-4 shadow-lg transition duration-300 ease-in-out hover:shadow-xl"
        :class="{ 'animate-pulse-on-hover': showPulse }"
    >
        <p class="mb-2 text-sm font-semibold text-gray-500">{{ title }}</p>

        <h2
            class="mb-3 text-4xl text-gray-900 transition-opacity duration-700"
            :class="{ 'opacity-0': isAnimating }"
        >
            {{ formattedValue }}
        </h2>

        <div
            class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold"
            :class="variationColorClasses"
        >
            <v-icon :icon="variationIcon" size="small" class="mr-1"></v-icon>

            <span>{{ variationValue }} {{ variationUnit }} Last Month</span>
        </div>
    </v-card>
</template>

<style scoped>
.animate-pulse-on-hover:hover {
    animation: none;
    transform: translateY(-5px);
}
.transition-opacity {
    transition: opacity 0.3s ease-in-out;
}
</style>
