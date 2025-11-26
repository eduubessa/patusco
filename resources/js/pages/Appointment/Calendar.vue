<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Agendamentos', href: '/appointments' },
];

const title = 'Calendário';
const description = "Todos os eventos";

const mode = ref<'stack' | 'column'>('stack');

const weekday = ref<number[]>([0, 1, 2, 3, 4, 5, 6]);

const value = ref<string>('');
const events = ref<Array<{
    name: string;
    start: Date;
    end: Date;
    color?: string;
    timed?: boolean;
}>>([]);

const colors = ['blue', 'indigo', 'deep-purple', 'cyan', 'green', 'orange', 'grey-darken-1'];
const page = usePage<{ events?: Array<any> }>();

onMounted(() => {
    if (page.props.value.events) {
        events.value = page.props.value.events.map(evt => ({
            ...evt,
            start: new Date(evt.start),
            end: new Date(evt.end),
            color: evt.color || colors[Math.floor(Math.random() * colors.length)],
            timed: evt.timed ?? true,
        }));
    }
});

function getEventColor(event: { color?: string }) {
    return event.color;
}
</script>

<template>
    <Head title="Calendário" />
    <AppLayout :breadcrumbs="breadcrumbs" :title="title" :description="description">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <v-sheet height="700">
                <v-calendar
                    ref="calendar"
                    v-model="value"
                    :type="'week'"
                    :weekdays="weekday"
                    :events="events"
                    :event-color="getEventColor"
                    :event-overlap-mode="mode"
                    :event-overlap-threshold="30"
                />
            </v-sheet>
        </div>
    </AppLayout>
</template>
