<template>
  <div
    v-if="open"
    class="fixed inset-0 flex items-center justify-center bg-black/50 z-50"
    @click.self="$emit('close')"
  >
    <div class="bg-white rounded-lg shadow-lg w-full max-w-lg p-6 relative">
      <!-- Close button -->
      <button
        @click="$emit('close')"
        class="absolute top-2 right-2 text-gray-500 hover:text-gray-700"
      >
        ✕
      </button>

      <!-- Title -->
      <h2 class="text-lg font-bold mb-4">{{ modalTitle }}</h2>

      <!-- Form (Create/Edit) -->
      <form
        v-if="mode === 'create' || mode === 'edit'"
        @submit.prevent="handleSubmit"
        class="space-y-4"
      >
        <div>
          <label class="block text-sm font-medium text-gray-700">Course Name</label>
          <input
            v-model="localCourse.name"
            type="text"
            required
            class="w-full mt-1 p-2 border rounded"
          />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700">Description</label>
          <textarea
            v-model="localCourse.description"
            rows="3"
            class="w-full mt-1 p-2 border rounded"
          />
        </div>

        <div class="flex justify-end gap-2">
          <button type="button" @click="$emit('close')" class="px-4 py-2 rounded border">
            Cancel
          </button>
          <button type="submit" class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700">
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
import { reactive, watch, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import axios from 'axios';

const props = defineProps<{
  open: boolean;
  mode: 'create' | 'edit' | 'view' | 'delete';
  course?: { id?: number; name: string; description?: string } | null;
}>();

const emit = defineEmits<{
  close: [];
  refresh: [newCourseId?: number];
}>();

// Local form state
const localCourse = reactive({
  name: '',
  description: '',
});

// Sync props into local state
watch(
  [() => props.course, () => props.mode],
  ([course, mode]) => {
    if (mode === 'edit' && course) {
      localCourse.name = course.name || '';
      localCourse.description = course.description || '';
    } else if (mode === 'create') {
      localCourse.name = '';
      localCourse.description = '';
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
