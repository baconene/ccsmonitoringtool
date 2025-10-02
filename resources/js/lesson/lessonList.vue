<template>
  <div class="space-y-4 h-full flex flex-col">
    <!-- Existing Lessons -->
    <template v-for="lesson in localLessons" :key="lesson.id">
      <div class="p-2 border rounded bg-white">
        <h3 class="font-semibold mb-1">{{ lesson.title }}</h3>
        <ModuleDetails v-model="lesson.description" :lesson-id="lesson.id" />
      </div>

      <div class="p-2 border rounded bg-white">
        <RelatedDocumentContainer v-model="lesson.documents" />
      </div>
    </template>

    <!-- Clickable Add Lesson / Activity -->
    <div
      v-if="localLessons.length === 0"
      @click="showAddLessonModal = true"
      class="flex items-center justify-center border-2 border-dashed border-gray-300 rounded p-6 text-gray-400 text-lg font-semibold cursor-pointer hover:bg-gray-50 transition"
    >
      Add Lesson / Activity
    </div>

    <!-- Add Lesson Modal -->
    <AddLessonModal
      :visible="showAddLessonModal"
      :module-id="moduleId"
      @close="showAddLessonModal = false"
      @added="refreshLessons"
    />
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, watch, onMounted } from 'vue';
import ModuleDetails from '@/course/ModuleDetails.vue';
import RelatedDocumentContainer from '@/course/RelatedDocumentContainer.vue';
import AddLessonModal from './AddLessonModal.vue';

const props = defineProps<{
  moduleId: number;
  lessons: Array<{
    id: number;
    title: string;
    description: string;
    documents: Array<{ id: number; name: string; file_path: string; doc_type: string }>;
  }>;
}>();

const emit = defineEmits<{
  (e: 'update:lessons', lessons: typeof props.lessons): void;
}>();

const showAddLessonModal = ref(false);

// Local state
const localLessons = reactive([...props.lessons]);

// Keep in sync if parent updates
watch(
  () => props.lessons,
  (newVal) => {
    localLessons.splice(0, localLessons.length, ...newVal);
  }
);

// 🔥 Refresh lessons from backend
async function refreshLessons() {
  try {
    const res = await fetch(`/modules/${props.moduleId}/lessons`);
    if (!res.ok) throw new Error('Failed to fetch lessons');
    const data = await res.json();

    localLessons.splice(0, localLessons.length, ...data);
    emit('update:lessons', [...data]);
  } catch (err) {
    console.error('Error refreshing lessons:', err);
  } finally {
    showAddLessonModal.value = false;
  }
}

// Optionally load on mount
onMounted(() => {
  refreshLessons();
});
</script>
