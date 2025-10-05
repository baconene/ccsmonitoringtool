<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref, onMounted, computed } from 'vue';

// Props
interface Props {
  course: Course;
}

const props = defineProps<Props>();

// TypeScript interfaces
interface Lesson {
  id: number;
  title: string;
  description: string;
  duration: number; // in minutes
  order: number;
  is_completed: boolean;
  completed_at: string | null;
  content_type: 'video' | 'text' | 'quiz' | 'assignment';
  module_name?: string;
}

interface Course {
  id: number;
  title: string;
  description: string;
  instructor_name: string;
  progress: number;
  is_completed: boolean;
  enrolled_at: string;
  total_lessons: number;
  completed_lessons: number;
  lessons: Lesson[];
}

// Reactive state
const course = ref<Course | null>(props.course);
const loading = ref(false);
const error = ref<string | null>(null);
const completingLesson = ref<number | null>(null);

// Load course details
const loadCourse = async () => {
  loading.value = true;
  error.value = null;
  
  try {
    // This will be populated by the server-side controller
    // The course data is passed as a prop from the server
    // For now, we'll use the course data from the page props
    
  } catch (err) {
    error.value = 'Failed to load course details';
    console.error('Error:', err);
  } finally {
    loading.value = false;
  }
};

// Mark lesson as completed
const markLessonComplete = async (lessonId: number) => {
  if (completingLesson.value) return;
  
  completingLesson.value = lessonId;
  
  try {
    const response = await fetch(`/student/courses/${course.value?.id}/lessons/${lessonId}/complete`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
      },
    });
    
    if (!response.ok) {
      throw new Error('Failed to complete lesson');
    }
    
    const data = await response.json();
    
    // Update lesson completion status
    if (course.value) {
      const lesson = course.value.lessons.find(l => l.id === lessonId);
      if (lesson && !lesson.is_completed) {
        lesson.is_completed = true;
        lesson.completed_at = new Date().toISOString();
        
        // Update course statistics
        course.value.completed_lessons++;
        course.value.progress = data.progress || Math.round((course.value.completed_lessons / course.value.total_lessons) * 100);
        
        if (course.value.progress >= 100) {
          course.value.is_completed = true;
        }
      }
    }
    
  } catch (err) {
    console.error('Error completing lesson:', err);
    error.value = 'Failed to mark lesson as complete. Please try again.';
    setTimeout(() => {
      error.value = null;
    }, 5000);
  } finally {
    completingLesson.value = null;
  }
};

// Computed properties
const groupedLessons = computed(() => {
  if (!course.value) return {};
  
  return course.value.lessons.reduce((groups, lesson) => {
    const module = lesson.module_name || 'General';
    if (!groups[module]) {
      groups[module] = [];
    }
    groups[module].push(lesson);
    return groups;
  }, {} as Record<string, Lesson[]>);
});

const totalDuration = computed(() => {
  if (!course.value) return 0;
  return course.value.lessons.reduce((total, lesson) => total + lesson.duration, 0);
});

// Load course on mount
onMounted(() => {
  // Course data is already available from props
  // loadCourse();
});

// Get content type icon and color
const getContentTypeInfo = (type: string) => {
  const types = {
    video: { icon: 'M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h8m-9-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z', color: 'text-red-600 dark:text-red-400', bg: 'bg-red-100 dark:bg-red-900/50' },
    text: { icon: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', color: 'text-blue-600 dark:text-blue-400', bg: 'bg-blue-100 dark:bg-blue-900/50' },
    quiz: { icon: 'M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z', color: 'text-green-600 dark:text-green-400', bg: 'bg-green-100 dark:bg-green-900/50' },
    assignment: { icon: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', color: 'text-purple-600 dark:text-purple-400', bg: 'bg-purple-100 dark:bg-purple-900/50' }
  };
  return types[type as keyof typeof types] || types.text;
};

// Format duration
const formatDuration = (minutes: number) => {
  if (minutes < 60) return `${minutes}m`;
  const hours = Math.floor(minutes / 60);
  const remainingMinutes = minutes % 60;
  return `${hours}h ${remainingMinutes}m`;
};
</script>

<template>
  <Head :title="course?.title || 'Course Details'" />

  <AppLayout>
    <div class="p-6 min-h-screen bg-gray-50 dark:bg-gray-900">
      <div class="max-w-7xl mx-auto">
        
        <!-- Back Button -->
        <div class="mb-6">
          <Link 
            href="/student/courses"
            class="inline-flex items-center text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-colors"
          >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Back to My Courses
          </Link>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="flex justify-center items-center py-12">
          <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 dark:border-blue-400"></div>
          <span class="ml-3 text-gray-600 dark:text-gray-300">Loading course details...</span>
        </div>

        <!-- Error State -->
        <div v-else-if="error" class="bg-red-50 dark:bg-red-900/50 border border-red-200 dark:border-red-700 rounded-lg p-4 mb-6">
          <div class="flex">
            <svg class="w-5 h-5 text-red-400 dark:text-red-300 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
            </svg>
            <div class="ml-3">
              <h3 class="text-sm font-medium text-red-800 dark:text-red-200">Error</h3>
              <p class="text-sm text-red-700 dark:text-red-300 mt-1">{{ error }}</p>
              <button @click="loadCourse" class="mt-2 text-sm text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-200 font-medium">
                Try again
              </button>
            </div>
          </div>
        </div>

        <!-- Course Content -->
        <div v-else-if="course">
          <!-- Course Header -->
          <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md border border-gray-200 dark:border-gray-700 p-6 mb-6">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
              <div class="flex-1">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">{{ course.title }}</h1>
                <p class="text-gray-600 dark:text-gray-300 mb-4">{{ course.description }}</p>
                
                <div class="flex items-center text-sm text-gray-500 dark:text-gray-400 mb-4">
                  <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                  </svg>
                  Instructor: {{ course.instructor_name }}
                </div>

                <div class="flex items-center space-x-6 text-sm text-gray-600 dark:text-gray-400">
                  <span>{{ course.completed_lessons }} / {{ course.total_lessons }} lessons completed</span>
                  <span>{{ formatDuration(totalDuration) }} total</span>
                  <span>Enrolled: {{ new Date(course.enrolled_at).toLocaleDateString() }}</span>
                </div>
              </div>

              <div class="lg:ml-6 mt-4 lg:mt-0">
                <div class="text-right mb-2">
                  <span class="text-2xl font-bold text-gray-900 dark:text-white">{{ course.progress }}%</span>
                  <span class="text-sm text-gray-500 dark:text-gray-400 ml-1">Complete</span>
                </div>
                <div class="w-48 bg-gray-200 dark:bg-gray-700 rounded-full h-3">
                  <div 
                    class="bg-blue-600 h-3 rounded-full transition-all duration-300"
                    :style="`width: ${course.progress}%`"
                  ></div>
                </div>
              </div>
            </div>
          </div>

          <!-- Lessons by Module -->
          <div class="space-y-6">
            <div v-for="(lessons, moduleName) in groupedLessons" :key="moduleName" class="bg-white dark:bg-gray-800 rounded-lg shadow-md border border-gray-200 dark:border-gray-700">
              <!-- Module Header -->
              <div class="bg-gray-50 dark:bg-gray-700/50 px-6 py-4 border-b border-gray-200 dark:border-gray-600 rounded-t-lg">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ moduleName }}</h2>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                  {{ lessons.filter((l: Lesson) => l.is_completed).length }} / {{ lessons.length }} lessons completed
                </p>
              </div>

              <!-- Lessons List -->
              <div class="divide-y divide-gray-200 dark:divide-gray-600">
                <div
                  v-for="lesson in lessons"
                  :key="lesson.id"
                  class="p-6 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors"
                >
                  <div class="flex items-start justify-between">
                    <div class="flex-1">
                      <div class="flex items-center mb-2">
                        <!-- Content Type Icon -->
                        <div :class="`p-2 rounded-full mr-3 ${getContentTypeInfo(lesson.content_type).bg}`">
                          <svg :class="`w-4 h-4 ${getContentTypeInfo(lesson.content_type).color}`" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="getContentTypeInfo(lesson.content_type).icon" />
                          </svg>
                        </div>

                        <div class="flex-1">
                          <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ lesson.title }}</h3>
                          <p class="text-sm text-gray-600 dark:text-gray-400">{{ lesson.description }}</p>
                        </div>
                      </div>

                      <div class="flex items-center space-x-4 text-sm text-gray-500 dark:text-gray-400 mt-2">
                        <span class="capitalize">{{ lesson.content_type }}</span>
                        <span>{{ formatDuration(lesson.duration) }}</span>
                        <span v-if="lesson.is_completed && lesson.completed_at" class="text-green-600 dark:text-green-400">
                          ✓ Completed {{ new Date(lesson.completed_at).toLocaleDateString() }}
                        </span>
                      </div>
                    </div>

                    <!-- Action Button -->
                    <div class="ml-6 flex-shrink-0">
                      <button
                        v-if="!lesson.is_completed"
                        @click="markLessonComplete(lesson.id)"
                        :disabled="completingLesson === lesson.id"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                      >
                        <span v-if="completingLesson === lesson.id" class="flex items-center">
                          <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                          </svg>
                          Completing...
                        </span>
                        <span v-else>Mark Complete</span>
                      </button>
                      <div v-else class="inline-flex items-center px-4 py-2 text-sm font-medium text-green-700 dark:text-green-300 bg-green-100 dark:bg-green-900/50 rounded-md">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Completed
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Course Completion Message -->
          <div v-if="course.is_completed" class="bg-green-50 dark:bg-green-900/50 border border-green-200 dark:border-green-700 rounded-lg p-6 mt-6">
            <div class="flex items-center">
              <svg class="w-6 h-6 text-green-600 dark:text-green-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <div>
                <h3 class="text-lg font-medium text-green-800 dark:text-green-200">Congratulations!</h3>
                <p class="text-green-700 dark:text-green-300 mt-1">You have successfully completed this course. Great job on your learning journey!</p>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </AppLayout>
</template>