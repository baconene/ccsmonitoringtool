<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Activity } from '@/types';
import { ArrowLeft, Edit, Trash2, FileText, Brain } from 'lucide-vue-next';
import QuizManagement from './Quiz/QuizManagement.vue';
import AssignmentManagement from './Assignment/AssignmentManagement.vue';

interface Props {
    activity: Activity & {
        activity_type: any;
        creator: any;
        quiz?: any;
        assignment?: any;
    };
}

const props = defineProps<Props>();

const handleBack = () => {
    router.visit('/activity-management');
};

const handleEdit = () => {
    router.visit(`/activities/${props.activity.id}/edit`);
};

const handleDelete = () => {
    if (confirm('Are you sure you want to delete this activity?')) {
        router.delete(`/activities/${props.activity.id}`, {
            onSuccess: () => {
                router.visit('/activity-management');
            },
        });
    }
};
</script>

<template>
    <AppLayout>
        <div class="py-6 px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-6">
                <button
                    @click="handleBack"
                    class="mb-4 flex items-center gap-2 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors"
                >
                    <ArrowLeft :size="20" />
                    Back to Activities
                </button>

                <div class="flex items-start justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                            {{ activity.title }}
                        </h1>
                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                            {{ activity.description }}
                        </p>
                        <div class="mt-4 flex items-center gap-4 text-sm text-gray-500 dark:text-gray-400">
                            <span>Type: {{ activity.activity_type?.name }}</span>
                            <span>•</span>
                            <span>Created by: {{ activity.creator?.name }}</span>
                            <span>•</span>
                            <span>{{ new Date(activity.created_at).toLocaleDateString() }}</span>
                        </div>
                    </div>

                    <div class="flex gap-2">
                        <button
                            @click="handleEdit"
                            class="flex items-center gap-2 px-4 py-2 text-sm bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors"
                        >
                            <Edit :size="16" />
                            Edit
                        </button>
                        <button
                            @click="handleDelete"
                            class="flex items-center gap-2 px-4 py-2 text-sm bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors"
                        >
                            <Trash2 :size="16" />
                            Delete
                        </button>
                    </div>
                </div>
            </div>

            <!-- Activity Content Based on Type -->
            <div class="mt-8">
                <!-- Quiz Management -->
                <QuizManagement
                    v-if="activity.activity_type?.name === 'Quiz'"
                    :activity="activity"
                    :quiz="activity.quiz"
                />

                <!-- Assignment Management -->
                <AssignmentManagement
                    v-else-if="activity.activity_type?.name === 'Assignment'"
                    :activity="activity"
                    :assignment="activity.assignment"
                />

                <!-- Default View -->
                <div v-else class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <p class="text-gray-600 dark:text-gray-400">
                        No specific management interface for this activity type yet.
                    </p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
