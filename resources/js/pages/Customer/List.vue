<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { computed } from 'vue';
import customers from '@/routes/customers';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { type Customer, type PaginatedData } from '@/types/Customer';
import {
    formatDateTime,
    generateHashedColor,
    getInitials,
} from '@/utils/formatters';

interface Props {
    customers_data: PaginatedData<Customer>,
    breadcrumbs: BreadcrumbItem[]
}

const props = defineProps<Props>();

const headers = [
    { title: '', key: 'avatar', align: 'center' },
    { title: 'Nome', key: 'name' },
    { title: 'Email', key: 'email' },
    { title: 'Telefone', key: 'phone_number' },
    { title: 'Criado em', key: 'created_at' },
    { title: 'Atualizado em', key: 'updated_at' },
    {
        title: 'Ações',
        key: 'actions',
        sortable: false,
        align: 'center',
        width: 120,
    },
];

const data = computed(() => {
    return props.customers_data.data.map((customer, index) => ({
        ...customer,
        line_number: index + 1,
        avatar: getInitials(customer.name),
        avatarColor: generateHashedColor(customer.name),
        created_at: formatDateTime(customer.created_at),
        updated_at: formatDateTime(customer.updated_at),
    }));
});



const handleOpenCustomerDetailPage = (event: Event, { item }: { item: any}) => {
    if(item.username){
        window.location.href = customers.list().url;
    }

    debugger;
};

const handlePageChange = (page: number) => {
    router.get(
        customers.list().url,
        { page: page},
        { preserveState: true, replace: true }
    )
};
</script>

<template>
    <Head title="Utentes" />

    <AppLayout :breadcrumbs="breadcrumbs" :title="'Utentes'" :description="'Lista de utentes'">
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto p-4"
        >
            <v-data-table-virtual
                :headers="headers"
                :items="data"
                item-value="username"
                fixed-header
                loading-text="A carregar ..."
                :show-select="true"
                class="cursor-pointer"
                @click:row="handleOpenCustomerDetailPage"
            >
                <template #item.avatar="{ item }">
                    <div class="py-2">
                        <v-avatar :color="item.avatarColor" size="36">
                            <span class="font-weight-medium text-sm text-white">
                                {{ item.avatar }}
                            </span>
                        </v-avatar>
                    </div>
                </template>
                <template #item.actions="{ item }">
                    <div class="flex items-center justify-center gap-2">
                        <v-btn
                            icon
                            size="small"
                            variant="plain"
                            color="blue"
                            title="Editar"
                        >
                            <v-icon size="small">mdi-pencil</v-icon>
                        </v-btn>

                        <v-btn
                            icon
                            size="small"
                            variant="text"
                            color="red"
                            title="Apagar"
                        >
                            <v-icon size="small">mdi-delete</v-icon>
                        </v-btn>
                    </div>
                </template>
            </v-data-table-virtual>
            <div class="mt-6 flex justify-center my-5 text-sm">
                <v-pagination
                    :length="props.customers_data.last_page"
                    :model-value="props.customers_data.current_page"
                    :total-visible="5"
                    @update:model-value="handlePageChange"
                    color="indigo-darken-3"
                ></v-pagination>
            </div>
        </div>
    </AppLayout>
</template>
