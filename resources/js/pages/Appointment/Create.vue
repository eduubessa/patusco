<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import format from 'date-fns/format';

import CustomerSelector from '@/components/CustomerSelector.vue';
import AnimalSelector from '@/components/AnimalSelector.vue';
import SituationInput from '@/components/SituationInput.vue';
import DoctorSelector from '@/components/DoctorSelector.vue';
import StatusSelector from '@/components/StatusSelector.vue';
import DateTimeSelector from '@/components/DateTimeSelector.vue';

interface Props {
    customers_data: any[];
    breadcrumbs: any[];
    doctors: any[];
}

const props = defineProps<Props>();

const user = computed(() => usePage().props.auth.user);
const isCustomer = computed(() => user.value.role === 'customer');

const selectedCustomer = ref(isCustomer.value ? user.value : null);
const selectedAnimal = ref(null);
const situation = ref('');

const period = ref(null);
const hour = ref(null);
const date = ref(null);

const selectedDoctor = ref(null);
const status = ref('pending');

const canSubmit = computed(() =>
    selectedCustomer.value &&
    selectedAnimal.value &&
    (isCustomer.value ? true : selectedDoctor.value) &&
    period.value &&
    hour.value &&
    date.value &&
    situation.value
);

const submit = async () => {
    if (!canSubmit.value) return;

    console.log(date.value);
    debugger;

    try {
        await router.post('/appointments', {
            customer: isCustomer.value ? user.value.username : selectedCustomer.value.username,
            animal: selectedAnimal.value.slug,
            doctor: isCustomer.value ? undefined : selectedDoctor.value.username,
            situation: situation.value,
            scheduled_at: date.value,
            status: status.value,
        }, {
            onSuccess: () => {
                // ex: limpar form ou redirecionar
            },
            onError: (errors) => {
                // ex: mostrar mensagens de erro no UI
                console.log(errors);
            }
        });
    } catch (err) {
        console.error('Erro ao criar agendamento', err);
    }
};
</script>

<template>
    <Head title="Criar Agendamento" />

    <AppLayout
        :breadcrumbs="props.breadcrumbs"
        title="Novo agendamento"
        description="Criar um novo agendamento"
    >
        <div class="flex h-full flex-1 flex-col rounded-xl p-4">
            <v-container class="space-y-8 py-8">

                <CustomerSelector
                    v-if="!isCustomer"
                    v-model="selectedCustomer"
                />

                <AnimalSelector
                    v-model="selectedAnimal"
                    :customer="selectedCustomer"
                />

                <DateTimeSelector
                    v-model:period="period"
                    v-model:hour="hour"
                    v-model:date="date"
                />

                <DoctorSelector
                    v-if="!isCustomer"
                    v-model="selectedDoctor"
                    :period="period"
                    :hour="hour"
                    :date="date"
                    :staticDoctors="props.doctors ?? []"
                />

                <StatusSelector v-model="status" />

                <SituationInput v-model="situation" />

                <v-row>
                    <v-col class="text-right">
                        <v-btn
                            :disabled="!canSubmit"
                            color="primary"
                            @click="submit"
                            :loading="isSubmitting"
                        >
                            Criar Agendamento
                        </v-btn>
                    </v-col>
                </v-row>

                <v-row>
                    <v-col class="text-right">
                        <v-btn
                            :disabled="!canSubmit"
                            color="primary"
                            @click="submit"
                            :loading="isSubmitting"
                        >
                            Criar Agendamento
                        </v-btn>
                    </v-col>
                </v-row>
            </v-container>
        </div>
    </AppLayout>
</template>
