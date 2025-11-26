<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';

const title = "Detalhes";
const description = "Toda a informação do animal"

import { type Animal } from '@/types/Animal';
import { calculateAge } from '@/utils/formatters';
import { formatDate } from '@vueuse/shared';
import { formatDateTime } from '@/utils/formatters';

interface Props {
    animal: Animal
}

const props = defineProps<Props>()
</script>

<template>
    <Head title="Dashboard" />
    <AppLayout :breadcrumbs="breadcrumbs" :title="title" :description="description">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <v-container>
                <v-row class="ga-3">
                    <v-col cols="10" md="3" class="text-white rounded-lg pa-0">
                        <div class="text-lg font-semibold ">
                            <div class="flex items-center position-relative bottom-5 pa-8 bg-green rounded-lg">
                                <v-avatar size="100" class="shadow-md border-2 border-gray-300 mb-2 position-relative top-20">
                                    <v-img
                                        src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTzENVpJBVboy9oA4nH6SKCUbOV2hkDD5nvgQ&s"
                                        alt="Foto de perfil do animal"
                                        cover
                                    ></v-img>
                                </v-avatar>
                            </div>

                            <div class="space-y-3 pa-5 mt-5">
                                <dl class="space-y-2">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-400 text-uppercase mt-2">
                                            Nome
                                        </dt>
                                        <dd class="text-lg font-semibold text-gray-900">
                                            {{ animal.name }}
                                        </dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-400 text-uppercase mt-3">
                                            Idade
                                        </dt>
                                        <dd class="text-lg font-semibold text-gray-900">
                                            {{ calculateAge(animal.birthday) }} anos
                                        </dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-400 text-uppercase mt-3">
                                            Tipo de Animal
                                        </dt>
                                        <dd class="text-lg font-semibold text-gray-900">
                                            {{ animal.species }}
                                        </dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-400 text-uppercase mt-3">
                                            Raça
                                        </dt>
                                        <dd class="text-lg font-semibold text-gray-900">
                                            {{ animal.breed }}
                                        </dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-400 text-uppercase mt-3">
                                            Dono(s)
                                        </dt>
                                        <dd v-if="animal.owners"  class="text-lg font-semibold text-gray-900">
                                              <span v-for="(onwer, index) in animal.owners" :key="index">
                                                  {{ onwer.name }}
                                                  <span v-if="index !== (animal.owners.length-1)">, </span>
                                              </span>

                                        </dd>
                                        <dd v-else  class="text-lg font-semibold text-gray-900">
                                           Sem donos
                                        </dd>

                                        <dt class="text-sm font-medium text-gray-400 text-uppercase mt-3">
                                            Veternário habitual
                                        </dt>
                                        <dd class="text-lg font-semibold text-gray-900">
                                            {{ animal.doctor.name }}
                                        </dd>
                                    </div>
                                </dl>
                            </div>
                        </div>
                    </v-col>
                    <v-col
                        cols="10"
                        md="4"
                        class="p-4 rounded-md border-s-thin"
                    >
                        <div class="text-lg font-semibold">
                            <h2 class="text-lg">Últimas consultas</h2>
                            <article class="border-thin pa-4 rounded-md my-3 bg-blue-100/45" v-for="appointment in animal.appointments" :key="appointment.id">
                                <h3 class="text-md font-weight-bold">Consulta em {{ formatDateTime(appointment.scheduled_at)}}</h3>
                                <p class="text-sm text-weight-normal">
                                    {{ appointment.situation}}
                                </p>
                                <p class="text-sm text-weight-normal mt-3 text-right">
                                    <strong>Atendido por:</strong> {{ appointment.doctor.name }}
                                </p>
                            </article>
                        </div>
                    </v-col>
                    <v-col
                        cols="10"
                        md="4"
                        class="p-4 rounded-md border-s-thin"
                    >
                        <div class="text-lg font-semibold">C3</div>
                    </v-col>
                </v-row>
            </v-container>
        </div>
    </AppLayout>
</template>

<style scoped>

</style>
