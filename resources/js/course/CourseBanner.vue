<template>
  <div
    class="mb-4 p-4 bg-white rounded shadow flex flex-col md:flex-row md:justify-between md:items-center gap-4"
  >
    <!-- Course Info -->
    <div>
      <h1 class="text-xl md:text-2xl font-bold">{{ reactiveCourse.name }}</h1>
      <span class="text-sm text-gray-600 block">{{ reactiveCourse.description }}</span>
    </div>

    <!-- Stats + Actions -->
    <div class="flex items-center w-full md:w-auto text-sm gap-4">
      <!-- Completion -->
      <span class="mr-2 text-gray-600">Total Completion:</span>
      <span
        class="flex items-center gap-1 mr-4"
        :class="totalCompletion === 100 ? 'text-green-600 font-semibold' : 'text-red-600 font-semibold'"
      >
        <strong>{{ totalCompletion }}</strong>%
      </span>

      <!-- Module count -->
      <span class="ml-auto text-gray-600">
        Modules: {{ reactiveCourse.modules.length }}
      </span>

      <!-- Action buttons -->
      <div class="flex gap-2 ml-4">
        <button
          class="px-3 py-1 text-sm rounded bg-yellow-500 text-white hover:bg-yellow-600"
          @click="$emit('edit')"
        >
          Edit
        </button>
        <button
          class="px-3 py-1 text-sm rounded bg-red-600 text-white hover:bg-red-700"
          @click="$emit('delete')"
        >
          Delete
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { reactive, computed, watch } from "vue";

const props = defineProps<{
  course: {
    id: number;
    name: string;
    description?: string;
    modules: Array<{
      completion_percentage: number;
    }>;
  };
}>();

defineEmits<{
  (e: "edit"): void;
  (e: "delete"): void;
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
</script>
