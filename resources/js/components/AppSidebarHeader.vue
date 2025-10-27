<script setup lang="ts">
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import NotificationBell from '@/components/NotificationBell.vue';
import { SidebarTrigger } from '@/components/ui/sidebar';
import type { BreadcrumbItemType } from '@/types';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

withDefaults(
    defineProps<{
        breadcrumbs?: BreadcrumbItemType[];
    }>(),
    {
        breadcrumbs: () => [],
    },
);

// Get user role to show notification bell only for instructors
const page = usePage();
const auth = computed(() => page.props.auth);

const isInstructor = computed(() => {
    const user = auth.value?.user;
    if (!user?.role) return false;
    
    // Handle both string and object role types
    if (typeof user.role === 'string') {
        return user.role === 'instructor';
    }
    return user.role.name === 'instructor';
});
</script>

<template>
    <header
        class="flex h-16 shrink-0 items-center gap-2 border-b border-sidebar-border/70 px-6 transition-[width,height] ease-linear group-has-data-[collapsible=icon]/sidebar-wrapper:h-12 md:px-4"
    >
        <div class="flex items-center gap-2 flex-1">
            <SidebarTrigger class="-ml-1" />
            <template v-if="breadcrumbs && breadcrumbs.length > 0">
                <Breadcrumbs :breadcrumbs="breadcrumbs" />
            </template>
        </div>

        <!-- Notification Bell (Top Right, Instructors Only) -->
        <div v-if="isInstructor" class="flex items-center">
            <NotificationBell />
        </div>
    </header>
</template>
