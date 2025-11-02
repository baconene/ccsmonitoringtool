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
                            <h1 class="text-2xl sm:text-3xl font-bold bg-gradient-to-r from-pink-600 via-purple-600 to-blue-600 bg-clip-text text-transparent">
                                Welcome to Team LEMA Web Sci
                            </h1>
                            <p class="text-gray-600 dark:text-gray-300 mt-1">Your STEM Career Learning Management System</p>
                        </div>
   
                    </div>
                </div>

                <!-- Responsive Grid Layout -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                    <!-- Main Content Area -->
                    <div class="lg:col-span-12">
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
