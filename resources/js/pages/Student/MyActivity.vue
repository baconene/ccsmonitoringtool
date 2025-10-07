<template>
  <AppLayout title="My Activities">
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
          <div class="flex justify-between items-center">
            <div>
              <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">My Activities</h1>
              <p class="mt-2 text-gray-600 dark:text-gray-400">
                Track your assignments, quizzes, and upcoming deadlines
              </p>
            </div>
            
            <!-- Filters -->
            <div class="flex gap-4">
              <select 
                v-model="selectedCourse" 
                @change="filterActivities"
                class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"
              >
                <option value="">All Courses</option>
                <option v-for="course in courses" :key="course.id" :value="course.id">
                  {{ course.title }}
                </option>
              </select>
              
              <select 
                v-model="selectedStatus" 
                @change="filterActivities"
                class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"
              >
                <option value="">All Status</option>
                <option value="not-taken">Not Taken</option>
                <option value="in-progress">In Progress</option>
                <option value="completed">Completed</option>
              </select>
            </div>
          </div>
          
          <!-- Summary Stats -->
          <div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
              <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Activities</div>
              <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ filteredActivities.length }}</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
              <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Not Taken</div>
              <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ getStatusCount('not-taken') }}</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
              <div class="text-sm font-medium text-gray-500 dark:text-gray-400">In Progress</div>
              <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ getStatusCount('in-progress') }}</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
              <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Completed</div>
              <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ getStatusCount('completed') }}</div>
            </div>
          </div>
        </div>

        <!-- Activities Grid -->
        <div v-if="filteredActivities.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div 
            v-for="activity in filteredActivities" 
            :key="activity.id"
            class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow"
          >
            <!-- Activity Header -->
            <div class="p-6">
              <div class="flex justify-between items-start mb-4">
                <div class="flex-1">
                  <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">
                    <Link 
                      :href="`/student/activities/${activity.id}`"
                      class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors"
                    >
                      {{ activity.title }}
                    </Link>
                  </h3>
                  <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                    {{ activity.description }}
                  </p>
                </div>
                
                <!-- Status Badge -->
                <span 
                  :class="getStatusBadgeClass(activity.status)"
                  class="px-2 py-1 text-xs font-medium rounded-full flex-shrink-0 ml-3"
                >
                  {{ getStatusText(activity.status) }}
                </span>
              </div>

              <!-- Metadata Links -->
              <div class="space-y-2 mb-4">
                <div class="flex items-center text-sm">
                  <BookOpen class="h-4 w-4 text-gray-400 mr-2" />
                  <Link 
                    :href="`/student/courses/${activity.course_id}`"
                    class="text-blue-600 dark:text-blue-400 hover:underline"
                  >
                    {{ activity.course_name }}
                  </Link>
                </div>
                
                <div class="flex items-center text-sm">
                  <Folder class="h-4 w-4 text-gray-400 mr-2" />
                  <Link 
                    :href="`/student/courses/${activity.course_id}#module-${activity.module_id}`"
                    class="text-blue-600 dark:text-blue-400 hover:underline"
                  >
                    {{ activity.module_name }}
                  </Link>
                </div>
                
                <div class="flex items-center text-sm">
                  <Calendar class="h-4 w-4 text-gray-400 mr-2" />
                  <span 
                    :class="activity.is_past_due && activity.status !== 'completed' 
                      ? 'text-red-600 dark:text-red-400 font-medium' 
                      : 'text-gray-600 dark:text-gray-400'"
                  >
                    Due: {{ activity.due_date_formatted }}
                    <span v-if="activity.is_past_due && activity.status !== 'completed'" class="ml-1">
                      (Overdue)
                    </span>
                  </span>
                </div>
              </div>

              <!-- Activity Details -->
              <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                <div class="flex justify-between items-center text-sm text-gray-600 dark:text-gray-400">
                  <div class="flex items-center">
                    <ClipboardList class="h-4 w-4 mr-1" />
                    <span>{{ activity.activity_type }}</span>
                  </div>
                  
                  <div class="flex items-center space-x-4">
                    <div v-if="activity.question_count > 0" class="flex items-center">
                      <HelpCircle class="h-4 w-4 mr-1" />
                      <span>{{ activity.question_count }} questions</span>
                    </div>
                    
                    <div v-if="activity.total_points > 0" class="flex items-center">
                      <Star class="h-4 w-4 mr-1" />
                      <span>{{ activity.total_points }} pts</span>
                    </div>
                  </div>
                </div>

                <!-- Progress Information -->
                <div v-if="activity.progress && activity.status !== 'not-taken'" class="mt-3">
                  <div class="text-sm text-gray-600 dark:text-gray-400 mb-1">
                    Progress: {{ activity.progress.completed_questions }}/{{ activity.progress.total_questions }} questions
                  </div>
                  <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                    <div 
                      class="bg-blue-600 h-2 rounded-full transition-all duration-300"
                      :style="{ width: `${(activity.progress.completed_questions / activity.progress.total_questions) * 100}%` }"
                    ></div>
                  </div>
                  <div v-if="activity.status === 'completed'" class="mt-2 text-sm">
                    <span class="text-green-600 dark:text-green-400 font-medium">
                      Score: {{ activity.progress.percentage_score }}%
                    </span>
                  </div>
                </div>
              </div>

              <!-- Action Button -->
              <div class="mt-4">
                <Link 
                  :href="getActivityLink(activity)"
                  :class="getActionButtonClass(activity.status)"
                  class="w-full flex items-center justify-center px-4 py-2 rounded-lg text-sm font-medium transition-colors"
                >
                  <component :is="getActionIcon(activity.status)" class="h-4 w-4 mr-2" />
                  {{ getActionText(activity.status) }}
                </Link>
              </div>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-else class="text-center py-12">
          <ClipboardList class="h-12 w-12 text-gray-400 mx-auto mb-4" />
          <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">No activities found</h3>
          <p class="text-gray-600 dark:text-gray-400">
            {{ selectedCourse || selectedStatus 
              ? 'Try adjusting your filters to see more activities.' 
              : 'You don\'t have any activities assigned yet.' }}
          </p>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { 
  BookOpen, 
  Folder, 
  Calendar, 
  ClipboardList, 
  HelpCircle, 
  Star,
  Play,
  RotateCcw,
  CheckCircle
} from 'lucide-vue-next';

interface Activity {
  id: number;
  title: string;
  description: string;
  activity_type: string;
  course_id: number;
  course_name: string;
  module_id: number;
  module_name: string;
  due_date: string;
  due_date_formatted: string;
  status: 'not-taken' | 'in-progress' | 'completed';
  is_past_due: boolean;
  progress?: {
    score: number;
    percentage_score: number;
    completed_questions: number;
    total_questions: number;
  };
  question_count: number;
  total_points: number;
}

interface Course {
  id: number;
  title: string;
}

const props = defineProps<{
  activities: Activity[];
  courses: Course[];
  filters: {
    course_id: string | null;
    status: string | null;
  };
}>();

const selectedCourse = ref(props.filters.course_id || '');
const selectedStatus = ref(props.filters.status || '');

const filteredActivities = computed(() => {
  let filtered = props.activities;
  
  if (selectedCourse.value) {
    filtered = filtered.filter(activity => activity.course_id.toString() === selectedCourse.value);
  }
  
  if (selectedStatus.value) {
    filtered = filtered.filter(activity => activity.status === selectedStatus.value);
  }
  
  return filtered;
});

const getStatusCount = (status: string) => {
  return filteredActivities.value.filter(activity => activity.status === status).length;
};

const getStatusBadgeClass = (status: string) => {
  switch (status) {
    case 'completed':
      return 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400';
    case 'in-progress':
      return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400';
    case 'not-taken':
    default:
      return 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
  }
};

const getStatusText = (status: string) => {
  switch (status) {
    case 'completed':
      return 'Completed';
    case 'in-progress':
      return 'In Progress';
    case 'not-taken':
    default:
      return 'Not Taken';
  }
};

const getActionButtonClass = (status: string) => {
  switch (status) {
    case 'completed':
      return 'bg-green-600 hover:bg-green-700 text-white';
    case 'in-progress':
      return 'bg-yellow-600 hover:bg-yellow-700 text-white';
    case 'not-taken':
    default:
      return 'bg-blue-600 hover:bg-blue-700 text-white';
  }
};

const getActionText = (status: string) => {
  switch (status) {
    case 'completed':
      return 'View Results';
    case 'in-progress':
      return 'Continue';
    case 'not-taken':
    default:
      return 'Start Activity';
  }
};

const getActionIcon = (status: string) => {
  switch (status) {
    case 'completed':
      return CheckCircle;
    case 'in-progress':
      return RotateCcw;
    case 'not-taken':
    default:
      return Play;
  }
};

const getActivityLink = (activity: Activity) => {
  if (activity.activity_type === 'Quiz') {
    if (activity.status === 'completed') {
      // For completed quizzes, link to results page (would need quiz progress ID)
      return `/student/quiz/start/${activity.id}`;
    } else {
      // For not taken or in-progress quizzes, start/continue quiz
      return `/student/quiz/start/${activity.id}`;
    }
  }
  
  // For other activity types, link to activity details
  return `/student/activities/${activity.id}`;
};

const filterActivities = () => {
  const params = new URLSearchParams();
  
  if (selectedCourse.value) {
    params.set('course_id', selectedCourse.value);
  }
  
  if (selectedStatus.value) {
    params.set('status', selectedStatus.value);
  }
  
  const queryString = params.toString();
  const newUrl = queryString ? `/student/activities?${queryString}` : '/student/activities';
  
  router.get(newUrl, {}, {
    preserveState: true,
    preserveScroll: true,
  });
};
</script>