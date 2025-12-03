<script setup lang="ts">
import { computed, ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import appointments from '@/routes/appointments';
import AppLayout from '@/layouts/AppLayout.vue';
import { formatDateTime } from '@/utils/formatters';
import type { AppointmentType, PaginatedData } from '@/types/Appointment';
import { BreadcrumbItemType } from '@/types';

interface Props {
    appointments_data: PaginatedData<AppointmentType>,
    breadcrumbs: BreadcrumbItemType,
    species: []
}

const props = defineProps<Props>();
const filterSpecies = ref<string | null>(null);
const filterStartDate = ref<string | null>(null);
const filterEndDate = ref<string | null>(null);
const showDeleteDialog = ref(false);

const headers = [
    { title: 'Situação', key: 'situation' },
    { title: 'Animal', key: 'animal' },
    { title: 'Tipo de Animal', key: 'species'},
    { title: 'Veternário', key: 'doctor' },
    { title: 'Agendado para', key: 'scheduled_at' },
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

    return props.appointments_data.data.map((appointment, index) => ({
        ...appointment,
        line_number: index + 1,
        animal: appointment.animal?.name ?? 'Sem animal',
        species: appointment.animal?.species,
        doctor: appointment.doctor?.name ?? 'Sem veternário',
        scheduled_at: formatDateTime(appointment.scheduled_at),
        created_at: formatDateTime(appointment.created_at),
        updated_at: formatDateTime(appointment.updated_at),
    }));
});

const handleOpenDoctorDetailPage = (event: Event, row: any) => {
    const appointment = row?.item;
    if(!appointment?.slug) return;
    router.visit(appointments.show(appointment.slug).url)

};

const handleFiltersChange  = () => {
    router.get(
        appointments.list().url,
        {
            animal_type: filterSpecies.value || undefined,
            start_date: filterStartDate.value ? filterStartDate.value.toString() : undefined,
            end_date: filterEndDate.value ? filterEndDate.value.toString() : undefined,
        },
        {
            preserveState: true,
            replace: true
        }
    );
};

const handlePageChange = (page: number) => {
    router.get(
        appointments.list().url,
        { page: page},
        { preserveState: true, replace: true }
    )
};

const handleEditButtonClick = (appointment: any) => {
    router.visit(appointments.edit(appointment.slug).url);
};
</script>

<template>
    <Head title="Veternários" />
    <AppLayout :breadcrumbs="props.breadcrumbs" :title="'Agendamentos'" :description="'Lista de agendamentos'">
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <div class="mb-4 flex items-center justify-between gap-4">
                <div class="flex items-center gap-3 pl-5">
                    <label class="font-medium text-gray-700">Tipo de animal:</label>
                    <select v-model="filterSpecies" @change="handleFiltersChange(filterSpecies)" class="border rounded px-2 py-1">
                        <option value="default" selected>Tipo de animal</option>
                        <option value="">Todos</option>
                        <option v-for="specie in species" :value="specie" :key="specie">{{ specie }}</option>
                    </select>
                </div>
                <div class="flex items-center gap-3 pr-5">
                    <label class="font-medium text-gray-700">Filtrar por datas:</label>
                    <input
                        type="date"
                        v-model="filterStartDate"
                        :max="filterEndDate"
                        @change="handleFiltersChange()"
                        class="border rounded px-2 py-1"
                    >
                    <input
                        type="date"
                        :min="filterStartDate"
                        v-model="filterEndDate"
                        @change="handleFiltersChange ()"
                        class="border rounded px-2 py-1"
                    >
                </div>
            </div>

            <v-data-table-virtual
                :headers="headers"
                :items="data"
                item-value="username"
                fixed-header
                loading-text="A carregar ..."
                :show-select="true"
                class="cursor-pointer"
                @click:row.stop="handleOpenDoctorDetailPage"
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
                            @click.prevent.stop="handleEditButtonClick(item)"
                        >
                            <v-icon size="small">mdi-pencil</v-icon>
                        </v-btn>

                        <v-btn
                            icon
                            size="small"
                            variant="text"
                            color="red"
                            title="Apagar"
                            @click.prevent.stop="showDeleteDialog = true"
                        >
                            <v-icon size="small">mdi-delete</v-icon>
                        </v-btn>
                    </div>
                </template>
            </v-data-table-virtual>
            <div class="mt-6 flex justify-center my-5 text-sm">
                <v-pagination
                    :length="props.appointments_data.last_page"
                    :model-value="props.appointments_data.current_page"
                    :total-visible="5"
                    @update:model-value="handlePageChange"
                    color="indigo-darken-3"
                ></v-pagination>
            </div>
            <v-dialog v-model="showDeleteDialog" max-width="400">
                <v-card>
                    <v-card-title class="text-sm bg-red-600 text-white">Deseja apagar?</v-card-title>
                    <v-card-text class="text-xs">
                        Tem certeza que quer apagar este agendamento?
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn text color="grey" @click="showDeleteDialog = false">Cancelar</v-btn>
                        <v-btn text color="red" @click="deleteAppointment">Apagar</v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
        </div>
    </AppLayout>
</template>
