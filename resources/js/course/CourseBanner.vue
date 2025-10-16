<template>
  <div
    class="mb-6 p-6 bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 transition-colors"
  >
    <!-- Header Row: Title + Completion + Action Buttons (Desktop) -->
    <div class="flex flex-col lg:flex-row lg:justify-between lg:items-start gap-4">
      <!-- Left: Title, Grade Levels, and Completion -->
      <div class="flex-1">
        <div class="flex flex-col sm:flex-row sm:items-center gap-3 flex-wrap">
          <!-- Title -->
          <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white">{{ reactiveCourse.name }}</h1>
          
          <!-- Completion (beside title on desktop, below on mobile) -->
          <div class="flex items-center gap-2 text-sm">
            <span class="text-gray-600 dark:text-gray-300">Total Completion:</span>
            <span
              class="flex items-center gap-1 font-semibold"
              :class="totalCompletion === 100 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'"
            >
              <strong>{{ totalCompletion }}</strong>%
            </span>
          </div>
        </div>

        <!-- Grade Levels -->
        <div v-if="reactiveCourse.grade_levels && reactiveCourse.grade_levels.length > 0" class="flex items-center gap-2 flex-wrap mt-2">
          <span 
            v-for="gradeLevel in reactiveCourse.grade_levels" 
            :key="gradeLevel.id"
            class="inline-flex items-center gap-1.5 px-3 py-1 text-sm font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 rounded-full border border-blue-200 dark:border-blue-800"
          >
            <GraduationCap class="w-4 h-4" />
            {{ gradeLevel.display_name }}
          </span>
        </div>
      </div>

      <!-- Right: Action Buttons (Top right on desktop, bottom on mobile) -->
      <div class="flex gap-2 lg:order-last order-last lg:mt-0">
        <!-- Schedule Button - Shows CalendarPlus for create, Edit3 for edit -->
        <button
          @click="handleScheduleButtonClick"
          class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors group"
          :title="hasSchedule ? 'Edit Schedule' : 'Create Schedule'"
        >
          <Edit3 
            v-if="hasSchedule"
            class="h-5 w-5 text-gray-600 dark:text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors" 
          />
          <CalendarPlus 
            v-else
            class="h-5 w-5 text-gray-600 dark:text-gray-400 group-hover:text-green-600 dark:group-hover:text-green-400 transition-colors" 
          />
        </button>
        <button
          @click="$emit('manageStudents')"
          class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors group"
          title="Manage Students"
        >
          <Users class="h-5 w-5 text-gray-600 dark:text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors" />
        </button>
        <button
          @click="$emit('edit')"
          class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors group"
          title="Edit Course"
        >
          <Edit3 class="h-5 w-5 text-gray-600 dark:text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors" />
        </button>
        <button
          @click="$emit('delete')"
          class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors group"
          title="Delete Course"
        >
          <Trash class="h-5 w-5 text-gray-600 dark:text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors" />
        </button>
      </div>
    </div>

    <!-- Description -->
    <p class="text-sm text-gray-600 dark:text-gray-300 mt-3">{{ reactiveCourse.description }}</p>
    
    <!-- Info Section -->
    <div class="flex flex-col gap-1.5 mt-3 pt-3 border-t border-gray-200 dark:border-gray-700">
      <!-- Instructor -->
      <div v-if="reactiveCourse.instructor" class="flex items-center gap-2 text-sm">
        <UserCircle class="w-4 h-4 text-purple-600 dark:text-purple-400 flex-shrink-0" />
        <span class="text-gray-600 dark:text-gray-400">Instructor:</span>
        <span class="font-medium text-gray-900 dark:text-white">{{ reactiveCourse.instructor.user?.name || 'Unknown' }}</span>
      </div>
      
      <!-- Creator -->
      <div class="flex items-center gap-2 text-sm">
        <UserPlus class="w-4 h-4 text-indigo-600 dark:text-indigo-400 flex-shrink-0" />
        <span class="text-gray-600 dark:text-gray-400">
          {{ reactiveCourse.instructor ? 'Added by:' : 'Added by Admin:' }}
        </span>
        <span class="font-medium text-gray-900 dark:text-white">{{ reactiveCourse.creator?.name || 'Unknown' }}</span>
      </div>

      <!-- Course Duration -->
      <div v-if="reactiveCourse.start_date || reactiveCourse.end_date" class="flex items-center gap-2 text-sm flex-wrap">
        <Calendar class="w-4 h-4 text-green-600 dark:text-green-400 flex-shrink-0" />
        <span class="text-gray-600 dark:text-gray-400">Duration:</span>
        <span class="font-medium text-gray-900 dark:text-white">
          {{ formatDate(reactiveCourse.start_date) }} - {{ formatDate(reactiveCourse.end_date) }}
        </span>
        <span v-if="courseStatus" :class="[
          'px-2 py-0.5 text-xs font-medium rounded-full',
          courseStatus === 'upcoming' ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300' : 
          courseStatus === 'active' ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300' : 
          'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300'
        ]">
          {{ courseStatus === 'upcoming' ? 'Upcoming' : courseStatus === 'active' ? 'Active' : 'Completed' }}
        </span>
      </div>

      <!-- Module count -->
      <div class="flex items-center gap-2 text-sm">
        <span class="text-gray-600 dark:text-gray-300">Modules:</span>
        <span class="font-medium text-gray-900 dark:text-white">{{ reactiveCourse.modules.length }}</span>
      </div>

      <!-- Course Schedules -->
      <div v-if="schedules.length > 0" class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
        <div class="flex items-center gap-2 mb-3">
          <Calendar class="w-4 h-4 text-blue-600 dark:text-blue-400" />
          <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Upcoming Schedules</span>
        </div>
        <div class="space-y-2">
          <div
            v-for="schedule in schedules.slice(0, 3)"
            :key="schedule.id"
            class="flex items-start gap-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg"
          >
            <div class="flex-shrink-0 mt-0.5">
              <div class="p-1.5 bg-blue-100 dark:bg-blue-900/30 rounded">
                <Clock class="w-4 h-4 text-blue-600 dark:text-blue-400" />
              </div>
            </div>
            <div class="flex-1 min-w-0">
              <div class="flex items-center gap-2 flex-wrap">
                <p class="text-sm font-medium text-gray-900 dark:text-white">
                  {{ schedule.title || reactiveCourse.name }}
                </p>
                <span
                  v-if="schedule.is_recurring"
                  class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-medium bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 rounded-full"
                >
                  <Repeat class="w-3 h-3" />
                  {{ getFrequencyText(schedule) }}
                </span>
                <span
                  v-if="schedule.session_number"
                  class="text-xs text-gray-500 dark:text-gray-400"
                >
                  Session #{{ schedule.session_number }}
                </span>
              </div>
              <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                {{ formatScheduleTime(schedule) }}
              </p>
              <p v-if="schedule.location" class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                📍 {{ schedule.location }}
              </p>
            </div>
          </div>
        </div>
        <div v-if="schedules.length > 3" class="mt-2 text-center">
          <span class="text-xs text-gray-500 dark:text-gray-400">
            +{{ schedules.length - 3 }} more schedule(s)
          </span>
        </div>
      </div>
    </div>
  </div>

  <!-- Course Schedule Modal -->
  <CourseScheduleModal
    :is-open="showScheduleModal"
    :course-id="reactiveCourse.id"
    :course-name="reactiveCourse.name"
    :existing-schedule="existingSchedule"
    @close="handleScheduleModalClose"
  />
</template>

<script setup lang="ts">
import { reactive, computed, watch, ref } from "vue";
import { Edit3, Trash, Users, GraduationCap, UserCircle, UserPlus, Calendar, CalendarPlus, Clock, Repeat } from "lucide-vue-next";
import CourseScheduleModal from "@/components/CourseScheduleModal.vue";

interface Schedule {
  id: number;
  title: string;
  from_datetime: string;
  to_datetime: string;
  is_recurring: boolean;
  recurrence_rule?: string;
  location?: string;
  session_number?: number;
}

const props = defineProps<{
  course: {
    id: number;
    name: string;
    description?: string;
    grade_level?: string;
    grade_levels?: Array<{
      id: number;
      name: string;
      display_name: string;
    }>;
    start_date?: string;
    end_date?: string;
    modules: Array<{
      completion_percentage: number;
    }>;
    instructor?: {
      id: number;
      user?: {
        id: number;
        name: string;
        email: string;
      };
    };
    creator?: {
      id: number;
      name: string;
      email: string;
    };
    schedules?: Schedule[];
  };
}>();

defineEmits<{
  (e: "edit"): void;
  (e: "delete"): void;
  (e: "manageStudents"): void;
}>();

// State
const showScheduleModal = ref(false);
const schedules = ref<Schedule[]>(props.course.schedules || []);
const existingSchedule = ref<Schedule | null>(null);

// Computed: Check if course has a schedule
const hasSchedule = computed(() => schedules.value.length > 0);

// Make a reactive copy so changes trigger updates
const reactiveCourse = reactive({ ...props.course });

// Watch for prop changes and update reactive copy
watch(
  () => props.course,
  (newCourse) => {
    Object.assign(reactiveCourse, newCourse);
    // Update schedules when course prop changes
    schedules.value = newCourse.schedules || [];
  },
  { deep: true, immediate: true }
);

// Average completion %
const totalCompletion = computed(() => {
  if (!reactiveCourse.modules.length) return 0;
  const total = reactiveCourse.modules.reduce(
    (sum, m) => sum + (m.completion_percentage || 0),
    0
  );
  return Math.round(total / reactiveCourse.modules.length);
});

// Format date helper
const formatDate = (dateString?: string) => {
  if (!dateString) return 'Not set';
  const date = new Date(dateString);
  return date.toLocaleDateString('en-US', { 
    year: 'numeric', 
    month: 'short', 
    day: 'numeric' 
  });
};

// Course status based on dates
const courseStatus = computed(() => {
  if (!reactiveCourse.start_date || !reactiveCourse.end_date) return null;
  
  const now = new Date();
  const start = new Date(reactiveCourse.start_date);
  const end = new Date(reactiveCourse.end_date);
  
  if (now < start) return 'upcoming';
  if (now > end) return 'completed';
  return 'active';
});

// Format schedule frequency
const getFrequencyText = (schedule: Schedule): string => {
  if (!schedule.is_recurring) return 'One-time';
  
  if (schedule.recurrence_rule) {
    const rule = schedule.recurrence_rule.toLowerCase();
    if (rule.includes('daily')) return 'Daily';
    if (rule.includes('weekly')) return 'Weekly';
    if (rule.includes('monthly')) return 'Monthly';
  }
  
  return 'Recurring';
};

// Format schedule time range
const formatScheduleTime = (schedule: Schedule): string => {
  const from = new Date(schedule.from_datetime);
  const to = new Date(schedule.to_datetime);
  
  const dateStr = from.toLocaleDateString('en-US', { 
    month: 'short', 
    day: 'numeric',
    year: 'numeric'
  });
  
  const timeStr = `${from.toLocaleTimeString('en-US', { 
    hour: 'numeric', 
    minute: '2-digit' 
  })} - ${to.toLocaleTimeString('en-US', { 
    hour: 'numeric', 
    minute: '2-digit' 
  })}`;
  
  return `${dateStr}, ${timeStr}`;
};

// Handle schedule button click
const handleScheduleButtonClick = () => {
  if (hasSchedule.value) {
    // Edit existing schedule - pass the first schedule
    existingSchedule.value = schedules.value[0];
  } else {
    // Create new schedule
    existingSchedule.value = null;
  }
  showScheduleModal.value = true;
};

// Handle schedule modal close
const handleScheduleModalClose = () => {
  showScheduleModal.value = false;
  existingSchedule.value = null;
};
</script>
