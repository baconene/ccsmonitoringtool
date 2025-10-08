<template>
  <AppLayout :title="`${module.title} - ${course.title}`">
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Breadcrumb -->
        <nav class="flex mb-8" aria-label="Breadcrumb">
          <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
              <Link 
                :href="`/student/courses`"
                class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white"
              >
                <BookOpen class="w-3 h-3 mr-2.5" />
                My Courses
              </Link>
            </li>
            <li>
              <div class="flex items-center">
                <ChevronRight class="w-3 h-3 text-gray-400 mx-1" />
                <Link 
                  :href="`/student/courses/${course.id}`"
                  class="ml-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ml-2 dark:text-gray-400 dark:hover:text-white"
                >
                  {{ course.title }}
                </Link>
              </div>
            </li>
            <li aria-current="page">
              <div class="flex items-center">
                <ChevronRight class="w-3 h-3 text-gray-400 mx-1" />
                <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2 dark:text-gray-400">
                  {{ module.title }}
                </span>
              </div>
            </li>
          </ol>
        </nav>

        <!-- Module Header -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 mb-8">
          <div class="p-6">
            <div class="flex justify-between items-start">
              <div class="flex-1">
                <div class="flex items-center mb-4">
                  <div class="p-3 bg-blue-100 dark:bg-blue-900/30 rounded-lg mr-4">
                    <FolderOpen class="h-8 w-8 text-blue-600 dark:text-blue-400" />
                  </div>
                  <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">
                      {{ module.title }}
                    </h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-1">
                      {{ course.title }} • Module {{ module.module_percentage }}% weight
                    </p>
                  </div>
                </div>
                
                <p class="text-gray-700 dark:text-gray-300 mb-6">
                  {{ module.description }}
                </p>

                <!-- Progress Stats -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                  <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Progress</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                      {{ stats.completion_percentage }}%
                    </div>
                  </div>
                  <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Lessons</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                      {{ stats.completed_lessons }}/{{ stats.total_lessons }}
                    </div>
                  </div>
                  <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Activities</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                      {{ stats.completed_activities }}/{{ stats.total_activities }}
                    </div>
                  </div>
                  <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</div>
                    <div class="flex items-center">
                      <span 
                        :class="module.is_completed 
                          ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' 
                          : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400'"
                        class="px-2 py-1 text-xs font-medium rounded-full"
                      >
                        {{ module.is_completed ? 'Completed' : 'In Progress' }}
                      </span>
                    </div>
                  </div>
                </div>

                <!-- Progress Bar -->
                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3 mb-6">
                  <div 
                    class="bg-blue-600 h-3 rounded-full transition-all duration-300"
                    :style="{ width: `${stats.completion_percentage}%` }"
                  ></div>
                </div>
              </div>

              <!-- Action Buttons -->
              <div class="ml-6 flex-shrink-0">
                <div v-if="!module.is_completed" class="space-y-2">
                  <button
                    v-if="canMarkModuleComplete"
                    @click="markModuleComplete"
                    :disabled="markingComplete"
                    class="w-full flex items-center justify-center px-4 py-2 bg-green-600 hover:bg-green-700 disabled:bg-gray-400 text-white font-medium rounded-lg transition-colors"
                  >
                    <CheckCircle2 class="h-4 w-4 mr-2" />
                    {{ markingComplete ? 'Marking Complete...' : 'Mark Complete' }}
                  </button>
                  <div v-else class="text-sm text-gray-500 dark:text-gray-400 text-center max-w-48">
                    Complete all lessons and activities to mark this module as complete
                  </div>
                </div>
                <div v-else class="flex items-center text-green-600 dark:text-green-400">
                  <CheckCircle2 class="h-5 w-5 mr-2" />
                  <span class="font-medium">Module Completed</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Module Documents -->
        <DocumentSection
          v-if="module.documents.length > 0"
          :documents="module.documents"
          title="Module Documents"
          class="mb-8"
        />

        <!-- Content Sections -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
          <!-- Lessons Section -->
          <LessonSection
            :lessons="module.lessons"
            :course-id="course.id"
          />

          <!-- Activities Section -->
          <ActivitySection
            :activities="module.activities"
          />
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import DocumentSection from '@/components/Student/DocumentSection.vue';
import LessonSection from '@/components/Student/LessonSection.vue';
import ActivitySection from '@/components/Student/ActivitySection.vue';
import { 
  BookOpen, 
  ChevronRight, 
  FolderOpen, 
  CheckCircle2
} from 'lucide-vue-next';

interface Course {
  id: number;
  title: string;
  description: string;
  instructor_name: string;
  progress: number;
  is_completed: boolean;
  enrolled_at: string;
}

interface Lesson {
  id: number;
  title: string;
  description: string;
  duration: number;
  order: number;
  content_type: string;
  is_completed: boolean;
  completed_at: string | null;
  documents?: Document[];
}

interface Activity {
  id: number;
  title: string;
  description: string;
  activity_type: string;
  question_count: number;
  total_points: number;
  is_completed: boolean;
  quiz_progress?: {
    id: number;
    is_completed: boolean;
    is_submitted: boolean;
    score: number;
    percentage_score: number;
    completed_questions: number;
    total_questions: number;
  } | null;
}

interface Document {
  id: number;
  name: string;
  file_path: string;
  doc_type: string;
}

interface Module {
  id: number;
  title: string;
  description: string;
  module_type: string;
  module_percentage: number;
  is_completed: boolean;
  completed_at: string | null;
  can_mark_complete: boolean;
  lessons: Lesson[];
  activities: Activity[];
  documents: Document[];
}

interface Stats {
  total_lessons: number;
  completed_lessons: number;
  total_activities: number;
  completed_activities: number;
  completion_percentage: number;
}

const props = defineProps<{
  course: Course;
  module: Module;
  stats: Stats;
}>();

const markingComplete = ref(false);

const sortedLessons = computed(() => {
  return [...props.module.lessons].sort((a, b) => a.order - b.order);
});

const canMarkModuleComplete = computed(() => {
  // All lessons must be completed
  const allLessonsComplete = props.module.lessons.every(lesson => lesson.is_completed);
  
  // All activities must be completed
  const allActivitiesComplete = props.module.activities.every(activity => activity.is_completed);
  
  return allLessonsComplete && allActivitiesComplete;
});

const formatDate = (dateString: string) => {
  return new Date(dateString).toLocaleDateString();
};

const markModuleComplete = async () => {
  if (!canMarkModuleComplete.value) return;
  
  markingComplete.value = true;
  
  try {
    router.post(`/student/courses/${props.course.id}/modules/${props.module.id}/complete`);
  } catch (error) {
    console.error('Error marking module complete:', error);
  } finally {
    markingComplete.value = false;
  }
};


</script>