<template>
  <div class="flex-1 p-4 border rounded bg-gray-50 overflow-y-auto">
    <template v-if="module">
      <div class="flex justify-between items-center mb-2">
        <h2 class="text-xl font-bold flex items-center gap-2">
          {{ module.description }}

          <!-- Edit Module -->
          <button
            @click="$emit('edit', module)"
            class="p-1 rounded hover:bg-gray-200"
            title="Edit Module"
          >
            <Edit3 class="h-4 w-4 text-gray-600" />
          </button>

          <!-- Remove Module -->
          <button
            @click="$emit('remove', module)"
            class="p-1 rounded hover:bg-gray-200"
            title="Remove Module"
          >
            <Trash class="h-4 w-4 text-gray-600" />
          </button>

          <!-- Add Lesson -->
          <button
            @click="$emit('add-lesson', module)"
            class="p-1 rounded hover:bg-gray-200"
            title="Add Lesson"
          >
            <Plus class="h-4 w-4 text-gray-600" />
          </button>
        </h2>

        <div class="flex items-center gap-2">
          <span class="text-sm text-gray-500">Seq: {{ module.sequence }}</span>
          <span class="text-gray-500 text-sm">
            Completion: {{ module.completion_percentage }}%
          </span>
        </div>
      </div>

      <!-- Lessons -->
      <LessonList :module-id="module.id" :lessons="module.lessons" />
    </template>

    <template v-else>
      <p class="text-gray-400 italic">Select a module to view details</p>
    </template>
  </div>
</template>

<script setup lang="ts">
import LessonList from "@/lesson/lessonList.vue";
import { Edit3, Trash, Plus } from "lucide-vue-next";

const props = defineProps<{
  module: {
    id: number;
    description: string;
    sequence: number;
    completion_percentage: number;
    lessons: Array<{
      id: number;
      title: string;
      description: string;
      documents: Array<{
        id: number;
        name: string;
        file_path: string;
        doc_type: string;
      }>;
    }>;
  } | null;
}>();

defineEmits<{
  (e: "edit", module: typeof props.module): void;
  (e: "remove", module: typeof props.module): void;
  (e: "add-lesson", module: typeof props.module): void;
}>();
</script>
