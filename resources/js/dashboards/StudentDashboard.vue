<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { usePage, router } from '@inertiajs/vue3';
import { BookOpen, Clock, AlertCircle, Calendar, ChevronLeft, ChevronRight } from 'lucide-vue-next';
import axios from 'axios';

// TypeScript interfaces
interface EnrolledCourse {
  id: number;
  title: string;
  instructor: string;
  progress: number;
  nextClass: string;
}

interface Assignment {
  id: number;
  title: string;
  course: string;
  courseId?: number;
  moduleId?: number;
  dueDate: string;
  status: 'pending' | 'completed' | 'overdue';
  activityType: string;
  activityTypeId: number;
}

interface Grade {
  course: string;
  assignment: string;
  score: number;
}

interface ScheduleItem {
  id: number;
  title: string;
  type: string;
  date: string;
  time: string;
  location: string;
}

interface DashboardStats {
  totalCourses: number;
  incompleteActivities: number;
  scheduledCourses: number;
  gradeAverage: number;
}

// Get authenticated user from Inertia page props
const page = usePage();
const user = page.props.auth.user;

// Reactive state for student-specific data
const enrolledCourses = ref<EnrolledCourse[]>([]);
const assignments = ref<Assignment[]>([]);
const overdueActivities = ref<Assignment[]>([]);
const grades = ref<Grade[]>([]);
const schedule = ref<ScheduleItem[]>([]);
const dashboardStats = ref<DashboardStats | null>(null);
const loading = ref(true);
const error = ref<string | null>(null);

// Pagination state for courses
const currentPage = ref(1);
const itemsPerPage = 5;

// Computed properties for student stats  
const totalCourses = computed(() => dashboardStats.value?.totalCourses || enrolledCourses.value.length);
const incompleteActivities = computed(() => dashboardStats.value?.incompleteActivities || 0);
const scheduledCourses = computed(() => dashboardStats.value?.scheduledCourses || 0);
const gradeAverage = computed(() => dashboardStats.value?.gradeAverage || 0);

// Paginated courses
const totalPages = computed(() => Math.ceil(enrolledCourses.value.length / itemsPerPage));
const paginatedCourses = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage;
  const end = start + itemsPerPage;
  return enrolledCourses.value.slice(start, end);
});

// Pagination functions
const goToPage = (page: number) => {
  if (page >= 1 && page <= totalPages.value) {
    currentPage.value = page;
  }
};

const nextPage = () => {
  if (currentPage.value < totalPages.value) {
    currentPage.value++;
  }
};

const prevPage = () => {
  if (currentPage.value > 1) {
    currentPage.value--;
  }
};

// Load real student data from API
const loadStudentData = async () => {
  loading.value = true;
  error.value = null;

  try {
    const [statsResponse, dataResponse] = await Promise.all([
      axios.get('/api/dashboard/stats'),
      axios.get('/api/dashboard/student-data')
    ]);
    
    dashboardStats.value = statsResponse.data;
    const data = dataResponse.data;
    
    // Update reactive data with API response
    enrolledCourses.value = data.enrolledCourses || [];
    assignments.value = data.assignments || [];
    overdueActivities.value = data.overdueActivities || [];
    grades.value = data.grades || [];
    schedule.value = data.schedule || [];

  } catch (err: any) {
    error.value = 'Failed to load student data';
    console.error('Error loading student data:', err);
    
    // Fallback to empty arrays
    enrolledCourses.value = [];
    assignments.value = [];
    overdueActivities.value = [];
    grades.value = [];
    schedule.value = [];
  } finally {
    loading.value = false;
  }
};

// Navigation functions
const navigateToCourse = (courseId: number) => {
  router.visit(`/student/courses/${courseId}`);
};

const navigateToActivity = (activity: Assignment) => {
  if (!activity.courseId || !activity.moduleId) {
    console.error('Missing course or module ID');
    return;
  }
  
  // Navigate based on activity type
  const activityTypeName = activity.activityType.toLowerCase();
  if (activityTypeName.includes('quiz')) {
    router.visit(`/student/courses/${activity.courseId}/modules/${activity.moduleId}/quiz/${activity.id}`);
  } else if (activityTypeName.includes('assignment')) {
    router.visit(`/student/courses/${activity.courseId}/modules/${activity.moduleId}/assignment/${activity.id}`);
  } else {
    router.visit(`/student/courses/${activity.courseId}/modules/${activity.moduleId}`);
  }
};

// Load data on component mount
onMounted(() => {
  loadStudentData();
});

// Refresh data function
const refreshData = async () => {
  await loadStudentData();
};

// Expose refresh function for parent components
defineExpose({
  refreshData
});
</script>

<template>
  <div class="student-dashboard">
    <!-- Welcome Header -->
    <div class="mb-6">
      <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">
        Welcome back, {{ user?.name || 'Student' }}!
      </h1>
      <p class="text-sm sm:text-base text-gray-600 dark:text-gray-300 mt-2">Here's your academic progress and upcoming activities.</p>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="flex justify-center items-center py-12">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 dark:border-blue-400"></div>
      <span class="ml-3 text-gray-600 dark:text-gray-300">Loading your dashboard...</span>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="bg-red-50 dark:bg-red-900/50 border border-red-200 dark:border-red-700 rounded-lg p-4 mb-6">
      <div class="flex">
        <AlertCircle class="w-5 h-5 text-red-400 dark:text-red-300 mt-0.5 flex-shrink-0" />
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
    <div v-else class="grid grid-cols-1 xs:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
      <!-- Total Courses -->
      <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm rounded-xl shadow-lg border border-blue-200/50 dark:border-blue-700/50 p-3 sm:p-4 transition-all hover:shadow-xl">
        <div class="flex items-center gap-2 sm:gap-3">
          <div class="p-1.5 sm:p-2 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-lg flex-shrink-0">
            <BookOpen class="w-4 h-4 sm:w-5 sm:h-5 text-white" />
          </div>
          <div class="min-w-0 flex-1">
            <p class="text-xs font-medium text-gray-600 dark:text-gray-300 truncate">Enrolled Courses</p>
            <p class="text-lg sm:text-xl lg:text-2xl font-bold bg-gradient-to-r from-blue-600 to-cyan-600 bg-clip-text text-transparent">{{ totalCourses }}</p>
          </div>
        </div>
      </div>

      <!-- Incomplete Activities -->
      <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm rounded-xl shadow-lg border border-orange-200/50 dark:border-orange-700/50 p-3 sm:p-4 transition-all hover:shadow-xl">
        <div class="flex items-center gap-2 sm:gap-3">
          <div class="p-1.5 sm:p-2 bg-gradient-to-br from-orange-500 to-amber-500 rounded-lg flex-shrink-0">
            <Clock class="w-4 h-4 sm:w-5 sm:h-5 text-white" />
          </div>
          <div class="min-w-0 flex-1">
            <p class="text-xs font-medium text-gray-600 dark:text-gray-300 truncate">Incomplete</p>
            <p class="text-lg sm:text-xl lg:text-2xl font-bold bg-gradient-to-r from-orange-600 to-amber-600 bg-clip-text text-transparent">{{ incompleteActivities }}</p>
          </div>
        </div>
      </div>

      <!-- Scheduled Courses -->
      <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm rounded-xl shadow-lg border border-purple-200/50 dark:border-purple-700/50 p-3 sm:p-4 transition-all hover:shadow-xl">
        <div class="flex items-center gap-2 sm:gap-3">
          <div class="p-1.5 sm:p-2 bg-gradient-to-br from-purple-500 to-pink-500 rounded-lg flex-shrink-0">
            <Calendar class="w-4 h-4 sm:w-5 sm:h-5 text-white" />
          </div>
          <div class="min-w-0 flex-1">
            <p class="text-xs font-medium text-gray-600 dark:text-gray-300 truncate">Scheduled</p>
            <p class="text-lg sm:text-xl lg:text-2xl font-bold bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent">{{ scheduledCourses }}</p>
          </div>
        </div>
      </div>

      <!-- Grade Average -->
      <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm rounded-xl shadow-lg border border-green-200/50 dark:border-green-700/50 p-3 sm:p-4 transition-all hover:shadow-xl">
        <div class="flex items-center gap-2 sm:gap-3">
          <div class="p-1.5 sm:p-2 bg-gradient-to-br from-green-500 to-emerald-500 rounded-lg flex-shrink-0">
            <svg class="w-4 h-4 sm:w-5 sm:h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
            </svg>
          </div>
          <div class="min-w-0 flex-1">
            <p class="text-xs font-medium text-gray-600 dark:text-gray-300 truncate">Grade Avg</p>
            <p class="text-lg sm:text-xl lg:text-2xl font-bold bg-gradient-to-r from-green-600 to-emerald-600 bg-clip-text text-transparent">
              {{ gradeAverage > 0 ? gradeAverage + '%' : 'N/A' }}
            </p>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-4 sm:gap-6 mt-6">
      <!-- Enrolled Courses -->
      <div class="xl:col-span-2 space-y-4 sm:space-y-6">
        <!-- Courses Section -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md border border-gray-200 dark:border-gray-700 p-4 sm:p-6 transition-colors">
          <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg sm:text-xl font-semibold text-gray-900 dark:text-white">My Courses</h2>
            <span class="text-xs sm:text-sm text-gray-500 dark:text-gray-400">{{ enrolledCourses.length }} total</span>
          </div>
          
          <div v-if="enrolledCourses.length === 0" class="text-center py-8">
            <BookOpen class="w-12 h-12 text-gray-400 dark:text-gray-500 mx-auto mb-3" />
            <p class="text-gray-500 dark:text-gray-400">You're not enrolled in any courses yet.</p>
          </div>
          
          <div v-else class="space-y-3 sm:space-y-4">
            <div
              v-for="course in paginatedCourses"
              :key="course.id"
              @click="navigateToCourse(course.id)"
              class="border border-gray-200 dark:border-gray-600 rounded-lg p-3 sm:p-4 hover:shadow-md dark:hover:shadow-lg transition-all cursor-pointer bg-gradient-to-br from-gray-50 to-white dark:from-gray-700 dark:to-gray-750 hover:scale-[1.02]"
            >
              <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start mb-2 gap-2">
                <div class="flex-1 min-w-0">
                  <h3 class="font-semibold text-base sm:text-lg text-gray-900 dark:text-white truncate">{{ course.title }}</h3>
                  <p class="text-gray-600 dark:text-gray-300 text-xs sm:text-sm">Instructor: {{ course.instructor }}</p>
                </div>
                <span class="text-xs sm:text-sm text-blue-600 dark:text-blue-400 font-medium whitespace-nowrap">{{ course.progress }}% Complete</span>
              </div>
              
              <!-- Progress Bar -->
              <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-2 mb-3">
                <div 
                  class="bg-gradient-to-r from-blue-500 to-cyan-500 h-2 rounded-full transition-all duration-300" 
                  :style="`width: ${course.progress}%`"
                ></div>
              </div>
              
              <div class="flex items-center justify-end text-xs sm:text-sm">
                <span class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 font-medium whitespace-nowrap">
                  View Course →
                </span>
              </div>
            </div>
          </div>
          
          <!-- Pagination -->
          <div v-if="totalPages > 1" class="mt-4 flex items-center justify-between border-t border-gray-200 dark:border-gray-700 pt-4">
            <button
              @click="prevPage"
              :disabled="currentPage === 1"
              class="flex items-center gap-1 px-3 py-1.5 text-sm text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
            >
              <ChevronLeft class="w-4 h-4" />
              <span class="hidden sm:inline">Previous</span>
            </button>
            
            <div class="flex items-center gap-2">
              <button
                v-for="page in totalPages"
                :key="page"
                @click="goToPage(page)"
                :class="[
                  'px-3 py-1.5 text-sm rounded-lg transition-colors',
                  page === currentPage
                    ? 'bg-blue-600 text-white'
                    : 'text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600'
                ]"
              >
                {{ page }}
              </button>
            </div>
            
            <button
              @click="nextPage"
              :disabled="currentPage === totalPages"
              class="flex items-center gap-1 px-3 py-1.5 text-sm text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
            >
              <span class="hidden sm:inline">Next</span>
              <ChevronRight class="w-4 h-4" />
            </button>
          </div>
        </div>
        
        <!-- Overdue Activities -->
        <div v-if="overdueActivities.length > 0" class="bg-white dark:bg-gray-800 rounded-lg shadow-md border border-red-200 dark:border-red-700 p-4 sm:p-6 transition-colors">
          <h2 class="text-lg sm:text-xl font-semibold text-red-600 dark:text-red-400 mb-4 flex items-center gap-2">
            <AlertCircle class="w-5 h-5" />
            Overdue Activities
          </h2>
          <div class="space-y-3">
            <div
              v-for="activity in overdueActivities"
              :key="activity.id"
              @click="navigateToActivity(activity)"
              class="border-l-4 border-red-500 dark:border-red-400 pl-3 sm:pl-4 py-2 bg-red-50 dark:bg-red-900/20 rounded-r-md cursor-pointer hover:bg-red-100 dark:hover:bg-red-900/30 transition-colors"
            >
              <div class="flex flex-wrap items-start justify-between mb-1 gap-2">
                <p class="font-medium text-gray-900 dark:text-white text-sm sm:text-base flex-1 min-w-0">{{ activity.title }}</p>
                <span class="px-2 py-0.5 text-xs font-medium bg-red-100 dark:bg-red-900/50 text-red-800 dark:text-red-200 rounded-full whitespace-nowrap flex-shrink-0">
                  {{ activity.activityType }}
                </span>
              </div>
              <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-300">{{ activity.course }}</p>
              <p class="text-xs sm:text-sm text-red-600 dark:text-red-400 font-medium">Due: {{ activity.dueDate }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Assignments & Schedule Sidebar -->
      <div class="xl:col-span-1 space-y-4 sm:space-y-6">
        <!-- Pending Activities -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md border border-gray-200 dark:border-gray-700 p-4 sm:p-6 transition-colors">
          <h2 class="text-lg sm:text-xl font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
            <Clock class="w-5 h-5" />
            Pending Activities
          </h2>
          <div class="space-y-3 max-h-96 overflow-y-auto">
            <div
              v-for="assignment in assignments.filter((a: Assignment) => a.status === 'pending')"
              :key="assignment.id"
              @click="navigateToActivity(assignment)"
              class="border-l-4 border-orange-500 dark:border-orange-400 pl-3 sm:pl-4 py-2 cursor-pointer hover:bg-orange-50 dark:hover:bg-orange-900/20 transition-colors rounded-r-md"
            >
              <div class="flex flex-wrap items-start justify-between mb-1 gap-2">
                <p class="font-medium text-gray-900 dark:text-white text-sm sm:text-base flex-1 min-w-0">{{ assignment.title }}</p>
                <span class="px-2 py-0.5 text-xs font-medium bg-blue-100 dark:bg-blue-900/50 text-blue-800 dark:text-blue-200 rounded-full whitespace-nowrap flex-shrink-0">
                  {{ assignment.activityType }}
                </span>
              </div>
              <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-300 truncate">{{ assignment.course }}</p>
              <p class="text-xs sm:text-sm text-orange-600 dark:text-orange-400">Due: {{ assignment.dueDate }}</p>
            </div>
            <div v-if="assignments.filter((a: Assignment) => a.status === 'pending').length === 0" class="text-gray-500 dark:text-gray-400 text-center py-8 text-sm">
              <Clock class="w-10 h-10 mx-auto mb-2 opacity-50" />
              <p>No pending activities</p>
            </div>
          </div>
        </div>

        <!-- Upcoming Schedule -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md border border-gray-200 dark:border-gray-700 p-4 sm:p-6 transition-colors">
          <h2 class="text-lg sm:text-xl font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
            <Calendar class="w-5 h-5" />
            Upcoming Schedule
          </h2>
          <div class="space-y-3">
            <div
              v-for="item in schedule"
              :key="item.id"
              class="border-l-4 border-purple-500 dark:border-purple-400 pl-3 sm:pl-4 py-2 bg-purple-50 dark:bg-purple-900/20 rounded-r-md"
            >
              <p class="font-medium text-gray-900 dark:text-white text-sm sm:text-base">{{ item.title }}</p>
              <p class="text-xs sm:text-sm text-purple-600 dark:text-purple-400 font-medium">{{ item.type }}</p>
              <div class="flex items-center gap-1 text-xs sm:text-sm text-gray-600 dark:text-gray-300 mt-1">
                <Calendar class="w-3 h-3" />
                <span>{{ item.date }}</span>
              </div>
              <div class="flex items-center gap-1 text-xs sm:text-sm text-gray-600 dark:text-gray-300">
                <Clock class="w-3 h-3" />
                <span>{{ item.time }}</span>
              </div>
              <p v-if="item.location" class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 flex items-center gap-1 mt-1">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                {{ item.location }}
              </p>
            </div>
            <div v-if="schedule.length === 0" class="text-gray-500 dark:text-gray-400 text-center py-8 text-sm">
              <Calendar class="w-10 h-10 mx-auto mb-2 opacity-50" />
              <p>No upcoming schedule</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.student-dashboard {
  min-height: 0;
}
</style>