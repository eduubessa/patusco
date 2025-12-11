<script setup lang="ts">
import AppShell from '@/components/AppShell.vue';
import AppSidebar from '@/components/AppSidebar.vue';
import AppHeader from '@/components/AppHeader.vue';
import { BreadcrumbItemType } from '@/types';

interface Props {
    title: string;
    description: string;
    breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
    title: 'A carregar dados ...',
    description: 'A carregar dados ...',
    breadcrumbs: () => []
})
</script>

<template>
    <v-app>
        <AppShell variant="sidebar">
            <AppSidebar />
            <div
                class="absolute left-20 right-0 top-0 bottom-0 flex flex-col min-h-screen"
            >
                <AppHeader :breadcrumbs="breadcrumbs" :title="title" :description="description" />
                <div class="flex-1 min-w-0 overflow-auto">
                    <slot />
                </div>
            </div>
        </AppShell>
    </v-app>
</template>
