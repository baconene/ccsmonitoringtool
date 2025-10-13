<template>
  <div class="w-full bg-transparent">
    <form @submit.prevent="saveModule" class="space-y-6">
      <!-- Title Field -->
      <div class="space-y-2">
        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200">
          Module Title
        </label>
        <input
          type="text"
          v-model="form.title"
          class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 transition-colors"
          placeholder="Enter module title..."
        />
        <p class="text-xs text-gray-500 dark:text-gray-400">
          Optional title for the module (e.g., "Module 1: Introduction").
        </p>
      </div>

      <!-- Description Field -->
      <div class="space-y-2">
        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200">
          Description <span class="text-red-500">*</span>
        </label>
        <textarea
          v-model="form.description"
          class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 transition-colors resize-none"
          rows="4"
          placeholder="Enter module description..."
          required
        ></textarea>
        <p class="text-xs text-gray-500 dark:text-gray-400">
          Provide a clear description of what this module covers.
        </p>
      </div>

      <!-- Module Type Field -->
      <div class="space-y-2">
        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200">
          Module Type <span class="text-red-500">*</span>
        </label>
        <select
          v-model="form.module_type"
          class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 transition-colors"
          required
        >
          <option value="">Select module type...</option>
          <option value="Lessons">Lessons</option>
          <option value="Activities">Activities</option>
          <option value="Mixed">Mixed (Lessons + Activities)</option>
          <option value="Quizzes">Quizzes</option>
          <option value="Assignments">Assignments</option>
          <option value="Assessment">Assessment</option>
        </select>
        <p class="text-xs text-gray-500 dark:text-gray-400">
          Specify the type of content this module will contain.
        </p>
      </div>

      <!-- Sequence Field -->
      <div class="space-y-2">
        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200">
          Sequence <span class="text-red-500">*</span>
        </label>
        <input
          type="number"
          v-model="form.sequence"
          min="1"
          class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 transition-colors"
          placeholder="Enter sequence number..."
          required
        />
        <p class="text-xs text-gray-500 dark:text-gray-400">
          The order in which this module should appear in the course.
        </p>
      </div>

      <!-- Module Percentage Field -->
      <div class="space-y-2">
        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200">
          Module Weight (%)
        </label>
        <div class="relative">
          <input
            type="number"
            v-model="form.module_percentage"
            min="0"
            max="100"
            step="0.01"
            class="w-full px-4 py-3 pr-12 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 transition-colors"
            placeholder="0"
          />
          <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
            <span class="text-gray-500 dark:text-gray-400 text-sm">%</span>
          </div>
        </div>
        <p class="text-xs text-gray-500 dark:text-gray-400">
          Optional percentage weight of this module in the course (0-100%).
        </p>
      </div>

      <!-- Completion Percentage Field -->
      <div class="space-y-2">
        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200">
          Completion Percentage
        </label>
        <div class="relative">
          <input
            type="number"
            v-model="form.completion_percentage"
            min="0"
            max="100"
            class="w-full px-4 py-3 pr-12 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 transition-colors"
            placeholder="0"
          />
          <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
            <span class="text-gray-500 dark:text-gray-400 text-sm">%</span>
          </div>
        </div>
        <p class="text-xs text-gray-500 dark:text-gray-400">
          Set the current completion status (0-100%).
        </p>
      </div>

      <!-- Error Display -->
      <div v-if="form.errors && Object.keys(form.errors).length > 0" class="p-4 rounded-lg bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800">
        <div class="flex items-start gap-2">
          <svg class="w-5 h-5 text-red-500 dark:text-red-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <div class="flex-1">
            <h4 class="text-sm font-medium text-red-800 dark:text-red-300 mb-1">Please correct the following errors:</h4>
            <ul class="text-sm text-red-700 dark:text-red-400 space-y-1">
              <li v-for="(error, field) in form.errors" :key="field">
                <strong class="capitalize">{{ field }}:</strong> {{ error }}
              </li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Action Buttons -->
      <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
        <button
          type="button"
          @click="$emit('cancel')"
          class="w-full sm:w-auto px-6 py-3 text-sm font-medium text-gray-700 dark:text-gray-200 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-colors border border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-gray-400 dark:focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800"
          :disabled="form.processing"
        >
          Cancel
        </button>
        <button
          type="submit"
          class="w-full sm:w-auto px-6 py-3 text-sm font-medium text-white bg-blue-600 dark:bg-blue-700 hover:bg-blue-700 dark:hover:bg-blue-800 rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:ring-offset-2 dark:focus:ring-offset-gray-800 flex items-center justify-center gap-2"
          :disabled="form.processing"
        >
          <svg v-if="form.processing" class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
          </svg>
          <span v-if="form.processing">{{ moduleId ? 'Updating...' : 'Creating...' }}</span>
          <span v-else>{{ moduleId ? 'Update Module' : 'Create Module' }}</span>
        </button>
      </div>
    </form>
  </div>
</template>

<script setup lang="ts">
import { useForm } from "@inertiajs/vue3";
import { ref, watch } from 'vue';

const props = defineProps<{
  moduleId?: number; // null for create
  courseId: number;  // which course this module belongs to
  defaults?: {
    title?: string;
    description?: string;
    sequence?: number;
    completion_percentage?: number;
    module_type?: string;
    module_percentage?: number;
  };
}>();

const emit = defineEmits<{
  (e: "saved"): void;
  (e: "cancel"): void;
}>();

const form = useForm({
  title: props.defaults?.title || "",
  description: props.defaults?.description || "",
  sequence: props.defaults?.sequence || 1,
  completion_percentage: props.defaults?.completion_percentage || 0,
  module_type: props.defaults?.module_type || "",
  module_percentage: props.defaults?.module_percentage || null,
});

// Ensure form fields are updated when defaults change (editing different module)
watch(
  () => props.defaults,
  (newDefaults) => {
    form.title = newDefaults?.title || "";
    form.description = newDefaults?.description || "";
    form.sequence = newDefaults?.sequence || 1;
    form.completion_percentage = newDefaults?.completion_percentage || 0;
    form.module_type = newDefaults?.module_type || "";
    form.module_percentage = newDefaults?.module_percentage || null;
  }
);

function saveModule() {
  // The form already has the correct fields:
  // form.description, form.sequence, form.completion_percentage

  if (props.moduleId) {
    // Update existing module
form.put(`/modules/${props.moduleId}`, {
  onSuccess: () => emit('saved'),
  // optional: skip Inertia handling for JSON
  preserveScroll: true,
});

  } else {
    // Create new module
    form.post(`/courses/${props.courseId}/modules`, {
      onSuccess: () => emit("saved"),
    });
  }
}

</script>
