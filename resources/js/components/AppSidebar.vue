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
import { Link, usePage } from '@inertiajs/vue3';
import { BookOpen, Folder, LayoutGrid, GraduationCap, ClipboardList, Users, Calendar, UserCog, Sliders } from 'lucide-vue-next';
import { computed } from 'vue';
import AppLogo from './AppLogo.vue';

// Get user role from page props
const page = usePage();
const user = computed(() => page.props.auth?.user as { 
    role?: string | { name: string; display_name: string }; 
    name?: string; 
    email?: string;
    role_name?: string;
} | null);

// Navigation items for admins
const adminNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
        icon: LayoutGrid,
    },
    {
        title: 'Schedule',
        href: '/schedule',
        icon: Calendar,
    },
    {
        title: 'Course Management',
        href: "/course-management",
        icon: GraduationCap,
    },
    {
        title: 'Student Management',
        href: "/student-management",
        icon: UserCog,
    },
    {
        title: 'Assessment Tool',
        href: "/assessment-tool",
        icon: BookOpen,
    },
    {
        title: 'Activities',
        href: "/activity-management",
        icon: ClipboardList,
    },
    {
        title: 'Role Management',
        href: "/role-management",
        icon: Users,
    }, 
    {
        title: 'Grade Settings',
        href: "/grade-settings",
        icon: Sliders,
    },
    {
        title: 'Grade Reports',
        href: "/instructor/report",
        icon: Folder,
    },
];

// Navigation items for instructors
const instructorNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
        icon: LayoutGrid,
    },
    {
        title: 'Schedule',
        href: '/schedule',
        icon: Calendar,
    },
    {
        title: 'Course Management',
        href: "/course-management",
        icon: GraduationCap,
    },
    {
        title: 'Student Management',
        href: "/student-management",
        icon: UserCog,
    },
    {
        title: 'Activities',
        href: "/activity-management",
        icon: ClipboardList,
    }, 
    {
        title: 'Assessment Tool',
        href: "/assessment-tool",
        icon: BookOpen,
    },
    {
        title: 'Grade Settings',
        href: "/grade-settings",
        icon: Sliders,
    },
    {
        title: 'Grade Reports',
        href: "/instructor/report",
        icon: Folder,
    },
];

// Navigation items for students
const studentNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
        icon: LayoutGrid,
    },
    {
        title: 'My Schedule',
        href: '/schedule',
        icon: Calendar,
    },
    {
        title: 'My Courses',
        href: '/student/courses',
        icon: BookOpen,
    },
    {
        title: 'My Activities',
        href: '/student/activities',
        icon: ClipboardList,
    },  
    {
        title: 'My Assessments',
        href: '/student/assessments',
        icon: GraduationCap,
    },
    {
        title: 'Grade Report',
        href: '/student/report',
        icon: Folder,
    },
];

// Computed navigation items based on user role
const mainNavItems = computed((): NavItem[] => {
    const currentUser = user.value;
    
    // Check both old role field and new role relationship
    let userRole: string;
    
    if (typeof currentUser?.role === 'object' && currentUser?.role?.name) {
        userRole = currentUser.role.name;
    } else if (typeof currentUser?.role === 'string') {
        userRole = currentUser.role;
    } else if (currentUser?.role_name) {
        userRole = currentUser.role_name;
    } else {
        userRole = 'instructor'; // default
    }
    
    if (userRole === 'student') {
        return studentNavItems;
    } else if (userRole === 'admin') {
        return adminNavItems;
    }
    
    // Default to instructor navigation
    return instructorNavItems;
});

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
