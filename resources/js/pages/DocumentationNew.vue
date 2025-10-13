<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import { 
  Book, 
  Home, 
  Users, 
  BookOpen, 
  GraduationCap, 
  Settings, 
  ChevronRight,
  Menu,
  X,
  Search
} from 'lucide-vue-next';
import { documentationSections, documentationCategories, getSectionById, searchDocumentation } from '@/data/documentationContent';

// State
const currentSection = ref('introduction');
const searchQuery = ref('');
const sidebarOpen = ref(false);

// Computed
const currentContent = computed(() => {
  return getSectionById(currentSection.value);
});

const filteredSections = computed(() => {
  if (!searchQuery.value.trim()) {
    return documentationSections;
  }
  return searchDocumentation(searchQuery.value);
});

// Methods
const navigateToSection = (sectionId: string) => {
  currentSection.value = sectionId;
  sidebarOpen.value = false;
  window.scrollTo({ top: 0, behavior: 'smooth' });
};

const toggleSidebar = () => {
  sidebarOpen.value = !sidebarOpen.value;
};
</script>

<template>
  <div>
    <Head title="Documentation - AstroLearn LMS" />
    
    <div class="min-h-screen bg-gradient-to-br from-gray-50 via-blue-50/30 to-purple-50/30 dark:from-gray-900 dark:via-blue-950/20 dark:to-purple-950/20">
      <!-- Header -->
      <div class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 sticky top-0 z-40">
        <div class="container mx-auto px-4 py-4">
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
              <!-- Mobile Menu Toggle -->
              <button
                @click="toggleSidebar"
                class="lg:hidden p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700"
              >
                <Menu v-if="!sidebarOpen" class="w-6 h-6" />
                <X v-else class="w-6 h-6" />
              </button>

              <!-- Logo and Title -->
              <Link href="/dashboard" class="flex items-center gap-3">
                <div class="p-2 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg">
                  <Book class="w-6 h-6 text-white" />
                </div>
                <div>
                  <h1 class="text-xl font-bold text-gray-900 dark:text-white">AstroLearn LMS</h1>
                  <p class="text-xs text-gray-600 dark:text-gray-400">Documentation</p>
                </div>
              </Link>
            </div>

            <!-- Back to Dashboard -->
            <Link
              href="/dashboard"
              class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition-colors"
            >
              <Home class="w-4 h-4" />
              <span class="hidden sm:inline">Back to Dashboard</span>
            </Link>
          </div>
        </div>
      </div>

      <div class="container mx-auto px-4 py-6 lg:py-8">
        <div class="flex gap-8">
          <!-- Sidebar -->
          <aside
            :class="[
              'fixed lg:sticky top-[73px] left-0 w-80 h-[calc(100vh-73px)] bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 overflow-y-auto z-30 transition-transform duration-300',
              sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'
            ]"
          >
            <div class="p-6">
              <!-- Search -->
              <div class="mb-6">
                <div class="relative">
                  <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                  <input
                    v-model="searchQuery"
                    type="text"
                    placeholder="Search documentation..."
                    class="w-full pl-10 pr-4 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                  />
                </div>
              </div>

              <!-- Navigation -->
              <nav class="space-y-6">
                <div
                  v-for="(category, key) in documentationCategories"
                  :key="key"
                  class="space-y-2"
                >
                  <h3 class="text-xs font-bold uppercase text-gray-500 dark:text-gray-400 mb-2">
                    {{ category.icon }} {{ category.title }}
                  </h3>
                  <button
                    v-for="section in category.sections"
                    :key="section.id"
                    @click="navigateToSection(section.id)"
                    :class="[
                      'w-full text-left px-3 py-2 rounded-lg text-sm transition-colors flex items-center justify-between group',
                      currentSection === section.id
                        ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 font-medium'
                        : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'
                    ]"
                  >
                    <span>{{ section.title }}</span>
                    <ChevronRight
                      :class="[
                        'w-4 h-4 transition-transform',
                        currentSection === section.id ? 'text-blue-600' : 'text-gray-400 group-hover:text-gray-600'
                      ]"
                    />
                  </button>
                </div>
              </nav>
            </div>
          </aside>

          <!-- Mobile Overlay -->
          <div
            v-if="sidebarOpen"
            @click="toggleSidebar"
            class="fixed inset-0 bg-black/50 z-20 lg:hidden"
          ></div>

          <!-- Main Content -->
          <main class="flex-1 min-w-0">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6 lg:p-8">
              <!-- Content Header -->
              <div class="mb-8 pb-6 border-b border-gray-200 dark:border-gray-700">
                <h1 class="text-3xl lg:text-4xl font-bold text-gray-900 dark:text-white mb-2">
                  {{ currentContent?.title }}
                </h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                  {{ documentationCategories[currentContent?.category || 'getting-started'].title }}
                </p>
              </div>

              <!-- Content Body -->
              <div
                v-if="currentContent"
                class="documentation-content"
                v-html="currentContent.content"
              ></div>

              <!-- Search Results -->
              <div v-if="searchQuery.trim() && filteredSections.length === 0" class="text-center py-12">
                <Search class="w-16 h-16 text-gray-400 mx-auto mb-4" />
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No results found</h3>
                <p class="text-gray-600 dark:text-gray-400">Try adjusting your search query</p>
              </div>

              <!-- Navigation Footer -->
              <div class="mt-12 pt-6 border-t border-gray-200 dark:border-gray-700 flex justify-between items-center">
                <button
                  v-if="currentContent && documentationSections.indexOf(currentContent) > 0"
                  @click="navigateToSection(documentationSections[documentationSections.indexOf(currentContent) - 1].id)"
                  class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/30 rounded-lg transition-colors"
                >
                  <ChevronRight class="w-4 h-4 rotate-180" />
                  Previous
                </button>
                <div v-else></div>

                <button
                  v-if="currentContent && documentationSections.indexOf(currentContent) < documentationSections.length - 1"
                  @click="navigateToSection(documentationSections[documentationSections.indexOf(currentContent) + 1].id)"
                  class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/30 rounded-lg transition-colors"
                >
                  Next
                  <ChevronRight class="w-4 h-4" />
                </button>
                <div v-else></div>
              </div>
            </div>

            <!-- Quick Links -->
            <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-4">
              <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                <h4 class="font-bold text-blue-900 dark:text-blue-300 mb-2 flex items-center gap-2">
                  <BookOpen class="w-4 h-4" />
                  Getting Started
                </h4>
                <p class="text-sm text-blue-700 dark:text-blue-400">
                  New to AstroLearn? Start here to learn the basics.
                </p>
              </div>

              <div class="bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800 rounded-lg p-4">
                <h4 class="font-bold text-purple-900 dark:text-purple-300 mb-2 flex items-center gap-2">
                  <Users class="w-4 h-4" />
                  User Guides
                </h4>
                <p class="text-sm text-purple-700 dark:text-purple-400">
                  Learn about different roles and their capabilities.
                </p>
              </div>

              <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
                <h4 class="font-bold text-green-900 dark:text-green-300 mb-2 flex items-center gap-2">
                  <Settings class="w-4 h-4" />
                  Advanced Features
                </h4>
                <p class="text-sm text-green-700 dark:text-green-400">
                  Explore advanced features and customization options.
                </p>
              </div>
            </div>
          </main>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Documentation Content Styling */
.documentation-content :deep(h3) {
  @apply text-2xl font-bold mt-8 mb-4 text-gray-900 dark:text-white;
}

.documentation-content :deep(h4) {
  @apply text-xl font-bold mt-6 mb-3 text-gray-900 dark:text-white;
}

.documentation-content :deep(p) {
  @apply text-gray-700 dark:text-gray-300 leading-relaxed mb-4;
}

.documentation-content :deep(ul) {
  @apply list-disc pl-6 space-y-2 mb-4 text-gray-700 dark:text-gray-300;
}

.documentation-content :deep(ol) {
  @apply list-decimal pl-6 space-y-2 mb-4 text-gray-700 dark:text-gray-300;
}

.documentation-content :deep(code) {
  @apply px-2 py-1 bg-gray-100 dark:bg-gray-700 rounded text-sm font-mono text-pink-600 dark:text-pink-400;
}

.documentation-content :deep(table) {
  @apply w-full border-collapse mb-6;
}

.documentation-content :deep(th) {
  @apply bg-gray-100 dark:bg-gray-700 font-semibold text-left p-3 border-b-2 border-gray-300 dark:border-gray-600;
}

.documentation-content :deep(td) {
  @apply p-3 border-b border-gray-200 dark:border-gray-700;
}

.documentation-content :deep(strong) {
  @apply font-semibold text-gray-900 dark:text-white;
}

.documentation-content :deep(.bg-blue-50) {
  @apply dark:bg-blue-900/20;
}

.documentation-content :deep(.border-blue-200) {
  @apply dark:border-blue-800;
}

/* Add similar classes for other colors */
</style>
