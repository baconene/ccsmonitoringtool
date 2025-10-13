<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import InstructorDashboard from '@/dashboards/InstructorDashboard.vue';
import StudentDashboard from '@/dashboards/StudentDashboard.vue';
import CourseModal from '@/course/CourseModal.vue';
import CosmicBackground from '@/components/CosmicBackground.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { ref, computed, onMounted } from 'vue';
import { coursesApi, dashboardApi, type Course, type DashboardStats } from '@/utils/api';
import { type BreadcrumbItem } from '@/types';

// Breadcrumb items
const breadcrumbItems: BreadcrumbItem[] = [
  { title: 'Home', href: '/' },
  { title: 'Dashboard', href: '/dashboard' }
];

// Props to determine which dashboard component to render
interface Props {
  dashboardComponent?: 'InstructorDashboard' | 'StudentDashboard';
}

const props = withDefaults(defineProps<Props>(), {
  dashboardComponent: 'InstructorDashboard'
});

// Get user role for additional checks
const page = usePage();
const user = page.props.auth.user as { role?: string; name?: string; email?: string };

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
const activeCourses = computed(() => {
  // Use dashboard stats first, fallback to courses array length
  return dashboardStats.value?.totalCourses || courses.value.length;
});
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

    <AppLayout :breadcrumbs="breadcrumbItems">
        <div class="min-h-screen bg-gradient-to-br from-gray-50 via-purple-50/30 to-pink-50/30 dark:from-gray-900 dark:via-purple-950/20 dark:to-pink-950/20 transition-colors relative overflow-hidden">
            <!-- Cosmic Background -->
            <CosmicBackground />
            
            <!-- Main Container with Responsive Grid -->
            <div class="container mx-auto px-4 py-6 max-w-7xl relative z-10">
                <!-- Dashboard Header -->
                <div class="mb-8">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">
                                Learning Management System
                            </h1>
                            <p class="text-gray-600 dark:text-gray-300 mt-1">Manage your courses and students effectively</p>
                        </div>
                        <div v-if="user?.role === 'instructor' || user?.role === 'admin'" class="mt-4 sm:mt-0">
                            <button 
                                @click="openNewCourseModal"
                                class="bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white px-6 py-3 rounded-xl font-semibold transition-all shadow-lg hover:shadow-xl hover:scale-105 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900 flex items-center gap-2"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
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
                            <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm rounded-xl shadow-lg border border-purple-200/50 dark:border-purple-700/50 p-5 transition-all hover:shadow-xl hover:scale-105">
                                <div class="flex items-center">
                                    <div class="p-3 bg-gradient-to-br from-purple-500 to-pink-500 rounded-xl shadow-lg">
                                        <!-- Rocket/Planet Icon -->
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Active Courses</p>
                                        <p class="text-2xl font-bold bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent">{{ activeCourses }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Total Students -->
                            <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm rounded-xl shadow-lg border border-blue-200/50 dark:border-blue-700/50 p-5 transition-all hover:shadow-xl hover:scale-105">
                                <div class="flex items-center">
                                    <div class="p-3 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-xl shadow-lg">
                                        <!-- Astronaut Icon -->
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Total Students</p>
                                        <p class="text-2xl font-bold bg-gradient-to-r from-blue-600 to-cyan-600 bg-clip-text text-transparent">{{ totalStudents }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Upcoming Classes -->
                            <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm rounded-xl shadow-lg border border-pink-200/50 dark:border-pink-700/50 p-5 transition-all hover:shadow-xl hover:scale-105">
                                <div class="flex items-center">
                                    <div class="p-3 bg-gradient-to-br from-pink-500 to-rose-500 rounded-xl shadow-lg">
                                        <!-- Star/Galaxy Icon -->
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Scheduled Classes</p>
                                        <p class="text-2xl font-bold bg-gradient-to-r from-pink-600 to-rose-600 bg-clip-text text-transparent">{{ scheduledClasses }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Assignments -->
                            <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm rounded-xl shadow-lg border border-indigo-200/50 dark:border-indigo-700/50 p-5 transition-all hover:shadow-xl hover:scale-105">
                                <div class="flex items-center">
                                    <div class="p-3 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl shadow-lg">
                                        <!-- Comet/Meteor Icon -->
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Assignments</p>
                                        <p class="text-2xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">{{ totalAssignments }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Dynamic Dashboard Component -->
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 transition-colors">
                            <StudentDashboard v-if="props.dashboardComponent === 'StudentDashboard' || user?.role === 'student'" />
                            <InstructorDashboard v-else />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>

    <!-- Course Modal (Only for instructors and admins) -->
    <CourseModal
        v-if="user?.role === 'instructor' || user?.role === 'admin'"
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
