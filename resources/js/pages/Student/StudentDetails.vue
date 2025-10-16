<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { ref, onMounted, computed } from 'vue';
import { ArrowLeft } from 'lucide-vue-next';
import { type BreadcrumbItem } from '@/types';

const props = defineProps<{
  student: {
    id: number;
    name: string;
    email: string;
    role_name: string;
    role_display_name?: string;
    grade_level?: string;
    section?: string;
  };
  enrolledCourses: Array<{
    id: number;
    title: string;
    progress: number;
    total_lessons: number;
    completed_lessons: number;
    last_activity?: string;
  }>;
}>();

// Get return URL from query params
const urlParams = new URLSearchParams(window.location.search);
const returnUrl = urlParams.get('returnUrl') || '/role-management';

// Determine breadcrumb based on return URL
const breadcrumbItems = computed<BreadcrumbItem[]>(() => {
  const items: BreadcrumbItem[] = [{ title: 'Home', href: '/' }];
  
  if (returnUrl.includes('/student-management')) {
    items.push({ title: 'Student Management', href: returnUrl });
  } else {
    items.push({ title: 'User Management', href: '/role-management' });
  }
  
  items.push({ title: 'Student Details', href: `/student/${props.student.id}/details` });
  
  return items;
});

const loading = ref(true);

const goBack = () => {
  router.visit(returnUrl);
};

onMounted(() => {
  loading.value = false;
});
</script>

<template>
  <Head :title="`Student Details - ${student.name}`" />

  <AppLayout :breadcrumbs="breadcrumbItems">
    <div class="p-6 min-h-screen bg-gray-50 dark:bg-gray-900">
      <div class="max-w-7xl mx-auto">
        <!-- Page Header with Back Button -->
        <div class="mb-6">
          <!-- Page Title with Back Button -->
          <div class="flex items-center space-x-4">
            <button
              @click="goBack"
              class="p-2 rounded-lg bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
              title="Go back"
            >
              <ArrowLeft class="w-5 h-5 text-gray-600 dark:text-gray-400" />
            </button>
            <div>
              <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Student Details</h1>
              <p class="text-gray-600 dark:text-gray-400 mt-1">View student information and course progress</p>
            </div>
          </div>
        </div>

        <!-- Student Info Card -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-6">
          <div class="flex items-start justify-between">
            <div class="flex items-center">
              <div class="h-20 w-20 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center">
                <span class="text-white text-3xl font-semibold">{{ student.name[0].toUpperCase() }}</span>
              </div>
              <div class="ml-6">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ student.name }}</h2>
                <p class="text-gray-600 dark:text-gray-300 mt-1">{{ student.email }}</p>
                <div class="flex items-center space-x-2 mt-3">
                  <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-200">
                    {{ student.role_display_name || 'Student' }}
                  </span>
                </div>
              </div>
            </div>
          </div>

          <!-- Additional Student Information -->
          <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
              <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Student ID</p>
                <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">#{{ student.id }}</p>
              </div>
              <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Grade Level</p>
                <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">
                  {{ student.grade_level || 'Not specified' }}
                </p>
              </div>
              <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Section</p>
                <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">
                  {{ student.section || 'Not assigned' }}
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="flex justify-center items-center py-12">
          <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 dark:border-blue-400"></div>
        </div>

        <!-- Enrolled Courses -->
        <div v-else>
          <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Enrolled Courses</h2>
          
          <div v-if="enrolledCourses.length > 0" class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div
              v-for="course in enrolledCourses"
              :key="course.id"
              class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 border border-gray-200 dark:border-gray-700"
            >
              <div class="flex justify-between items-start mb-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ course.title }}</h3>
                <span class="text-sm text-gray-500 dark:text-gray-400">
                  {{ course.completed_lessons }}/{{ course.total_lessons }} lessons
                </span>
              </div>

              <!-- Progress Bar -->
              <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5 mb-4">
                <div
                  class="bg-blue-600 dark:bg-blue-500 h-2.5 rounded-full"
                  :style="{ width: `${course.progress}%` }"
                ></div>
              </div>

              <div class="flex justify-between items-center text-sm">
                <span class="text-gray-600 dark:text-gray-300">
                  {{ course.progress }}% Complete
                </span>
                <span v-if="course.last_activity" class="text-gray-500 dark:text-gray-400">
                  Last activity: {{ course.last_activity }}
                </span>
              </div>
            </div>
          </div>

          <!-- No Courses Message -->
          <div
            v-else
            class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 text-center"
          >
            <p class="text-gray-600 dark:text-gray-300">This student is not enrolled in any courses.</p>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>