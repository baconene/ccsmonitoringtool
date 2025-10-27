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
          class="bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600"
        >
          <div class="p-6">
            <!-- Lesson Header -->
            <div class="flex items-start justify-between mb-4">
              <div class="flex items-start gap-3">
                <div class="flex-shrink-0">
                  <div 
                    :class="isLessonCompleted(lesson)
                      ? 'bg-green-100 text-green-600 dark:bg-green-900/30 dark:text-green-400' 
                      : 'bg-gray-100 text-gray-400 dark:bg-gray-600 dark:text-gray-500'"
                    class="w-8 h-8 rounded-full flex items-center justify-center"
                  >
                    <CheckCircle2 v-if="isLessonCompleted(lesson)" class="h-4 w-4" />
                    <Circle v-else class="h-4 w-4" />
                  </div>
                </div>
                <div>
                  <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">
                    {{ lesson.title }}
                  </h3>
                  <div class="flex flex-wrap items-center text-sm text-gray-500 dark:text-gray-400 gap-3">
                    <div class="flex items-center">
                      <Clock class="h-4 w-4 mr-1" />
                      <span>{{ lesson.duration }} minutes</span>
                    </div>
                    <span class="capitalize">{{ lesson.content_type }}</span>
                    <div v-if="lesson.completed_at" class="text-green-600 dark:text-green-400 flex items-center">
                      <CheckCircle2 class="h-4 w-4 mr-1" />
                      Completed {{ formatDate(lesson.completed_at) }}
                    </div>
                  </div>
                </div>
              </div>
              <div v-if="!isLessonCompleted(lesson)" class="flex-shrink-0">
                <button
                  @click="markLessonComplete(lesson.id)"
                  :disabled="markingLesson === lesson.id"
                  class="px-4 py-2 text-sm font-medium bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 text-white rounded-lg transition-colors shadow-sm"
                >
                  {{ markingLesson === lesson.id ? 'Marking...' : 'Mark Complete' }}
                </button>
              </div>
            </div>

            <!-- Full Width Lesson Content -->
            <div class="mt-6">
              <div 
                :key="`lesson-${lesson.id}-${isDarkMode}`"
                class="lesson-content prose prose-base max-w-none dark:prose-invert bg-transparent"
                v-html="transformContentForTheme(lesson.description)"
              ></div>
            </div>
          </div>

          <!-- Lesson Documents -->
          <div v-if="lesson.documents && lesson.documents.length > 0" class="px-4 pb-4 border-t border-gray-200 dark:border-gray-600">
            <div class="pt-4">
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
import { ref, computed, watch, onMounted } from 'vue';
import { router, Link } from '@inertiajs/vue3';
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

// Helper function to check if lesson is completed (handles various data types)
const isLessonCompleted = (lesson: Lesson): boolean => {
  // Handle boolean, string, or number values
  if (typeof lesson.is_completed === 'boolean') {
    return lesson.is_completed;
  }
  if (typeof lesson.is_completed === 'string') {
    return lesson.is_completed === 'true' || lesson.is_completed === '1';
  }
  if (typeof lesson.is_completed === 'number') {
    return lesson.is_completed === 1;
  }
  // Also check if completed_at exists as fallback
  return !!lesson.completed_at;
};

const markLessonComplete = (lessonId: number) => {
  if (markingLesson.value) return;
  
  markingLesson.value = lessonId;
  
  router.post(`/student/courses/${props.courseId}/lessons/${lessonId}/complete`, {}, {
    onSuccess: () => {
      markingLesson.value = null;
    },
    onError: (errors) => {
      console.error('Error marking lesson complete:', errors);
      markingLesson.value = null;
    }
  });
};

// Dark mode detection with reactivity
const isDarkMode = ref(false);
const updateTheme = () => {
  if (typeof document !== 'undefined') {
    isDarkMode.value = document.documentElement.classList.contains('dark');
  }
};

// Watch for theme changes
onMounted(() => {
  updateTheme();
  
  // Watch for changes to the document class (theme changes)
  if (typeof document !== 'undefined') {
    const observer = new MutationObserver(() => {
      updateTheme();
    });
    
    observer.observe(document.documentElement, {
      attributes: true,
      attributeFilter: ['class']
    });
  }
});

// Transform WYSIWYG content for current theme
const transformContentForTheme = (content: string): string => {
  if (!content) return '';
  
  // Create a temporary DOM element to parse and modify the content
  const tempDiv = document.createElement('div');
  tempDiv.innerHTML = content;
  
  // Transform based on current theme
  if (isDarkMode.value) {
    // Dark mode transformations
    transformForDarkMode(tempDiv);
  } else {
    // Light mode transformations
    transformForLightMode(tempDiv);
  }
  
  return tempDiv.innerHTML;
};

const transformForDarkMode = (element: HTMLElement) => {
  // Transform all text elements
  const textElements = element.querySelectorAll('p, div, span, h1, h2, h3, h4, h5, h6, li, td, th');
  textElements.forEach((el: Element) => {
    const htmlEl = el as HTMLElement;
    
    // Remove any light mode text colors and apply dark mode
    htmlEl.style.color = '';
    htmlEl.classList.remove('text-black', 'text-gray-900', 'text-gray-800', 'text-gray-700');
    
    // Apply dark mode text color based on element type
    if (el.tagName.match(/^H[1-6]$/)) {
      htmlEl.style.color = 'rgb(243 244 246)'; // gray-100 for headings
    } else {
      htmlEl.style.color = 'rgb(209 213 219)'; // gray-300 for regular text
    }
  });
  
  // Transform background colors
  const bgElements = element.querySelectorAll('*');
  bgElements.forEach((el: Element) => {
    const htmlEl = el as HTMLElement;
    const bgColor = window.getComputedStyle(htmlEl).backgroundColor;
    
    // Convert light backgrounds to dark equivalents
    if (bgColor === 'rgb(255, 255, 255)' || bgColor === 'white') {
      htmlEl.style.backgroundColor = 'transparent';
    } else if (bgColor.includes('rgb(248, 249, 250)') || bgColor.includes('rgb(243, 244, 246)')) {
      htmlEl.style.backgroundColor = 'rgb(55 65 81)'; // gray-700
    }
  });
  
  // Transform links
  const links = element.querySelectorAll('a');
  links.forEach((link: Element) => {
    const htmlLink = link as HTMLElement;
    htmlLink.style.color = 'rgb(96 165 250)'; // blue-400
  });
  
  // Transform strong/bold elements
  const strongElements = element.querySelectorAll('strong, b');
  strongElements.forEach((strong: Element) => {
    const htmlStrong = strong as HTMLElement;
    htmlStrong.style.color = 'rgb(243 244 246)'; // gray-100
  });
};

const transformForLightMode = (element: HTMLElement) => {
  // Transform all text elements for light mode
  const textElements = element.querySelectorAll('p, div, span, h1, h2, h3, h4, h5, h6, li, td, th');
  textElements.forEach((el: Element) => {
    const htmlEl = el as HTMLElement;
    
    // Remove any dark mode text colors
    htmlEl.style.color = '';
    htmlEl.classList.remove('text-white', 'text-gray-100', 'text-gray-200', 'text-gray-300');
    
    // Apply light mode text color based on element type
    if (el.tagName.match(/^H[1-6]$/)) {
      htmlEl.style.color = 'rgb(17 24 39)'; // gray-900 for headings
    } else {
      htmlEl.style.color = 'rgb(55 65 81)'; // gray-700 for regular text
    }
  });
  
  // Transform background colors
  const bgElements = element.querySelectorAll('*');
  bgElements.forEach((el: Element) => {
    const htmlEl = el as HTMLElement;
    const bgColor = window.getComputedStyle(htmlEl).backgroundColor;
    
    // Convert dark backgrounds to light equivalents
    if (bgColor.includes('rgb(31, 41, 55)') || bgColor.includes('rgb(55, 65, 81)')) {
      htmlEl.style.backgroundColor = 'rgb(248 250 252)'; // gray-50
    }
  });
  
  // Transform links
  const links = element.querySelectorAll('a');
  links.forEach((link: Element) => {
    const htmlLink = link as HTMLElement;
    htmlLink.style.color = 'rgb(37 99 235)'; // blue-600
  });
  
  // Transform strong/bold elements
  const strongElements = element.querySelectorAll('strong, b');
  strongElements.forEach((strong: Element) => {
    const htmlStrong = strong as HTMLElement;
    htmlStrong.style.color = 'rgb(17 24 39)'; // gray-900
  });
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