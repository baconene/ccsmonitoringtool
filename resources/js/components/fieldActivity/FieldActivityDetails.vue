<template>
  <div v-if="loading" class="p-4 text-center">Loading activity...</div>

  <div v-else-if="error" class="p-4 text-red-600">
    Error: {{ error }}
  </div>

  <div v-else class="p-4">
    <!-- Tabs -->
    <div class="flex space-x-4 border-b mb-6">
      <button
        v-for="tab in tabs"
        :key="tab"
        @click="activeTab = tab"
        :class="[
          'px-4 py-2 border-b-2 text-sm font-medium',
          activeTab === tab
            ? 'border-blue-600 text-blue-600'
            : 'border-transparent text-gray-500 hover:text-blue-600'
        ]"
      >
        {{ tab }}
      </button>
    </div>

    <!-- Summary Tab -->
    <div v-if="activeTab === 'Summary'" class="space-y-4">
      <div class="border p-4 rounded shadow bg-white">
        <p><strong>ID:</strong> {{ activity.activity }}</p>
        <p><strong>Type:</strong> {{ activity.activityType }}</p>
        <p><strong>Status:</strong> {{ activity.status }}</p>
        <p><strong>Business Object:</strong> {{ activity.businessObject }}</p>
        <p><strong>Start Date:</strong> {{ formatDate(activity.startDateTime) }}</p>
      </div>
    </div>

    <!-- Business Object Data Tab -->
    <div v-if="activeTab === 'Business Object Data'" class="space-y-4">
      <div v-if="parsedDataArea" class="border p-4 rounded shadow bg-white">
        <h3 class="text-lg font-semibold mb-2">Business Object Data</h3>
        <div class="space-y-2">
          <ParsedField
            v-for="(value, key) in parsedDataArea"
            :key="key"
            :label="key"
            :value="value"
          />
        </div>
      </div>
    </div>

<!-- Activity Logs Tab -->
<div v-if="activeTab === 'Activity Logs'" class="space-y-4">
  <div class="border p-4 rounded shadow bg-white">
    <h3 class="text-lg font-semibold mb-2">Activity Logs</h3>

    <div v-if="activity.activityLog.length" class="overflow-x-auto">
      <table class="min-w-full table-auto text-sm text-left border">
        <thead class="bg-gray-100 text-gray-700 uppercase">
          <tr>
            <th class="px-4 py-2 border">Date/Time</th>
            <th class="px-4 py-2 border">Status</th>
            <th class="px-4 py-2 border">Message</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="log in activity.activityLog"
            :key="log.sequence"
            class="hover:bg-gray-50"
          >
            <td class="px-4 py-2 border">{{ formatDate(log.logDateTime) }}</td>
            <td class="px-4 py-2 border">{{ log.status }}</td>
            <td class="px-4 py-2 border">{{ getLogMessage(log) }}</td>
          </tr>
        </tbody>
      </table>
    </div>

    <div v-else class="text-gray-500 mt-4">No logs available.</div>
  </div>
</div>

    <!-- Related Objects Tab -->
    <div v-if="activeTab === 'Related Objects'" class="space-y-4">
      <div class="border p-4 rounded shadow bg-white">
        <h3 class="text-lg font-semibold mb-2">Related Objects</h3>
        <ul class="list-disc pl-6">
          <li
            v-for="obj in activity.activityRelatedObject"
            :key="obj.primaryKeyValue1"
          >
            {{ obj.maintenanceObject }}: {{ obj.primaryKeyValue1 }}
          </li>
        </ul>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref, computed } from 'vue'
import axios from 'axios'
import ParsedField from './ParsedField.vue'

const props = defineProps({
  activityId: {
    type: String,
    required: true
  }
})

const activity = ref(null)
const loading = ref(true)
const error = ref(null)

const activeTab = ref('Summary')
const tabs = ['Summary', 'Business Object Data', 'Activity Logs', 'Related Objects']
const activeLogIndex = ref(0)

const formatDate = (dateStr) =>
  dateStr ? new Date(dateStr).toLocaleString() : '—'

const getLogMessage = (log) => {
  const param = log.activityLogParameter?.[0]
  return param?.messageParameterValue || '—'
}

const currentLog = computed(() =>
  activity.value?.activityLog?.[activeLogIndex.value] || {}
)

// Recursive XML parser
const parseXmlNode = (node) => {
  const obj = {}
  node.childNodes.forEach((child) => {
    if (child.nodeType === 1) {
      const key = child.nodeName
      const value =
        child.childNodes.length > 1
          ? parseXmlNode(child)
          : child.textContent.trim()

      if (obj[key]) {
        if (!Array.isArray(obj[key])) obj[key] = [obj[key]]
        obj[key].push(value)
      } else {
        obj[key] = value
      }
    }
  })
  return obj
}

const parsedDataArea = computed(() => {
  const rawXml = activity.value?.businessObjectDataArea
  if (!rawXml) return null

  try {
    const wrappedXml = `<root>${rawXml}</root>`
    const parser = new DOMParser()
    const xmlDoc = parser.parseFromString(wrappedXml, 'application/xml')
    return parseXmlNode(xmlDoc.documentElement)
  } catch (e) {
    console.error('Failed to parse XML:', e)
    return null
  }
})

onMounted(async () => {
  try {
    const response = await axios.get(
      `/field-activity/getActivity/${props.activityId}`
    )
    activity.value = response.data
  } catch (err) {
    error.value = err.message || 'Failed to fetch activity.'
  } finally {
    loading.value = false
  }
})
</script>
