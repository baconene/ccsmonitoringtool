<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Activity, ActivityType } from '@/types';
import { Plus } from 'lucide-vue-next';
import ActivityCard from './components/ActivityCard.vue';
import ActivityFilter from './components/ActivityFilter.vue';
import NewActivityModal from './components/NewActivityModal.vue';

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
        <div class="py-6 px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                        My Activities
                    </h1>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                        Create and manage your quizzes, assignments, and exercises
                    </p>
                </div>
                <button
                    @click="showNewActivityModal = true"
                    class="flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-white hover:bg-blue-700 transition-colors"
                >
                    <Plus :size="20" />
                    New Activity
                </button>
            </div>

            <!-- Filter Component -->
            <ActivityFilter
                :activity-types="activityTypes"
                :filters="filters"
                class="mb-6"
            />

            <!-- Activity Cards Grid -->
            <div v-if="activities.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <ActivityCard
                    v-for="activity in activities"
                    :key="activity.id"
                    :activity="activity"
                />
            </div>

            <!-- Empty State -->
            <div
                v-else
                class="text-center py-12 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700"
            >
                <svg
                    class="mx-auto h-12 w-12 text-gray-400"
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
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No activities found</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Get started by creating a new activity
                </p>
                <div class="mt-6">
                    <button
                        @click="showNewActivityModal = true"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors"
                    >
                        <Plus :size="20" />
                        Create Activity
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
    </AppLayout>
</template>
