<template>
  <div class="p-6 max-w-6xl mx-auto">
    <!-- Top-right Info/Help Buttons -->
<div class="p-6 max-w-6xl mx-auto">
  <!-- Top-right Info/Help Buttons -->
<div class="flex justify-end gap-2 mb-4">
    <!-- Read Me Button -->
    <button
      @click="openModal('readme')"
      class="p-2 rounded-full bg-white text-gray hover:bg-gray-300 shadow-md transition"
      title="Read Me"
    >
      <Info class="w-5 h-5" />
    </button>

    <!-- Instructions Button -->
    <button
      @click="openModal('instructions')"
      class="p-2 rounded-full bg-white text-gray hover:bg-gray-300 shadow-md transition"
      title="Instructions"
    >
      <HelpCircle class="w-5 h-5" />
    </button>
  </div>
</div>

    <!-- File Upload -->
    <div class="mb-4">
      <label class="block font-semibold mb-1">Upload XML File</label>
      <input
        type="file"
        accept=".xml"
        @change="handleFileUpload"
        class="border p-2 rounded w-full"
      />
    </div>

    <!-- External ID -->
    <div class="mb-4">
      <label class="block font-semibold mb-1">External ID</label>
      <input
        v-model="externalId"
        placeholder="Enter External ID"
        class="border p-2 rounded w-full"
      />
    </div>

    <!-- Submit Button -->
    <button
      @click="fetchApiMatches"
      class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded mb-6"
    >
      Submit
    </button>

    <!-- Match/No Match Cards -->
    <div
      v-if="devicePayload.length"
      class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6"
    >
      <div
        class="bg-green-50 border border-green-200 rounded p-4 text-center cursor-pointer hover:bg-green-100"
        @click="filterType = filterType === 'match' ? null : 'match'"
      >
        <h3 class="font-semibold text-lg text-green-700">Matches</h3>
        <p class="text-green-800 text-2xl font-bold">{{ matchCount }}</p>
      </div>
      <div
        class="bg-red-50 border border-red-200 rounded p-4 text-center cursor-pointer hover:bg-red-100"
        @click="filterType = filterType === 'nomatch' ? null : 'nomatch'"
      >
        <h3 class="font-semibold text-lg text-red-700">No Matches</h3>
        <p class="text-red-800 text-2xl font-bold">{{ noMatchCount }}</p>
      </div>
    </div>

    <!-- Search Filters -->
    <div class="flex gap-4 mb-4">
      <input
        v-model="searchMiu"
        placeholder="Search MIU ID"
        class="border p-2 rounded flex-1"
      />
      <input
        v-model="searchMeter"
        placeholder="Search Meter Number"
        class="border p-2 rounded flex-1"
      />
    </div>

    <!-- Data Table -->
    <div class="overflow-x-auto border rounded">
      <table class="min-w-full border-collapse">
        <thead class="bg-gray-100">
          <tr>
            <th class="border px-3 py-2 text-left">MIU ID</th>
            <th class="border px-3 py-2 text-left">Meter Number</th>
            <th class="border px-3 py-2 text-left">Reading</th>
            <th class="border px-3 py-2 text-left">Reading Datetime</th>
            <th class="border px-3 py-2 text-left">Analysis</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="dev in filteredDevices" :key="dev.miu_id + dev.meter_number">
            <td class="border px-3 py-1">{{ dev.miu_id }}</td>
            <td class="border px-3 py-1">{{ dev.meter_number }}</td>
            <td class="border px-3 py-1">{{ dev.reading }}</td>
            <td class="border px-3 py-1">{{ dev.reading_datetime }}</td>
            <td
              class="border px-3 py-1 font-semibold"
              :class="{
                'text-red-600': dev.analysis.includes('Duplicate') || dev.analysis.includes('No Match'),
                'text-green-600': dev.analysis.includes('Match Found')
              }"
            >
              {{ dev.analysis }}
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modal -->
    <div 
  v-if="modalType"
  class="fixed inset-0 bg-black/30 flex items-center justify-center z-50"
>
      <div class="bg-white rounded-lg shadow-lg w-full max-w-3xl p-6 relative">
        <button
          @click="closeModal"
          class="absolute top-3 right-3 text-gray-500 hover:text-gray-700 text-xl"
        >
          ✖
        </button>

        <!-- Read Me -->
        <div v-if="modalType === 'readme'">
          <h2 class="font-bold text-lg mb-2 text-blue-700">Read Me</h2>
          <ol class="list-decimal list-inside text-gray-700 space-y-1">
            <li>Check the Error Folder in the OCI After the AMI Batch Runs</li>
            <li>
              If there is an Error, Check the Date and Page Number
              <i>(ex. <strong>20250812_p_5</strong>)</i>
            </li>
            <li>Get the same file in the Archive Folder and Upload in the tool</li>
            <li>
              The externalId field be the file name but can be part of the file
              name such as <i>(ex. <strong>20250812_p_5</strong>)</i>
            </li>
            <li>
              <strong>QUERY</strong>
              <pre class="bg-gray-100 p-2 rounded text-xs overflow-x-auto">
SELECT
    DID.D1_DEVICE_ID,
    DID.ID_VALUE AS MIU_ID,
    DC.DEVICE_CONFIG_ID,
    MC.MEASR_COMP_ID,
    IMD.INIT_MSRMT_DATA_ID 
FROM
    D1_DVC_IDENTIFIER DID
    JOIN D1_DVC_CFG               DC ON DC.D1_DEVICE_ID = DID.D1_DEVICE_ID
                          AND DID.DVC_ID_TYPE_FLG = 'D1SN'
    JOIN D1_MEASR_COMP            MC ON MC.DEVICE_CONFIG_ID = DC.DEVICE_CONFIG_ID
    JOIN D1_MEASR_COMP_IDENTIFIER MID ON MID.MEASR_COMP_ID = MC.MEASR_COMP_ID
                                         AND MID.ID_VALUE = '01'
    JOIN D1_INIT_MSRMT_DATA       IMD ON IMD.MEASR_COMP_ID = MC.MEASR_COMP_ID
                                   AND IMD.IMD_EXT_ID LIKE '%'|| :F1 || '%'
              </pre>
            </li>
          </ol>
        </div>

        <!-- Instructions -->
        <div v-if="modalType === 'instructions'">
          <h2 class="font-bold text-lg mb-2 text-blue-700">Instructions</h2>
          <ol class="list-decimal list-inside text-gray-700 space-y-1">
            <li>Click <strong>Upload XML File</strong> and select your XML file containing MIU and Meter data.</li>
            <li>Enter the <strong>External ID</strong> provided by the IMD system.</li>
            <li>Click <strong>Submit</strong> to send the file and ID for validation.</li>
            <li>
              Review the results:
              <ul class="list-disc list-inside ml-5">
                <li><span class="text-green-700 font-semibold">Match Found</span> → MIU ID exists in the system.</li>
                <li><span class="text-red-700 font-semibold">No Match</span> → MIU ID not found.</li>
                <li><span class="text-orange-700 font-semibold">Duplicate MIU</span> → Same MIU appears more than once in the file.</li>
              </ul>
            </li>
          </ol>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from "vue"
import { Info, HelpCircle } from "lucide-vue-next" // Tailwind/shadcn compatible icons

const xmlDocRef = ref<Document | null>(null)
const externalId = ref("")
const devicePayload = ref<any[]>([])
const matchCount = ref(0)
const noMatchCount = ref(0)
const searchMiu = ref("")
const searchMeter = ref("")
const filterType = ref<"match" | "nomatch" | null>(null)
const modalType = ref<"readme" | "instructions" | null>(null)

function openModal(type: "readme" | "instructions") {
  modalType.value = type
}
function closeModal() {
  modalType.value = null
}

// Handle XML file upload
function handleFileUpload(e: Event) {
  const file = (e.target as HTMLInputElement).files?.[0]
  if (!file) return
  const reader = new FileReader()
  reader.onload = (ev) => {
    const parser = new DOMParser()
    xmlDocRef.value = parser.parseFromString(
      ev.target?.result as string,
      "application/xml"
    )
  }
  reader.readAsText(file)
}

// Fetch and merge API + XML
async function fetchApiMatches() {
  if (!externalId.value) {
    alert("Please enter External ID before submitting.")
    return
  }
  if (!xmlDocRef.value) {
    alert("Please upload an XML file first.")
    return
  }

  try {
    const res = await fetch(`/neptune/get-scalar-imd/${externalId.value}`)
    if (!res.ok) {
      throw new Error(`HTTP error! status: ${res.status}`)
    }

    const data = await res.json()

    if (!data || !Array.isArray(data.results)) {
      devicePayload.value = []
      matchCount.value = 0
      noMatchCount.value = 0
      return
    }

    const apiSet = new Set(
      data.results.map((item: any) => item.miu_id?.toString().toLowerCase())
    )

    const miuCounts: Record<string, number> = {}
    const rows = Array.from(xmlDocRef.value.getElementsByTagName("miu_id")).map((miuNode) => {
      const miu = miuNode.textContent?.trim() || ""
      miuCounts[miu] = (miuCounts[miu] || 0) + 1
      const parent = miuNode.parentElement!
      const meter = parent.querySelector("meter_number")?.textContent?.trim() || ""
      const reading = parent.querySelector("reading")?.textContent?.trim() || ""
      const readingDatetime = parent.querySelector("reading_datetime")?.textContent?.trim() || ""
      return {
        miu_id: miu,
        meter_number: meter,
        reading,
        reading_datetime: readingDatetime,
        analysis: apiSet.has(miu.toLowerCase()) ? "Match Found" : "No Match"
      }
    })

    rows.forEach((r) => {
      if (miuCounts[r.miu_id] > 1) r.analysis = "Duplicate MIU"
    })

    matchCount.value = rows.filter((r) => r.analysis === "Match Found").length
    noMatchCount.value = rows.filter((r) => r.analysis === "No Match").length

    devicePayload.value = rows
  } catch (err) {
    console.error("Processing error:", err)
    devicePayload.value = []
    matchCount.value = 0
    noMatchCount.value = 0
  }
}

const filteredDevices = computed(() => {
  let list = devicePayload.value

  if (filterType.value === "match") {
    list = list.filter((r) => r.analysis === "Match Found")
  } else if (filterType.value === "nomatch") {
    list = list.filter((r) => r.analysis === "No Match")
  }

  return list.filter(
    (r) =>
      r.miu_id.toLowerCase().includes(searchMiu.value.toLowerCase()) &&
      r.meter_number.toLowerCase().includes(searchMeter.value.toLowerCase())
  )
})
</script>
