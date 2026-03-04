<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
// import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard, logout } from '@/routes';
import { type NavItem } from '@/types';
import { Link, router, usePage } from '@inertiajs/vue3';
import { BookOpen, Folder, LayoutGrid, GraduationCap, ClipboardList, Users, Calendar, UserCog, Sliders, BarChart3, Settings, LogOut } from 'lucide-vue-next';
import { computed } from 'vue';

// Get user role from page props
const page = usePage();
const user = computed(() => page.props.auth?.user as { 
    role?: string | { name: string; display_name: string }; 
    name?: string; 
    email?: string;
    role_name?: string;
} | null);

// Compute user initials from name
const userInitials = computed(() => {
    const userName = user.value?.name || 'Guest';
    return userName
        .split(' ')
        .map(n => n[0])
        .join('')
        .toUpperCase()
        .slice(0, 2);
});

// Navigation items for admins
const adminNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
        icon: LayoutGrid,
    },
    // {
    //     title: 'Schedule',
    //     href: '/schedule',
    //     icon: Calendar,
    // },
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
    // {
    //     title: 'Assessment Tool',
    //     href: "/assessment-tool",
    //     icon: BookOpen,
    // },
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
        title: 'Admin Configuration',
        href: "/admin/configuration",
        icon: Settings,
    },
    {
        title: 'Grade Settings',
        href: "/grade-settings",
        icon: Sliders,
    },
    // {
    //     title: 'Grade Reports',
    //     href: "/instructor/report",
    //     icon: Folder,
    // },
];

// Navigation items for instructors
const instructorNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
        icon: LayoutGrid,
    },
    // {
    //     title: 'Schedule',
    //     href: '/schedule',
    //     icon: Calendar,
    // },
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
    // {
    //     title: 'Assessment Tool',
    //     href: "/assessment-tool",
    //     icon: BookOpen,
    // },
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
    // {
    //     title: 'My Schedule',
    //     href: '/schedule',
    //     icon: Calendar,
    // },
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
    // {
    //     title: 'My Assessments',
    //     href: '/student/assessments',
    //     icon: GraduationCap,
    // },
    {
        title: 'Assessment',
        href: '/student/assessment',
        icon: BarChart3,
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

// Logout handler
const handleLogout = (event: Event) => {
    event.preventDefault();
    router.flushAll();
    router.post(logout(), {}, {
        preserveState: false,
        preserveScroll: false,
        replace: true,
        onSuccess: () => {
            window.location.href = '/';
        }
    });
};
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <!-- User Profile Section -->
            <div class="flex items-center gap-2 px-2 py-3 border-b border-sidebar-border group-data-[collapsible=icon]:px-1 group-data-[collapsible=icon]:py-2">
                <!-- User Avatar with Initials - Clickable -->
                <Link href="/settings/profile" class="h-10 w-10 rounded-full bg-gradient-to-br from-blue-500 via-yellow-400 to-gray-600 flex items-center justify-center shadow-md flex-shrink-0 group-data-[collapsible=icon]:h-9 group-data-[collapsible=icon]:w-9 cursor-pointer hover:opacity-90 transition-opacity">
                    <span class="text-white font-bold text-xs group-data-[collapsible=icon]:text-xs">{{ userInitials }}</span>
                </Link>
                
                <!-- User Info (Hidden when collapsed) -->
                <div class="flex-1 min-w-0 group-data-[collapsible=icon]:hidden">
                    <div class="text-sm font-bold text-gray-900 dark:text-white truncate">
                        {{ user?.name || 'Guest' }}
                    </div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 truncate">
                        {{ user?.email || 'No email' }}
                    </div>
                </div>
            </div>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            
            <!-- Logout Button -->
            <div class="px-2 py-3 border-t border-sidebar-border">
                <SidebarMenu>
                    <SidebarMenuItem>
                        <SidebarMenuButton
                            @click="handleLogout"
                            class="w-full justify-center text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg bg-gray-50 dark:bg-gray-900 font-medium py-2"
                            data-test="sidebar-logout-button"
                        >
                            <LogOut class="h-4 w-4" />
                            <span class="group-data-[collapsible=icon]:hidden">Sign out</span>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </div>
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
