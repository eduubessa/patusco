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
    generateHashedColor,
    getInitials
} from '@/utils/formatters';
import StatCard from '@/components/StatCard.vue';

interface Props {
    animals_data: PaginatedData<Animal>;
    breadcrumbs: BreadcrumbItem[];
}

const props = defineProps<Props>();

const headers = [
    { title: 'Referência', key: 'ref' },
    { title: '', key: 'avatar', align: 'center' },
    { title: 'Nome', key: 'name' },
    { title: 'Idade', key: 'birthday' },
    { title: 'Espécie', key: 'species' },
    { title: 'Raça', key: 'breed' },
    { title: 'Veternário', key: 'doctor.name' },
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
    return props.animals_data.data.map((animal) => ({
        ...animal,
        ref: animal.registration_id,
        avatar: getInitials(animal.name),
        avatarColor: generateHashedColor(animal.name),
        birthday: calculateAge(animal.birthday) + ' anos',
        created_at: formatDateTime(animal.created_at),
        updated_at: formatDateTime(animal.updated_at),
    }));
});

const handleOpenCustomerDetailPage = (event: Event, row: any) => {
    const animal = row?.item;
    if(!animal.slug) return;

    console.log(animal.slug);
    debugger;

    router.visit(animals.show(animal.slug).url);
};

const handlePageChange = (page: number) => {
    router.get(
        animals.list().url,
        { page: page },
        { preserveState: true, replace: true },
    );
};

</script>

<template>
    <Head title="Animais" />
    <AppLayout :breadcrumbs="breadcrumbs" title="Animais" description="Lista de todos os animais">
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <v-container>
                <v-row class="mt-4">
                    <v-col cols="12" md="4" lg="4">
                        <StatCard
                            title="Total de animais"
                            :value="animals_data.total"
                            variation-value="4.2%"
                            variation-unit=""
                            :is-positive="true"
                        />
                    </v-col>
                    <v-col cols="12" md="4" lg="4">
                        <StatCard
                            title="Novas Consultas"
                            :value="0"
                            variation-value="3.6%"
                            variation-unit=""
                            :is-positive="true"
                        />
                    </v-col>
                    <v-col cols="12" md="4" lg="4">
                        <StatCard
                            title=""
                            :value="427"
                            variation-value="1.5%"
                            variation-unit=""
                            :is-positive="false"

                        />
                    </v-col>
                </v-row>
            </v-container>
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
            <div class="my-5 mt-6 flex justify-center text-sm">
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
