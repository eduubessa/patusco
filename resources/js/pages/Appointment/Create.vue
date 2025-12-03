<script setup lang="ts">
import { ref, watch, computed } from 'vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import type { Customer } from '@/types/Customer';
import type { Doctor } from '@/types/Doctor';

interface Props {
    customers_data: Customer[];
    breadcrumbs: BreadcrumbItem[];
    doctors: Doctor[];
}

const props = defineProps<Props>();
const user = computed(() => usePage().auth);

const searchCustomers = ref("");
const selectedCustomer = ref<Customer | null>(null);
const remoteCustomers = ref<Customer[]>([]);
const loadingCustomers = ref(false);

let debounceTimer: number | null = null;

console.log(user);

const fetchCustomers = async (query: string) => {
    loadingCustomers.value = true;
    try {
        const response = await fetch(`/api/customers?search=${encodeURIComponent(query)}`);
        const json = await response.json();
        remoteCustomers.value = json.data;
    } catch (error) {
        console.error("fetch customer error:", error);
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
const selectedAnimal = ref(null);

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
        console.error("animals fetch error", error);
    } finally {
        loadingAnimals.value = false;
    }
});

const selectedDoctor = ref<Doctor | null>(null);
const availableDoctors = ref<Doctor[]>([]);

const situation = ref("");
const scheduled_at = ref("");
const status = ref("pending");

const period = ref<string | null>(null);
const hour = ref<string | null>(null);
const date = ref<string | null>(null);

const fetchAvailableDoctors = async () => {
    if (!period.value || !hour.value || !date.value) return;

    selectedDoctor.value = null;

    try {
        const response = await fetch(`/api/doctors/?period=${encodeURIComponent(period.value)}&hour=${encodeURIComponent(hour.value)}&date=${encodeURIComponent(date.value)}`);
        const json = await response.json();
        availableDoctors.value = json.data ?? [];
    } catch (error) {
        console.error("fetch available doctors error", error);
        availableDoctors.value = [];
    }
};

watch([period, hour, date], fetchAvailableDoctors);

const canSubmit = computed(() =>
    selectedCustomer.value &&
    selectedAnimal.value &&
    selectedDoctor.value &&
    period.value &&
    hour.value &&
    date.value &&
    situation.value.trim() !== "" &&
    scheduled_at.value.trim() !== "" &&
    status.value.trim() !== ""
);

const submit = () => {
    if (!canSubmit.value) return;

    router.post("/appointments", {
        animal: selectedAnimal.value!.id,
        doctor: selectedDoctor.value!.id,
        situation: situation.value,
        scheduled_at: `${date.value} ${hour.value}`,
        status: status.value,
    });
};
</script>

<template>
    <Head title="Criar Agendamento" />
    <AppLayout
        :breadcrumbs="props.breadcrumbs"
        title="Novo agendamento"
        description="Criar um novo agendamento">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <v-container max-width="100%" class="py-8 space-y-8">
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
                    />
                </div>
                <div class="form-row">
                    <label class="form-label">Animal</label>
                    <v-select
                        v-model="selectedAnimal"
                        :items="animals"
                        :loading="loadingAnimals"
                        item-title="name"
                        item-value="id"
                        return-object
                        variant="outlined"
                        hide-details="auto"
                        density="comfortable"
                        :disabled="!selectedCustomer"
                        class="form-input"
                    />
                </div>
                <div class="form-row">
                    <label class="form-label">Situação</label>
                    <v-text-field
                        v-model="situation"
                        variant="outlined"
                        density="comfortable"
                        class="form-input"
                    />
                </div>
                <div class="form-row">
                    <label class="form-label">Período</label>
                    <v-select
                        v-model="period"
                        :items="['Manhã', 'Tarde']"
                        variant="outlined"
                        density="comfortable"
                        class="form-input"
                    />
                </div>
                <div class="form-row">
                    <label class="form-label">Hora</label>
                    <v-select
                        v-model="hour"
                        :items="period === 'Manhã' ? ['08:00','09:00','10:00','11:00'] : ['14:00','15:00','16:00','17:00']"
                        variant="outlined"
                        density="comfortable"
                        class="form-input"
                    />
                </div>
                <div class="form-row">
                    <label class="form-label">Data</label>
                    <v-text-field
                        v-model="date"
                        type="date"
                        variant="outlined"
                        density="comfortable"
                        class="form-input"
                    />
                </div>
                <div class="form-row">
                    <label class="form-label">Veterinário</label>
                    <v-select
                        v-model="selectedDoctor"
                        :items="availableDoctors.length ? availableDoctors : props.doctors"
                        item-title="name"
                        item-value="id"
                        return-object
                        variant="outlined"
                        hide-details="auto"
                        density="comfortable"
                        class="form-input"
                        :disabled="!period || !hour || !date"
                    />
                </div>
                <div class="form-row">
                    <label class="form-label">Estado</label>
                    <v-select
                        v-model="status"
                        :items="['pending', 'confirmed', 'cancelled']"
                        variant="outlined"
                        density="comfortable"
                        class="form-input"
                    />
                </div>
                <div class="button-wrapper">
                    <v-btn
                        color="white"
                        variant="outlined"
                        :disabled="!canSubmit"
                        class="!py-3 px-6 text-base"
                        @click="submit"
                    >
                        Criar Agendamento
                    </v-btn>
                </div>
            </v-container>
        </div>
    </AppLayout>
</template>

<style scoped>
.form-row {
    display: flex;
    align-items: center;
    gap: 16px;
    margin-bottom: 20px;
}

.form-label {
    width: 180px;
    font-weight: 600;
    color: #333;
}

.form-input {
    flex: 1;
}

.button-wrapper {
    width: 100%;
    display: flex;
    justify-content: flex-end;
}
</style>
