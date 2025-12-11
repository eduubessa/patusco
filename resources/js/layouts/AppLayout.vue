<script setup lang="ts">
import AppLayout from '@/layouts/app/AppSidebarLayout.vue';
import type { BreadcrumbItemType } from '@/types';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
    title: string;
    description: string;
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
    title: 'A carregar dados ...',
    description: 'A carregar dados ...'
});
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs" :title="title" :description="description">
        <slot />
    </AppLayout>
</template>
