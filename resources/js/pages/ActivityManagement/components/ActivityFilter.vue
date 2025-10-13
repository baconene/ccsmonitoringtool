<script setup lang="ts">
import { ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import { Search, Filter, X } from 'lucide-vue-next';

interface ActivityType {
    id: number;
    name: string;
    description: string | null;
}

interface Filters {
    search: string | null;
    type: number | null;
    date_from: string | null;
    date_to: string | null;
}

interface Props {
    activityTypes: ActivityType[];
    filters: Filters;
}

const props = defineProps<Props>();

const search = ref(props.filters.search || '');
const selectedType = ref(props.filters.type || '');
const dateFrom = ref(props.filters.date_from || '');
const dateTo = ref(props.filters.date_to || '');
const showFilters = ref(false);

// Watch for changes and apply filters with debounce
let timeout: number;
watch([search, selectedType, dateFrom, dateTo], () => {
    clearTimeout(timeout);
    timeout = setTimeout(() => {
        applyFilters();
    }, 300);
});

const applyFilters = () => {
    router.get(
        '/activity-management',
        {
            search: search.value || undefined,
            type: selectedType.value || undefined,
            date_from: dateFrom.value || undefined,
            date_to: dateTo.value || undefined,
        },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        }
    );
};

const clearFilters = () => {
    search.value = '';
    selectedType.value = '';
    dateFrom.value = '';
    dateTo.value = '';
    showFilters.value = false;
    applyFilters();
};

const hasActiveFilters = () => {
    return search.value || selectedType.value || dateFrom.value || dateTo.value;
};
</script>

<template>
    <div class="space-y-4">
        <!-- Search Bar and Filter Toggle -->
        <div class="flex flex-col sm:flex-row gap-3">
            <!-- Search Input -->
            <div class="flex-1 relative">
                <Search
                    class="absolute left-3 top-1/2 transform -translate-y-1/2 text-purple-400"
                    :size="20"
                />
                <input
                    v-model="search"
                    type="text"
                    placeholder="Search activities..."
                    class="w-full pl-10 pr-4 py-2 sm:py-2.5 border border-purple-300 dark:border-purple-700 rounded-lg bg-white/80 dark:bg-gray-700/80 backdrop-blur-sm text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 shadow-lg transition-all"
                />
            </div>

            <!-- Filter Toggle Button -->
            <button
                @click="showFilters = !showFilters"
                :class="[
                    hasActiveFilters()
                        ? 'bg-gradient-to-r from-purple-600 to-pink-600 text-white shadow-lg'
                        : 'bg-white/80 dark:bg-gray-700/80 backdrop-blur-sm text-gray-700 dark:text-gray-300 border border-purple-300 dark:border-purple-700',
                ]"
                class="px-4 py-2 sm:py-2.5 rounded-lg flex items-center justify-center gap-2 hover:scale-105 transition-all shadow-lg"
            >
                <Filter :size="20" />
                <span class="font-medium">Filters</span>
                <span
                    v-if="hasActiveFilters()"
                    class="px-2 py-0.5 bg-white/20 rounded-full text-xs"
                >
                    Active
                </span>
            </button>

            <!-- Clear Filters Button -->
            <button
                v-if="hasActiveFilters()"
                @click="clearFilters"
                class="px-4 py-2 sm:py-2.5 border border-red-300 dark:border-red-600 bg-white/80 dark:bg-gray-700/80 backdrop-blur-sm text-red-600 dark:text-red-400 rounded-lg flex items-center justify-center gap-2 hover:bg-red-50 dark:hover:bg-red-900/20 transition-all shadow-lg hover:scale-105"
            >
                <X :size="20" />
                <span class="font-medium">Clear</span>
            </button>
        </div>

        <!-- Advanced Filters Panel -->
        <transition
            enter-active-class="transition ease-out duration-200"
            enter-from-class="opacity-0 transform -translate-y-2"
            enter-to-class="opacity-100 transform translate-y-0"
            leave-active-class="transition ease-in duration-150"
            leave-from-class="opacity-100 transform translate-y-0"
            leave-to-class="opacity-0 transform -translate-y-2"
        >
            <div
                v-if="showFilters"
                class="p-4 sm:p-6 bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm border border-purple-200/50 dark:border-purple-700/50 rounded-xl shadow-lg"
            >
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <!-- Activity Type Filter -->
                    <div>
                        <label
                            for="activity-type"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                        >
                            Activity Type
                        </label>
                        <select
                            id="activity-type"
                            v-model="selectedType"
                            class="w-full px-3 py-2 border border-purple-300 dark:border-purple-700 rounded-lg bg-white/50 dark:bg-gray-700/50 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all"
                        >
                            <option value="">All Types</option>
                            <option
                                v-for="type in activityTypes"
                                :key="type.id"
                                :value="type.id"
                            >
                                {{ type.name }}
                            </option>
                        </select>
                    </div>

                    <!-- Date From Filter -->
                    <div>
                        <label
                            for="date-from"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                        >
                            Created From
                        </label>
                        <input
                            id="date-from"
                            v-model="dateFrom"
                            type="date"
                            class="w-full px-3 py-2 border border-purple-300 dark:border-purple-700 rounded-lg bg-white/50 dark:bg-gray-700/50 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all"
                        />
                    </div>

                    <!-- Date To Filter -->
                    <div>
                        <label
                            for="date-to"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                        >
                            Created To
                        </label>
                        <input
                            id="date-to"
                            v-model="dateTo"
                            type="date"
                            class="w-full px-3 py-2 border border-purple-300 dark:border-purple-700 rounded-lg bg-white/50 dark:bg-gray-700/50 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all"
                        />
                    </div>
                </div>
            </div>
        </transition>
    </div>
</template>
