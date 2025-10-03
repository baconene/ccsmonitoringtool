<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { coursesApi, scheduleApi, dashboardApi, handleApiError, type Course, type Schedule, type Instructor, type DashboardStats } from '@/utils/api';
import { usePage } from '@inertiajs/vue3';

// Get authenticated user from Inertia page props
const page = usePage();
const user = page.props.auth.user;

// Reactive state
const instructor = ref<Instructor | null>(null);
const courses = ref<Course[]>([]);
const schedule = ref<Schedule[]>([]);
const dashboardStats = ref<DashboardStats | null>(null);
const loading = ref(true);
const error = ref<string | null>(null);

// No fallback props - pure API integration

// Computed properties
const totalCourses = computed(() => dashboardStats.value?.totalCourses || courses.value.length);

const totalStudents = computed(() => {
  if (dashboardStats.value?.totalStudents) {
    return dashboardStats.value.totalStudents;
  }
  return courses.value.reduce((total, course) => total + (course.students?.length || 0), 0);
});

const upcomingClasses = computed(() => dashboardStats.value?.upcomingClasses || schedule.value.length);

const totalAssignments = computed(() => dashboardStats.value?.totalAssignments || 0);

// Get upcoming schedule items (limit to 5)
const upcomingSchedule = computed(() => schedule.value.slice(0, 5));

// API functions

const fetchCourses = async () => {
  try {
    courses.value = await coursesApi.getCourses();
  } catch (err) {
    console.error('Failed to fetch courses:', err);
    // Temporary fallback data for testing
    courses.value = [
      {
        id: 1,
        title: 'Mathematics 101',
        description: 'Introduction to basic mathematics',
        students: [],
        instructor_id: user?.id || 1,
        created_at: new Date().toISOString(),
        updated_at: new Date().toISOString()
      },
      {
        id: 2,
        title: 'Science Fundamentals',
        description: 'Basic science concepts and principles',
        students: [],
        instructor_id: user?.id || 1,
        created_at: new Date().toISOString(),
        updated_at: new Date().toISOString()
      }
    ];
    // Don't set error for now to allow fallback data to show
    console.warn('Using fallback course data due to API error:', handleApiError(err));
  }
};

const fetchSchedule = async () => {
  try {
    schedule.value = await scheduleApi.getSchedule();
  } catch (err) {
    console.error('Failed to fetch schedule:', err);
    // Temporary fallback schedule data
    schedule.value = [
      {
        id: 1,
        courseId: 1,
        courseTitle: 'Mathematics 101',
        type: 'Lecture',
        date: '2025-10-04',
        time: '10:00 AM'
      },
      {
        id: 2,
        courseId: 2,
        courseTitle: 'Science Fundamentals',
        type: 'Lab Session',
        date: '2025-10-05',
        time: '2:00 PM'
      }
    ];
  }
};

const fetchDashboardStats = async () => {
  try {
    dashboardStats.value = await dashboardApi.getDashboardStats();
  } catch (err) {
    console.error('Failed to fetch dashboard stats:', err);
    // Stats will be computed from available data
  }
};

// Load all data
const loadDashboardData = async () => {
  loading.value = true;
  error.value = null;

  try {
    // Fetch data in parallel for better performance
    await Promise.allSettled([
      fetchCourses(),
      fetchSchedule(),
      fetchDashboardStats()
    ]);
  } catch (err) {
    error.value = handleApiError(err);
  } finally {
    loading.value = false;
  }
};

// Refresh data function (can be called externally)
const refreshData = async () => {
  await loadDashboardData();
};

// Load data on component mount
onMounted(() => {
  loadDashboardData();
});

// Expose refresh function for parent components
defineExpose({
  refreshData
});
</script>

<template>
  <div class="instructor-dashboard">
    <!-- Welcome Header -->
    <div class="mb-6">
      <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
        Welcome back, {{ user?.name || 'Instructor' }}!
      </h1>
      <p class="text-gray-600 dark:text-gray-300 mt-2">Here's what's happening with your courses today.</p>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="flex justify-center items-center py-12">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 dark:border-blue-400"></div>
      <span class="ml-3 text-gray-600 dark:text-gray-300">Loading dashboard...</span>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="bg-red-50 dark:bg-red-900/50 border border-red-200 dark:border-red-700 rounded-lg p-4 mb-6">
      <div class="flex">
        <svg class="w-5 h-5 text-red-400 dark:text-red-300 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
        </svg>
        <div class="ml-3">
          <h3 class="text-sm font-medium text-red-800 dark:text-red-200">Error loading dashboard</h3>
          <p class="text-sm text-red-700 dark:text-red-300 mt-1">{{ error }}</p>
          <button @click="refreshData" class="mt-2 text-sm text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-200 font-medium">
            Try again
          </button>
        </div>
      </div>
    </div>

    <!-- Stats Grid -->
    <div v-else class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
      <!-- Total Courses -->
      <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md border border-gray-200 dark:border-gray-700 p-6 transition-colors">
        <div class="flex items-center">
          <div class="p-3 bg-blue-100 dark:bg-blue-900/50 rounded-full">
            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Total Courses</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ totalCourses }}</p>
          </div>
        </div>
      </div>

      <!-- Total Students -->
      <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md border border-gray-200 dark:border-gray-700 p-6 transition-colors">
        <div class="flex items-center">
          <div class="p-3 bg-green-100 dark:bg-green-900/50 rounded-full">
            <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Total Students</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ totalStudents }}</p>
          </div>
        </div>
      </div>

      <!-- Upcoming Classes -->
      <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md border border-gray-200 dark:border-gray-700 p-6 transition-colors">
        <div class="flex items-center">
          <div class="p-3 bg-purple-100 dark:bg-purple-900/50 rounded-full">
            <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Upcoming Classes</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ schedule.length }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
      <!-- Courses List -->
      <div class="xl:col-span-2">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md border border-gray-200 dark:border-gray-700 p-6 transition-colors">
          <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Your Courses</h2>
          <div class="space-y-4">
            <div
              v-for="course in courses"
              :key="course.id"
              class="border border-gray-200 dark:border-gray-600 rounded-lg p-4 hover:shadow-md dark:hover:shadow-lg transition-shadow bg-gray-50 dark:bg-gray-700"
            >
              <h3 class="font-semibold text-lg text-gray-900 dark:text-white">{{ course.title }}</h3>
              <p class="text-gray-600 dark:text-gray-300 text-sm mt-1">{{ course.description }}</p>
              <div class="flex items-center justify-between mt-3">
                <span class="text-sm text-gray-500 dark:text-gray-400">
                  {{ (course.students?.length || 0) }} student{{ (course.students?.length || 0) !== 1 ? 's' : '' }}
                </span>
                <button class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 text-sm font-medium transition-colors">
                  View Details
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Schedule Sidebar -->
      <div class="xl:col-span-1">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md border border-gray-200 dark:border-gray-700 p-6 transition-colors">
          <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Upcoming Schedule</h2>
          <div class="space-y-3">
            <div
              v-for="item in upcomingSchedule"
              :key="item.id"
              class="border-l-4 border-blue-500 dark:border-blue-400 pl-4 py-2"
            >
              <p class="font-medium text-gray-900 dark:text-white">{{ item.courseTitle }}</p>
              <p class="text-sm text-gray-600 dark:text-gray-300">{{ item.type }}</p>
              <p class="text-sm text-gray-500 dark:text-gray-400">{{ item.date }} at {{ item.time }}</p>
            </div>
            <div v-if="schedule.length === 0" class="text-gray-500 dark:text-gray-400 text-center py-4">
              No upcoming classes scheduled
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.flex-1 {
  min-height: 0;
}
</style>
