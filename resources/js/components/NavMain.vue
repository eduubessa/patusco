<script setup lang="ts">
import {
    SidebarGroup,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { urlIsActive } from '@/lib/utils';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';

defineProps<{
    items: NavItem[];
}>();

const page = usePage();
</script>

<template>
    <SidebarGroup class="px-2 py-0">
        <SidebarMenu>
            <SidebarMenuItem v-for="item in items" :key="item.title">
                <SidebarMenuButton
                    as-child
                    :is-active="urlIsActive(item.href, page.url)"
                    :tooltip="item.title"
                    class="w-full justify-center"
                >
                <Link
                    :href="item.href"
                    class="!w-12 !h-12 flex items-center justify-center pa-0 my-2 text-[#9fa7b2 bg-blue-300 rounded-full mx-auto"
                >
                    <component
                        :is="item.icon"
                        :size="30"
                        class="!w-6 !h-6 hover:bg-red-500"
                    />
                </Link>
                </SidebarMenuButton>
            </SidebarMenuItem>
        </SidebarMenu>
    </SidebarGroup>
</template>
