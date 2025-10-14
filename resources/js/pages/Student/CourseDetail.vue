<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { Module, Activity, Quiz, StudentQuizProgress } from '@/types';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { BookOpen, CheckCircle2, Clock, Trophy, PlayCircle, FileText, ClipboardList, AlertCircle, XCircle, HelpCircle, Star, Tag, Book } from 'lucide-vue-next';
import { useNotification } from '@/composables/useNotification';
import Notification from '@/components/Notification.vue';
import DocumentViewer from '@/components/DocumentViewer.vue';

// Additional type interfaces
interface Lesson {
  id: number;
  title: string;
  description: string;
  duration?: number;
  content_type?: string;
  is_completed?: boolean;
  completed_at?: string | null;
}

interface ModuleDocument {
  id: number;
  name: string;
  original_name: string;
  file_path: string;
  file_size: number;
  file_size_human: string;
  mime_type: string;
  extension: string;
  document_type: string;
  file_url: string;
  uploaded_by: string;
  created_at: string;
  visibility: string;
  is_required: boolean;
}

interface Course {
  id: number;
  title: string;
  description: string;
  instructor_name: string;
  enrolled_at?: string;
  progress?: number;
  total_modules?: number;
  completed_modules?: number;
}

// Props
interface Props {
  course: Course & {
    lessons?: Lesson[];
    modules: (Module & {
      lessons: Lesson[];
        activities: (Activity & {
        time_limit?: number;
        question_count?: number;
        total_points?: number;
        due_date?: string;
        is_past_due?: boolean;
        activity_type?: string;
        is_completed?: boolean;
        quiz?: Quiz & {
          question_count: number;
          total_points: number;
        };
        quiz_progress?: StudentQuizProgress | null;
      })[];
      documents?: ModuleDocument[];
      is_completed?: boolean;
      completed_at?: string | null;
    })[];
  };
  enrollment: {
    is_completed: boolean;
    created_at: string;
  };
}

const props = defineProps<Props>();

// Debug: Check all activities
// Debug removed - activity segregation is working correctly

// Notification
const { notification, showNotification } = useNotification();

// State
const completingLesson = ref<number | null>(null);
const completingModule = ref<number | null>(null);
const completingActivity = ref<number | null>(null);
const viewingDocument = ref<ModuleDocument | null>(null);
const isDocumentViewerOpen = ref(false);

// Computed properties
const totalActivities = computed(() => {
  return props.course.modules.reduce((total: number, module: any) => 
    total + module.activities.length, 0
  );
});

const completedActivities = computed(() => {
  return props.course.modules.reduce((total: number, module: any) => {
    const completed = module.activities.filter((activity: any) => 
      activity.quiz_progress?.is_completed || activity.is_completed
    ).length;
    return total + completed;
  }, 0);
});

const totalLessons = computed(() => {
  return props.course.modules.reduce((total: number, module: any) => 
    total + (module.lessons?.length || 0), 0
  );
});

const completedLessons = computed(() => {
  return props.course.modules.reduce((total: number, module: any) => {
    const completed = module.lessons?.filter((lesson: any) => isLessonCompleted(lesson)).length || 0;
    return total + completed;
  }, 0);
});

// Check if a module can be completed (all activities and lessons must be completed)
const canCompleteModule = (moduleId: number) => {
  const module = props.course.modules.find(m => m.id === moduleId);
  if (!module) return false;
  
  // Check if all activities are completed
  const allActivitiesCompleted = module.activities.every((activity: any) => 
    isActivityCompleted(activity)
  );
  
  // Check if all lessons are completed using the same logic as isLessonCompleted
  const allLessonsCompleted = module.lessons?.every((lesson: any) => 
    isLessonCompleted(lesson)
  ) ?? true; // If no lessons, consider them completed
  
  return allActivitiesCompleted && allLessonsCompleted;
};

// Get completion status message for a module
const getModuleCompletionStatus = (moduleId: number) => {
  const module = props.course.modules.find(m => m.id === moduleId);
  if (!module) return { canComplete: false, message: 'Module not found' };
  
  const incompleteActivities = module.activities.filter((activity: any) => 
    !isActivityCompleted(activity)
  );
  const incompleteLessons = module.lessons?.filter((lesson: any) => 
    !isLessonCompleted(lesson)
  ) || [];
  
  if (incompleteActivities.length === 0 && incompleteLessons.length === 0) {
    return { canComplete: true, message: 'All requirements completed' };
  }
  
  const messages = [];
  if (incompleteActivities.length > 0) {
    messages.push(`${incompleteActivities.length} activity(s) incomplete`);
  }
  if (incompleteLessons.length > 0) {
    messages.push(`${incompleteLessons.length} lesson(s) incomplete`);
  }
  
  return { 
    canComplete: false, 
    message: `Complete remaining: ${messages.join(', ')}` 
  };
};

// Helper function to check if lesson is completed with flexible data types
const isLessonCompleted = (lesson: Lesson): boolean => {
  if (!lesson) return false;
  
  // Handle boolean, string, or number values
  if (typeof lesson.is_completed === 'boolean') {
    return lesson.is_completed;
  }
  if (typeof lesson.is_completed === 'string') {
    return lesson.is_completed === 'true' || lesson.is_completed === '1';
  }
  if (typeof lesson.is_completed === 'number') {
    return lesson.is_completed === 1;
  }
  
  // If is_completed is undefined, check in the course-level lessons array
  if (lesson.is_completed === undefined) {
    const courseLesson = props.course.lessons?.find((l: any) => l.id === lesson.id);
    if (courseLesson) {
      if (typeof courseLesson.is_completed === 'boolean') {
        return courseLesson.is_completed;
      }
      if (typeof courseLesson.is_completed === 'string') {
        return courseLesson.is_completed === 'true' || courseLesson.is_completed === '1';
      }
      if (typeof courseLesson.is_completed === 'number') {
        return courseLesson.is_completed === 1;
      }
      return !!courseLesson.completed_at;
    }
  }
  
  // Also check if completed_at exists as fallback
  return !!lesson.completed_at;
};

// Mark lesson as complete
const markLessonComplete = (lessonId: number) => {
  completingLesson.value = lessonId;
  
  router.post(`/student/courses/${props.course.id}/lessons/${lessonId}/complete`, {}, {
    preserveScroll: true,
    onSuccess: () => {
      completingLesson.value = null;
    },
    onError: (errors) => {
      console.error('Failed to mark lesson as complete:', errors);
      completingLesson.value = null;
    }
  });
};

// Mark module as complete
const markModuleComplete = (moduleId: number) => {
  // Check if module can be completed before making the request
  if (!canCompleteModule(moduleId)) {
    const status = getModuleCompletionStatus(moduleId);
    alert(`Cannot complete module: ${status.message}`);
    return;
  }
  
  completingModule.value = moduleId;
  
  router.post(`/student/courses/${props.course.id}/modules/${moduleId}/complete`, {}, {
    preserveScroll: true,
    onSuccess: () => {
      completingModule.value = null;
    },
    onError: (errors) => {
      console.error('Failed to mark module as complete:', errors);
      completingModule.value = null;
    }
  });
};

// Quiz button helpers
const getQuizButtonText = (activity: Activity & { quiz_progress?: StudentQuizProgress | null }) => {
  const progress = activity.quiz_progress;
  if (!progress) return 'Start Quiz';
  if (progress.is_completed) return 'Review Results';
  return 'Continue Quiz';
};

const getQuizButtonVariant = (activity: Activity & { quiz_progress?: StudentQuizProgress | null }) => {
  const progress = activity.quiz_progress;
  if (!progress) return 'default';
  if (progress.is_completed) return 'outline';
  return 'secondary';
};

const getQuizStatusBadge = (activity: Activity & { quiz_progress?: StudentQuizProgress | null }) => {
  const progress = activity.quiz_progress;
  if (!progress) {
    return { 
      text: 'Not Started', 
      component: Clock,
      class: 'text-gray-600 dark:text-gray-400',
      textClass: 'text-gray-700 dark:text-gray-300'
    };
  }
  if (progress.is_completed) {
    const score = Number(progress.percentage_score) || 0;
    const passed = score >= 70;
    return { 
      text: `${score.toFixed(0)}% (${progress.score}/${activity.total_points || 0} pts)`, 
      component: passed ? CheckCircle2 : Trophy,
      class: passed ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400',
      textClass: passed ? 'text-green-700 dark:text-green-300' : 'text-red-700 dark:text-red-300'
    };
  }
  return { 
    text: `In Progress (${progress.completed_questions}/${progress.total_questions})`, 
    component: Clock,
    class: 'text-blue-600 dark:text-blue-400',
    textClass: 'text-blue-700 dark:text-blue-300'
  };
};

const handleQuizClick = (activity: Activity & { quiz_progress?: StudentQuizProgress | null; is_past_due?: boolean }) => {
  // Check if activity is overdue and not yet completed
  if (activity.is_past_due && !activity.quiz_progress?.is_completed) {
    showNotification('error', 'This activity is overdue and can no longer be submitted.');
    return;
  }

  const progress = activity.quiz_progress;
  if (progress?.is_completed) {
    router.visit(`/student/quiz/${progress.id}/results`);
  } else {
    router.visit(`/student/quiz/start/${activity.id}`);
  }
};

const getActivityIcon = (activityType: string) => {
  switch(activityType) {
    case 'quiz': return Trophy;
    case 'assignment': return FileText;
    case 'discussion': return ClipboardList;
    default: return BookOpen;
  }
};

const formatDuration = (minutes: number) => {
  if (minutes < 60) return `${minutes}min`;
  const hours = Math.floor(minutes / 60);
  const remainingMinutes = minutes % 60;
  return `${hours}h ${remainingMinutes}m`;
};

// Activity helper functions
const isActivityCompleted = (activity: any) => {
  return activity.quiz_progress?.is_completed || activity.is_completed;
};

const getActivitiesByType = (activities: any[], type: string) => {
  return activities.filter(activity => activity.activity_type === type);
};

const getCompletedActivitiesByType = (activities: any[], type: string) => {
  return activities.filter(activity => 
    activity.activity_type === type && isActivityCompleted(activity)
  );
};

const getOtherActivities = (activities: any[]) => {
  return activities.filter(activity => 
    !['Quiz', 'Assignment'].includes(activity.activity_type)
  );
};

const getCompletedOtherActivities = (activities: any[]) => {
  return activities.filter(activity => 
    !['Quiz', 'Assignment'].includes(activity.activity_type) && isActivityCompleted(activity)
  );
};

const getActivityStatusText = (activity: any) => {
  // For quiz-based activities, show quiz progress score
  if (activity.quiz_progress?.percentage_score !== undefined) {
    const score = Number(activity.quiz_progress.percentage_score) || 0;
    const passed = score >= 70;
    return `${score.toFixed(0)}% (${activity.quiz_progress.score}/${activity.total_points || 0} pts)`;
  }
  
  // For non-quiz activities (like Assignments), show student activity score
  if (activity.student_activity?.percentage_score !== undefined) {
    const score = Number(activity.student_activity.percentage_score) || 0;
    // For activities with 0 points, don't show point breakdown
    if (activity.total_points === 0) {
      return `${score.toFixed(0)}% Complete`;
    }
    return `${score.toFixed(0)}% (${activity.student_activity.score}/${activity.total_points || 0} pts)`;
  }
  
  return 'Completed';
};

// Mark activity as complete
const markActivityComplete = async (activity: any, module: any) => {
  if (completingActivity.value) return;
  
  completingActivity.value = activity.id;
  
  try {
    await router.post(`/student/activities/${activity.id}/mark-complete`, {
      module_id: module.id
    }, {
      preserveScroll: true,
      onSuccess: () => {
        // Update the activity locally to reflect the completion
        activity.is_completed = true;
        completingActivity.value = null;
        showNotification('success', 'Activity marked as complete successfully!');
      },
      onError: (errors) => {
        console.error('Failed to mark activity as complete:', errors);
        showNotification('error', 'Failed to mark activity as complete. Please try again.');
        completingActivity.value = null;
      }
    });
  } catch (error) {
    console.error('Unexpected error:', error);
    showNotification('error', 'An unexpected error occurred. Please try again.');
    completingActivity.value = null;
  }
};

// Document viewer functions
const openDocumentViewer = (doc: ModuleDocument) => {
  viewingDocument.value = doc;
  isDocumentViewerOpen.value = true;
};

const closeDocumentViewer = () => {
  isDocumentViewerOpen.value = false;
  viewingDocument.value = null;
};
</script>

<template>
  <AppLayout>
    <Head :title="course.title" />

    <div class="py-8 px-4 sm:px-6 lg:px-8">
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

            <div class="grid grid-cols-2 gap-4 text-sm text-gray-600 dark:text-gray-400">
              <div>
                <span class="font-medium">Lessons:</span> {{ completedLessons }} / {{ totalLessons }}
              </div>
              <div>
                <span class="font-medium">Activities:</span> {{ completedActivities }} / {{ totalActivities }}
              </div>
              <div class="col-span-2">
                <span class="font-medium">Enrolled:</span> {{ course.enrolled_at ? new Date(course.enrolled_at).toLocaleDateString() : 'N/A' }}
              </div>
            </div>
          </div>

          <div class="lg:ml-6 mt-4 lg:mt-0">
            <div class="text-right mb-2">
              <span class="text-2xl font-bold text-gray-900 dark:text-white">
                {{ Math.round(course.progress || 0) }}%
              </span>
              <span class="text-sm text-gray-500 dark:text-gray-400 ml-1">Complete</span>
            </div>
            <div class="w-48 bg-gray-200 dark:bg-gray-700 rounded-full h-3">
              <div 
                class="bg-blue-600 h-3 rounded-full transition-all duration-300"
                :style="`width: ${course.progress || 0}%`"
              ></div>
            </div>
            <div class="text-sm text-gray-500 dark:text-gray-400 mt-2 text-right">
              {{ course.completed_modules || 0 }} / {{ course.total_modules || 0 }} modules completed
            </div>
          </div>
        </div>
      </div>

      <!-- Modules -->
      <div class="space-y-6">
        <div v-for="module in course.modules" :key="module.id" class="bg-white dark:bg-gray-800 rounded-lg shadow-md border border-gray-200 dark:border-gray-700">
          <!-- Module Header -->
          <div class="bg-gray-50 dark:bg-gray-700/50 px-6 py-4 border-b border-gray-200 dark:border-gray-600 rounded-t-lg">
            <div class="flex items-start justify-between">
              <div class="flex-1">
                <div class="flex items-center gap-2">
                  <Link 
                    :href="`/student/courses/${course.id}/modules/${module.id}`"
                    class="text-lg font-semibold text-gray-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400 transition-colors"
                  >
                    {{ module.title }}
                  </Link>
                  <Badge v-if="module.is_completed" variant="outline" class="bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-300 border-green-200 dark:border-green-800">
                    <CheckCircle2 class="w-3 h-3 mr-1" />
                    Completed
                  </Badge>
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ module.description }}</p>
                
                <!-- Module Progress Indicator -->
                <div class="mt-3 space-y-2">
                  <div class="flex items-center space-x-4 text-sm text-gray-500 dark:text-gray-400">
                    <span class="flex items-center">
                      <BookOpen class="w-3 h-3 mr-1" />
                      <span class="font-medium mr-1">Lessons:</span>
                      <span class="flex items-center" :class="[
                        (module.lessons?.every((l: any) => isLessonCompleted(l)) ?? true) 
                          ? 'text-green-600 dark:text-green-400' 
                          : 'text-amber-600 dark:text-amber-400'
                      ]">
                        <CheckCircle2 v-if="(module.lessons?.every((l: any) => isLessonCompleted(l)) ?? true)" class="w-3 h-3 mr-1" />
                        <AlertCircle v-else class="w-3 h-3 mr-1" />
                        {{ module.lessons?.filter((l: any) => isLessonCompleted(l)).length || 0 }} / {{ module.lessons?.length || 0 }}
                      </span>
                    </span>
                    <span class="flex items-center">
                      <FileText class="w-3 h-3 mr-1" />
                      <span class="font-medium mr-1">Activities:</span>
                      <span class="flex items-center" :class="[
                        module.activities.every((a: any) => a.quiz_progress?.is_completed || a.is_completed) 
                          ? 'text-green-600 dark:text-green-400' 
                          : 'text-amber-600 dark:text-amber-400'
                      ]">
                        <CheckCircle2 v-if="module.activities.every((a: any) => a.quiz_progress?.is_completed || a.is_completed)" class="w-3 h-3 mr-1" />
                        <AlertCircle v-else class="w-3 h-3 mr-1" />
                        {{ module.activities.filter((a: any) => a.quiz_progress?.is_completed || a.is_completed).length }} / {{ module.activities.length }}
                      </span>
                    </span>
                  </div>
                  
                  <!-- Completion Requirement Notice -->
                  <div v-if="!module.is_completed && !canCompleteModule(module.id)" class="flex items-start space-x-2 text-xs text-amber-700 dark:text-amber-300 bg-amber-50 dark:bg-amber-900/20 rounded-md p-2">
                    <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                    <span>Complete all lessons and activities to mark this module as finished</span>
                  </div>
                </div>
              </div>
              <!-- Module Actions -->
              <div class="ml-4 flex flex-col gap-2">
                <div class="flex gap-2">
                  <Link
                    :href="`/student/courses/${course.id}/modules/${module.id}`"
                    class="inline-flex items-center px-3 py-2 text-sm font-medium text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 border border-blue-300 hover:border-blue-400 rounded-md transition-colors"
                  >
                    <BookOpen class="w-4 h-4 mr-2" />
                    View Details
                  </Link>
                  <Button
                    v-if="!module.is_completed"
                    size="sm"
                    :variant="canCompleteModule(module.id) ? 'outline' : 'secondary'"
                    @click="markModuleComplete(module.id)"
                    :disabled="completingModule === module.id || !canCompleteModule(module.id)"
                    :title="!canCompleteModule(module.id) ? getModuleCompletionStatus(module.id).message : ''"
                  >
                    <span v-if="completingModule === module.id">Marking...</span>
                    <span v-else>Mark Module Complete</span>
                  </Button>
                </div>
                <!-- Completion Status Message -->
                <div v-if="!module.is_completed && !canCompleteModule(module.id)" class="text-xs text-amber-600 dark:text-amber-400 mt-1 max-w-48">
                  {{ getModuleCompletionStatus(module.id).message }}
                </div>
              </div>
            </div>
          </div>

          <!-- Module Content -->
          <div class="p-6">
            <!-- Lessons -->
            <div v-if="module.lessons && module.lessons.length > 0" class="mb-6">
              <h3 class="text-md font-semibold text-gray-900 dark:text-white mb-3">Lessons</h3>
              <div class="space-y-3">
                <div
                  v-for="lesson in module.lessons"
                  :key="lesson.id"
                  :class="[
                    'flex items-start justify-between p-4 rounded-lg transition-colors',
                    (lesson as any).is_completed 
                      ? 'bg-green-50 dark:bg-green-900/20 hover:bg-green-100 dark:hover:bg-green-900/30 border border-green-200 dark:border-green-800'
                      : 'bg-amber-50 dark:bg-amber-900/20 hover:bg-amber-100 dark:hover:bg-amber-900/30 border border-amber-200 dark:border-amber-800'
                  ]"
                >
                  <div class="flex-1">
                    <div class="flex items-center mb-2">
                      <BookOpen class="w-4 h-4 mr-2 text-gray-600 dark:text-gray-400" />
                      <h4 class="text-sm font-medium text-gray-900 dark:text-white">{{ lesson.title }}</h4>
                    </div>
                    <div 
                      class="text-xs text-gray-600 dark:text-gray-400 mt-1 prose prose-sm max-w-none dark:prose-invert"
                      v-html="lesson.description"
                    ></div>
                    <div class="flex items-center space-x-3 text-xs text-gray-500 dark:text-gray-400 mt-2">
                      <span class="capitalize">{{ lesson.content_type }}</span>
                      <span v-if="lesson.duration">{{ formatDuration(lesson.duration) }}</span>
                      <span v-if="lesson.is_completed" class="text-green-600 dark:text-green-400 flex items-center">
                        <CheckCircle2 class="w-3 h-3 mr-1" />
                        Completed
                      </span>
                    </div>
                  </div>
                  
                  <!-- Mark Complete Button -->
                  <div class="ml-4">
                    <button
                      v-if="!isLessonCompleted(lesson)"
                      @click="markLessonComplete(lesson.id)"
                      :disabled="completingLesson === lesson.id"
                      class="px-3 py-1.5 text-xs font-medium rounded-md text-white bg-green-600 hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                    >
                      <span v-if="completingLesson === lesson.id">Completing...</span>
                      <span v-else>Mark Complete</span>
                    </button>
                    <div v-else class="px-3 py-1.5 text-xs font-medium rounded-md text-green-700 dark:text-green-300 bg-green-100 dark:bg-green-900/50">
                      <CheckCircle2 class="w-4 h-4" />
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Activities by Type -->
            <div v-if="module.activities && module.activities.length > 0">
              <!-- Quizzes Section -->
              <div v-if="getActivitiesByType(module.activities, 'Quiz').length > 0" class="mb-6">
                <div class="flex items-center mb-4">
                  <Trophy class="h-5 w-5 text-blue-600 dark:text-blue-400 mr-2" />
                  <h3 class="text-md font-semibold text-gray-900 dark:text-white">
                    Quizzes ({{ getCompletedActivitiesByType(module.activities, 'Quiz').length }}/{{ getActivitiesByType(module.activities, 'Quiz').length }})
                  </h3>
                </div>
                <div class="space-y-4">
                  <div
                    v-for="activity in getActivitiesByType(module.activities, 'Quiz')"
                    :key="activity.id"
                    :class="[
                      'border rounded-lg p-5 transition-all duration-200',
                      isActivityCompleted(activity)
                        ? 'border-green-200 dark:border-green-700 bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20'
                        : 'border-blue-200 dark:border-blue-700 bg-gradient-to-r from-blue-50 to-cyan-50 dark:from-blue-900/20 dark:to-cyan-900/20'
                    ]"
                  >
                    <div class="flex items-start justify-between">
                      <div class="flex-1">
                        <div class="flex items-center mb-2">
                          <Trophy class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-2" />
                          <h4 class="text-base font-semibold text-gray-900 dark:text-white">{{ activity.title }}</h4>
                        </div>
                        <div 
                          class="text-sm text-gray-600 dark:text-gray-400 mb-3 prose prose-sm max-w-none dark:prose-invert"
                          v-html="activity.description"
                        ></div>
                        
                        <div class="flex items-center space-x-4 text-sm text-gray-600 dark:text-gray-400 mb-3">
                          <span>{{ activity.question_count || 0 }} questions</span>
                          <span>{{ activity.total_points || 0 }} points</span>
                          <span v-if="activity.time_limit">{{ activity.time_limit }} minutes</span>
                        </div>

                        <!-- Quiz Status Badge -->
                        <div v-if="isActivityCompleted(activity)" class="inline-flex items-center">
                          <CheckCircle2 class="w-4 h-4 mr-1 text-green-600 dark:text-green-400" />
                          <span class="text-sm font-medium text-green-700 dark:text-green-300">
                            {{ getActivityStatusText(activity) }}
                          </span>
                        </div>
                        <div v-else-if="activity.quiz_progress && !activity.quiz_progress.is_completed" class="inline-flex items-center">
                          <Clock class="w-4 h-4 mr-1 text-blue-600 dark:text-blue-400" />
                          <span class="text-sm font-medium text-blue-700 dark:text-blue-300">
                            In Progress ({{ activity.quiz_progress.completed_questions }}/{{ activity.quiz_progress.total_questions }})
                          </span>
                        </div>
                        <div v-else class="inline-flex items-center">
                          <PlayCircle class="w-4 h-4 mr-1 text-gray-600 dark:text-gray-400" />
                          <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Not Started</span>
                        </div>
                      </div>

                      <!-- Action Button -->
                      <div class="ml-4">
                        <!-- Start/Continue/View Quiz Button for quizzes with questions -->
                        <button
                          v-if="activity.question_count && activity.question_count > 0"
                          @click="handleQuizClick(activity)"
                          :class="[
                            'px-4 py-2 rounded-lg font-medium text-sm transition-colors',
                            getQuizButtonVariant(activity) === 'default' 
                              ? 'bg-blue-600 hover:bg-blue-700 text-white' 
                              : getQuizButtonVariant(activity) === 'outline'
                              ? 'border-2 border-blue-600 text-blue-600 hover:bg-blue-50 dark:text-blue-400 dark:border-blue-400 dark:hover:bg-blue-900/20'
                              : 'bg-gray-200 hover:bg-gray-300 text-gray-700 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-300'
                          ]"
                        >
                          {{ getQuizButtonText(activity) }}
                        </button>
                        
                        <!-- Mark as Complete Button for quizzes with 0 questions -->
                        <button
                          v-else-if="!isActivityCompleted(activity)"
                          @click="markActivityComplete(activity, module)"
                          :disabled="completingActivity === activity.id"
                          class="px-4 py-2 text-sm font-medium bg-green-600 hover:bg-green-700 disabled:bg-gray-400 text-white rounded-lg transition-colors"
                        >
                          {{ completingActivity === activity.id ? 'Marking...' : 'Mark as Complete' }}
                        </button>
                        
                        <!-- View Activity Button for completed activities -->
                        <Link
                          v-else
                          :href="`/student/activities/${activity.id}`"
                          class="px-4 py-2 text-sm font-medium bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors inline-block text-center"
                        >
                          View Activity
                        </Link>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Assignments Section -->
              <div v-if="getActivitiesByType(module.activities, 'Assignment').length > 0" class="mb-6">
                <div class="flex items-center mb-4">
                  <FileText class="h-5 w-5 text-orange-600 dark:text-orange-400 mr-2" />
                  <h3 class="text-md font-semibold text-gray-900 dark:text-white">
                    Assignments ({{ getCompletedActivitiesByType(module.activities, 'Assignment').length }}/{{ getActivitiesByType(module.activities, 'Assignment').length }})
                  </h3>
                </div>
                <div class="space-y-4">
                  <div
                    v-for="activity in getActivitiesByType(module.activities, 'Assignment')"
                    :key="activity.id"
                    :class="[
                      'border rounded-lg p-5 transition-all duration-200',
                      isActivityCompleted(activity)
                        ? 'border-green-200 dark:border-green-700 bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20'
                        : 'border-orange-200 dark:border-orange-700 bg-gradient-to-r from-orange-50 to-yellow-50 dark:from-orange-900/20 dark:to-yellow-900/20'
                    ]"
                  >
                    <div class="flex items-start justify-between">
                      <div class="flex-1">
                        <div class="flex items-center mb-2">
                          <FileText class="w-5 h-5 text-orange-600 dark:text-orange-400 mr-2" />
                          <h4 class="text-base font-semibold text-gray-900 dark:text-white">{{ activity.title }}</h4>
                        </div>
                        <div 
                          class="text-sm text-gray-600 dark:text-gray-400 mb-3 prose prose-sm max-w-none dark:prose-invert"
                          v-html="activity.description"
                        ></div>
                        
                        <div class="flex items-center space-x-4 text-sm text-gray-600 dark:text-gray-400 mb-3">
                          <span>{{ activity.question_count || 0 }} questions</span>
                          <span>{{ activity.total_points || 0 }} points</span>
                          <span v-if="activity.time_limit">{{ activity.time_limit }} minutes</span>
                        </div>

                        <!-- Assignment Status -->
                        <div v-if="isActivityCompleted(activity)" class="inline-flex items-center">
                          <CheckCircle2 class="w-4 h-4 mr-1 text-green-600 dark:text-green-400" />
                          <span class="text-sm font-medium text-green-700 dark:text-green-300">Completed</span>
                        </div>
                        <div v-else class="inline-flex items-center">
                          <AlertCircle class="w-4 h-4 mr-1 text-orange-600 dark:text-orange-400" />
                          <span class="text-sm font-medium text-orange-700 dark:text-orange-300">Not Started</span>
                        </div>
                      </div>

                      <!-- Action Button -->
                      <div class="ml-4">
                        <!-- Mark as Complete Button for assignments with 0 questions -->
                        <button
                          v-if="!isActivityCompleted(activity) && (!activity.question_count || activity.question_count === 0)"
                          @click="markActivityComplete(activity, module)"
                          :disabled="completingActivity === activity.id"
                          class="px-4 py-2 text-sm font-medium bg-green-600 hover:bg-green-700 disabled:bg-gray-400 text-white rounded-lg transition-colors"
                        >
                          {{ completingActivity === activity.id ? 'Marking...' : 'Mark as Complete' }}
                        </button>
                        
                        <!-- Completed Badge - No action button for completed assignments -->
                        <div v-else-if="isActivityCompleted(activity)" class="px-4 py-2 text-sm font-medium text-green-700 dark:text-green-300 bg-green-100 dark:bg-green-900/50 rounded-lg">
                          <CheckCircle2 class="w-4 h-4 inline mr-1" />
                          Completed
                        </div>
                        
                        <!-- View Activity Button for assignments with questions -->
                        <Link
                          v-else-if="activity.question_count && activity.question_count > 0"
                          :href="`/student/activities/${activity.id}`"
                          class="px-4 py-2 text-sm font-medium bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors inline-block text-center"
                        >
                          View Activity
                        </Link>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Other Activities Section -->
              <div v-if="getOtherActivities(module.activities).length > 0">
                <div class="flex items-center mb-4">
                  <BookOpen class="h-5 w-5 text-green-600 dark:text-green-400 mr-2" />
                  <h3 class="text-md font-semibold text-gray-900 dark:text-white">
                    Other Activities ({{ getCompletedOtherActivities(module.activities).length }}/{{ getOtherActivities(module.activities).length }})
                  </h3>
                </div>
                <div class="space-y-4">
                  <div
                    v-for="activity in getOtherActivities(module.activities)"
                    :key="activity.id"
                    :class="[
                      'border rounded-lg p-5 transition-all duration-200',
                      isActivityCompleted(activity)
                        ? 'border-green-200 dark:border-green-700 bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20'
                        : 'border-green-200 dark:border-green-700 bg-gradient-to-r from-green-50 to-lime-50 dark:from-green-900/20 dark:to-lime-900/20'
                    ]"
                  >
                    <div class="flex items-start justify-between">
                      <div class="flex-1">
                        <div class="flex items-center mb-2">
                          <BookOpen class="w-5 h-5 text-green-600 dark:text-green-400 mr-2" />
                          <h4 class="text-base font-semibold text-gray-900 dark:text-white">{{ activity.title }}</h4>
                        </div>
                        <div 
                          class="text-sm text-gray-600 dark:text-gray-400 mb-3 prose prose-sm max-w-none dark:prose-invert"
                          v-html="activity.description"
                        ></div>
                        
                        <div class="flex items-center space-x-4 text-sm text-gray-600 dark:text-gray-400 mb-3">
                          <span class="flex items-center">
                            <ClipboardList class="h-3 w-3 mr-1" />
                            {{ activity.activity_type }}
                          </span>
                        </div>

                        <!-- Activity Status -->
                        <div v-if="isActivityCompleted(activity)" class="inline-flex items-center">
                          <CheckCircle2 class="w-4 h-4 mr-1 text-green-600 dark:text-green-400" />
                          <span class="text-sm font-medium text-green-700 dark:text-green-300">Completed</span>
                        </div>
                        <div v-else class="inline-flex items-center">
                          <AlertCircle class="w-4 h-4 mr-1 text-green-600 dark:text-green-400" />
                          <span class="text-sm font-medium text-green-700 dark:text-green-300">Not Started</span>
                        </div>
                      </div>

                      <!-- Action Button -->
                      <div class="ml-4">
                        <!-- Mark as Complete Button for other activities -->
                        <button
                          v-if="!isActivityCompleted(activity)"
                          @click="markActivityComplete(activity, module)"
                          :disabled="completingActivity === activity.id"
                          class="px-4 py-2 text-sm font-medium bg-green-600 hover:bg-green-700 disabled:bg-gray-400 text-white rounded-lg transition-colors"
                        >
                          {{ completingActivity === activity.id ? 'Marking...' : 'Mark as Complete' }}
                        </button>
                        
                        <!-- View Activity Button -->
                        <Link
                          v-else
                          :href="`/student/activities/${activity.id}`"
                          class="px-4 py-2 text-sm font-medium bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors inline-block text-center"
                        >
                          View Activity
                        </Link>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Module Documents Section -->
            <div v-if="module.documents && module.documents.length > 0" class="mt-6">
              <div class="flex items-center mb-4">
                <FileText class="h-5 w-5 text-purple-600 dark:text-purple-400 mr-2" />
                <h3 class="text-md font-semibold text-gray-900 dark:text-white">
                  Module Documents ({{ module.documents.length }})
                </h3>
              </div>
              <div class="space-y-2">
                <div
                  v-for="doc in module.documents"
                  :key="doc.id"
                  class="border border-purple-200 dark:border-purple-700 rounded-lg p-4 hover:bg-purple-50 dark:hover:bg-purple-900/20 transition-colors"
                >
                  <div class="flex items-start justify-between">
                    <div class="flex-1">
                      <div class="flex items-center mb-1">
                        <FileText class="w-4 h-4 text-purple-600 dark:text-purple-400 mr-2" />
                        <h4 class="text-sm font-medium text-gray-900 dark:text-white">{{ doc.name }}</h4>
                      </div>
                      <div class="flex items-center space-x-3 text-xs text-gray-500 dark:text-gray-400">
                        <span>{{ doc.file_size_human }}</span>
                        <span class="uppercase">{{ doc.extension }}</span>
                        <span>Uploaded by {{ doc.uploaded_by }}</span>
                        <span v-if="doc.is_required" class="text-red-600 dark:text-red-400 font-medium">Required</span>
                      </div>
                    </div>
                    <div class="ml-4 flex gap-2">
                      <button 
                        @click="openDocumentViewer(doc)"
                        class="px-3 py-1.5 text-xs font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 transition-colors"
                      >
                        View
                      </button>
                      <a
                        :href="`/documents/${doc.id}/download`"
                        target="_blank"
                        class="px-3 py-1.5 text-xs font-medium rounded-md text-purple-600 dark:text-purple-400 border border-purple-600 dark:border-purple-400 hover:bg-purple-50 dark:hover:bg-purple-900/20 transition-colors"
                      >
                        Download
                      </a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Course Completion Message -->
      <div v-if="completedActivities === totalActivities && totalActivities > 0" class="bg-green-50 dark:bg-green-900/50 border border-green-200 dark:border-green-700 rounded-lg p-6 mt-6">
        <div class="flex items-center">
          <Trophy class="w-6 h-6 text-green-600 dark:text-green-400 mr-3" />
          <div>
            <h3 class="text-lg font-medium text-green-800 dark:text-green-200">Congratulations!</h3>
            <p class="text-green-700 dark:text-green-300 mt-1">You have successfully completed all activities in this course. Great job on your learning journey!</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Notifications -->
    <Notification :notification="notification" />
    
    <!-- Document Viewer -->
    <DocumentViewer
      :isOpen="isDocumentViewerOpen"
      :document="viewingDocument"
      @close="closeDocumentViewer"
    />
  </AppLayout>
</template>