<script setup lang="ts">
import { ref, watch } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import type { Customer } from '@/types/Customer';
import customers from "@/routes/customers";

interface Props {
    customers_data: Customer[],
    breadcrumbs: BreadcrumbItem[]
}

const props = defineProps<Props>();

// 🔍 Texto digitado no autocomplete
const searchCustomers = ref("");
const selectedCustomers = ref<Customer | null>(null);
const remoteCustomers = ref<Customer[]>([]);
const loading = ref(false);

// ⏳ Debounce timer
let debounceTimer: number | null = null;

const fetchCustomers = async (query: string) => {
    loading.value = true;

    try {
        const response = await fetch(`/api/customers?search=${encodeURIComponent(query)}`);
        const json = await response.json();

        remoteCustomers.value = json.data;
    } catch (error) {
        console.error("Erro ao buscar clientes:", error);
    } finally {
        loading.value = false;
    }
};

watch(searchCustomers, (value) => {
    if (debounceTimer) clearTimeout(debounceTimer);

    debounceTimer = window.setTimeout(() => {
        fetchCustomers(value);
    }, 300);
});

const addCustomerButtonClickHandler = () => {
    router.visit(customers.create().url);
};
</script>
<template>
    <Head title="Criar Agendamento" />
    <AppLayout
        :breadcrumbs="props.breadcrumbs"
        title="Novo agendamento"
        description="Criar um novo agendamento"
    >
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <v-container max-width="700px" class="py-8">

                <v-autocomplete
                    v-model="selectedCustomers"
                    :items="remoteCustomers"
                    item-title="name"
                    item-value="id"
                    label="Cliente"
                    placeholder="Pesquisar cliente..."
                    clearable
                    return-object
                    hide-details
                    variant="outlined"
                    density="comfortable"
                    :loading="loading"
                    @update:search="searchCustomers = $event"
                >
                    <template #no-data>
                        <div class="p-3 text-gray-600">
                            Nenhum cliente encontrado.
                            <v-btn
                                class="ml-2"
                                size="small"
                                variant="tonal"
                                color="primary"
                                @click="addCustomerButtonClickHandler"
                            >
                                Criar Cliente
                            </v-btn>
                        </div>
                    </template>
                </v-autocomplete>

            </v-container>
        </div>
    </AppLayout>
</template>
