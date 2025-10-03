<template>
  <div class="w-full">
    <!-- Readonly View -->
    <div
      v-if="!editing"
      class="group relative p-4 border border-gray-200 dark:border-gray-600 rounded-xl cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 transition-all duration-200 shadow-sm hover:shadow-md"
      @dblclick="startEditing"
    >
      <!-- Edit Hint -->
      <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
        <div class="text-xs text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded-md">
          Double-click to edit
        </div>
      </div>
      
      <!-- Content -->
      <div class="prose prose-sm max-w-none text-gray-900 dark:text-white bg-transparent">
        <div 
          v-if="modelValue && modelValue.trim()" 
          v-html="modelValue"
          class="content-display bg-transparent dark:text-white"
        ></div>
        <div v-else class="text-gray-500 dark:text-gray-400 italic flex items-center gap-2">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
          </svg>
          No content available. Double-click to add content.
        </div>
      </div>
    </div>

    <!-- Inline Edit Mode -->
    <div
      v-else
      class="border border-gray-200 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-800 shadow-lg overflow-hidden"
    >
      <!-- Edit Header -->
      <div class="px-4 py-3 bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
        <div class="flex items-center justify-between">
          <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Edit Content</h3>
          <div class="text-xs text-gray-500 dark:text-gray-400">
            Use the toolbar below to format your content
          </div>
        </div>
      </div>

      <!-- Editor Container -->
      <div class="p-4">
        <!-- Quill Editor -->
        <div ref="quillEditor" class="border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 quill-editor-container"></div>
      </div>

      <!-- Footer actions -->
      <div class="flex items-center justify-between px-4 py-3 bg-gray-50 dark:bg-gray-700 border-t border-gray-200 dark:border-gray-600">
        <div class="text-xs text-gray-500 dark:text-gray-400">
          Changes will be saved automatically when you click Save
        </div>
        <div class="flex gap-3">
          <button
            @click="stopEditing"
            class="px-4 py-2 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-500 transition-colors font-medium text-sm"
            :disabled="saving"
          >
            Cancel
          </button>
          <button
            @click="saveContent"
            class="px-4 py-2 bg-blue-600 dark:bg-blue-500 text-white rounded-lg hover:bg-blue-700 dark:hover:bg-blue-600 transition-colors font-medium text-sm disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
            :disabled="saving"
          >
            <svg v-if="saving" class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            {{ saving ? 'Saving...' : 'Save' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, nextTick, onMounted, watch } from 'vue';
import Quill from 'quill';
import 'quill/dist/quill.snow.css';
import axios from 'axios';

const props = defineProps<{
  modelValue: string;
  lessonId: number;
}>();

const emit = defineEmits<{
  (e: 'update:modelValue', value: string): void;
}>();

const editing = ref(false);
const quillEditor = ref<HTMLDivElement | null>(null);
let quill: Quill | null = null;
const saving = ref(false);

// Function to ensure content text is white in dark mode
function ensureWhiteTextInDarkMode() {
  if (document.documentElement.classList.contains('dark')) {
    // Find all content display elements and force white text
    const contentDisplays = document.querySelectorAll('.content-display, .content-display *');
    contentDisplays.forEach(element => {
      const el = element as HTMLElement;
      el.style.color = '#ffffff !important';
      el.style.backgroundColor = 'transparent !important';
    });
    
    // Also target prose elements
    const proseElements = document.querySelectorAll('.prose, .prose *');
    proseElements.forEach(element => {
      const el = element as HTMLElement;
      if (el.closest('.dark') || document.documentElement.classList.contains('dark')) {
        el.style.color = '#ffffff !important';
        el.style.backgroundColor = 'transparent !important';
      }
    });
  }
}

// Watch for prop changes and ensure white text
watch(() => props.modelValue, () => {
  nextTick(() => {
    ensureWhiteTextInDarkMode();
  });
});

// Ensure white text on mount
onMounted(() => {
  ensureWhiteTextInDarkMode();
  
  // Set up a mutation observer to watch for theme changes
  const observer = new MutationObserver(() => {
    ensureWhiteTextInDarkMode();
  });
  
  observer.observe(document.documentElement, {
    attributes: true,
    attributeFilter: ['class']
  });
});

function startEditing() {
  editing.value = true;
  nextTick(() => {
    if (quillEditor.value) {
      quill = new Quill(quillEditor.value, {
        theme: 'snow',
        modules: {
          toolbar: [
            [{ 'header': [1, 2, 3, false] }],
            ['bold', 'italic', 'underline', 'strike'],
            [{ 'color': [] }, { 'background': [] }],
            [{ 'list': 'ordered'}, { 'list': 'bullet' }],
            [{ 'align': [] }],
            ['link', 'blockquote'],
            ['clean']
          ],
        },
        placeholder: 'Enter lesson content...',
      });
      quill.root.innerHTML = props.modelValue || '';
      
      // Apply theme-aware classes to Quill editor
      const toolbar = quill.container.querySelector('.ql-toolbar') as HTMLElement;
      const editor = quill.container.querySelector('.ql-editor') as HTMLElement;
      const container = quill.container as HTMLElement;
      
      // Check if dark mode is active
      const isDarkMode = document.documentElement.classList.contains('dark');
      
      if (toolbar) {
        toolbar.style.backgroundColor = isDarkMode ? '#1f2937' : '#ffffff';
        toolbar.style.borderBottomColor = isDarkMode ? '#4b5563' : '#e5e7eb';
        toolbar.style.color = isDarkMode ? '#f9fafb' : '#1f2937';
      }
      
      if (editor) {
        editor.style.backgroundColor = isDarkMode ? '#111827' : '#ffffff';
        editor.style.color = isDarkMode ? '#ffffff' : '#1f2937';
        
        // Apply styles to all content in editor
        const allElements = editor.querySelectorAll('*');
        allElements.forEach(el => {
          const element = el as HTMLElement;
          if (isDarkMode) {
            element.style.color = '#ffffff';
            element.style.backgroundColor = 'transparent';
          } else {
            element.style.color = '#1f2937';
            element.style.backgroundColor = 'transparent';
          }
        });
      }
      
      if (container) {
        container.style.backgroundColor = isDarkMode ? '#111827' : '#ffffff';
      }
      
      // Listen for theme changes while editing
      const themeObserver = new MutationObserver(() => {
        const isDark = document.documentElement.classList.contains('dark');
        if (toolbar) {
          toolbar.style.backgroundColor = isDark ? '#1f2937' : '#ffffff';
          toolbar.style.borderBottomColor = isDark ? '#4b5563' : '#e5e7eb';
          toolbar.style.color = isDark ? '#f9fafb' : '#1f2937';
        }
        if (editor) {
          editor.style.backgroundColor = isDark ? '#111827' : '#ffffff';
          editor.style.color = isDark ? '#ffffff' : '#1f2937';
          
          const allElements = editor.querySelectorAll('*');
          allElements.forEach(el => {
            const element = el as HTMLElement;
            if (isDark) {
              element.style.color = '#ffffff';
              element.style.backgroundColor = 'transparent';
            } else {
              element.style.color = '#1f2937';
              element.style.backgroundColor = 'transparent';
            }
          });
        }
        if (container) {
          container.style.backgroundColor = isDark ? '#111827' : '#ffffff';
        }
      });
      
      themeObserver.observe(document.documentElement, {
        attributes: true,
        attributeFilter: ['class']
      });
      
      // Clean up observer when editing stops
      quill.on('editor-change', () => {
        if (!editing.value) {
          themeObserver.disconnect();
        }
      });
    }
  });
}

// Function to clean HTML and add theme-responsive classes
function cleanContentForStorage(html: string): string {
  // Create a temporary DOM element to parse the HTML
  const tempDiv = document.createElement('div');
  tempDiv.innerHTML = html;
  
  // Function to clean an element and its children
  function cleanElement(element: Element) {
    // Remove inline color and background styles
    element.removeAttribute('style');
    
    // Add theme-responsive classes based on element type
    if (element.tagName === 'P') {
      element.classList.add('text-gray-900', 'dark:text-white');
    } else if (element.tagName.match(/^H[1-6]$/)) {
      element.classList.add('text-gray-900', 'dark:text-white', 'font-semibold');
    } else if (element.tagName === 'STRONG' || element.tagName === 'B') {
      element.classList.add('text-gray-900', 'dark:text-white', 'font-bold');
    } else if (element.tagName === 'EM' || element.tagName === 'I') {
      element.classList.add('text-gray-700', 'dark:text-gray-200', 'italic');
    } else if (element.tagName === 'A') {
      element.classList.add('text-blue-600', 'dark:text-blue-400', 'hover:underline');
    } else if (element.tagName === 'LI') {
      element.classList.add('text-gray-900', 'dark:text-white');
    } else if (element.tagName === 'UL' || element.tagName === 'OL') {
      element.classList.add('text-gray-900', 'dark:text-white');
    } else if (element.tagName === 'BLOCKQUOTE') {
      element.classList.add('text-gray-700', 'dark:text-gray-300', 'border-l-4', 'border-gray-300', 'dark:border-gray-600', 'pl-4', 'italic');
    } else {
      // For other elements, add basic text color classes
      element.classList.add('text-gray-900', 'dark:text-white');
    }
    
    // Recursively clean child elements
    Array.from(element.children).forEach(cleanElement);
  }
  
  // Clean all elements
  Array.from(tempDiv.children).forEach(cleanElement);
  
  return tempDiv.innerHTML;
}

async function saveContent() {
  if (!quill) return;
  const rawContent = quill.root.innerHTML;
  const cleanedContent = cleanContentForStorage(rawContent);
  saving.value = true;

  try {
    await axios.put(`lessons/${props.lessonId}`, {
      description: cleanedContent,
    });

    emit('update:modelValue', cleanedContent);
    stopEditing();
  } catch (error) {
    console.error('Failed to save lesson:', error);
    alert('Failed to save lesson content.');
  } finally {
    saving.value = false;
  }
}

function stopEditing() {
  editing.value = false;
  quill = null;
}
</script>

<style scoped>
.quill-editor-container .ql-container {
  min-height: 250px;
  font-size: 14px;
  line-height: 1.6;
}

.quill-editor-container .ql-editor {
  padding: 16px;
}

.quill-editor-container .ql-toolbar {
  border-bottom: 1px solid #e5e7eb;
  padding: 8px 16px;
}

/* Dark mode styles for Quill */
:global(.dark) .quill-editor-container .ql-toolbar {
  border-bottom-color: #4b5563 !important;
  background-color: #1f2937 !important;
  color: #f9fafb !important;
}

:global(.dark) .quill-editor-container .ql-editor {
  background-color: #111827 !important;
  color: #ffffff !important;
}

:global(.dark) .quill-editor-container .ql-editor * {
  background-color: transparent !important;
  color: #ffffff !important;
}

:global(.dark) .quill-editor-container .ql-editor p {
  color: #ffffff !important;
  background-color: transparent !important;
}

:global(.dark) .quill-editor-container .ql-editor div {
  color: #ffffff !important;
  background-color: transparent !important;
}

:global(.dark) .quill-editor-container .ql-editor span {
  color: #ffffff !important;
  background-color: transparent !important;
}

:global(.dark) .quill-editor-container .ql-editor::placeholder {
  color: #9ca3af !important;
}

:global(.dark) .quill-editor-container .ql-container {
  background-color: #111827 !important;
}

/* Toolbar button styles for dark mode */
:global(.dark) .quill-editor-container .ql-toolbar .ql-stroke {
  stroke: #d1d5db;
}

:global(.dark) .quill-editor-container .ql-toolbar .ql-fill {
  fill: #d1d5db;
}

:global(.dark) .quill-editor-container .ql-toolbar button:hover .ql-stroke {
  stroke: #3b82f6;
}

:global(.dark) .quill-editor-container .ql-toolbar button:hover .ql-fill {
  fill: #3b82f6;
}

:global(.dark) .quill-editor-container .ql-toolbar button.ql-active .ql-stroke {
  stroke: #3b82f6;
}

:global(.dark) .quill-editor-container .ql-toolbar button.ql-active .ql-fill {
  fill: #3b82f6;
}

/* Dropdown styles for dark mode */
:global(.dark) .quill-editor-container .ql-toolbar .ql-picker {
  color: #d1d5db;
}

:global(.dark) .quill-editor-container .ql-toolbar .ql-picker-options {
  background-color: #1f2937;
  border-color: #4b5563;
}

:global(.dark) .quill-editor-container .ql-toolbar .ql-picker-item:hover {
  background-color: #374151;
}

/* Content display styling */
.content-display {
  line-height: 1.6;
  background-color: transparent;
}

/* Ensure content respects Tailwind classes */
.content-display * {
  background-color: transparent;
}

/* Prose styles for better content display */
.prose {
  line-height: 1.7;
  color: inherit;
}

/* Force white text in prose for dark mode */
:global(.dark) .prose {
  color: #ffffff !important;
}

:global(.dark) .prose * {
  color: #ffffff !important;
}

.prose h1, .prose h2, .prose h3, .prose h4, .prose h5, .prose h6 {
  margin-top: 1.5em;
  margin-bottom: 0.5em;
  font-weight: 600;
  color: inherit;
}

.prose h1 {
  font-size: 1.5rem;
  color: #1f2937;
}

.prose h2 {
  font-size: 1.25rem;
  color: #374151;
}

.prose h3 {
  font-size: 1.125rem;
  color: #4b5563;
}

.prose h4, .prose h5, .prose h6 {
  font-size: 1rem;
  color: #6b7280;
}

.prose p {
  margin-bottom: 0.75em;
  color: inherit;
}

.prose ul, .prose ol {
  margin: 0.75em 0;
  padding-left: 1.5em;
  color: inherit;
}

.prose li {
  margin-bottom: 0.25em;
  color: inherit;
}

.prose blockquote {
  border-left: 4px solid #e5e7eb;
  padding-left: 1em;
  margin: 1em 0;
  font-style: italic;
  color: #6b7280;
  background-color: #f9fafb;
  padding: 1em;
  border-radius: 0.5rem;
}

.prose a {
  color: #3b82f6;
  text-decoration: underline;
  font-weight: 500;
}

.prose a:hover {
  color: #1d4ed8;
}

.prose strong, .prose b {
  font-weight: 600;
  color: inherit;
}

.prose em, .prose i {
  font-style: italic;
  color: inherit;
}

.prose code {
  background-color: #f3f4f6;
  padding: 0.25em 0.5em;
  border-radius: 0.25rem;
  font-size: 0.875em;
  color: #1f2937;
  font-family: 'Courier New', monospace;
}

.prose pre {
  background-color: #f3f4f6;
  padding: 1em;
  border-radius: 0.5rem;
  overflow-x: auto;
  color: #1f2937;
}

.prose table {
  width: 100%;
  border-collapse: collapse;
  margin: 1em 0;
}

.prose th, .prose td {
  border: 1px solid #d1d5db;
  padding: 0.5em;
  text-align: left;
}

.prose th {
  background-color: #f9fafb;
  font-weight: 600;
}

/* Dark mode prose styles */
:global(.dark) .prose h1 {
  color: #f9fafb;
}

:global(.dark) .prose h2 {
  color: #f3f4f6;
}

:global(.dark) .prose h3 {
  color: #e5e7eb;
}

:global(.dark) .prose h4,
:global(.dark) .prose h5,
:global(.dark) .prose h6 {
  color: #d1d5db;
}

:global(.dark) .prose blockquote {
  border-left-color: #4b5563;
  color: #9ca3af;
  background-color: #1f2937;
}

:global(.dark) .prose a {
  color: #60a5fa;
}

:global(.dark) .prose a:hover {
  color: #93c5fd;
}

:global(.dark) .prose code {
  background-color: #374151;
  color: #e5e7eb;
}

:global(.dark) .prose pre {
  background-color: #1f2937;
  color: #e5e7eb;
}

:global(.dark) .prose th,
:global(.dark) .prose td {
  border-color: #4b5563;
}

:global(.dark) .prose th {
  background-color: #374151;
  color: #f9fafb;
}

:global(.dark) .prose td {
  color: #d1d5db;
}

/* Ensure all content elements inherit theme colors */
:global(.dark) .prose * {
  color: #ffffff !important;
}

.prose * {
  color: inherit;
}

/* Override inherit for dark mode */
:global(.dark) .prose * {
  color: #ffffff !important;
}

/* Content display specific styling - Light mode */
.content-display {
  background-color: transparent !important;
  background: transparent !important;
}

.content-display * {
  background-color: transparent !important;
  background: transparent !important;
}

.content-display p {
  color: #374151;
  background-color: transparent !important;
  background: transparent !important;
  margin-bottom: 0.75em;
}

.content-display h1,
.content-display h2,
.content-display h3,
.content-display h4,
.content-display h5,
.content-display h6 {
  color: #1f2937;
  background-color: transparent !important;
  background: transparent !important;
  font-weight: 600;
  margin-top: 1.25em;
  margin-bottom: 0.5em;
}

.content-display ul,
.content-display ol {
  color: #374151;
  background-color: transparent !important;
  background: transparent !important;
  margin: 0.75em 0;
  padding-left: 1.5em;
}

.content-display li {
  color: #374151;
  background-color: transparent !important;
  background: transparent !important;
  margin-bottom: 0.25em;
}

.content-display strong,
.content-display b {
  color: #1f2937;
  background-color: transparent !important;
  background: transparent !important;
  font-weight: 600;
}

.content-display em,
.content-display i {
  color: #374151;
  background-color: transparent !important;
  background: transparent !important;
  font-style: italic;
}

.content-display div {
  background-color: transparent !important;
  background: transparent !important;
}

/* Dark mode content display - White text for dark backgrounds */
:global(.dark) .content-display {
  background-color: transparent !important;
  background: transparent !important;
}

:global(.dark) .content-display * {
  background-color: transparent !important;
  background: transparent !important;
  color: #ffffff !important;
}

:global(.dark) .content-display p {
  color: #ffffff !important;
  background-color: transparent !important;
  background: transparent !important;
}

:global(.dark) .content-display h1,
:global(.dark) .content-display h2,
:global(.dark) .content-display h3,
:global(.dark) .content-display h4,
:global(.dark) .content-display h5,
:global(.dark) .content-display h6 {
  color: #ffffff !important;
  background-color: transparent !important;
  background: transparent !important;
}

:global(.dark) .content-display ul,
:global(.dark) .content-display ol,
:global(.dark) .content-display li {
  color: #ffffff !important;
  background-color: transparent !important;
  background: transparent !important;
}

:global(.dark) .content-display div {
  background-color: transparent !important;
  background: transparent !important;
  color: #ffffff !important;
}

:global(.dark) .content-display strong,
:global(.dark) .content-display b {
  color: #ffffff !important;
  background-color: transparent !important;
  background: transparent !important;
}

:global(.dark) .content-display em,
:global(.dark) .content-display i {
  color: #ffffff !important;
  background-color: transparent !important;
  background: transparent !important;
}

:global(.dark) .content-display span {
  color: #ffffff !important;
  background-color: transparent !important;
  background: transparent !important;
}

:global(.dark) .content-display a {
  color: #60a5fa !important;
  background-color: transparent !important;
  background: transparent !important;
}

:global(.dark) .content-display a:hover {
  color: #93c5fd !important;
}

/* Additional overrides to prevent white backgrounds in dark mode */
:global(.dark) * {
  background-color: transparent;
}

:global(.dark) .prose {
  background-color: transparent !important;
}

:global(.dark) .prose * {
  background-color: transparent !important;
}

/* Force remove any potential Quill-generated white backgrounds */
:global(.dark) .ql-editor p {
  background-color: transparent !important;
  color: #ffffff !important;
}

:global(.dark) .ql-editor div {
  background-color: transparent !important;
  color: #ffffff !important;
}

:global(.dark) .ql-editor span {
  background-color: transparent !important;
  color: #ffffff !important;
}

/* Override any inline styles in dark mode */
:global(.dark) [style*="background-color: white"],
:global(.dark) [style*="background-color: #ffffff"],
:global(.dark) [style*="background-color: #fff"],
:global(.dark) [style*="background: white"],
:global(.dark) [style*="background: #ffffff"],
:global(.dark) [style*="background: #fff"] {
  background-color: transparent !important;
  background: transparent !important;
}

:global(.dark) [style*="color: white"],
:global(.dark) [style*="color: #ffffff"],
:global(.dark) [style*="color: #fff"] {
  color: #ffffff !important;
}

/* Ensure all text is white in dark mode */
:global(.dark) .content-display {
  color: #ffffff !important;
}

/* Force all prose content to be white in dark mode */
:global(.dark) .prose,
:global(.dark) .prose p,
:global(.dark) .prose div,
:global(.dark) .prose span,
:global(.dark) .prose h1,
:global(.dark) .prose h2,
:global(.dark) .prose h3,
:global(.dark) .prose h4,
:global(.dark) .prose h5,
:global(.dark) .prose h6,
:global(.dark) .prose li,
:global(.dark) .prose ul,
:global(.dark) .prose ol {
  color: #ffffff !important;
  background-color: transparent !important;
}

/* Override any potential inline styles */
:global(.dark) .content-display [style] {
  color: #ffffff !important;
  background-color: transparent !important;
}

/* Additional specific targeting for readonly content display */
:global(.dark) .prose.prose-sm {
  color: #ffffff !important;
}

:global(.dark) .prose.prose-sm * {
  color: #ffffff !important;
}

:global(.dark) .prose.prose-sm .content-display {
  color: #ffffff !important;
}

:global(.dark) .prose.prose-sm .content-display * {
  color: #ffffff !important;
}

/* Ultimate override - force all text to white in dark mode */
:global(.dark) .prose *,
:global(.dark) .content-display *,
:global(.dark) .prose p,
:global(.dark) .prose div,
:global(.dark) .prose span,
:global(.dark) .prose strong,
:global(.dark) .prose em,
:global(.dark) .prose b,
:global(.dark) .prose i,
:global(.dark) .prose h1,
:global(.dark) .prose h2,
:global(.dark) .prose h3,
:global(.dark) .prose h4,
:global(.dark) .prose h5,
:global(.dark) .prose h6,
:global(.dark) .prose li,
:global(.dark) .prose ul,
:global(.dark) .prose ol {
  color: #ffffff !important;
  background-color: transparent !important;
}

/* Override inherit color specifically */
:global(.dark) .prose * {
  color: #ffffff !important;
}

/* Target the specific div that contains the content */
:global(.dark) .prose .content-display {
  color: #ffffff !important;
}

:global(.dark) .prose .content-display * {
  color: #ffffff !important;
}

/* Clean fallback for any remaining inline styles */
.content-display *[style] {
  background-color: transparent !important;
}
</style>
