<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import axios from 'axios';
import { BookOpen, Users, Activity, Calendar, Clock, AlertCircle } from 'lucide-vue-next';

// Get authenticated user from Inertia page props
const page = usePage();
const user = page.props.auth.user;

// Interfaces
interface Course {
  id: number;
  title: string;
  description: string;
  students_count: number;
  activities_count: number;
}

interface ScheduleItem {
  id: number;
  title: string;
  date: string;
  time: string;
  location?: string;
}

interface DashboardStats {
  totalCourses: number;
  totalStudents: number;
  totalActivities: number;
  upcomingSchedules: number;
  pendingReviews: string;
}

// Reactive state
const stats = ref<DashboardStats>({
  totalCourses: 0,
  totalStudents: 0,
  totalActivities: 0,
  upcomingSchedules: 0,
  pendingReviews: 'N/A'
});

const courses = ref<Course[]>([]);
const schedule = ref<ScheduleItem[]>([]);
const loading = ref(true);
const error = ref<string | null>(null);

// Computed properties
const totalCourses = computed(() => stats.value.totalCourses);
const totalStudents = computed(() => stats.value.totalStudents);
const totalActivities = computed(() => stats.value.totalActivities);
const upcomingSchedules = computed(() => stats.value.upcomingSchedules);
const pendingReviews = computed(() => stats.value.pendingReviews);

// Load instructor data
const loadInstructorData = async () => {
  loading.value = true;
  error.value = null;

  try {
    // Fetch both stats and detailed data in parallel
    const [statsResponse, dataResponse] = await Promise.all([
      axios.get('/api/dashboard/stats'),
      axios.get('/api/dashboard/instructor-data')
    ]);

    stats.value = statsResponse.data;
    courses.value = dataResponse.data.courses || [];
    schedule.value = dataResponse.data.schedule || [];
  } catch (err: any) {
    console.error('Error loading instructor dashboard:', err);
    error.value = err.response?.data?.message || 'Failed to load dashboard data';
  } finally {
    loading.value = false;
  }
};

// Navigate to course management with prepopulated course
const navigateToCourse = (courseId: number) => {
  router.visit(`/course-management?course=${courseId}`);
};

// Load data on component mount
onMounted(() => {
  loadInstructorData();
});
</script>

<template>
  <div class="min-h-screen bg-gradient-to-br from-purple-50 via-pink-50 to-blue-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 p-4 sm:p-6 lg:p-8">
    <!-- Welcome Header -->
    <div class="mb-8">
      <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold bg-gradient-to-r from-purple-600 via-pink-600 to-blue-600 bg-clip-text text-transparent">
        Welcome back, {{ user?.name || 'Instructor' }}! 👋
      </h1>
      <p class="text-gray-600 dark:text-gray-300 mt-2 text-sm sm:text-base">Manage your courses and track student progress</p>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="flex justify-center items-center py-20">
      <div class="animate-spin rounded-full h-16 w-16 border-b-4 border-purple-600"></div>
      <span class="ml-4 text-gray-600 dark:text-gray-300 text-lg">Loading dashboard...</span>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="bg-red-50/80 dark:bg-red-900/20 backdrop-blur-sm border border-red-200 dark:border-red-700 rounded-xl p-6 mb-6">
      <div class="flex items-start gap-4">
        <AlertCircle class="w-6 h-6 text-red-500 flex-shrink-0 mt-0.5" />
        <div class="flex-1">
          <h3 class="text-lg font-semibold text-red-800 dark:text-red-200">Error loading dashboard</h3>
          <p class="text-red-700 dark:text-red-300 mt-2">{{ error }}</p>
          <button 
            @click="loadInstructorData" 
            class="mt-4 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors font-medium"
          >
            Try Again
          </button>
        </div>
      </div>
    </div>

    <!-- Dashboard Content -->
    <div v-else>
      <!-- Stats Grid -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 sm:gap-6 mb-8">
        <!-- Courses Handling -->
        <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm rounded-xl shadow-lg border border-purple-200/50 dark:border-purple-700/50 p-5 sm:p-6 transition-all hover:shadow-xl hover:scale-105">
          <div class="flex items-center gap-4">
            <div class="p-3 bg-gradient-to-br from-purple-500 to-pink-500 rounded-xl shadow-lg">
              <BookOpen class="w-6 h-6 sm:w-7 sm:h-7 text-white" />
            </div>
            <div>
              <p class="text-xs sm:text-sm font-medium text-gray-600 dark:text-gray-300">Courses Handling</p>
              <p class="text-2xl sm:text-3xl font-bold bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent">
                {{ totalCourses }}
              </p>
            </div>
          </div>
        </div>

        <!-- Unique Students -->
        <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm rounded-xl shadow-lg border border-blue-200/50 dark:border-blue-700/50 p-5 sm:p-6 transition-all hover:shadow-xl hover:scale-105">
          <div class="flex items-center gap-4">
            <div class="p-3 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-xl shadow-lg">
              <Users class="w-6 h-6 sm:w-7 sm:h-7 text-white" />
            </div>
            <div>
              <p class="text-xs sm:text-sm font-medium text-gray-600 dark:text-gray-300">Unique Students</p>
              <p class="text-2xl sm:text-3xl font-bold bg-gradient-to-r from-blue-600 to-cyan-600 bg-clip-text text-transparent">
                {{ totalStudents }}
              </p>
            </div>
          </div>
        </div>

        <!-- Total Activities -->
        <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm rounded-xl shadow-lg border border-green-200/50 dark:border-green-700/50 p-5 sm:p-6 transition-all hover:shadow-xl hover:scale-105">
          <div class="flex items-center gap-4">
            <div class="p-3 bg-gradient-to-br from-green-500 to-emerald-500 rounded-xl shadow-lg">
              <Activity class="w-6 h-6 sm:w-7 sm:h-7 text-white" />
            </div>
            <div>
              <p class="text-xs sm:text-sm font-medium text-gray-600 dark:text-gray-300">Total Activities</p>
              <p class="text-2xl sm:text-3xl font-bold bg-gradient-to-r from-green-600 to-emerald-600 bg-clip-text text-transparent">
                {{ totalActivities }}
              </p>
            </div>
          </div>
        </div>

        <!-- Upcoming Schedules -->
        <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm rounded-xl shadow-lg border border-pink-200/50 dark:border-pink-700/50 p-5 sm:p-6 transition-all hover:shadow-xl hover:scale-105">
          <div class="flex items-center gap-4">
            <div class="p-3 bg-gradient-to-br from-pink-500 to-rose-500 rounded-xl shadow-lg">
              <Calendar class="w-6 h-6 sm:w-7 sm:h-7 text-white" />
            </div>
            <div>
              <p class="text-xs sm:text-sm font-medium text-gray-600 dark:text-gray-300">Upcoming Schedules</p>
              <p class="text-2xl sm:text-3xl font-bold bg-gradient-to-r from-pink-600 to-rose-600 bg-clip-text text-transparent">
                {{ upcomingSchedules }}
              </p>
            </div>
          </div>
        </div>

        <!-- Pending Reviews -->
        <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm rounded-xl shadow-lg border border-indigo-200/50 dark:border-indigo-700/50 p-5 sm:p-6 transition-all hover:shadow-xl hover:scale-105">
          <div class="flex items-center gap-4">
            <div class="p-3 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl shadow-lg">
              <Clock class="w-6 h-6 sm:w-7 sm:h-7 text-white" />
            </div>
            <div>
              <p class="text-xs sm:text-sm font-medium text-gray-600 dark:text-gray-300">Pending Reviews</p>
              <p class="text-2xl sm:text-3xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                {{ pendingReviews }}
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- Main Content Grid -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 sm:gap-8">
        <!-- Courses List -->
        <div class="lg:col-span-2">
          <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm rounded-xl shadow-lg border border-gray-200/50 dark:border-gray-700/50 p-6">
            <h2 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
              <BookOpen class="w-6 h-6 text-purple-600" />
              Your Courses
            </h2>
            
            <div v-if="courses.length === 0" class="text-center py-12">
              <BookOpen class="w-16 h-16 text-gray-300 dark:text-gray-600 mx-auto mb-4" />
              <p class="text-gray-500 dark:text-gray-400">No courses assigned yet</p>
            </div>

            <div v-else class="space-y-4">
              <div
                v-for="course in courses"
                :key="course.id"
                @click="navigateToCourse(course.id)"
                class="group bg-gradient-to-r from-purple-50 to-pink-50 dark:from-gray-700 dark:to-gray-700 rounded-xl p-5 border border-purple-200/50 dark:border-purple-700/50 hover:shadow-lg hover:scale-[1.02] transition-all cursor-pointer"
              >
                <h3 class="text-lg sm:text-xl font-semibold text-gray-900 dark:text-white mb-2 group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors">
                  {{ course.title }}
                </h3>
                <p class="text-sm text-gray-600 dark:text-gray-300 mb-4 line-clamp-2">
                  {{ course.description }}
                </p>
                <div class="flex flex-wrap items-center gap-4 text-sm">
                  <span class="flex items-center gap-1 text-gray-700 dark:text-gray-300">
                    <Users class="w-4 h-4" />
                    <span class="font-medium">{{ course.students_count }}</span> students
                  </span>
                  <span class="flex items-center gap-1 text-gray-700 dark:text-gray-300">
                    <Activity class="w-4 h-4" />
                    <span class="font-medium">{{ course.activities_count }}</span> activities
                  </span>
                  <span class="ml-auto text-purple-600 dark:text-purple-400 font-medium group-hover:translate-x-1 transition-transform">
                    View Details →
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Schedule Sidebar -->
        <div class="lg:col-span-1">
          <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm rounded-xl shadow-lg border border-gray-200/50 dark:border-gray-700/50 p-6">
            <h2 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
              <Calendar class="w-6 h-6 text-purple-600" />
              Upcoming Schedule
            </h2>
            
            <div v-if="schedule.length === 0" class="text-center py-12">
              <Calendar class="w-16 h-16 text-gray-300 dark:text-gray-600 mx-auto mb-4" />
              <p class="text-gray-500 dark:text-gray-400 text-sm">No upcoming schedules</p>
            </div>

            <div v-else class="space-y-4 max-h-96 overflow-y-auto">
              <div
                v-for="item in schedule"
                :key="item.id"
                class="bg-gradient-to-r from-purple-50 to-pink-50 dark:from-gray-700 dark:to-gray-700 rounded-lg p-4 border-l-4 border-purple-500 dark:border-purple-400"
              >
                <p class="font-semibold text-gray-900 dark:text-white mb-2 text-sm sm:text-base truncate">
                  {{ item.title }}
                </p>
                <div class="flex items-center gap-2 text-xs sm:text-sm text-gray-600 dark:text-gray-300 mb-1">
                  <Calendar class="w-4 h-4 text-purple-600" />
                  <span>{{ item.date }}</span>
                </div>
                <div class="flex items-center gap-2 text-xs sm:text-sm text-gray-600 dark:text-gray-300 mb-1">
                  <Clock class="w-4 h-4 text-purple-600" />
                  <span>{{ item.time }}</span>
                </div>
                <div v-if="item.location" class="text-xs text-gray-500 dark:text-gray-400 mt-2 truncate">
                  📍 {{ item.location }}
                </div>
              </div>
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
