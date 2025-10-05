<script setup lang="ts">
import { ref, computed } from 'vue';
import { Search, Filter, X } from 'lucide-vue-next';

// Props
defineProps<{
  resultsCount: number;
  totalCount: number;
}>();

// Emits
const emit = defineEmits<{
  (e: 'update:searchQuery', value: string): void;
  (e: 'update:selectedRoles', value: string[]): void;
  (e: 'update:selectedStatuses', value: string[]): void;
  (e: 'update:selectedDateRange', value: string): void;
  (e: 'clearFilters'): void;
}>();

// Local state
const searchQuery = ref('');
const selectedRoles = ref<string[]>([]);
const selectedStatuses = ref<string[]>([]);
const selectedDateRange = ref<string>('all');
const showFilters = ref(false);

// Check if any filters are active
const hasActiveFilters = computed(() => {
  return searchQuery.value.trim() !== '' ||
         selectedRoles.value.length > 0 ||
         selectedStatuses.value.length > 0 ||
         selectedDateRange.value !== 'all';
});

// Watch and emit changes
const updateSearchQuery = (value: string) => {
  searchQuery.value = value;
  emit('update:searchQuery', value);
};

const updateSelectedRoles = (roles: string[]) => {
  selectedRoles.value = roles;
  emit('update:selectedRoles', roles);
};

const updateSelectedStatuses = (statuses: string[]) => {
  selectedStatuses.value = statuses;
  emit('update:selectedStatuses', statuses);
};

const updateSelectedDateRange = (range: string) => {
  selectedDateRange.value = range;
  emit('update:selectedDateRange', range);
};

// Clear all filters
const clearFilters = () => {
  searchQuery.value = '';
  selectedRoles.value = [];
  selectedStatuses.value = [];
  selectedDateRange.value = 'all';
  emit('clearFilters');
};

// Toggle role selection
const toggleRole = (role: string) => {
  const index = selectedRoles.value.indexOf(role);
  if (index > -1) {
    selectedRoles.value.splice(index, 1);
  } else {
    selectedRoles.value.push(role);
  }
  updateSelectedRoles(selectedRoles.value);
};

// Toggle status selection
const toggleStatus = (status: string) => {
  const index = selectedStatuses.value.indexOf(status);
  if (index > -1) {
    selectedStatuses.value.splice(index, 1);
  } else {
    selectedStatuses.value.push(status);
  }
  updateSelectedStatuses(selectedStatuses.value);
};
</script>

<template>
  <div class="space-y-4">
    <!-- Search Bar -->
    <div class="relative">
      <div class="relative">
        <Search class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" />
        <input
          :value="searchQuery"
          @input="updateSearchQuery(($event.target as HTMLInputElement).value)"
          type="text"
          placeholder="Search by name, email, role, grade level, or section..."
          class="w-full pl-10 pr-10 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
        />
        <button
          v-if="searchQuery"
          @click="updateSearchQuery('')"
          class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
        >
          <X class="w-5 h-5" />
        </button>
      </div>
    </div>

    <!-- Filter Toggle Button -->
    <div class="flex items-center justify-between">
      <button
        @click="showFilters = !showFilters"
        class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors"
      >
        <Filter class="w-4 h-4 mr-2" />
        {{ showFilters ? 'Hide Filters' : 'Show Filters' }}
        <span v-if="hasActiveFilters" class="ml-2 px-2 py-0.5 text-xs bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 rounded-full">
          Active
        </span>
      </button>

      <button
        v-if="hasActiveFilters"
        @click="clearFilters"
        class="inline-flex items-center px-3 py-2 text-sm font-medium text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300"
      >
        <X class="w-4 h-4 mr-1" />
        Clear Filters
      </button>
    </div>

    <!-- Advanced Filters -->
    <transition
      enter-active-class="transition ease-out duration-200"
      enter-from-class="transform opacity-0 -translate-y-2"
      enter-to-class="transform opacity-100 translate-y-0"
      leave-active-class="transition ease-in duration-150"
      leave-from-class="transform opacity-100 translate-y-0"
      leave-to-class="transform opacity-0 -translate-y-2"
    >
      <div v-if="showFilters" class="grid grid-cols-1 sm:grid-cols-3 gap-4 p-4 bg-gray-50 dark:bg-gray-900/50 rounded-lg border border-gray-200 dark:border-gray-700">
        <!-- Role Filter (Multi-select) -->
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
            Role
            <span v-if="selectedRoles.length > 0" class="ml-2 text-xs text-blue-600 dark:text-blue-400">({{ selectedRoles.length }} selected)</span>
          </label>
          <div class="space-y-2">
            <label class="flex items-center cursor-pointer group">
              <input
                type="checkbox"
                :checked="selectedRoles.includes('admin')"
                @change="toggleRole('admin')"
                class="w-4 h-4 text-blue-600 bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 rounded focus:ring-blue-500 dark:focus:ring-blue-600 focus:ring-2 cursor-pointer"
              />
              <span class="ml-2 text-sm text-gray-700 dark:text-gray-300 group-hover:text-gray-900 dark:group-hover:text-white">Admin</span>
            </label>
            <label class="flex items-center cursor-pointer group">
              <input
                type="checkbox"
                :checked="selectedRoles.includes('instructor')"
                @change="toggleRole('instructor')"
                class="w-4 h-4 text-blue-600 bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 rounded focus:ring-blue-500 dark:focus:ring-blue-600 focus:ring-2 cursor-pointer"
              />
              <span class="ml-2 text-sm text-gray-700 dark:text-gray-300 group-hover:text-gray-900 dark:group-hover:text-white">Instructor</span>
            </label>
            <label class="flex items-center cursor-pointer group">
              <input
                type="checkbox"
                :checked="selectedRoles.includes('student')"
                @change="toggleRole('student')"
                class="w-4 h-4 text-blue-600 bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 rounded focus:ring-blue-500 dark:focus:ring-blue-600 focus:ring-2 cursor-pointer"
              />
              <span class="ml-2 text-sm text-gray-700 dark:text-gray-300 group-hover:text-gray-900 dark:group-hover:text-white">Student</span>
            </label>
          </div>
        </div>

        <!-- Status Filter (Multi-select) -->
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
            Status
            <span v-if="selectedStatuses.length > 0" class="ml-2 text-xs text-blue-600 dark:text-blue-400">({{ selectedStatuses.length }} selected)</span>
          </label>
          <div class="space-y-2">
            <label class="flex items-center cursor-pointer group">
              <input
                type="checkbox"
                :checked="selectedStatuses.includes('verified')"
                @change="toggleStatus('verified')"
                class="w-4 h-4 text-blue-600 bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 rounded focus:ring-blue-500 dark:focus:ring-blue-600 focus:ring-2 cursor-pointer"
              />
              <span class="ml-2 text-sm text-gray-700 dark:text-gray-300 group-hover:text-gray-900 dark:group-hover:text-white">Verified</span>
            </label>
            <label class="flex items-center cursor-pointer group">
              <input
                type="checkbox"
                :checked="selectedStatuses.includes('unverified')"
                @change="toggleStatus('unverified')"
                class="w-4 h-4 text-blue-600 bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 rounded focus:ring-blue-500 dark:focus:ring-blue-600 focus:ring-2 cursor-pointer"
              />
              <span class="ml-2 text-sm text-gray-700 dark:text-gray-300 group-hover:text-gray-900 dark:group-hover:text-white">Unverified</span>
            </label>
          </div>
        </div>

        <!-- Date Range Filter -->
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
            Created Date
          </label>
          <select
            :value="selectedDateRange"
            @change="updateSelectedDateRange(($event.target as HTMLSelectElement).value)"
            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
          >
            <option value="all">All Time</option>
            <option value="today">Today</option>
            <option value="week">Last 7 Days</option>
            <option value="month">Last 30 Days</option>
            <option value="year">Last Year</option>
          </select>
        </div>
      </div>
    </transition>

    <!-- Results Count -->
    <div class="text-sm text-gray-600 dark:text-gray-400">
      Showing <span class="font-semibold text-gray-900 dark:text-white">{{ resultsCount }}</span> of <span class="font-semibold text-gray-900 dark:text-white">{{ totalCount }}</span> users
    </div>
  </div>
</template>
