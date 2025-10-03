<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import { BookOpen, Folder, LayoutGrid, GraduationCap, ClipboardList } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
        icon: LayoutGrid,
    },
    {
        title: 'Course Management',
        href: "/course-management",
        icon: GraduationCap, // Use a graduation cap for course management
    },
    {
        title: 'Assignment',
        href: "/assignment",
        icon: ClipboardList, // Use a clipboard list for assignments
    }, 
    {
        title: 'Assessment Tool',
        href: "/assessment-tool",
        icon: BookOpen,
    },
    {
        title: 'Report',
        href: "/report",
        icon: Folder,
    },
    
];

const footerNavItems: NavItem[] = [
 
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <!-- Logo at the very top -->
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="'/'">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
            
            <!-- User details below logo -->
            <div class="px-3 py-6 border-b border-sidebar-border bg-sidebar-accent/5">
                <NavUser />
            </div>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
