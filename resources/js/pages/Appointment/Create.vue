<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { ref, computed, watch, reactive } from 'vue';
import { Head, router, usePage } from '@inertiajs/vue3';

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
const errors = reactive({ ...usePage().props.errors });

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

    console.log('Submitting appointment', {
        customer: isCustomer.value ? user.value.username : selectedCustomer.value.username,
        animal: selectedAnimal.value.slug,
        doctor: isCustomer.value ? undefined : selectedDoctor.value.username,
        situation: situation.value,
        scheduled_at: date.value,
        status: status.value,
    });

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
            console.log(`Submit success`);
        },
            onError: () => {
                console.log(`Submit error`);
                console.log(errors);
        }
    });
    } catch (err) {
        console.error('Erro ao criar agendamento', err);
    }
};

watch(() => usePage().props.errors,
    (val) => Object.assign(errors, val)
);
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
                    :validation="errors.customer"
                />

                <AnimalSelector
                    v-model="selectedAnimal"
                    :customer="selectedCustomer"
                    :validation="errors.animal"
                />

                <DateTimeSelector
                    v-model:period="period"
                    v-model:hour="hour"
                    v-model:date="date"
                    :validation="errors.scheduled_at"
                />

                <DoctorSelector
                    v-if="!isCustomer"
                    v-model="selectedDoctor"
                    :period="period"
                    :hour="hour"
                    :date="date"
                    :staticDoctors="props.doctors ?? []"
                    :validation="errors.doctor"
                />

                <StatusSelector v-if="!isCustomer" v-model="status" :validation="errors.status" />

                <SituationInput v-model="situation" :validation="errors.situation" />

                <v-row>
                    <v-col class="text-right">
                        <v-btn
                            @click="submit"
                            :disabled="!canSubmit"
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
