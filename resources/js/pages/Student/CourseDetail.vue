<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { Module, Activity, Quiz, StudentQuizProgress } from '@/types';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { BookOpen, CheckCircle2, Clock, Trophy, PlayCircle, FileText, ClipboardList, AlertCircle, XCircle } from 'lucide-vue-next';
import { useNotification } from '@/composables/useNotification';
import Notification from '@/components/Notification.vue';

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
    modules: (Module & {
      lessons: Lesson[];
      activities: (Activity & {
        time_limit?: number;
        question_count?: number;
        total_points?: number;
        due_date?: string;
        is_past_due?: boolean;
        quiz?: Quiz & {
          question_count: number;
          total_points: number;
        };
        quiz_progress?: StudentQuizProgress | null;
      })[];
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

// Notification
const { notification, showNotification } = useNotification();

// State
const completingLesson = ref<number | null>(null);
const completingModule = ref<number | null>(null);

// Computed properties
const totalActivities = computed(() => {
  return props.course.modules.reduce((total: number, module: any) => 
    total + module.activities.length, 0
  );
});

const completedActivities = computed(() => {
  return props.course.modules.reduce((total: number, module: any) => {
    const completed = module.activities.filter((activity: any) => 
      activity.quiz_progress?.is_completed
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
    const completed = module.lessons?.filter((lesson: any) => lesson.is_completed).length || 0;
    return total + completed;
  }, 0);
});

// Check if a module can be completed (all activities and lessons must be completed)
const canCompleteModule = (moduleId: number) => {
  const module = props.course.modules.find(m => m.id === moduleId);
  if (!module) return false;
  
  // Check if all activities are completed
  const allActivitiesCompleted = module.activities.every((activity: any) => 
    activity.quiz_progress?.is_completed === true
  );
  
  // Check if all lessons are completed
  const allLessonsCompleted = module.lessons?.every((lesson: any) => 
    lesson.is_completed === true
  ) ?? true; // If no lessons, consider them completed
  
  return allActivitiesCompleted && allLessonsCompleted;
};

// Get completion status message for a module
const getModuleCompletionStatus = (moduleId: number) => {
  const module = props.course.modules.find(m => m.id === moduleId);
  if (!module) return { canComplete: false, message: 'Module not found' };
  
  const incompleteActivities = module.activities.filter((activity: any) => 
    !activity.quiz_progress?.is_completed
  );
  const incompleteLessons = module.lessons?.filter((lesson: any) => 
    !lesson.is_completed
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
                        (module.lessons?.every((l: any) => l.is_completed) ?? true) 
                          ? 'text-green-600 dark:text-green-400' 
                          : 'text-amber-600 dark:text-amber-400'
                      ]">
                        <CheckCircle2 v-if="(module.lessons?.every((l: any) => l.is_completed) ?? true)" class="w-3 h-3 mr-1" />
                        <AlertCircle v-else class="w-3 h-3 mr-1" />
                        {{ module.lessons?.filter((l: any) => l.is_completed).length || 0 }} / {{ module.lessons?.length || 0 }}
                      </span>
                    </span>
                    <span class="flex items-center">
                      <FileText class="w-3 h-3 mr-1" />
                      <span class="font-medium mr-1">Activities:</span>
                      <span class="flex items-center" :class="[
                        module.activities.every((a: any) => a.quiz_progress?.is_completed) 
                          ? 'text-green-600 dark:text-green-400' 
                          : 'text-amber-600 dark:text-amber-400'
                      ]">
                        <CheckCircle2 v-if="module.activities.every((a: any) => a.quiz_progress?.is_completed)" class="w-3 h-3 mr-1" />
                        <AlertCircle v-else class="w-3 h-3 mr-1" />
                        {{ module.activities.filter((a: any) => a.quiz_progress?.is_completed).length }} / {{ module.activities.length }}
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
                      v-if="!lesson.is_completed"
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

            <!-- Activities (Quizzes) -->
            <div v-if="module.activities && module.activities.length > 0">
              <h3 class="text-md font-semibold text-gray-900 dark:text-white mb-3">Quizzes</h3>
              <div class="space-y-4">
                <div
                  v-for="activity in module.activities"
                  :key="activity.id"
                  :class="[
                    'border rounded-lg p-5 transition-all duration-200',
                    (activity as any).quiz_progress?.is_completed 
                      ? 'border-green-200 dark:border-green-700 bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20'
                      : 'border-amber-200 dark:border-amber-700 bg-gradient-to-r from-amber-50 to-orange-50 dark:from-amber-900/20 dark:to-orange-900/20 ring-2 ring-amber-200 dark:ring-amber-700/50'
                  ]"
                >
                  <div class="flex items-start justify-between">
                    <div class="flex-1">
                      <div class="flex items-center mb-2">
                        <FileText class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-2" />
                        <h4 class="text-base font-semibold text-gray-900 dark:text-white">{{ activity.title }}</h4>
                      </div>
                      <div 
                        class="text-sm text-gray-600 dark:text-gray-400 mb-3 prose prose-sm max-w-none dark:prose-invert"
                        v-html="activity.description"
                      ></div>
                      
                      <div class="flex items-center space-x-4 text-sm text-gray-600 dark:text-gray-400 mb-3">
                        <span>{{ activity.question_count }} questions</span>
                        <span>{{ activity.total_points }} points</span>
                        <span v-if="activity.time_limit">{{ activity.time_limit }} minutes</span>
                      </div>

                      <!-- Quiz Status Badge -->
                      <div class="inline-flex items-center">
                        <component 
                          :is="getQuizStatusBadge(activity).component"
                          :class="getQuizStatusBadge(activity).class"
                          class="w-4 h-4 mr-1"
                        />
                        <span :class="getQuizStatusBadge(activity).textClass" class="text-sm font-medium">
                          {{ getQuizStatusBadge(activity).text }}
                        </span>
                      </div>
                    </div>

                    <!-- Action Button -->
                    <div class="ml-4">
                      <button
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
  </AppLayout>
</template>