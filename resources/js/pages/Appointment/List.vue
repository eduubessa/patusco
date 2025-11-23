<script setup lang="ts">
import { Head } from '@inertiajs/vue3';

import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { type Customer } from '@/types/Customer';

interface Props {
    customers: Customer[]
}

const props = defineProps<Props>();

const headers = [
    { name: 'ID', key: 'id' },
    { name: 'Nome', key: 'name' },
    { name: 'Email', key: 'email' },
    { name: 'Telefone', key: 'phone_number' },
    { name: 'Registo', key: 'created_at' },
];

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Agendamentos',
        href: '/appointments'
    },
];
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <v-data-table-virtual
                :headers="headers"
                :items="virtualBoats"
                height="400"
                item-value="name"
                fixed-header
            ></v-data-table-virtual>
        </div>
    </AppLayout>
</template>
