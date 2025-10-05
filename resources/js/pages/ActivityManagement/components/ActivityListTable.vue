<script setup lang="ts">
import { Activity, ActivityType } from '@/types';
import { Edit, Trash2, Eye, Calendar } from 'lucide-vue-next';

interface Props {
    activities: Activity[];
    activityTypes: ActivityType[];
}

const props = defineProps<Props>();

const emit = defineEmits<{
    edit: [activity: Activity];
    delete: [activity: Activity];
    view: [activity: Activity];
}>();

const getActivityTypeName = (activityTypeId: number) => {
    const type = props.activityTypes.find(t => t.id === activityTypeId);
    return type?.name || 'Unknown';
};

const getActivityTypeColor = (activityTypeId: number) => {
    const colors: Record<number, string> = {
        1: 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
        2: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
        3: 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300',
    };
    return colors[activityTypeId] || 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
};

const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
};
</script>

<template>
    <div class="overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                            Title
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                            Type
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                            Created By
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                            Created At
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                    <tr
                        v-for="activity in activities"
                        :key="activity.id"
                        class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                    >
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ activity.title }}
                            </div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                {{ activity.description?.substring(0, 50) }}{{ activity.description && activity.description.length > 50 ? '...' : '' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span
                                :class="[
                                    'inline-flex rounded-full px-2 text-xs font-semibold leading-5',
                                    getActivityTypeColor(activity.activity_type)
                                ]"
                            >
                                {{ getActivityTypeName(activity.activity_type) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                            {{ activity.created_by }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            <div class="flex items-center gap-1">
                                <Calendar :size="14" />
                                {{ formatDate(activity.created_at) }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex justify-end gap-2">
                                <button
                                    @click="emit('view', activity)"
                                    class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300"
                                    title="View"
                                >
                                    <Eye :size="18" />
                                </button>
                                <button
                                    @click="emit('edit', activity)"
                                    class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 dark:hover:text-yellow-300"
                                    title="Edit"
                                >
                                    <Edit :size="18" />
                                </button>
                                <button
                                    @click="emit('delete', activity)"
                                    class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                                    title="Delete"
                                >
                                    <Trash2 :size="18" />
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="activities.length === 0">
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                            No activities found. Create your first activity to get started.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
