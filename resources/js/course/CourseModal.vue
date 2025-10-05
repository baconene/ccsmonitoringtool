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

      <!-- Form (Create/Edit) -->
      <form
        v-if="mode === 'create' || mode === 'edit'"
        @submit.prevent="handleSubmit"
        class="space-y-4"
      >
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Course Name</label>
          <input
            v-model="localCourse.name"
            type="text"
            required
            class="w-full mt-1 p-2 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:border-blue-400 transition-colors"
          />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
          <textarea
            v-model="localCourse.description"
            rows="3"
            class="w-full mt-1 p-2 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:border-blue-400 transition-colors"
          />
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
          <button type="submit" class="px-4 py-2 rounded bg-blue-600 dark:bg-blue-500 text-white hover:bg-blue-700 dark:hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition-colors">
            {{ mode === 'create' ? 'Save' : 'Update' }}
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
    name: string; 
    description?: string; 
    grade_level?: string;
    grade_levels?: CourseGradeLevel[];
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
  name: '',
  description: '',
  grade_level_ids: [] as number[],
});

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
      localCourse.name = course.name || '';
      localCourse.description = course.description || '';
      localCourse.grade_level_ids = course.grade_levels?.map(gl => gl.id) || [];
    } else if (mode === 'create') {
      localCourse.name = '';
      localCourse.description = '';
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
  try {
    const formData = {
      name: localCourse.name,
      description: localCourse.description,
      grade_level_ids: localCourse.grade_level_ids,
    };

    if (props.mode === 'create') {
      // Use axios for create to get the response data
      const response = await axios.post('/courses', formData);
      const newCourseId = response.data.course?.id || response.data.id;
      
      console.log('Course created with ID:', newCourseId);
      emit('refresh', newCourseId);
      
    } else if (props.mode === 'edit' && props.course?.id) {
      // Use axios for update
      await axios.put(`/courses/${props.course.id}`, formData);
      emit('refresh');
    }
  } catch (error) {
    console.error('Course operation failed:', error);
    // You might want to show an error message to the user here
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
