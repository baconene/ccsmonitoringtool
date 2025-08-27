<template>
  <div class="w-full">
    <div class="flex justify-end p-4">
      <button
        @click="showExportModal = true"
        class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm"
      >
        Export CSV
      </button>
    </div>

<!-- Scrollable Table Container -->
<div class="overflow-auto max-h-[70vh] border shadow rounded w-full">
  <table class="w-full min-w-[800px] text-sm divide-y divide-gray-300">
    <!-- Sticky Table Header -->
    <thead class="bg-gray-100 sticky top-0 z-10">
      <tr>
        <th
          v-for="header in headers"
          :key="header"
          class="px-4 py-2 text-left font-medium text-gray-700 cursor-pointer whitespace-nowrap bg-gray-100"
          @click="toggleSort(header)"
        >
          {{ header }}
          <span v-if="sortField === header">
            {{ sortDirection === 'asc' ? '▲' : '▼' }}
          </span>
        </th>
      </tr>

      <!-- Filter Row -->
      <tr class="bg-gray-100 sticky top-[42px] z-10">
        <th
          v-for="header in headers"
          :key="header"
          class="px-4 py-1 bg-gray-100"
        >
          <input
            type="text"
            v-model="filters[header]"
            placeholder="Filter..."
            class="w-full border rounded px-2 py-1 text-xs"
          />
        </th>
      </tr>
    </thead>

        <!-- Table Body -->
        <tbody class="divide-y divide-gray-100">
          <tr v-for="(row, rowIndex) in filteredAndSortedRows" :key="rowIndex">
            <td
              v-for="header in headers"
              :key="header"
              class="px-4 py-2 break-words"
            >
              {{ row[header] }}
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Export Modal -->
  <div v-if="showExportModal" class="fixed inset-0 bg-black/30 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-sm space-y-4">
      <h2 class="text-lg font-semibold">Export CSV</h2>
      <input
        type="text"
        v-model="fileName"
        placeholder="Enter file name"
        class="w-full border px-3 py-2 rounded text-sm"
      />
      <div class="flex justify-end space-x-2">
        <button
          @click="showExportModal = false"
          class="px-3 py-1 text-sm text-gray-600 hover:underline"
        >
          Cancel
        </button>
        <button
          @click="exportCSV"
          class="bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700"
        >
          Download
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'

const showExportModal = ref(false)
const fileName = ref('export.csv')

const props = defineProps({
  rows: {
    type: Array,
    required: true,
  },
})

// Headers
const headers = computed(() =>
  props.rows.length ? Object.keys(props.rows[0]) : []
)

// Sorting
const sortField = ref('')
const sortDirection = ref('asc')

function toggleSort(header) {
  if (sortField.value === header) {
    sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc'
  } else {
    sortField.value = header
    sortDirection.value = 'asc'
  }
}

// Filtering
const filters = ref({})
watch(
  headers,
  (newHeaders) => {
    newHeaders.forEach((key) => {
      if (!(key in filters.value)) filters.value[key] = ''
    })
  },
  { immediate: true }
)

// Filter + Sort
const filteredAndSortedRows = computed(() => {
  let result = [...props.rows]

  // Filter
  result = result.filter((row) =>
    headers.value.every((key) =>
      String(row[key] ?? '')
        .toLowerCase()
        .includes(filters.value[key]?.toLowerCase() || '')
    )
  )

  // Sort
  if (sortField.value) {
    result.sort((a, b) => {
      const valA = a[sortField.value] ?? ''
      const valB = b[sortField.value] ?? ''
      return sortDirection.value === 'asc'
        ? String(valA).localeCompare(String(valB))
        : String(valB).localeCompare(String(valA))
    })
  }

  return result
})

function exportCSV() {
  if (!fileName.value.trim()) return

  const rows = [
    headers.value,
    ...filteredAndSortedRows.value.map((row) =>
      headers.value.map((key) => `"${String(row[key]).replace(/"/g, '""')}"`)
    ),
  ]
  const csvContent = rows.map((r) => r.join(',')).join('\n')

  const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' })
  const link = document.createElement('a')
  link.href = URL.createObjectURL(blob)
  link.setAttribute(
    'download',
    fileName.value.toLowerCase().endsWith('.csv') ? fileName.value : `${fileName.value}.csv`
  )
  document.body.appendChild(link)
  link.click()
  document.body.removeChild(link)

  showExportModal.value = false
}
</script>
