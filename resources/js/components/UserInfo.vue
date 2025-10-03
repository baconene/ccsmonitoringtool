<script setup lang="ts">
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { useInitials } from '@/composables/useInitials';
import type { User } from '@/types';
import { computed } from 'vue';

interface Props {
    user: User;
    showRole?: boolean;
    size?: 'sm' | 'md' | 'lg';
}

const props = withDefaults(defineProps<Props>(), {
    showRole: false,
    size: 'md',
});

const { getInitials } = useInitials();

// Compute whether we should show the avatar image
const showAvatar = computed(
    () => props.user.avatar && props.user.avatar !== '',
);

// Compute avatar size classes
const avatarClasses = computed(() => {
    switch (props.size) {
        case 'sm':
            return 'h-8 w-8';
        case 'lg':
            return 'h-12 w-12'; // More compact for centered layout
        default:
            return 'h-10 w-10';
    }
});

// Compute text size classes
const textClasses = computed(() => {
    switch (props.size) {
        case 'sm':
            return 'text-xs';
        case 'lg':
            return 'text-base';
        default:
            return 'text-sm';
    }
});

// Compute fallback text size
const fallbackTextClasses = computed(() => {
    switch (props.size) {
        case 'sm':
            return 'text-xs';
        case 'lg':
            return 'text-xl font-bold';
        default:
            return 'text-lg font-semibold';
    }
});

// Compute user role with fallback
const userRole = computed(() => {
    if (props.user.role) {
        return props.user.role.toUpperCase();
    }
    // Fallback role determination based on email patterns
    const email = props.user.email?.toLowerCase() || '';
    if (email.includes('admin')) {
        return 'ADMIN';
    } else if (email.includes('student')) {
        return 'STUDENT';
    }
    // Default to instructor for dashboard users
    return 'INSTRUCTOR';
});
</script>

<template>
    <div class="flex flex-col items-center justify-center w-full space-y-2">
        <!-- Centered Avatar -->
        <Avatar :class="`${avatarClasses} overflow-hidden rounded-full border-2 border-sidebar-accent/20 shadow-md`">
            <AvatarImage v-if="showAvatar" :src="user.avatar!" :alt="user.name" class="object-cover" />
            <AvatarFallback :class="`rounded-full text-white bg-gradient-to-br from-blue-500 to-purple-600 ${fallbackTextClasses}`">
                {{ getInitials(user.name) }}
            </AvatarFallback>
        </Avatar>

        <!-- User Details Below Avatar -->
        <div :class="`text-center ${textClasses} leading-tight space-y-1 w-full max-w-[140px]`">
            <div class="font-semibold text-sidebar-foreground truncate px-1">{{ user.name }}</div>
            <div v-if="showRole" class="text-xs text-sidebar-foreground/70 truncate px-1 uppercase font-medium">{{
                userRole
            }}</div>
        </div>
    </div>
</template>
