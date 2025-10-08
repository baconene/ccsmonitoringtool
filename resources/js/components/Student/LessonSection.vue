<template>
  <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
    <div class="p-6">
      <div class="flex items-center mb-6">
        <BookMarked class="h-6 w-6 text-blue-600 dark:text-blue-400 mr-3" />
        <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
          Lessons ({{ completedLessons }}/{{ lessons.length }})
        </h2>
      </div>

      <div v-if="lessons.length > 0" class="space-y-4">
        <div 
          v-for="lesson in sortedLessons" 
          :key="lesson.id"
          class="bg-gray-50 dark:bg-gray-700 rounded-lg"
        >
          <div class="flex items-center justify-between p-4">
            <div class="flex items-center flex-1">
              <div class="flex-shrink-0 mr-4">
                <div 
                  :class="lesson.is_completed 
                    ? 'bg-green-100 text-green-600 dark:bg-green-900/30 dark:text-green-400' 
                    : 'bg-gray-100 text-gray-400 dark:bg-gray-600 dark:text-gray-500'"
                  class="w-8 h-8 rounded-full flex items-center justify-center"
                >
                  <CheckCircle2 v-if="lesson.is_completed" class="h-4 w-4" />
                  <Circle v-else class="h-4 w-4" />
                </div>
              </div>
              <div class="flex-1">
                <h3 class="font-medium text-gray-900 dark:text-gray-100">
                  {{ lesson.title }}
                </h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                  {{ lesson.description }}
                </p>
                <div class="flex items-center mt-2 text-xs text-gray-500 dark:text-gray-400">
                  <Clock class="h-3 w-3 mr-1" />
                  <span>{{ lesson.duration }} minutes</span>
                  <span class="mx-2">•</span>
                  <span class="capitalize">{{ lesson.content_type }}</span>
                  <span v-if="lesson.completed_at" class="mx-2">•</span>
                  <span v-if="lesson.completed_at" class="text-green-600 dark:text-green-400">
                    Completed {{ formatDate(lesson.completed_at) }}
                  </span>
                </div>
              </div>
            </div>

            <div class="flex-shrink-0 ml-4">
              <button
                v-if="!lesson.is_completed"
                @click="markLessonComplete(lesson.id)"
                :disabled="markingLesson === lesson.id"
                class="px-3 py-1 text-sm bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 text-white rounded-md transition-colors"
              >
                {{ markingLesson === lesson.id ? 'Marking...' : 'Mark Complete' }}
              </button>
            </div>
          </div>

          <!-- Lesson Documents -->
          <div v-if="lesson.documents && lesson.documents.length > 0" class="px-4 pb-4">
            <div class="border-t border-gray-200 dark:border-gray-600 pt-4">
              <div class="flex items-center mb-3">
                <FileText class="h-4 w-4 text-amber-600 dark:text-amber-400 mr-2" />
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                  Lesson Resources ({{ lesson.documents.length }})
                </span>
              </div>
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                <div 
                  v-for="document in lesson.documents" 
                  :key="document.id"
                  class="flex items-center justify-between p-2 bg-white dark:bg-gray-600 rounded border border-gray-200 dark:border-gray-500"
                >
                  <div class="flex items-center flex-1 min-w-0">
                    <component 
                      :is="getDocumentIcon(document.doc_type)" 
                      class="h-4 w-4 text-amber-600 dark:text-amber-400 mr-2 flex-shrink-0" 
                    />
                    <span class="text-sm text-gray-700 dark:text-gray-300 truncate">
                      {{ document.name }}
                    </span>
                  </div>
                  <div class="flex gap-1 ml-2">
                    <button
                      @click="viewDocument(document)"
                      class="p-1 text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300"
                      title="View"
                    >
                      <Eye class="h-4 w-4" />
                    </button>
                    <button
                      @click="downloadDocument(document)"
                      class="p-1 text-gray-600 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300"
                      title="Download"
                    >
                      <Download class="h-4 w-4" />
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div v-else class="text-center py-8 text-gray-500 dark:text-gray-400">
        <BookMarked class="h-12 w-12 mx-auto mb-4 opacity-50" />
        <p>No lessons available for this module</p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import { 
  BookMarked, 
  CheckCircle2, 
  Circle, 
  Clock, 
  FileText, 
  Eye, 
  Download,
  File, 
  FileImage, 
  FileVideo, 
  FileAudio,
  Presentation
} from 'lucide-vue-next';

interface Document {
  id: number;
  name: string;
  file_path: string;
  doc_type: string;
}

interface Lesson {
  id: number;
  title: string;
  description: string;
  duration: number;
  order: number;
  content_type: string;
  is_completed: boolean;
  completed_at: string | null;
  documents?: Document[];
}

const props = defineProps<{
  lessons: Lesson[];
  courseId: number;
}>();

const markingLesson = ref<number | null>(null);

const sortedLessons = computed(() => {
  return [...props.lessons].sort((a, b) => a.order - b.order);
});

const completedLessons = computed(() => {
  return props.lessons.filter(lesson => lesson.is_completed).length;
});

const formatDate = (dateString: string) => {
  return new Date(dateString).toLocaleDateString();
};

const markLessonComplete = async (lessonId: number) => {
  markingLesson.value = lessonId;
  
  try {
    router.post(`/student/courses/${props.courseId}/lessons/${lessonId}/complete`);
  } catch (error) {
    console.error('Error marking lesson complete:', error);
  } finally {
    markingLesson.value = null;
  }
};

const getDocumentIcon = (docType: string) => {
  switch (docType.toLowerCase()) {
    case 'pdf':
    case 'document':
      return FileText;
    case 'image':
      return FileImage;
    case 'video':
      return FileVideo;
    case 'audio':
      return FileAudio;
    case 'presentation':
      return Presentation;
    default:
      return File;
  }
};

const viewDocument = (document: Document) => {
  window.open(`/storage/${document.file_path}`, '_blank');
};

const downloadDocument = (document: Document) => {
  const link = window.document.createElement('a');
  link.href = `/storage/${document.file_path}`;
  link.download = document.name;
  link.click();
};
</script>