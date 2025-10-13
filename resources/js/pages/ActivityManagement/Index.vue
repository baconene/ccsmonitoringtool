<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Activity, ActivityType } from '@/types';
import { Plus } from 'lucide-vue-next';
import ActivityCard from '@/pages/ActivityManagement/components/ActivityCard.vue';
import ActivityFilter from '@/pages/ActivityManagement/components/ActivityFilter.vue';
import NewActivityModal from '@/pages/ActivityManagement/components/NewActivityModal.vue';
import CosmicBackground from '@/components/CosmicBackground.vue';

interface Filters {
    search: string | null;
    type: number | null;
    date_from: string | null;
    date_to: string | null;
}

interface Props {
    activities: Activity[];
    activityTypes: ActivityType[];
    filters: Filters;
}

const props = defineProps<Props>();

const showNewActivityModal = ref(false);

// Debug logging
onMounted(() => {
    console.log('=== Activity Management Debug ===');
    console.log('Activities received:', props.activities);
    console.log('Activity Types received:', props.activityTypes);
    if (props.activities.length > 0) {
        console.log('First activity (full object):', props.activities[0]);
        console.log('First activity.activityType:', props.activities[0].activityType);
        console.log('First activity.activity_type:', (props.activities[0] as any).activity_type);
        console.log('Activity keys:', Object.keys(props.activities[0]));
    } else {
        console.log('No activities found - user may not have created any activities yet');
    }
    console.log('=================================');
});

const handleCreateActivity = (activityData: any) => {
    router.post('/activities', activityData, {
        onSuccess: () => {
            showNewActivityModal.value = false;
        },
    });
};
</script>

<template>
    <AppLayout>
        <div class="relative min-h-screen bg-gradient-to-br from-gray-50 via-purple-50/30 to-pink-50/30 dark:from-gray-900 dark:via-purple-950/20 dark:to-pink-950/20 transition-colors duration-300">
            <CosmicBackground />
            
            <div class="relative z-10 py-6 px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-6 sm:mb-8">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div>
                            <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-900 dark:text-white bg-clip-text text-transparent bg-gradient-to-r from-purple-600 to-pink-600 dark:from-purple-400 dark:to-pink-400">
                                My Activities
                            </h1>
                            <p class="mt-2 text-sm sm:text-base text-gray-600 dark:text-gray-400">
                                Create and manage your quizzes, assignments, and exercises
                            </p>
                        </div>
                        <button
                            @click="showNewActivityModal = true"
                            class="flex items-center justify-center gap-2 rounded-lg bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 px-4 sm:px-6 py-2 sm:py-2.5 text-sm sm:text-base text-white font-medium shadow-lg hover:shadow-xl transition-all duration-200 hover:scale-105"
                        >
                            <Plus :size="20" />
                            <span>New Activity</span>
                        </button>
                    </div>
                </div>

                <!-- Filter Component -->
                <ActivityFilter
                    :activity-types="activityTypes"
                    :filters="filters"
                    class="mb-6"
                />

                <!-- Activity Cards Grid -->
                <div v-if="activities.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6">
                    <ActivityCard
                        v-for="activity in activities"
                        :key="activity.id"
                        :activity="activity"
                    />
                </div>

                <!-- Empty State -->
                <div
                    v-else
                    class="text-center py-12 sm:py-16 bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm rounded-xl border border-purple-200/50 dark:border-purple-700/50 shadow-lg"
                >
                    <svg
                        class="mx-auto h-12 w-12 sm:h-16 sm:w-16 text-purple-400 dark:text-purple-500"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        aria-hidden="true"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                        />
                    </svg>
                    <h3 class="mt-4 text-base sm:text-lg font-medium text-gray-900 dark:text-gray-100">No activities found</h3>
                    <p class="mt-2 text-sm sm:text-base text-gray-500 dark:text-gray-400">
                        Get started by creating a new activity
                    </p>
                    <div class="mt-6">
                        <button
                            @click="showNewActivityModal = true"
                            class="inline-flex items-center gap-2 px-4 sm:px-6 py-2 sm:py-2.5 bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white font-medium rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 hover:scale-105"
                        >
                            <Plus :size="20" />
                            <span>Create Activity</span>
                        </button>
                    </div>
                </div>

                <!-- New Activity Modal -->
                <NewActivityModal
                    :show="showNewActivityModal"
                    :activity-types="activityTypes"
                    @close="showNewActivityModal = false"
                    @submit="handleCreateActivity"
                />
            </div>
        </div>
    </AppLayout>
</template>
