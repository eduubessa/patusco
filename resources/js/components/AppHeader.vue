<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import {
    Sheet,
    SheetContent,
    SheetHeader,
    SheetTitle,
    SheetTrigger,
} from '@/components/ui/sheet';
import UserMenuContent from '@/components/UserMenuContent.vue';
import { getInitials } from '@/composables/useInitials';
import { toUrl, urlIsActive } from '@/lib/utils';
import { dashboard } from '@/routes';
import type { BreadcrumbItem, NavItem } from '@/types';
import { InertiaLinkProps, Link, usePage } from '@inertiajs/vue3';
import { LayoutGrid, Menu } from 'lucide-vue-next';
import { computed } from 'vue';
import appointments from '@/routes/appointments';
import animals from '@/routes/animals';
import doctors from '@/routes/doctors';

interface Props {
    breadcrumbs?: BreadcrumbItem[];
    title: string;
    description: string;
}

const props = withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
    title: '',
    description: ''
});

const page = usePage();
const auth = computed(() => page.props.auth);

const isCurrentRoute = computed(
    () => (url: NonNullable<InertiaLinkProps['href']>) =>
        urlIsActive(url, page.url),
);

const activeItemStyles = computed(
    () => (url: NonNullable<InertiaLinkProps['href']>) =>
        isCurrentRoute.value(toUrl(url))
            ? 'text-neutral-900 dark:bg-neutral-800 dark:text-neutral-100'
            : '',
);

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: LayoutGrid,
    },
];

const addButtonClickHandler = (url: string) => {
    router.visit(url);
}
</script>

<template>
    <div>
        <div class="border-sidebar-border/80 pa-3 border-b">
            <div class="mx-auto flex h-16 items-center px-4 md:max-w-7xl">
                <!-- Mobile Menu -->
                <div class="lg:hidden">
                    <Sheet>
                        <SheetTrigger :as-child="true">
                            <Button
                                variant="ghost"
                                size="icon"
                                class="mr-2 h-9 w-9"
                            >
                                <Menu class="h-5 w-5" />
                            </Button>
                        </SheetTrigger>
                        <SheetContent side="left" class="w-[300px] p-6">
                            <SheetTitle class="sr-only"
                                >Navigation Menu</SheetTitle
                            >
                            <SheetHeader class="flex justify-start text-left">
                                <AppLogoIcon
                                    class="size-6 fill-current text-black dark:text-white"
                                />
                            </SheetHeader>
                            <div
                                class="flex h-full flex-1 flex-col justify-between space-y-4 py-6"
                            >
                                <nav class="-mx-3 space-y-1">
                                    <Link
                                        v-for="item in mainNavItems"
                                        :key="item.title"
                                        :href="item.href"
                                        class="hover:bg-accent flex items-center gap-x-3 rounded-lg px-3 py-2 text-sm font-medium"
                                        :class="activeItemStyles(item.href)"
                                    >
                                        <component
                                            v-if="item.icon"
                                            :is="item.icon"
                                            class="h-5 w-5"
                                        />
                                        {{ item.title }}
                                    </Link>
                                </nav>
                            </div>
                        </SheetContent>
                    </Sheet>
                </div>
                <div class="ml-auto flex items-center space-x-2">
                    <div class="relative flex items-center space-x-1">
                        <Button
                            variant="ghost"
                            size="icon"
                            class="group h-9 w-9 cursor-pointer"
                        >
                        </Button>
                    </div>

                    <DropdownMenu>
                        <DropdownMenuTrigger :as-child="true">
                            <Button
                                variant="ghost"
                                size="icon"
                                class="focus-within:ring-primary relative size-10 w-auto rounded-full p-1 focus-within:ring-2"
                            >
                                <Avatar
                                    class="size-8 overflow-hidden rounded-full"
                                >
                                    <AvatarImage
                                        v-if="auth.user.avatar"
                                        :src="auth.user.avatar"
                                        :alt="auth.user.name"
                                    />
                                    <AvatarFallback
                                        class="rounded-lg bg-neutral-200 font-semibold text-black dark:bg-neutral-700 dark:text-white"
                                    >
                                        {{ getInitials(auth.user?.name) }}
                                    </AvatarFallback>
                                </Avatar>
                            </Button>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent align="end" class="w-56">
                            <UserMenuContent :user="auth.user" />
                        </DropdownMenuContent>
                    </DropdownMenu>
                </div>
            </div>
        </div>

        <div
            v-if="props.breadcrumbs.length > 1"
            class="border-sidebar-border/70 flex w-full border-b"
        >
            <div
                class="mx-auto flex h-12 w-full items-center justify-start px-4 text-neutral-500 md:max-w-7xl"
            >
                <Breadcrumbs :breadcrumbs="breadcrumbs" />
            </div>
        </div>
        <div class="ma-5 border-b-thin flex items-center justify-between pb-10">
            <div>
                <h1 class="text-3xl text-gray-900">{{ title }}</h1>
                <p class="text-bold text-gray-400">{{ description }}</p>
            </div>
            <div>
                <v-menu offset-y>
                    <template #activator="{ props }">
                        <v-btn
                            v-bind="props"
                            color=""
                            dark
                        >
                            Adicionar
                            <v-icon right>mdi-menu-down</v-icon>
                        </v-btn>
                    </template>

                    <v-list>
                        <v-list-item @click.prevent.stop="addButtonClickHandler(appointments.create().url)">
                            <v-list-item-title>Adicionar marcação</v-list-item-title>
                        </v-list-item>
                        <v-list-item @click.prevent.stop="addButtonClickHandler(animals.create().url)">
                            <v-list-item-title>Adicionar animal</v-list-item-title>
                        </v-list-item>
                        <v-list-item @click.prevent.stop="addButtonClickHandler(doctors.create().url)">
                            <v-list-item-title>Adicionar médico</v-list-item-title>
                        </v-list-item>
                    </v-list>
                </v-menu>
            </div>
        </div>
    </div>
</template>
