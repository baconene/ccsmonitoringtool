<template>
  <div
    v-if="open"
    class="fixed inset-0 flex items-center justify-center bg-black/50 z-50"
    @click.self="$emit('close')"
  >
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg w-full max-w-lg p-6 relative border border-gray-200 dark:border-gray-700 transition-colors">
      <!-- Close button -->
      <button
        @click="$emit('close')"
        class="absolute top-2 right-2 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-colors"
      >
        ✕
      </button>

      <!-- Title -->
      <h2 class="text-lg font-bold mb-4 text-gray-900 dark:text-white">{{ modalTitle }}</h2>

      <!-- Success Message -->
      <div v-if="successMessage" class="mb-4 p-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
        <p class="text-sm text-green-700 dark:text-green-400">{{ successMessage }}</p>
      </div>

      <!-- General Error -->
      <div v-if="errors.general" class="mb-4 p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
        <p class="text-sm text-red-700 dark:text-red-400">{{ errors.general }}</p>
      </div>

      <!-- Form (Create/Edit) -->
      <form
        v-if="mode === 'create' || mode === 'edit'"
        @submit.prevent="handleSubmit"
        class="space-y-4"
      >
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Course Title</label>
          <input
            v-model="localCourse.title"
            type="text"
            required
            :class="[
              'w-full mt-1 p-2 border rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:border-blue-400 transition-colors',
              errors.title ? 'border-red-300 dark:border-red-600' : 'border-gray-300 dark:border-gray-600'
            ]"
            placeholder="Enter course title"
          />
          <p v-if="errors.title" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ errors.title[0] }}</p>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Course Code</label>
          <input
            v-model="localCourse.course_code"
            type="text"
            :class="[
              'w-full mt-1 p-2 border rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:border-blue-400 transition-colors',
              errors.course_code ? 'border-red-300 dark:border-red-600' : 'border-gray-300 dark:border-gray-600'
            ]"
            placeholder="e.g., CS101"
          />
          <p v-if="errors.course_code" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ errors.course_code[0] }}</p>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
          <textarea
            v-model="localCourse.description"
            rows="3"
            class="w-full mt-1 p-2 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:border-blue-400 transition-colors"
          />
        </div>

        <!-- Date Range -->
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Start Date</label>
            <input
              v-model="localCourse.start_date"
              type="date"
              :class="[
                'w-full mt-1 p-2 border rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:border-blue-400 transition-colors',
                errors.start_date ? 'border-red-300 dark:border-red-600' : 'border-gray-300 dark:border-gray-600'
              ]"
            />
            <p v-if="errors.start_date" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ errors.start_date[0] }}</p>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">End Date</label>
            <input
              v-model="localCourse.end_date"
              type="date"
              :class="[
                'w-full mt-1 p-2 border rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:border-blue-400 transition-colors',
                errors.end_date ? 'border-red-300 dark:border-red-600' : 'border-gray-300 dark:border-gray-600'
              ]"
            />
            <p v-if="errors.end_date" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ errors.end_date[0] }}</p>
          </div>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Eligible Grade Levels</label>
          <div class="space-y-2 max-h-48 overflow-y-auto p-3 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700">
            <label 
              v-for="grade in availableGradeLevels" 
              :key="grade.id"
              class="flex items-center gap-2 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-600 p-2 rounded transition-colors"
            >
              <input
                type="checkbox"
                :value="grade.id"
                v-model="localCourse.grade_level_ids"
                class="w-4 h-4 text-blue-600 bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 rounded focus:ring-blue-500 cursor-pointer"
              />
              <span class="text-sm text-gray-900 dark:text-white">{{ grade.display_name }}</span>
            </label>
            <div v-if="availableGradeLevels.length === 0" class="text-sm text-gray-500 dark:text-gray-400 text-center py-2">
              Loading grade levels...
            </div>
          </div>
          <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
            Select one or more grade levels that can take this course. Leave empty if open to all students.
          </p>
        </div>

        <div class="flex justify-end gap-2">
          <button type="button" @click="$emit('close')" class="px-4 py-2 rounded border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
            Cancel
          </button>
          <button 
            type="submit" 
            :disabled="isSubmitting"
            :class="[
              'px-4 py-2 rounded text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition-colors flex items-center gap-2',
              isSubmitting 
                ? 'bg-gray-400 cursor-not-allowed' 
                : 'bg-blue-600 dark:bg-blue-500 hover:bg-blue-700 dark:hover:bg-blue-600'
            ]"
          >
            <svg v-if="isSubmitting" class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            {{ isSubmitting ? 'Saving...' : (mode === 'create' ? 'Save' : 'Update') }}
          </button>
        </div>
      </form>

      <!-- Delete confirmation -->
      <div v-else-if="mode === 'delete'" class="space-y-4">
        <p>Are you sure you want to delete <strong>{{ course?.name }}</strong>?</p>
        <div class="flex justify-end gap-2">
          <button @click="$emit('close')" class="px-4 py-2 rounded border">
            Cancel
          </button>
          <button
            @click="handleDelete"
            class="px-4 py-2 rounded bg-red-600 text-white hover:bg-red-700"
          >
            Delete
          </button>
        </div>
      </div>

      <!-- View only -->
      <div v-else-if="mode === 'view'" class="space-y-2">
        <p><strong>Name:</strong> {{ course?.name }}</p>
        <p><strong>Description:</strong> {{ course?.description }}</p>
        <button @click="$emit('close')" class="mt-4 px-4 py-2 rounded border">
          Close
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { reactive, watch, computed, ref, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';
import axios from 'axios';

interface GradeLevel {
  id: number;
  name: string;
  display_name: string;
  level: number;
}

interface CourseGradeLevel {
  id: number;
  name: string;
  display_name: string;
}

const props = defineProps<{
  open: boolean;
  mode: 'create' | 'edit' | 'view' | 'delete';
  course?: { 
    id?: number; 
    title?: string;
    name?: string; 
    course_code?: string;
    description?: string; 
    grade_level?: string;
    grade_levels?: CourseGradeLevel[];
    start_date?: string;
    end_date?: string;
  } | null;
}>();

const emit = defineEmits<{
  close: [];
  refresh: [newCourseId?: number];
}>();

// Grade levels state
const availableGradeLevels = ref<GradeLevel[]>([]);

// Local form state
const localCourse = reactive({
  title: '',
  name: '',
  course_code: '',
  description: '',
  start_date: '',
  end_date: '',
  grade_level_ids: [] as number[],
});

// Form state
const isSubmitting = ref(false);
const errors = ref<Record<string, string>>({});
const successMessage = ref('');

// Fetch grade levels on mount
onMounted(async () => {
  try {
    const response = await axios.get('/api/grade-levels');
    availableGradeLevels.value = response.data.grade_levels;
  } catch (error) {
    console.error('Failed to fetch grade levels:', error);
  }
});

// Sync props into local state
watch(
  [() => props.course, () => props.mode],
  ([course, mode]) => {
    if (mode === 'edit' && course) {
      localCourse.title = course.title || course.name || '';
      localCourse.name = course.name || course.title || '';
      localCourse.course_code = course.course_code || '';
      localCourse.description = course.description || '';
      localCourse.start_date = course.start_date || '';
      localCourse.end_date = course.end_date || '';
      localCourse.grade_level_ids = course.grade_levels?.map(gl => gl.id) || [];
    } else if (mode === 'create') {
      localCourse.title = '';
      localCourse.name = '';
      localCourse.course_code = '';
      localCourse.description = '';
      localCourse.start_date = '';
      localCourse.end_date = '';
      localCourse.grade_level_ids = [];
    }
  },
  { immediate: true }
);

const modalTitle = computed(() => {
  switch (props.mode) {
    case 'create':
      return 'Add New Course';
    case 'edit':
      return 'Edit Course';
    case 'view':
      return 'Course Details';
    case 'delete':
      return 'Delete Course';
    default:
      return 'Course';
  }
});

// Handle create/update
async function handleSubmit() {
  if (isSubmitting.value) return;
  
  isSubmitting.value = true;
  errors.value = {};
  successMessage.value = '';

  try {
    const formData = {
      title: localCourse.title,
      name: localCourse.name || localCourse.title, // Backend compatibility
      course_code: localCourse.course_code,
      description: localCourse.description,
      start_date: localCourse.start_date,
      end_date: localCourse.end_date,
      grade_level_ids: localCourse.grade_level_ids,
      // instructor_id will be automatically set by backend based on user role
      // No need to pass it from frontend
    };

    if (props.mode === 'create') {
      // Use axios for create to get the response data
      const response = await axios.post('/courses', formData);
      const newCourseId = response.data.course?.id || response.data.id;
      
      successMessage.value = 'Course created successfully!';
      setTimeout(() => {
        emit('refresh', newCourseId);
        emit('close');
      }, 1500);
      
    } else if (props.mode === 'edit' && props.course?.id) {
      // Use axios for update
      await axios.put(`/courses/${props.course.id}`, formData);
      successMessage.value = 'Course updated successfully!';
      setTimeout(() => {
        emit('refresh');
        emit('close');
      }, 1500);
    }
  } catch (error: any) {
    console.error('Course operation failed:', error);
    
    if (error.response?.data?.errors) {
      // Laravel validation errors
      errors.value = error.response.data.errors;
    } else if (error.response?.data?.message) {
      // General error message
      errors.value = { general: error.response.data.message };
    } else {
      // Generic error
      errors.value = { general: 'An unexpected error occurred. Please try again.' };
    }
  } finally {
    isSubmitting.value = false;
  }
}

// Handle delete
async function handleDelete() {
  if (!props.course?.id) return;
  try {
    await axios.delete(`/courses/${props.course.id}`);
    emit('refresh'); // no new course ID for delete
  } catch (error) {
    console.error('Error deleting course:', error);
    // You might want to show an error message to the user here
  }
}
</script>
