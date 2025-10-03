<template>
  <div
    class="mb-6 p-6 bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 flex flex-col lg:flex-row lg:justify-between lg:items-center gap-4 transition-colors"
  >
    <!-- Course Info -->
    <div class="flex-1">
      <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white">{{ reactiveCourse.name }}</h1>
      <span class="text-sm text-gray-600 dark:text-gray-300 block mt-1">{{ reactiveCourse.description }}</span>
    </div>

    <!-- Stats + Actions -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center w-full lg:w-auto text-sm gap-4">
      <!-- Stats Container -->
      <div class="flex items-center gap-6">
        <!-- Completion -->
        <div class="flex items-center gap-2">
          <span class="text-gray-600 dark:text-gray-300">Total Completion:</span>
          <span
            class="flex items-center gap-1 font-semibold"
            :class="totalCompletion === 100 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'"
          >
            <strong>{{ totalCompletion }}</strong>%
          </span>
        </div>

        <!-- Module count -->
        <span class="text-gray-600 dark:text-gray-300">
          Modules: {{ reactiveCourse.modules.length }}
        </span>
      </div>

      <!-- Action buttons -->
      <div class="flex gap-2">
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
  </div>
</template>

<script setup lang="ts">
import { reactive, computed, watch } from "vue";
import { Edit3, Trash } from "lucide-vue-next";

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
