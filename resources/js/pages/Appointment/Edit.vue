<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import appointments from '@/routes/appointments';
import type { BreadcrumbItem } from '@/types';
import type { Customer } from '@/types/Customer';
import type { Doctor } from '@/types/Doctor';
import type { Appointment } from '@/types/Appointment'
import { Head, router, usePage, Form } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';

interface Props {
    appointment_data: Appointment,
    customers_data: Customer[];
    breadcrumbs: BreadcrumbItem[];
    doctors: Doctor[];
}

const props = defineProps<Props>();
const user = computed(() => usePage().auth);

const selectedCustomer = ref<Customer | null>(props.appointment_data.customer);
const selectedAnimal = ref(props.appointment_data.animal);
const selectedDoctor = ref<Doctor | null>(props.appointment_data.doctor);

const situation = ref(props.appointment_data.situation);
const status = ref(props.appointment_data.status);

const period = ref<string | null>(props.appointment_data.period);
const hour = ref<string | null>(props.appointment_data.hour);
const date = ref<string | null>(props.appointment_data.date);

const searchCustomers = ref('');
const remoteCustomers = ref<Customer[]>([]);
const loadingCustomers = ref(false);

let debounceTimer: number | null = null;

const fetchCustomers = async (query: string) => {
    loadingCustomers.value = true;
    try {
        const response = await fetch(
            `/api/customers?search=${encodeURIComponent(query)}`,
        );
        const json = await response.json();
        remoteCustomers.value = json.data;
    } catch (error) {
        console.error('fetch customer error:', error);
    } finally {
        loadingCustomers.value = false;
    }
};

watch(searchCustomers, (value) => {
    if (debounceTimer) clearTimeout(debounceTimer);
    debounceTimer = window.setTimeout(() => fetchCustomers(value), 300);
});

const animals = ref([]);
const loadingAnimals = ref(false);

watch(selectedCustomer, async (customer) => {
    if (!customer) {
        animals.value = [];
        selectedAnimal.value = null;
        return;
    }

    loadingAnimals.value = true;
    try {
        const response = await fetch(`/api/customers/${customer.username}`);
        const json = await response.json();
        animals.value = json.data.animals ?? [];
    } catch (error) {
        console.error('animals fetch error', error);
    } finally {
        loadingAnimals.value = false;
    }
}, { immediate: true });

const availableDoctors = ref<Doctor[]>([]);

const fetchAvailableDoctors = async () => {
    if (!period.value || !hour.value || !date.value) return;

    try {
        const response = await fetch(
            `/api/doctors/?period=${encodeURIComponent(period.value)}&hour=${encodeURIComponent(hour.value)}&date=${encodeURIComponent(date.value)}`,
        );
        const json = await response.json();
        availableDoctors.value = json.data ?? [];
    } catch (error) {
        console.error('fetch available doctors error', error);
        availableDoctors.value = [];
    }
};

watch([period, hour, date], fetchAvailableDoctors);

const canSubmit = computed(
    () =>
        selectedCustomer.value &&
        selectedAnimal.value &&
        selectedDoctor.value &&
        period.value &&
        hour.value &&
        date.value &&
        situation.value.trim() !== '' &&
        status.value.trim() !== '',
);

const submit = () => {
    if (!canSubmit.value) return;

    router.put(appointments.update(props.appointment_data.id).url, {
        animal: selectedAnimal.value.slug,
        doctor: selectedDoctor.value.username,
        situation: situation.value,
        scheduled_at: `${date.value} ${hour.value}`,
        status: status.value,
    });
};
</script>
<template>
    <Head title="Editar Agendamento" />
    <AppLayout
        :breadcrumbs="props.breadcrumbs"
        title="Editar agendamento"
        description="Alterar dados do agendamento"
    >
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <Form
                id="appointment-edit-form"
                :action="appointments.update(props.appointment_data.id).url"
                method="put"
                #default="{
                    errors,
                    wasSuccessful,
                    submit,
                }"
            >
                <div v-if="wasSuccessful">Agendamento atualizado com sucesso!</div>

                <v-container max-width="100%" class="space-y-8 py-8">
                    <!-- Cliente -->
                    <div class="form-row">
                        <label class="form-label">Cliente</label>
                        <v-autocomplete
                            v-model="selectedCustomer"
                            v-model:search="searchCustomers"
                            :items="remoteCustomers"
                            :loading="loadingCustomers"
                            item-title="name"
                            item-value="username"
                            return-object
                            variant="outlined"
                            hide-details="auto"
                            density="comfortable"
                            class="form-input"
                            :error="errors.customer != null"
                            :error-messages="errors.customer"
                            name="customer"
                            :rules="[v => !!v || 'Selecione um cliente']"
                        />
                    </div>

                    <!-- Animal -->
                    <div class="form-row">
                        <label class="form-label">Animal</label>
                        <v-select
                            v-model="selectedAnimal"
                            :items="animals"
                            :loading="loadingAnimals"
                            item-title="name"
                            item-value="slug"
                            return-object
                            variant="outlined"
                            hide-details="auto"
                            density="comfortable"
                            :disabled="!selectedCustomer"
                            class="form-input"
                            name="animal"
                            :error="errors.animal != null"
                            :error-messages="errors.animal"
                            :rules="[v => !!v || 'Selecione um animal']"
                        />
                    </div>

                    <!-- Situação -->
                    <div class="form-row">
                        <label class="form-label">Situação</label>
                        <v-text-field
                            v-model="situation"
                            variant="outlined"
                            density="comfortable"
                            class="form-input"
                            :error="errors.situation != null"
                            :error-messages="errors.situation"
                        />
                    </div>

                    <!-- Período -->
                    <div class="form-row">
                        <label class="form-label">Período</label>
                        <v-select
                            v-model="period"
                            :items="['Manhã', 'Tarde']"
                            variant="outlined"
                            density="comfortable"
                            class="form-input"
                            :error="errors.period != null"
                            :error-messages="errors.period"
                            name="period"
                        />
                    </div>

                    <!-- Hora -->
                    <div class="form-row">
                        <label class="form-label">Hora</label>
                        <v-select
                            v-model="hour"
                            :items="
                                period === 'Manhã'
                                    ? ['08:00', '09:00', '10:00', '11:00']
                                    : ['14:00', '15:00', '16:00', '17:00']
                            "
                            variant="outlined"
                            density="comfortable"
                            class="form-input"
                            :error="errors.hour != null"
                            :error-messages="errors.hour"
                            name="hour"
                        />
                    </div>

                    <!-- Data -->
                    <div class="form-row">
                        <label class="form-label">Data</label>
                        <v-text-field
                            v-model="date"
                            type="date"
                            variant="outlined"
                            density="comfortable"
                            class="form-input"
                            :error="errors.date != null"
                            :error-messages="errors.date"
                            name="date"
                        />
                    </div>

                    <!-- Veterinário -->
                    <div class="form-row">
                        <label class="form-label">Veterinário</label>
                        <v-select
                            v-model="selectedDoctor"
                            :items="
                                availableDoctors.length
                                    ? availableDoctors
                                    : props.doctors
                            "
                            item-title="name"
                            item-value="username"
                            return-object
                            variant="outlined"
                            hide-details="auto"
                            density="comfortable"
                            class="form-input"
                            :disabled="!period || !hour || !date"
                            :error="errors.doctor != null"
                            :error-messages="errors.doctor"
                            name="doctor"
                            :rules="[v => !!v || 'Selecione um veterinário']"
                        />
                    </div>

                    <!-- Estado -->
                    <div class="form-row">
                        <label class="form-label">Estado</label>
                        <v-select
                            v-model="status"
                            :items="['pending', 'confirmed', 'cancelled']"
                            variant="outlined"
                            density="comfortable"
                            class="form-input"
                            :error="errors.status != null"
                            :error-messages="errors.status"
                            name="status"
                        />
                    </div>

                    <!-- Botão -->
                    <div class="button-wrapper">
                        <v-btn
                            color="black"
                            variant="outlined"
                            class="px-6 !py-3 text-base"
                            @click.prevent="submit"
                        >
                            Guardar Alterações
                        </v-btn>
                    </div>
                </v-container>
            </Form>
        </div>
    </AppLayout>
</template>
