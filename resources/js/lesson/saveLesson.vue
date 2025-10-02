<template>
  <button
    @click="saveLesson"
    class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 disabled:opacity-50"
    :disabled="loading"
  >
    <span v-if="loading">Saving...</span>
    <span v-else>Save Lesson</span>
  </button>
</template>

<script setup lang="ts">
import { ref } from "vue";
import { router } from "@inertiajs/vue3"; // for Inertia request

// Props: data needed to save a lesson
const props = defineProps<{
  title: string;
  description: string;
  moduleId: number;
  documents?: { id?: number; name: string; file_path: string; doc_type: string }[];
}>();

const loading = ref(false);

function saveLesson() {
  loading.value = true;

  router.post(
    "/lessons",
    {
      title: props.title,
      description: props.description,
      module_id: props.moduleId,
      documents: props.documents || [],
    },
    {
      onFinish: () => (loading.value = false),
      onSuccess: () => {
        // maybe show toast or reset WYSIWYG later
        console.log("Lesson saved!");
      },
    }
  );
}
</script>
