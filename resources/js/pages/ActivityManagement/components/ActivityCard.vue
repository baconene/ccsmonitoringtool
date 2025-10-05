<script setup lang="ts">
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import { Calendar, Clock, BookOpen, Pencil, Trash2 } from 'lucide-vue-next';
import { router } from '@inertiajs/vue3';
import DeleteActivityModal from './DeleteActivityModal.vue';

interface ActivityType {
    id: number;
    name: string;
    description: string | null;
}

interface User {
    id: number;
    name: string;
    email: string;
}

interface Activity {
    id: number;
    title: string;
    description: string | null;
    activity_type_id: number;
    created_by: number;
    created_at: string;
    updated_at: string;
    activityType?: ActivityType;
    creator?: User;
    question_count?: number;
    total_points?: number;
    has_due_date?: boolean;
    used_in_modules?: Array<{
        id: number;
        title: string;
    }>;
}

interface Props {
    activity: Activity;
}

const props = defineProps<Props>();

// Get activity type name - handle both camelCase and snake_case from backend
const getActivityTypeName = (): string => {
    // Try activityType first (camelCase from eager loading)
    if (props.activity.activityType?.name) {
        return props.activity.activityType.name;
    }
    
    // Try activity_type (snake_case - sometimes Laravel serializes this way)
    const activityData = props.activity as any;
    if (activityData.activity_type?.name) {
        return activityData.activity_type.name;
    }
    
    return '';
};

// Color scheme based on activity type
const getCardColorClass = (typeName: string) => {
    if (!typeName) return 'bg-gray-100 dark:bg-gray-800 border-gray-300 dark:border-gray-700';
    
    const type = typeName.toLowerCase();
    if (type.includes('quiz')) {
        return 'bg-blue-100 dark:bg-blue-900/30 border-blue-300 dark:border-blue-700';
    } else if (type.includes('assignment')) {
        return 'bg-green-100 dark:bg-green-900/30 border-green-300 dark:border-green-700';
    } else if (type.includes('exercise')) {
        return 'bg-purple-100 dark:bg-purple-900/30 border-purple-300 dark:border-purple-700';
    }
    return 'bg-gray-100 dark:bg-gray-800 border-gray-300 dark:border-gray-700';
};

const getTitleColorClass = (typeName: string) => {
    if (!typeName) return 'bg-gray-500 dark:bg-gray-600';
    
    const type = typeName.toLowerCase();
    if (type.includes('quiz')) {
        return 'bg-blue-500 dark:bg-blue-600';
    } else if (type.includes('assignment')) {
        return 'bg-green-500 dark:bg-green-600';
    } else if (type.includes('exercise')) {
        return 'bg-purple-500 dark:bg-purple-600';
    }
    return 'bg-gray-500 dark:bg-gray-600';
};

const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
};

const showDeleteModal = ref(false);
const isDeleting = ref(false);

const handleDeleteClick = () => {
    showDeleteModal.value = true;
};

const handleConfirmDelete = () => {
    isDeleting.value = true;
    router.delete(`/activities/${props.activity.id}`, {
        onSuccess: () => {
            showDeleteModal.value = false;
            isDeleting.value = false;
        },
        onError: () => {
            isDeleting.value = false;
        },
    });
};

const handleCloseModal = () => {
    if (!isDeleting.value) {
        showDeleteModal.value = false;
    }
};
</script>

<template>
    <div
        :class="getCardColorClass(getActivityTypeName())"
        class="border rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200"
    >
        <!-- Card Header with Activity Type Color -->
        <div :class="getTitleColorClass(getActivityTypeName())" class="px-4 py-3 rounded-t-lg">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-white truncate">
                    {{ activity.title }}
                </h3>
                <span
                    v-if="getActivityTypeName()"
                    class="px-2 py-1 text-xs font-medium text-white bg-white/20 rounded-full whitespace-nowrap ml-2"
                >
                    {{ getActivityTypeName() }}
                </span>
            </div>
        </div>

        <!-- Card Body -->
        <div class="p-4 space-y-3">
            <!-- Description -->
            <p
                v-if="activity.description"
                class="text-sm text-gray-700 dark:text-gray-300 line-clamp-2"
            >
                {{ activity.description }}
            </p>

            <!-- Activity Stats -->
            <div class="flex flex-wrap gap-3 text-xs text-gray-600 dark:text-gray-400">
                <div v-if="activity.question_count" class="flex items-center gap-1">
                    <BookOpen :size="16" />
                    <span>{{ activity.question_count }} Questions</span>
                </div>
                <div v-if="activity.total_points" class="flex items-center gap-1">
                    <span class="font-semibold">{{ activity.total_points }} Points</span>
                </div>
                <div v-if="activity.has_due_date" class="flex items-center gap-1">
                    <Clock :size="16" />
                    <span>Has Due Date</span>
                </div>
            </div>

            <!-- Created Date -->
            <div class="flex items-center gap-1 text-xs text-gray-500 dark:text-gray-400">
                <Calendar :size="16" />
                <span>Created {{ formatDate(activity.created_at) }}</span>
            </div>

            <!-- Module Usage Summary -->
            <div
                v-if="activity.used_in_modules && activity.used_in_modules.length > 0"
                class="pt-3 border-t border-gray-300 dark:border-gray-600"
            >
                <p class="text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Used in Modules:
                </p>
                <div class="flex flex-wrap gap-1">
                    <span
                        v-for="module in activity.used_in_modules"
                        :key="module.id"
                        class="px-2 py-1 text-xs bg-white dark:bg-gray-700 rounded-full text-gray-700 dark:text-gray-300"
                    >
                        {{ module.title }}
                    </span>
                </div>
            </div>
            <div
                v-else
                class="pt-3 border-t border-gray-300 dark:border-gray-600"
            >
                <p class="text-xs text-gray-500 dark:text-gray-400 italic">
                    Not used in any module yet
                </p>
            </div>
        </div>

        <!-- Card Footer with Actions -->
        <div class="px-4 py-3 bg-white/50 dark:bg-gray-800/50 rounded-b-lg flex justify-end gap-2">
            <Link
                :href="`/activities/${activity.id}`"
                class="px-3 py-1.5 text-xs font-medium text-blue-700 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/50 rounded-md transition-colors"
            >
                View Details
            </Link>
            <Link
                :href="`/activities/${activity.id}/edit`"
                class="p-1.5 text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-md transition-colors"
            >
                <Pencil :size="16" />
            </Link>
            <button
                @click="handleDeleteClick"
                class="p-1.5 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/50 rounded-md transition-colors"
            >
                <Trash2 :size="16" />
            </button>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <DeleteActivityModal
        :show="showDeleteModal"
        :title="`Delete ${activity.title}?`"
        :message="`Are you sure you want to delete this activity? This action cannot be undone and will remove all associated quizzes and assignments.`"
        :is-deleting="isDeleting"
        @close="handleCloseModal"
        @confirm="handleConfirmDelete"
    />
</template>
