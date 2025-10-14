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
    </div>
  </div>
</template>

<script setup lang="ts">
import { reactive, computed, watch } from "vue";
import { Edit3, Trash, Users, GraduationCap, UserCircle, UserPlus, Calendar } from "lucide-vue-next";

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
  };
}>();

defineEmits<{
  (e: "edit"): void;
  (e: "delete"): void;
  (e: "manageStudents"): void;
}>();

// Make a reactive copy so changes trigger updates
const reactiveCourse = reactive({ ...props.course });

// Watch for prop changes and update reactive copy
watch(
  () => props.course,
  (newCourse) => {
    Object.assign(reactiveCourse, newCourse);
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
</script>
