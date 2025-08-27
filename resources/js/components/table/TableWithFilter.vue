<template>
  <!-- Toggle Button: Visible on mobile only -->
  <div class="sm:hidden p-4">
    <button @click="toggleFilters"
      class="w-full border border-none shadow-md bg-white rounded px-4 py-2 flex justify-between items-center">
      <span class="text-sm font-medium"> {{ showFilters ?'Hide Filters ':'Show Filters'}}</span>
      <svg :class="{ 'rotate-180': showFilters }" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" stroke-width="2"
        viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
      </svg>
    </button>
  </div>
 <!-- Collapsible Filter Panel (Mobile only collapsible) -->
  <div
    :class="[
      'sm:block sm:max-h-none sm:static transition-all duration-300 overflow-hidden',
      showFilters ? 'max-h-[1000px]' : 'max-h-0'
    ]"
    class="w-full px-4 sm:px-0"
  >
    <div class="bg-white shadow-xl border rounded p-4 mt-2 sm:mt-0">
      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 items-end">
        <!-- From Date -->
        <div>
          <label class="block text-sm font-medium mb-1">From Date</label>
          <input type="date" v-model="filters.fromStartDate" class="border p-2 rounded w-full" />
        </div>

        <!-- To Date -->
        <div>
          <label class="block text-sm font-medium mb-1">To Date</label>
          <input type="date" v-model="filters.toStartDate" class="border p-2 rounded w-full" />
        </div>

        <!-- Status -->
        <div>
          <label class="block text-sm font-medium mb-1">Status</label>
          <select v-model="filters.status" class="border p-2 rounded w-full">
            <option value="">All</option>
            <option v-for="status in statusOptions" :key="status" :value="status">{{ status }}</option>
          </select>
        </div>

        <!-- Activity Types -->
        <div ref="dropdownRef" class="w-full border px-4 py-2 rounded bg-white text-left">
          <label class="block text-sm font-medium mb-1">Activity Types</label>
<button @click="dropdownOpen = !dropdownOpen"
  class="w-full border px-4 py-2 rounded bg-white text-left">
  {{
    filters.activityTypes && filters.activityTypes.length > 0
      ? filters.activityTypes.join(', ')
      : (activityTypeOptions.length === 0 ? 'NO Activity TYPE' : 'Select Activity Type')
  }}
</button>

          <!-- Dropdown -->
          <div v-if="dropdownOpen"
            class="absolute z-20 mt-1 bg-white border rounded shadow-md w-full max-h-60 overflow-y-auto">
            <div v-for="type in activityTypeOptions" :key="type"
              class="flex items-center px-4 py-2 hover:bg-gray-100">
              <input type="checkbox" :value="type" v-model="filters.activityTypes" class="mr-2" />
              <span class="text-sm">{{ type }}</span>
            </div>
            <div class="p-2 border-t flex justify-between items-center text-xs">
              <button @click="filters.activityTypes = []" class="text-red-600 hover:underline">Clear</button>
              <button @click="dropdownOpen = false" class="text-blue-600 hover:underline">Close</button>
            </div>
          </div>
        </div>

        <!-- Fetch Button -->
        <div>
          <button @click="applyFilter"
            class="w-full bg-gray-900 text-white px-4 py-2 rounded hover:bg-gray-500">
            Fetch
          </button>
        </div>
      </div>
    </div>
  </div>
    <!-- Chart Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 p-4">
        <div class="max-w-sm w-full mx-auto shadow-xl rounded-lg overflow-hidden"></div>
        <div class="max-w-sm w-full mx-auto shadow-xl rounded-lg overflow-hidden"></div>
        <div class="max-w-sm w-full mx-auto shadow-xl rounded-lg overflow-hidden">
            <div class="bg-gray-900 px-4 py-3 border-b border-gray-200">
                <h2 class="text-sm font-semibold  text-white">Field Task Type Distribution</h2>
            </div>
            <PieChart :results="filteredResults" />
        </div>

        <div class="w-full mx-auto rounded-lg overflow-hidden">
            <CounterCard :number="pendingCount" text="Pending Activities" textClass="text-green-900">
                <template #icon>
                    <ChartBarIcon class="w-8 h-4" />
                </template>
            </CounterCard>
            <CounterCard :number="inProgressCount" text="In Progress Activities" textClass="text-yellow-700">
                <template #icon>
                    <ChartBarIcon class="w-8 h-4" />
                </template>
            </CounterCard>
            <CounterCard :number="completedCount" text="Completed Activities">
                <template #icon>
                    <ChartBarIcon class="w-8 h-4" />
                </template>
            </CounterCard>
        </div>
    </div>

    <!-- Main Section -->
    <div class="p-4 sm:p-6 max-w-7xl mx-auto shadow-xl rounded-lg">
        <h2 class="text-xl sm:text-2xl font-bold mb-4">Field Activities</h2>

    

        <!-- Table -->
        <div v-if="loading" class="text-gray-500">Loading...</div>
        <div v-else class="overflow-x-auto">
            <table class="min-w-full bg-white border mb-6 text-sm">
                <thead class="bg-gray-900 text-left text-white">
                    <tr>
                        <th class="py-2 px-4 border-b">Activity ID</th>
                        <th class="py-2 px-4 border-b">Status</th>
                        <th class="py-2 px-4 border-b">Start Date</th>
                        <th class="py-2 px-4 border-b">Activity Type</th>
                        <th class="py-2 px-4 border-b">Field Task Type</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="activity in paginatedResults" :key="activity.activityId">
                        <td class="py-2 px-4 border-b text-blue-600 hover:underline cursor-pointer"
                            @click="openModal(activity)">
                            {{ activity.activityId }}
                        </td>
                        <td class="py-2 px-4 border-b">{{ activity.status }}</td>
                        <td class="py-2 px-4 border-b">{{ formatDate(activity.startDate) }}</td>
                        <td class="py-2 px-4 border-b">{{ activity.activityType }}</td>
                        <td class="py-2 px-4 border-b">{{ activity.fieldTaskType }}</td>
                    </tr>
                    <tr v-if="paginatedResults.length === 0">
                        <td colspan="5" class="text-center py-4 text-gray-500">No data found</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div v-if="totalPages > 1" class="flex flex-wrap justify-center items-center gap-2">
            <button @click="currentPage--" :disabled="currentPage === 1"
                class="px-3 py-1 border rounded hover:bg-gray-100 disabled:opacity-50">
                Prev
            </button>
            <span>Page {{ currentPage }} of {{ totalPages }}</span>
            <button @click="currentPage++" :disabled="currentPage === totalPages"
                class="px-3 py-1 border rounded hover:bg-gray-100 disabled:opacity-50">
                Next
            </button>
        </div>
    </div>

    <Transition name="slide">
        <div v-if="showModal" class="fixed inset-0 z-40 bg-black/50 flex justify-end">
            <div class="bg-white h-full w-full sm:w-1/2 max-w-full p-6 relative z-50 shadow-xl overflow-y-auto">
                <button class="absolute top-4 right-4 text-gray-600 hover:text-gray-900 text-2xl" @click="closeModal">
                    &times;
                </button>
                <h3 class="text-lg font-semibold mb-4 mt-2 pr-8">Activity Details</h3>
                <div v-if="selectedActivity">
                    <FieldActivityDetails :activityId="selectedActivity.activityId" />
                </div>
                <button class="mt-6 bg-gray-800 text-white px-4 py-2 rounded hover:bg-gray-700 block sm:hidden"
                    @click="closeModal">
                    Close
                </button>
            </div>
        </div>
    </Transition>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'
import { ChartBarIcon } from '@heroicons/vue/24/outline'
import PieChart from '@/components/charts/PieChart.vue';
import CounterCard from '../cards/CounterCard.vue';
import FieldActivityDetails from '../fieldActivity/FieldActivityDetails.vue';
import { onBeforeUnmount } from 'vue'
const today = new Date().toISOString().slice(0, 10)
const dropdownOpen = ref(false)
const filters = ref({
    fromStartDate: today,
    toStartDate: today,
    status: '',
    activityTypes: []
})
const showFilters = ref(false)

const toggleFilters = () => {
  showFilters.value = !showFilters.value
}

const showModal = ref(false)
const selectedActivity = ref(null)
const dropdownRef = ref(null)
useClickOutside(dropdownRef, () => dropdownOpen.value = false)
const allResults = ref([])
const filteredResults = ref([])
const loading = ref(false)
const currentPage = ref(1)
const pageSize = 100

const openModal = (activity) => {
    selectedActivity.value = activity
    showModal.value = true
}

const closeModal = () => {
    showModal.value = false
    selectedActivity.value = null
}
function useClickOutside(refElement, callback) {
    const handler = (event) => {
        if (refElement.value && !refElement.value.contains(event.target)) {
            callback()
        }
    }

    onMounted(() => document.addEventListener('click', handler))
    onBeforeUnmount(() => document.removeEventListener('click', handler))
}

const statusOptions = computed(() => {
    return [...new Set(allResults.value.map(a => a.status))].filter(Boolean)
})

const activityTypeOptions = computed(() => {
    return [...new Set(allResults.value.map(a => a.activityType))].filter(Boolean)
})

const completedCount = computed(() =>
    allResults.value.filter(item => item.status === 'COMPLETED').length
)

const pendingCount = computed(() =>
    allResults.value.filter(item => item.status === 'PENDING').length
)

const inProgressCount = computed(() =>
    allResults.value.filter(item => item.status === 'ACTPROGRESS').length
)

const totalPages = computed(() => {
    return Math.ceil(filteredResults.value.length / pageSize)
})

const paginatedResults = computed(() => {
    const start = (currentPage.value - 1) * pageSize
    const end = start + pageSize
    return filteredResults.value.slice(start, end)
})

const formatDate = (startDate) => startDate.replace('-00.00.00', '')

const applyFilter = async () => {
    loading.value = true
    try {
        const { data } = await axios.get('/field-activity/getActivities', {
            params: {
                fromStartDate: filters.value.fromStartDate,
                toStartDate: filters.value.toStartDate
            }
        })

        allResults.value = data.results || []

        filteredResults.value = allResults.value.filter(item => {
            const date = item.startDate.slice(0, 10)
            const inRange =
                (!filters.value.fromStartDate || date >= filters.value.fromStartDate) &&
                (!filters.value.toStartDate || date <= filters.value.toStartDate)

            const statusMatch =
                !filters.value.status || item.status === filters.value.status

            const activityTypeMatch =
                filters.value.activityTypes.length === 0 ||
                filters.value.activityTypes.includes(item.activityType)

            return inRange && statusMatch && activityTypeMatch
        })

        currentPage.value = 1
    } catch (e) {
        console.error('API error', e)
    } finally {
        loading.value = false
    }
}

onMounted(() => {
    applyFilter()
})
</script>

<style scoped>
.slide-enter-active,
.slide-leave-active {
    transition: transform 0.3s ease;
}

.slide-enter-from {
    transform: translateX(100%);
}

.slide-leave-to {
    transform: translateX(100%);
}
</style>
