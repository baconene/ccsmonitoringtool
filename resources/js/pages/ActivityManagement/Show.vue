<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Activity } from '@/types';
import { fetchActivityTypes, isManageableActivityType } from '@/constants/activityTypes';
import { ArrowLeft, Edit, Trash2, FileText, Brain, ChevronDown, ChevronUp, BookOpen, GraduationCap } from 'lucide-vue-next'; 
import QuizManagement from '@/pages/ActivityManagement/Quiz/QuizManagement.vue';
import AssignmentManagement from '@/pages/ActivityManagement/Assignment/AssignmentManagement.vue';
import CosmicBackground from '@/components/CosmicBackground.vue';

interface RelatedCourse {
    id: number;
    title: string;
    description: string;
    module_id: number;
    module_title: string;
}

interface Props {
    activity: Activity & {
        activity_type: any;
        creator: any;
        quiz?: any;
        assignment?: any;
        modules_count?: number;
        courses_count?: number;
        related_courses?: RelatedCourse[];
    };
}

const props = defineProps<Props>();

// Collapsible sections state
const showQuestions = ref(true);
const showRelatedCourses = ref(true);

// Ensure activity types are loaded
onMounted(async () => {
    await fetchActivityTypes();
});

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

const toggleQuestions = () => {
    showQuestions.value = !showQuestions.value;
};

const toggleRelatedCourses = () => {
    showRelatedCourses.value = !showRelatedCourses.value;
};

const navigateToCourse = (courseId: number) => {
    router.visit(`/course-management?course=${courseId}`);
};
</script>

<template>
    <AppLayout>
        <div class="relative min-h-screen bg-gradient-to-br from-gray-50 via-purple-50/30 to-pink-50/30 dark:from-gray-900 dark:via-purple-950/20 dark:to-pink-950/20 transition-colors duration-300">
            <CosmicBackground />
            
            <div class="relative z-10 py-6 px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-6 sm:mb-8">
                    <button
                        @click="handleBack"
                        class="mb-4 flex items-center gap-2 text-gray-600 dark:text-gray-400 hover:text-purple-600 dark:hover:text-purple-400 transition-colors group"
                    >
                        <ArrowLeft :size="20" class="group-hover:-translate-x-1 transition-transform" />
                        <span>Back to Activities</span>
                    </button>

                    <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                        <div class="flex-1">
                            <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-900 dark:text-white">
                                {{ activity.title }}
                            </h1>
                            <p class="mt-2 text-sm sm:text-base text-gray-600 dark:text-gray-400">
                                {{ activity.description }}
                            </p>
                            <div class="mt-4 flex flex-wrap items-center gap-3 sm:gap-4 text-xs sm:text-sm text-gray-500 dark:text-gray-400">
                                <span class="px-3 py-1 bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 rounded-full font-medium">
                                    {{ activity.activity_type?.name }}
                                </span>
                                <span class="hidden sm:inline">•</span>
                                <span>Created by: <strong class="text-gray-700 dark:text-gray-300">{{ activity.creator?.name || 'Unknown' }}</strong></span>
                                <span class="hidden sm:inline">•</span>
                                <span>{{ new Date(activity.created_at).toLocaleDateString() }}</span>
                            </div>
                            
                            <!-- Module and Course Count -->
                            <div class="mt-3 flex flex-wrap items-center gap-3 text-xs sm:text-sm">
                                <div class="flex items-center gap-2 px-3 py-1.5 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 rounded-full">
                                    <BookOpen :size="16" />
                                    <span><strong>{{ activity.modules_count || 0 }}</strong> Module{{ (activity.modules_count || 0) !== 1 ? 's' : '' }}</span>
                                </div>
                                <div class="flex items-center gap-2 px-3 py-1.5 bg-pink-100 dark:bg-pink-900/30 text-pink-700 dark:text-pink-300 rounded-full">
                                    <GraduationCap :size="16" />
                                    <span><strong>{{ activity.courses_count || 0 }}</strong> Course{{ (activity.courses_count || 0) !== 1 ? 's' : '' }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-wrap items-center gap-2">
                            <button
                                @click="handleEdit"
                                class="flex items-center gap-1.5 px-3 py-2 text-sm bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                                title="Edit Activity"
                            >
                                <Edit :size="16" />
                                <span class="hidden sm:inline">Edit</span>
                            </button>
                            <button
                                @click="handleDelete"
                                class="flex items-center gap-1.5 px-3 py-2 text-sm bg-white dark:bg-gray-800 text-red-600 dark:text-red-400 border border-red-300 dark:border-red-600 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors"
                                title="Delete Activity"
                            >
                                <Trash2 :size="16" />
                                <span class="hidden sm:inline">Delete</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Related Courses Section -->
                <div v-if="activity.related_courses && activity.related_courses.length > 0" class="mt-6 sm:mt-8">
                    <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm rounded-xl shadow-lg border border-purple-200/50 dark:border-purple-700/50 overflow-hidden">
                        <!-- Collapsible Header -->
                        <button
                            @click="toggleRelatedCourses"
                            class="w-full flex items-center justify-between p-4 sm:p-6 hover:bg-purple-50/50 dark:hover:bg-purple-900/20 transition-colors"
                        >
                            <div class="flex items-center gap-3">
                                <GraduationCap :size="24" class="text-purple-600 dark:text-purple-400" />
                                <h2 class="text-lg sm:text-xl font-semibold text-gray-900 dark:text-white">
                                    Related Courses
                                </h2>
                                <span class="px-2 py-1 bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 text-xs font-medium rounded-full">
                                    {{ activity.related_courses.length }}
                                </span>
                            </div>
                            <ChevronDown v-if="showRelatedCourses" :size="20" class="text-gray-500 dark:text-gray-400 transition-transform" />
                            <ChevronUp v-else :size="20" class="text-gray-500 dark:text-gray-400 transition-transform" />
                        </button>

                        <!-- Collapsible Content -->
                        <transition
                            enter-active-class="transition ease-out duration-200"
                            enter-from-class="opacity-0 transform -translate-y-2"
                            enter-to-class="opacity-100 transform translate-y-0"
                            leave-active-class="transition ease-in duration-150"
                            leave-from-class="opacity-100 transform translate-y-0"
                            leave-to-class="opacity-0 transform -translate-y-2"
                        >
                            <div v-show="showRelatedCourses" class="border-t border-purple-200/50 dark:border-purple-700/50 p-4 sm:p-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    <div
                                        v-for="course in activity.related_courses"
                                        :key="course.id"
                                        @click="navigateToCourse(course.id)"
                                        class="group bg-gradient-to-br from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20 rounded-lg p-4 border border-purple-200/50 dark:border-purple-700/50 cursor-pointer hover:shadow-lg hover:scale-105 transition-all duration-200"
                                    >
                                        <div class="flex items-start gap-3">
                                            <div class="p-2 bg-purple-100 dark:bg-purple-900/40 rounded-lg group-hover:bg-purple-200 dark:group-hover:bg-purple-800/40 transition-colors">
                                                <GraduationCap :size="20" class="text-purple-600 dark:text-purple-400" />
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <h3 class="font-semibold text-gray-900 dark:text-white truncate group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors">
                                                    {{ course.title }}
                                                </h3>
                                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 line-clamp-2">
                                                    {{ course.description || 'No description available' }}
                                                </p>
                                                <div class="mt-2 flex items-center gap-2">
                                                    <BookOpen :size="14" class="text-indigo-500 dark:text-indigo-400" />
                                                    <span class="text-xs text-indigo-600 dark:text-indigo-400 font-medium">
                                                        {{ course.module_title }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </transition>
                    </div>
                </div>

                <!-- Activity Content Based on Type (Collapsible Questions Section) -->
                <div class="mt-6 sm:mt-8">
                    <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm rounded-xl shadow-lg border border-purple-200/50 dark:border-purple-700/50 overflow-hidden">
                        <!-- Collapsible Header for Quiz/Assignment -->
                        <button
                            v-if="activity.activity_type?.name === 'Quiz' || activity.activity_type?.name === 'Assignment'"
                            @click="toggleQuestions"
                            class="w-full flex items-center justify-between p-4 sm:p-6 hover:bg-purple-50/50 dark:hover:bg-purple-900/20 transition-colors"
                        >
                            <div class="flex items-center gap-3">
                                <FileText :size="24" class="text-purple-600 dark:text-purple-400" />
                                <h2 class="text-lg sm:text-xl font-semibold text-gray-900 dark:text-white">
                                    {{ activity.activity_type?.name === 'Quiz' ? 'Quiz Questions' : 'Assignment Details' }}
                                </h2>
                                <span v-if="activity.quiz?.questions" class="px-2 py-1 bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 text-xs font-medium rounded-full">
                                    {{ activity.quiz.questions.length }} Questions
                                </span>
                            </div>
                            <ChevronDown v-if="showQuestions" :size="20" class="text-gray-500 dark:text-gray-400 transition-transform" />
                            <ChevronUp v-else :size="20" class="text-gray-500 dark:text-gray-400 transition-transform" />
                        </button>

                        <!-- Collapsible Content -->
                        <transition
                            enter-active-class="transition ease-out duration-200"
                            enter-from-class="opacity-0 transform -translate-y-2"
                            enter-to-class="opacity-100 transform translate-y-0"
                            leave-active-class="transition ease-in duration-150"
                            leave-from-class="opacity-100 transform translate-y-0"
                            leave-to-class="opacity-0 transform -translate-y-2"
                        >
                            <div v-show="showQuestions" class="border-t border-purple-200/50 dark:border-purple-700/50">
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
                            </div>
                        </transition>

                        <!-- Default View (Non-collapsible) -->
                        <div v-if="activity.activity_type?.name !== 'Quiz' && activity.activity_type?.name !== 'Assignment'" class="p-6 sm:p-8">
                            <p class="text-gray-600 dark:text-gray-400 text-center">
                                {{ isManageableActivityType(activity.activity_type?.name || '') ? 
                                    'Loading management interface...' : 
                                    'No specific management interface for this activity type yet.' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
