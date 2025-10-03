<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import InstructorDashboard from '@/dashboards/InstructorDashboard.vue';
import CourseModal from '@/course/CourseModal.vue';
import { Head } from '@inertiajs/vue3';
import { ref, computed, onMounted } from 'vue';
import { coursesApi, dashboardApi, type Course, type DashboardStats } from '@/utils/api';

// Reactive state
const courses = ref<Course[]>([]);
const dashboardStats = ref<DashboardStats | null>(null);
const loading = ref(true);
const error = ref<string | null>(null);

// CourseModal state
const showCourseModal = ref(false);
const courseModalMode = ref<'create' | 'edit' | 'view' | 'delete'>('create');
const selectedCourse = ref<Course | null>(null);

// Transform course data for modal compatibility
const modalCourse = computed(() => {
  if (!selectedCourse.value) return null;
  return {
    id: selectedCourse.value.id,
    name: selectedCourse.value.title || '', // Map title to name for modal
    description: selectedCourse.value.description
  };
});

// Computed properties
const activeCourses = computed(() => courses.value.length);
const totalStudents = computed(() => {
  if (dashboardStats.value?.totalStudents) {
    return dashboardStats.value.totalStudents;
  }
  return courses.value.reduce((total: number, course: Course) => total + (course.students?.length || 0), 0);
});
const scheduledClasses = computed(() => dashboardStats.value?.upcomingClasses || 0);
const totalAssignments = computed(() => dashboardStats.value?.totalAssignments || 12);

// API functions
const loadDashboardData = async () => {
  loading.value = true;
  error.value = null;

  try {
    // Load data from backend only
    const [coursesResponse, statsResponse] = await Promise.allSettled([
      coursesApi.getCourses(),
      dashboardApi.getDashboardStats()
    ]);

    if (coursesResponse.status === 'fulfilled') {
      courses.value = coursesResponse.value;
    } else {
      console.error('Failed to fetch courses:', coursesResponse.reason);
      error.value = 'Failed to load courses data.';
    }

    if (statsResponse.status === 'fulfilled') {
      dashboardStats.value = statsResponse.value;
    } else {
      console.warn('Failed to fetch dashboard stats, computing from available data');
    }
  } catch (err) {
    console.error('Error loading dashboard data:', err);
    error.value = 'Failed to load dashboard data. Please refresh the page.';
  } finally {
    loading.value = false;
  }
};

// Load data on component mount
onMounted(() => {
  loadDashboardData();
});

// Refresh function
const refreshData = async () => {
  await loadDashboardData();
};

// CourseModal functions
const openNewCourseModal = () => {
  selectedCourse.value = null;
  courseModalMode.value = 'create';
  showCourseModal.value = true;
};

const closeCourseModal = () => {
  showCourseModal.value = false;
  selectedCourse.value = null;
};

const handleCourseModalRefresh = async (newCourseId?: number) => {
  closeCourseModal();
  await refreshData(); // Refresh dashboard data
  
  if (newCourseId) {
    console.log(`New course created with ID: ${newCourseId}`);
  }
};
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout>
        <div class="min-h-screen bg-gray-50 dark:bg-gray-900 transition-colors">
            <!-- Main Container with Responsive Grid -->
            <div class="container mx-auto px-4 py-6 max-w-7xl">
                <!-- Dashboard Header -->
                <div class="mb-8">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">
                                Learning Management System
                            </h1>
                            <p class="text-gray-600 dark:text-gray-300 mt-1">Manage your courses and students effectively</p>
                        </div>
                        <div class="mt-4 sm:mt-0">
                            <button 
                                @click="openNewCourseModal"
                                class="bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 text-white px-4 py-2 rounded-lg font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900"
                            >
                                New Course
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Responsive Grid Layout -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                    <!-- Main Content Area -->
                    <div class="lg:col-span-12">
                        <!-- Quick Stats Grid -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                            <!-- Active Courses -->
                            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 transition-colors">
                                <div class="flex items-center">
                                    <div class="p-2 bg-blue-100 dark:bg-blue-900/50 rounded-lg">
                                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Active Courses</p>
                                        <p class="text-lg font-bold text-gray-900 dark:text-white">{{ activeCourses }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Total Students -->
                            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 transition-colors">
                                <div class="flex items-center">
                                    <div class="p-2 bg-green-100 dark:bg-green-900/50 rounded-lg">
                                        <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Total Students</p>
                                        <p class="text-lg font-bold text-gray-900 dark:text-white">{{ totalStudents }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Upcoming Classes -->
                            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 transition-colors">
                                <div class="flex items-center">
                                    <div class="p-2 bg-purple-100 dark:bg-purple-900/50 rounded-lg">
                                        <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Scheduled Classes</p>
                                        <p class="text-lg font-bold text-gray-900 dark:text-white">{{ scheduledClasses }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Assignments -->
                            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 transition-colors">
                                <div class="flex items-center">
                                    <div class="p-2 bg-orange-100 dark:bg-orange-900/50 rounded-lg">
                                        <svg class="w-5 h-5 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Assignments</p>
                                        <p class="text-lg font-bold text-gray-900 dark:text-white">{{ totalAssignments }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Instructor Dashboard Component -->
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 transition-colors">
                            <InstructorDashboard />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>

    <!-- Course Modal -->
    <CourseModal
        :open="showCourseModal"
        :mode="courseModalMode"
        :course="modalCourse"
        @close="closeCourseModal"
        @refresh="handleCourseModalRefresh"
    />
</template>

<style scoped>
.container {
    max-width: 1200px;
}

/* Responsive breakpoints */
@media (max-width: 640px) {
    .container {
        padding-left: 1rem;
        padding-right: 1rem;
    }
}

@media (min-width: 1024px) {
    .container {
        padding-left: 2rem;
        padding-right: 2rem;
    }
}
</style>
