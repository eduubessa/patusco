<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { computed } from 'vue';
import doctors from '@/routes/doctors';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { type Doctor, type PaginatedData } from '@/types/Doctor';
import {
    formatDateTime,
    generateHashedColor,
    getInitials,
} from '@/utils/formatters';

interface Props {
    doctors_data: PaginatedData<Doctor>,
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
    return props.doctors_data.data.map((doctor, index) => ({
        ...doctor,
        line_number: index + 1,
        avatar: getInitials(doctor.name),
        avatarColor: generateHashedColor(doctor.name),
        created_at: formatDateTime(doctor.created_at),
        updated_at: formatDateTime(doctor.updated_at),
    }));
});

const handleOpendoctorDetailPage = (event: Event, { item }: { item: any}) => {
    if(item.username){
        window.location.href = doctors.list().url;
    }

    debugger;
};

const handlePageChange = (page: number) => {
    router.get(
        doctors.list().url,
        { page: page},
        { preserveState: true, replace: true }
    )
};
</script>

<template>
    <Head title="Utentes" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <v-data-table-virtual
                :headers="headers"
                :items="data"
                item-value="username"
                fixed-header
                loading-text="A carregar ..."
                :show-select="true"
                class="cursor-pointer"
                @click:row="handleOpendoctorDetailPage"
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
                    :length="props.doctors_data.last_page"
                    :model-value="props.doctors_data.current_page"
                    :total-visible="5"
                    @update:model-value="handlePageChange"
                    color="indigo-darken-3"
                ></v-pagination>
            </div>
        </div>
    </AppLayout>
</template>
