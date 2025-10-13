<script setup lang="ts">
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import { Calendar, FileText, Upload } from 'lucide-vue-next';

interface Props {
    activity: any;
    assignment?: any;
}

const props = defineProps<Props>();

const formData = ref({
    title: props.assignment?.title || props.activity.title,
    description: props.assignment?.description || props.activity.description,
    document_id: props.assignment?.document_id || null,
});

const handleCreateAssignment = () => {
    router.post('/assignments', {
        activity_id: props.activity.id,
        ...formData.value,
    });
};

const handleUpdateAssignment = () => {
    router.put(`/assignments/${props.assignment.id}`, formData.value);
};

const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};
</script>

<template>
    <div class="space-y-6">
        <!-- Assignment Header -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Assignment Management</h2>

            <!-- Create Assignment if it doesn't exist -->
            <div v-if="!assignment" class="text-center py-8">
                <p class="text-gray-600 dark:text-gray-400 mb-4">
                    No assignment has been created for this activity yet.
                </p>
                <button
                    @click="handleCreateAssignment"
                    class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                >
                    Create Assignment
                </button>
            </div>

            <!-- Assignment Details -->
            <div v-else class="space-y-6">
                <!-- Info Display -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Title
                        </label>
                        <p class="text-gray-900 dark:text-white">{{ assignment.title }}</p>
                    </div>

                    <div v-if="activity.due_date">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Due Date
                        </label>
                        <p class="flex items-center gap-2"
                            :class="{
                                'text-red-600 dark:text-red-400 font-semibold': new Date(activity.due_date) < new Date(),
                                'text-orange-600 dark:text-orange-400 font-semibold': new Date(activity.due_date) >= new Date() && (new Date(activity.due_date).getTime() - new Date().getTime()) / (1000 * 60 * 60 * 24) <= 7,
                                'text-gray-900 dark:text-white': new Date(activity.due_date) >= new Date() && (new Date(activity.due_date).getTime() - new Date().getTime()) / (1000 * 60 * 60 * 24) > 7,
                            }">
                            <Calendar :size="18" />
                            {{ formatDate(activity.due_date) }}
                            <span v-if="new Date(activity.due_date) < new Date()" class="ml-1 text-xs">⚠️ Overdue</span>
                            <span v-else-if="(new Date(activity.due_date).getTime() - new Date().getTime()) / (1000 * 60 * 60 * 24) <= 7" class="ml-1 text-xs">⏰ Due Soon</span>
                        </p>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Description
                        </label>
                        <p class="text-gray-900 dark:text-white">
                            {{ assignment.description || 'No description provided' }}
                        </p>
                    </div>

                    <div v-if="assignment.document" class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Attached Document
                        </label>
                        <div class="flex items-center gap-2 text-blue-600 dark:text-blue-400">
                            <FileText :size="18" />
                            <a :href="`/documents/${assignment.document.id}`" class="hover:underline">
                                {{ assignment.document.name }}
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Edit Form -->
                <div class="pt-6 border-t border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Edit Assignment</h3>
                    
                    <form @submit.prevent="handleUpdateAssignment" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Title
                            </label>
                            <input
                                v-model="formData.title"
                                type="text"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                                required
                            />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Description
                            </label>
                            <textarea
                                v-model="formData.description"
                                rows="4"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                            />
                        </div>

                        <div class="flex justify-end">
                            <button
                                type="submit"
                                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                            >
                                Update Assignment
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Student Submissions (Placeholder) -->
        <div v-if="assignment" class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Student Submissions</h3>
            <p class="text-gray-600 dark:text-gray-400 text-center py-8">
                Submission tracking feature coming soon...
            </p>
        </div>
    </div>
</template>
