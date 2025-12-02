<script setup lang="ts">
import { ref, computed } from 'vue';
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
const searchCustomers = ref("");
const selectedCustomers = ref<Customer | null>(null);

const filteredCustomers = computed(() => {
    if (!searchCustomers.value) return customers.value;

    const query = searchCustomers.value.toLowerCase();

    return customers.value.filter(c =>
        c.name.toLowerCase().includes(query) ||
        (c.phone ?? "").includes(searchCustomers.value)
    );
});

const addCustomerButtonClickHandler = () => {
    router.visit(customers.create().url);
};
</script>

<template>
    <Head title="Criar Agendamento" />
    <AppLayout :breadcrumbs="props.breadcrumbs" :title="'Novo agendamento'" :description="'Criar um novo agendamento'">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <v-container max-width="700px" class="py-8">

                <v-autocomplete
                    v-model="selectedCustomers"
                    :items="filteredCustomers"
                    :search="searchCustomers"
                    label="Cliente"
                    placeholder="Pesquisar cliente..."
                    clearable
                    return-object
                    hide-details
                    variant="outlined"
                    density="comfortable"
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

