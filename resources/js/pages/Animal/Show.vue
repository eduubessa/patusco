<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type Animal } from '@/types/Animal';
import { calculateAge, formatDateTime } from '@/utils/formatters';
import { Clock, Calendar, Stethoscope } from 'lucide-vue-next';


const props = defineProps<{ animal: Animal}>();

const title = 'Detalhes';
const description = 'Toda a informação do animal';

</script>

<template>
    <Head :title="title" />
    <AppLayout :breadcrumbs="props.breadcrumbs" :title="title" :description="description">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">

            <v-container class="bg-gray-100/30">
                <v-row class="gap-3">

                    <v-col cols="12" md="3" class="text-white rounded-lg p-0">
                        <div class="text-lg font-semibold">
                            <div class="flex justify-center">
                                <v-avatar size="100" class="shadow-md border-2 border-gray-300">
                                    <v-img
                                        src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTzENVpJBVboy9oA4nH6SKCUbOV2hkDD5nvgQ&s"
                                        alt="Foto de perfil do animal"
                                        cover
                                    />
                                </v-avatar>
                            </div>

                            <div class="space-y-3 px-5 mt-8">
                                <dl class="space-y-2">

                                    <!-- Nome -->
                                    <div>
                                        <dt class="text-sm font-medium text-gray-400 uppercase mt-2">Nome</dt>
                                        <dd class="text-lg font-semibold text-gray-900">{{ animal.name }}</dd>
                                    </div>

                                    <!-- Idade -->
                                    <div>
                                        <dt class="text-sm font-medium text-gray-400 uppercase mt-3">Idade</dt>
                                        <dd class="text-lg font-semibold text-gray-900">
                                            {{ calculateAge(animal.birthday) }} anos
                                        </dd>
                                    </div>

                                    <!-- Tipo -->
                                    <div>
                                        <dt class="text-sm font-medium text-gray-400 uppercase mt-3">Tipo de Animal</dt>
                                        <dd class="text-lg font-semibold text-gray-900">{{ animal.species }}</dd>
                                    </div>

                                    <!-- Raça -->
                                    <div>
                                        <dt class="text-sm font-medium text-gray-400 uppercase mt-3">Raça</dt>
                                        <dd class="text-lg font-semibold text-gray-900">{{ animal.breed }}</dd>
                                    </div>

                                    <!-- Owners -->
                                    <div v-if="animal.owners?.length > 0">
                                        <dt class="text-sm font-medium text-gray-400 uppercase mt-3">Dono(s)</dt>
                                        <dd class="text-lg font-semibold text-gray-900">
                                          <span v-for="(owner, index) in animal.owners" :key="index">
                                            {{ owner.name }}<span v-if="index !== (animal.owners.length - 1)">, </span>
                                          </span>
                                        </dd>
                                    </div>

                                    <!-- Veterinário habitual -->
                                    <div v-if="animal.doctor">
                                        <dt class="text-sm font-medium text-gray-400 uppercase mt-3">Veterinário habitual</dt>
                                        <dd class="text-lg font-semibold text-gray-900">
                                            {{ animal.doctor.name ?? 'Sem veterinário' }}
                                        </dd>
                                    </div>

                                </dl>
                            </div>
                        </div>
                    </v-col>

                    <!-- Coluna central: Appointments -->
                    <v-col cols="12" md="4" class="p-4 border-l border-gray-200">
                        <div class="text-lg font-semibold">
                            <h2 class="text-lg mb-3">Últimas consultas ({{ animal.appointments?.length ?? 0 }})</h2>
                            <section v-if="animal.appointments?.length > 0" id="animal-appointments">
                                <article
                                    v-for="appointment in animal.appointments"
                                    :key="appointment.slug"
                                    class="border-l-4 border-blue-300 p-4 rounded-md mb-3 cursor-pointer hover:bg-gray-50 py-2 px-3"
                                >
                                    <h3 class="text-md font-medium mb-1">Agendamento de consulta</h3>
                                    <p class="text-sm text-gray-400 mb-2">{{ appointment.situation }}</p>

                                    <div class="flex flex-wrap text-xs text-gray-600 gap-5">

                                        <!-- Data de criação -->
                                        <div class="flex items-center gap-1">
                                            <Calendar size="12"/>
                                            <span>{{ formatDateTime(appointment.created_at) }}</span>
                                        </div>

                                        <!-- Data de atualização -->
                                        <div class="flex items-center gap-1">
                                            <Clock size="12"/>
                                            <span>{{ formatDateTime(appointment.updated_at) }}</span>
                                        </div>

                                        <!-- Veterinário -->
                                        <div class="flex items-center gap-1">
                                            <Stethoscope size="12"/>
                                            <span>{{ appointment.doctor?.name ?? 'Sem veterinário' }}</span>
                                        </div>

                                    </div>
                                </article>
                            </section>
                            <section v-else>
                                <h3 class="text-sm mb-1 text-center mt-2">Não tem consultas agendadas</h3>
                            </section>
                        </div>
                    </v-col>

                    <!-- Coluna direita: placeholder -->
                    <v-col cols="12" md="4" class="p-4 rounded-md border-l border-gray-200">
                        <div class="text-lg font-semibold">C3</div>
                    </v-col>

                </v-row>
            </v-container>
        </div>
    </AppLayout>
</template>

<style scoped>
</style>
