<script setup lang="ts">
import NavMain from '@/components/NavMain.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import {
    CalendarClock,
    Home,
    PawPrint,
    Stethoscope,
    Users,
} from 'lucide-vue-next';
import AppLogo from '@/components/AppLogo.vue';

import { dashboard } from '@/routes';
import animals from '@/routes/animals';
import customers from '@/routes/customers';
import appointments from '@/routes/appointments';
import doctors from '@/routes/doctors';

const mainNavItems: NavItem[] = [
    {
        title: 'Painel de Controlo',
        href: dashboard(),
        icon: Home,
    },
    {
        title: 'Agendamentos',
        href: appointments.list(),
        icon: CalendarClock,
    },
    {
        title: 'Utentes',
        href: customers.list(),
        icon: Users,
    },
    {
        title: 'Animais',
        href: animals.list(),
        icon: PawPrint,
    },
    {
        title: 'Médicos',
        href: doctors.list(),
        icon: Stethoscope,
    },
];
</script>
<template>
    <Sidebar
        collapsible="icon"
        variant="inset"
        class="fixed top-0 left-0 z-10 h-full w-20 border-r-1 border-gray-200"
    >
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent class="align-center flex justify-center">
            <NavMain :items="mainNavItems" />
        </SidebarContent>
    </Sidebar>
    <slot />
</template>
