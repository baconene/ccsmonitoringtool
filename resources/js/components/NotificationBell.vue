<script setup lang="ts">
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Badge } from '@/components/ui/badge';
import { ScrollArea } from '@/components/ui/scroll-area';
import { Bell, Check, CheckCheck, Trash2, Clock } from 'lucide-vue-next';
import { ref, onMounted, onUnmounted, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import axios from 'axios';

interface Notification {
    id: number;
    type: string;
    title: string;
    message: string;
    data: {
        student_id?: number;
        assignment_id?: number;
        activity_id?: number;
        course_id?: number;
        requires_grading?: boolean;
    };
    is_read: boolean;
    read_at: string | null;
    related_type: string | null;
    related_id: number | null;
    created_at: string;
}

interface NotificationResponse {
    data: Notification[];
    current_page: number;
    last_page: number;
    total: number;
}

const unreadCount = ref(0);
const notifications = ref<Notification[]>([]);
const isLoading = ref(false);
const isOpen = ref(false);
let pollInterval: number | null = null;

// Fetch unread count
const fetchUnreadCount = async () => {
    try {
        const response = await axios.get('/instructor/notifications/unread-count', {
            headers: {
                'Accept': 'application/json'
            }
        });
        unreadCount.value = response.data.count;
    } catch (error) {
        console.error('Failed to fetch unread count:', error);
    }
};

// Fetch notifications list
const fetchNotifications = async () => {
    if (isLoading.value) return;
    
    isLoading.value = true;
    try {
        // Using test endpoint temporarily to verify it works
        const response = await axios.get<NotificationResponse>('/instructor/notifications/test', {
            headers: {
                'Accept': 'application/json'
            }
        });
        console.log('Notifications API response:', response.data);
        console.log('Response type:', typeof response.data);
        
        // The test route returns data wrapped in a "data" property
        if (response.data && response.data.data && 'data' in response.data.data) {
            notifications.value = response.data.data.data as Notification[];
            console.log('Set notifications from test route:', notifications.value.length);
        } else if (response.data && typeof response.data === 'object' && 'data' in response.data) {
            notifications.value = response.data.data as Notification[];
            console.log('Set notifications (paginated):', notifications.value.length);
        } else if (Array.isArray(response.data)) {
            notifications.value = response.data as Notification[];
            console.log('Set notifications (array):', notifications.value.length);
        } else {
            console.warn('Unexpected response format:', response.data);
            notifications.value = [];
            console.log('No notifications found, set to empty array');
        }
    } catch (error) {
        console.error('Failed to fetch notifications:', error);
        if (axios.isAxiosError(error) && error.response) {
            console.error('Response status:', error.response.status);
            console.error('Response data:', error.response.data);
        }
        notifications.value = []; // Ensure it's always an array
    } finally {
        isLoading.value = false;
    }
};

// Mark single notification as read
const markAsRead = async (notification: Notification) => {
    if (notification.is_read) return;
    
    try {
        await axios.post(`/instructor/notifications/${notification.id}/read`, {}, {
            headers: {
                'Accept': 'application/json'
            }
        });
        notification.is_read = true;
        notification.read_at = new Date().toISOString();
        unreadCount.value = Math.max(0, unreadCount.value - 1);
    } catch (error) {
        console.error('Failed to mark notification as read:', error);
    }
};

// Mark all as read
const markAllAsRead = async () => {
    try {
        await axios.post('/instructor/notifications/read-all', {}, {
            headers: {
                'Accept': 'application/json'
            }
        });
        notifications.value = notifications.value.map(n => ({
            ...n,
            is_read: true,
            read_at: new Date().toISOString(),
        }));
        unreadCount.value = 0;
    } catch (error) {
        console.error('Failed to mark all as read:', error);
    }
};

// Delete notification
const deleteNotification = async (notificationId: number) => {
    try {
        await axios.delete(`/instructor/notifications/${notificationId}`, {
            headers: {
                'Accept': 'application/json'
            }
        });
        notifications.value = notifications.value.filter(n => n.id !== notificationId);
        await fetchUnreadCount(); // Refresh count
    } catch (error) {
        console.error('Failed to delete notification:', error);
    }
};

// Handle notification click
const handleNotificationClick = async (notification: Notification) => {
    await markAsRead(notification);
    
    // Navigate based on notification type
    if (notification.type === 'assignment_submitted') {
        if (notification.data.activity_id) {
            // Navigate to activity management with submissions tab active
            router.visit(`/activities/${notification.data.activity_id}/manage?tab=submissions`);
        } else if (notification.data.assignment_id) {
            // Fallback: navigate to assignments route
            router.visit(`/instructor/assignments/${notification.data.assignment_id}/submissions`);
        }
    }
    
    isOpen.value = false;
};

// Format relative time
const formatRelativeTime = (dateString: string): string => {
    const date = new Date(dateString);
    const now = new Date();
    const diffInSeconds = Math.floor((now.getTime() - date.getTime()) / 1000);
    
    if (diffInSeconds < 60) return 'Just now';
    if (diffInSeconds < 3600) return `${Math.floor(diffInSeconds / 60)}m ago`;
    if (diffInSeconds < 86400) return `${Math.floor(diffInSeconds / 3600)}h ago`;
    if (diffInSeconds < 604800) return `${Math.floor(diffInSeconds / 86400)}d ago`;
    
    return date.toLocaleDateString();
};

// Handle dropdown open
const handleOpenChange = (open: boolean) => {
    isOpen.value = open;
    if (open && notifications.value.length === 0) {
        fetchNotifications();
    }
};

// Start polling for new notifications
const startPolling = () => {
    fetchUnreadCount();
    pollInterval = window.setInterval(() => {
        fetchUnreadCount();
    }, 10000); // Poll every 10 seconds
};

// Stop polling
const stopPolling = () => {
    if (pollInterval !== null) {
        clearInterval(pollInterval);
        pollInterval = null;
    }
};

// Has unread notifications
const hasUnread = computed(() => unreadCount.value > 0);

// Unread notifications
const unreadNotifications = computed(() => 
    notifications.value.filter(n => !n.is_read)
);

// Read notifications
const readNotifications = computed(() => 
    notifications.value.filter(n => n.is_read)
);

onMounted(() => {
    startPolling();
});

onUnmounted(() => {
    stopPolling();
});
</script>

<template>
    <DropdownMenu :open="isOpen" @update:open="handleOpenChange">
        <DropdownMenuTrigger as-child>
            <Button
                variant="ghost"
                size="icon"
                class="group relative h-9 w-9 cursor-pointer"
            >
                <Bell class="size-5 opacity-80 group-hover:opacity-100" />
                <Badge
                    v-if="hasUnread"
                    variant="destructive"
                    class="absolute -right-1 -top-1 flex h-5 min-w-[20px] items-center justify-center rounded-full px-1 text-xs font-semibold"
                >
                    {{ unreadCount > 99 ? '99+' : unreadCount }}
                </Badge>
            </Button>
        </DropdownMenuTrigger>
        <DropdownMenuContent align="end" class="w-96 p-0">
            <div class="flex items-center justify-between border-b px-4 py-3">
                <h3 class="text-sm font-semibold">Notifications</h3>
                <Button
                    v-if="hasUnread"
                    variant="ghost"
                    size="sm"
                    class="h-7 text-xs"
                    @click="markAllAsRead"
                >
                    <CheckCheck class="mr-1 h-3 w-3" />
                    Mark all read
                </Button>
            </div>

            <ScrollArea class="h-[400px]">
                <div v-if="isLoading" class="flex items-center justify-center py-8">
                    <div class="text-sm text-muted-foreground">Loading...</div>
                </div>

                <div v-else-if="notifications.length === 0" class="flex flex-col items-center justify-center py-12">
                    <Bell class="mb-2 h-12 w-12 text-muted-foreground opacity-30" />
                    <p class="text-sm text-muted-foreground">No notifications yet</p>
                </div>

                <div v-else class="divide-y">
                    <!-- Unread Notifications -->
                    <div
                        v-for="notification in unreadNotifications"
                        :key="notification.id"
                        class="group relative flex cursor-pointer gap-3 p-4 transition-colors hover:bg-accent"
                        @click="handleNotificationClick(notification)"
                    >
                        <div
                            class="mt-1 h-2 w-2 flex-shrink-0 rounded-full bg-primary"
                        ></div>
                        <div class="flex-1 space-y-1">
                            <div class="flex items-start justify-between gap-2">
                                <p class="text-sm font-medium leading-tight">
                                    {{ notification.title }}
                                </p>
                                <Button
                                    variant="ghost"
                                    size="icon"
                                    class="h-6 w-6 flex-shrink-0 opacity-0 transition-opacity group-hover:opacity-100"
                                    @click.stop="deleteNotification(notification.id)"
                                >
                                    <Trash2 class="h-3 w-3" />
                                </Button>
                            </div>
                            <p class="text-xs text-muted-foreground line-clamp-2">
                                {{ notification.message }}
                            </p>
                            <div class="flex items-center gap-1 text-xs text-muted-foreground">
                                <Clock class="h-3 w-3" />
                                {{ formatRelativeTime(notification.created_at) }}
                            </div>
                        </div>
                    </div>

                    <!-- Read Notifications -->
                    <div
                        v-for="notification in readNotifications"
                        :key="notification.id"
                        class="group relative flex cursor-pointer gap-3 p-4 opacity-60 transition-colors hover:bg-accent hover:opacity-100"
                        @click="handleNotificationClick(notification)"
                    >
                        <div class="mt-1 h-2 w-2 flex-shrink-0"></div>
                        <div class="flex-1 space-y-1">
                            <div class="flex items-start justify-between gap-2">
                                <p class="text-sm font-medium leading-tight">
                                    {{ notification.title }}
                                </p>
                                <Button
                                    variant="ghost"
                                    size="icon"
                                    class="h-6 w-6 flex-shrink-0 opacity-0 transition-opacity group-hover:opacity-100"
                                    @click.stop="deleteNotification(notification.id)"
                                >
                                    <Trash2 class="h-3 w-3" />
                                </Button>
                            </div>
                            <p class="text-xs text-muted-foreground line-clamp-2">
                                {{ notification.message }}
                            </p>
                            <div class="flex items-center gap-1 text-xs text-muted-foreground">
                                <Clock class="h-3 w-3" />
                                {{ formatRelativeTime(notification.created_at) }}
                            </div>
                        </div>
                    </div>
                </div>
            </ScrollArea>
        </DropdownMenuContent>
    </DropdownMenu>
</template>
