<script setup lang="ts">
import { computed, ref, watch } from 'vue';
import { useDebounceFn }  from '@vueuse/core';
import type { Animal } from '@/types/Animal';
import type { Customer } from '@/types/Customer';

interface Props {}

const props = defineProps<Props>();

const search = ref('');
const selectedCustomer = ref<Customer | null>(null);
const customers = ref([]);

const fetchCustomers = useDebounceFn(async () => {
    if(!search.value){
        customers.value = [];
        return;
    }

    const response = await fetch(`/api/customers/?search=${encodeURIComponent(search.value)}`);
    const data = await response.json();

    customers.value = Array.isArray(data) ? data : data.data ?? [];

}, 300);

watch(search, fetchCustomers);

const animals = ref<Animal[]>([]);
const selectedAnimal = ref<Animal | null>(null);
const loadingAnimals = ref(false);

watch(selectedCustomer, async (customer) => {
    selectedAnimal.value = null;
    animals.value = [];

    if(!customer) return;

    loadingAnimals.value = true;

    try {
        const response = await fetch(`/customers/${customer.id}`);
        animals.value = response.data.animals;
    }finally{
        loadingAnimals.value = false;
    }
});
</script>

<template>
    <div class="space-y-4">
        <!-- Autocomplete de Clientes -->
        <v-autocomplete
            v-model="selectedCustomer"
            :items="customers"
            item-title="name"
            item-value="id"
            label="Pesquisar cliente"
            placeholder="Digite o nome ou telefone..."
            clearable
            hide-details
            :search="search"
            @update:search="search = $event">
            <template #no-data>
                <div class="p-5 text-gray-600 text-center">
                    Nenhum cliente encontrado.
                </div>
            </template>
        </v-autocomplete>

        <v-autocomplete
            class="mt-5"
            v-if="selectedCustomer"
            v-model="selectedAnimal"
            :items="animals"
            item-title="name"
            item-value="id"
            label="Animal"
            placeholder="Selecione um animal..."
            return-object
            :loading="loadingAnimals"
            hide-details
        >
            <template #no-data>
                <div class="p-3 text-gray-600 text-center" v-if="!loadingAnimals">
                    Nenhum animal encontrado para este cliente.
                </div>
            </template>
        </v-autocomplete>
    </div>
</template>
<style scoped></style>
