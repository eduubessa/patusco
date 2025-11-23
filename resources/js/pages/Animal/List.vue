<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { computed } from 'vue';
import animals from '@/routes/animals';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { type Animal, type PaginatedData } from '@/types/Animal';
import {
    calculateAge,
    formatDateTime,
} from '@/utils/formatters';

interface Props {
    animals_data: PaginatedData<Animal>,
    breadcrumbs: BreadcrumbItem[]
}

const props = defineProps<Props>();

const headers = [
    { title: 'ID', key: 'id'},
    { title: 'Nome', key: 'name' },
    { title: 'Idade', key: 'birthday' },
    { title: 'Veternário', key: 'doctor.name'},
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
    console.log(props.animals_data)

    return props.animals_data.data.map((animal, index) => ({
        ...animal,
        line_number: index + 1,
        birthday: calculateAge(animal.birthday) + " anos",
        created_at: formatDateTime(animal.created_at),
        updated_at: formatDateTime(animal.updated_at),
    }));
});



const handleOpenCustomerDetailPage = (event: Event, { item }: { item: any}) => {
    if(item.id){
        window.location.href = animals.list().url;
    }

    debugger;
};

const handlePageChange = (page: number) => {
    router.get(
        animals.list().url,
        { page: page},
        { preserveState: true, replace: true }
    )
};
</script>

<template>
    <Head title="Animais" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <v-data-table-virtual
                :headers="headers"
                :items="data"
                item-value="id"
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
                    :length="props.animals_data.last_page"
                    :model-value="props.animals_data.current_page"
                    :total-visible="5"
                    @update:model-value="handlePageChange"
                    color="indigo-darken-3"
                ></v-pagination>
            </div>
        </div>
    </AppLayout>
</template>
