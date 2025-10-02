<template>
  <div class="max-w-lg mx-auto bg-white shadow rounded p-6">
    <h2 class="text-xl font-bold mb-4">
      {{ moduleId ? "Edit Module" : "Create Module" }}
    </h2>

    <form @submit.prevent="saveModule" class="space-y-4">
      <!-- Description -->
      <div>
        <label class="block text-sm font-medium text-gray-700">Description</label>
        <textarea
          v-model="form.description"
          class="mt-1 block w-full border rounded p-2 text-gray-800"
          rows="3"
          required
        ></textarea>
      </div>

      <!-- Sequence -->
      <div>
        <label class="block text-sm font-medium text-gray-700">Sequence</label>
        <input
          type="number"
          v-model="form.sequence"
          min="1"
          class="mt-1 block w-full border rounded p-2 text-gray-800"
          required
        />
      </div>

      <!-- Completion Percentage -->
      <div>
        <label class="block text-sm font-medium text-gray-700">Completion %</label>
        <input
          type="number"
          v-model="form.completion_percentage"
          min="0"
          max="100"
          class="mt-1 block w-full border rounded p-2 text-gray-800"
        />
      </div>

      <!-- Submit -->
      <div class="flex justify-end gap-2">
        <button
          type="button"
          @click="$emit('cancel')"
          class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400"
        >
          Cancel
        </button>
        <button
          type="submit"
          class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
          :disabled="form.processing"
        >
          <span v-if="form.processing">Saving...</span>
          <span v-else>Save</span>
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
    description?: string;
    sequence?: number;
    completion_percentage?: number;
  };
}>();

const emit = defineEmits<{
  (e: "saved"): void;
  (e: "cancel"): void;
}>();

const form = useForm({
  description: props.defaults?.description || "",
  sequence: props.defaults?.sequence || 1,
  completion_percentage: props.defaults?.completion_percentage || 0,
});

// Ensure form fields are updated when defaults change (editing different module)
watch(
  () => props.defaults,
  (newDefaults) => {
    form.description = newDefaults?.description || "";
    form.sequence = newDefaults?.sequence || 1;
    form.completion_percentage = newDefaults?.completion_percentage || 0;
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
