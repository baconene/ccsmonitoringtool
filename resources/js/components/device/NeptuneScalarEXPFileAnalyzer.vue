<template> 
  <div class="p-6 max-w-6xl mx-auto">
    <h2 class="text-xl font-bold mb-4">.EXP & JSON File Comparison - MIU_ID</h2>

    <!-- Upload EXP Files -->
    <div class="mb-4">
      <label class="block font-semibold mb-1">Upload .EXP Files</label>
      <input
        type="file"
        accept=".exp"
        multiple
        @change="handleExpUpload"
        class="border p-2 rounded w-full"
      />
    </div>

    <!-- Upload JSON File -->
    <div class="mb-4">
      <label class="block font-semibold mb-1">Upload JSON File</label>
      <input
        type="file"
        accept=".json"
        @change="handleJsonUpload"
        class="border p-2 rounded w-full"
      />
    </div>

    <!-- Summary Cards -->
    <div
      v-if="summaryReady"
      class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6"
    >
      <div
        class="p-4 bg-blue-100 rounded shadow text-center cursor-pointer hover:bg-blue-200"
        @click="setFilter('exp')"
      >
        <p class="font-bold text-lg">{{ expMiuIds.size }}</p>
        <p>Total MIU_ID in EXP</p>
      </div>
      <div
        class="p-4 bg-green-100 rounded shadow text-center cursor-pointer hover:bg-green-200"
        @click="setFilter('json')"
      >
        <p class="font-bold text-lg">{{ jsonMiuIds.size }}</p>
        <p>Total MIU_ID in JSON (CCS)</p>
      </div>
      <div
        class="p-4 bg-red-100 rounded shadow text-center cursor-pointer hover:bg-red-200"
        @click="setFilter('missingInJson')"
      >
        <p class="font-bold text-lg">{{ missingInJson.length }}</p>
        <p>Missing in JSON (from EXP)</p>
      </div>
      <div
        class="p-4 bg-yellow-100 rounded shadow text-center cursor-pointer hover:bg-yellow-200"
        @click="setFilter('missingInExp')"
      >
        <p class="font-bold text-lg">{{ missingInExp.length }}</p>
        <p>Missing in EXP (from JSON)</p>
      </div>
    </div>

    <!-- Filter Reset & Export -->
    <div v-if="activeFilter || summaryReady" class="mb-4 flex justify-between">
      <button
        class="px-3 py-1 bg-gray-200 rounded hover:bg-gray-300 text-sm"
        @click="setFilter(null)"
      >
        Clear Filter
      </button>
      <button
        class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 text-sm"
        @click="showExportModal = true"
      >
        Export CSV
      </button>
    </div>

<!-- Results Table -->
<div v-if="filteredResults.length" class="overflow-x-auto mt-6">
  <table class="table-auto border-collapse border border-gray-400 text-sm w-full">
    <thead>
      <tr>
        <th class="border border-gray-300 px-2 py-1 bg-gray-100">MIU_ID</th>
        <th class="border border-gray-300 px-2 py-1 bg-gray-100">Analysis</th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="(row, idx) in filteredResults" :key="idx">
        <td
          class="border border-gray-300 px-2 py-1 text-blue-600 cursor-pointer hover:underline"
          @click="openDetails(row.jsonMiu || row.expMiu)"
        >
          {{ row.expMiu || row.jsonMiu }}
        </td>
        <td
          class="border border-gray-300 px-2 py-1"
          :class="{
            'text-green-600 font-bold': row.analysis === 'Match',
            'text-red-600 font-bold': row.analysis === 'Missing in JSON',
            'text-yellow-600 font-bold': row.analysis === 'Missing in EXP',
          }"
        >
          {{ row.analysis }}
        </td>
      </tr>
    </tbody>
  </table>
</div>

<!-- 🔹 Right-side Drawer -->
<!-- Overlay + Slide Panel -->
  <transition name="fade">
    <div
      v-if="showDetails"
      class="fixed inset-0 bg-black/50 z-40"
      @click="closeDetails"
    />
  </transition>

  <transition name="slide">
    <div
      v-if="showDetails"
      class="fixed top-0 right-0 h-full w-1/2 bg-white shadow-lg z-50"
    >
      <!-- Header -->
      <div class="flex justify-between items-center p-4 border-b">
        <h3 class="text-lg font-semibold">
          MIU Details - {{ selectedMiu }}
        </h3>
        <button
          class="text-gray-500 hover:text-black"
          @click="closeDetails"
        >
          ✕
        </button>
      </div>

      <!-- Content -->
      <div class="p-4 overflow-y-auto h-full">
        <pre class="bg-gray-100 p-3 rounded text-sm overflow-x-auto">
{{ selectedMiuDetails }}
        </pre>
      </div>
    </div>
  </transition>

    <!-- Export Modal -->
    <div
      v-if="showExportModal"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
    >
      <div class="bg-white p-6 rounded-lg shadow-lg w-80">
        <h3 class="text-lg font-semibold mb-4">Export CSV</h3>
        <label class="block mb-2 text-sm font-medium">Enter file name:</label>
        <input
          type="text"
          v-model="exportFileName"
          class="border rounded px-3 py-2 w-full mb-4"
          placeholder="miu_comparison"
        />
        <div class="flex justify-end space-x-2">
          <button
            class="px-3 py-1 bg-gray-300 rounded hover:bg-gray-400 text-sm"
            @click="showExportModal = false"
          >
            Cancel
          </button>
          <button
            class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 text-sm"
            @click="confirmExport"
          >
            Export
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
<style scoped>
/* Background fade */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

/* Slide from right */
.slide-enter-active,
.slide-leave-active {
  transition: transform 0.8s ease;
}
.slide-enter-from,
.slide-leave-to {
  transform: translateX(100%);
}
</style>

<script setup lang="ts">
import { ref, computed } from "vue";

const expMiuIds = ref<Set<string>>(new Set());
const jsonMiuIds = ref<Set<string>>(new Set());
const showDetails = ref(false);
const selectedMiu = ref<string | null>(null);
const selectedMiuDetails = ref<any>(null);

const comparisonResults = ref<
  { expMiu: string | null; jsonMiu: string | null; analysis: string }[]
>([]);
const activeFilter = ref<string | null>(null);

// Export modal state
const showExportModal = ref(false);
const exportFileName = ref("miu_comparison");



const missingInJson = computed(() =>
  Array.from(expMiuIds.value).filter((miu) => !jsonMiuIds.value.has(miu))
);
const missingInExp = computed(() =>
  Array.from(jsonMiuIds.value).filter((miu) => !expMiuIds.value.has(miu))
);
const summaryReady = computed(
  () => expMiuIds.value.size > 0 || jsonMiuIds.value.size > 0
);

const filteredResults = computed(() => {
  if (activeFilter.value === "missingInJson") {
    return comparisonResults.value.filter(
      (r) => r.analysis === "Missing in JSON"
    );
  }
  if (activeFilter.value === "missingInExp") {
    return comparisonResults.value.filter(
      (r) => r.analysis === "Missing in EXP"
    );
  }
  if (activeFilter.value === "exp") {
    return comparisonResults.value.filter((r) => r.expMiu);
  }
  if (activeFilter.value === "json") {
    return comparisonResults.value.filter((r) => r.jsonMiu);
  }
  return comparisonResults.value;
});

// Open drawer with details
function openDetails(miu: string | null) {
  if (!miu) return;
  selectedMiu.value = miu;

  // Try to extract JSON details for that MIU from the uploaded JSON file
  // (Assumes jsonMiuIds came from a parsed array of objects)
  try {
    const fileData = JSON.parse(lastJsonContent.value || "[]"); // we'll store the raw JSON
    const match = fileData.find((obj: any) => String(obj?.MIU_ID || obj?.miu_id) === miu);
    selectedMiuDetails.value = match || { message: "No details found in JSON" };
  } catch (e) {
    selectedMiuDetails.value = { error: "Invalid or missing JSON" };
  }

  showDetails.value = true;
}

// Close drawer
function closeDetails() {
  showDetails.value = false;
  selectedMiu.value = null;
  selectedMiuDetails.value = null;
}

// Keep a copy of raw JSON file contents for lookup
const lastJsonContent = ref<string | null>(null);



function setFilter(filter: string | null) {
  activeFilter.value = filter;
}

function handleExpUpload(event: Event) {
  const input = event.target as HTMLInputElement;
  const files = input.files;
  if (!files) return;

  const promises: Promise<void>[] = [];

  for (const file of files) {
    promises.push(
      new Promise((resolve) => {
        const reader = new FileReader();
        reader.onload = () => {
          const content = reader.result as string;
          const lines = content
            .split(/\r?\n/)
            .filter((l) => l.startsWith("RDGDTWTR"));
          lines.forEach((line) => {
            const parts = line.trim().split(/\s+/);
            if (parts.length > 1) {
              expMiuIds.value.add(parts[1]);
            }
          });
          resolve();
        };
        reader.readAsText(file);
      })
    );
  }

  Promise.all(promises).then(updateComparison);
}

function handleJsonUpload(event: Event) {
  const input = event.target as HTMLInputElement;
  const file = input.files?.[0];
  if (!file) return;

  const reader = new FileReader();
  reader.onload = () => {
    try {
      const data = JSON.parse(reader.result as string);
      lastJsonContent.value = reader.result as string; // store raw JSON for later lookup
      jsonMiuIds.value.clear();

      if (Array.isArray(data)) {
        data.forEach((obj) => {
          const miu = obj?.miu_id || obj?.MIU_ID;
          if (miu) jsonMiuIds.value.add(String(miu));
        });
      }

      updateComparison();
    } catch (e) {
      console.error("Invalid JSON", e);
    }
  };
  reader.readAsText(file);
}

function updateComparison() {
  const allMius = new Set([
    ...Array.from(expMiuIds.value),
    ...Array.from(jsonMiuIds.value),
  ]);

  comparisonResults.value = Array.from(allMius).map((miu) => {
    const inExp = expMiuIds.value.has(miu);
    const inJson = jsonMiuIds.value.has(miu);

    let analysis = "Match";
    if (inExp && !inJson) analysis = "Missing in JSON";
    if (!inExp && inJson) analysis = "Missing in EXP";

    return {
      expMiu: inExp ? miu : null,
      jsonMiu: inJson ? miu : null,
      analysis,
    };
  });
}

// Confirm export with custom filename
function confirmExport() {
  exportToCSV(exportFileName.value || "miu_comparison");
  showExportModal.value = false;
}

// Export results to CSV
function exportToCSV(filename: string) {
  const header = ["MIU_ID", "Analysis"];
  const rows = filteredResults.value.map((r) => [
    r.expMiu || r.jsonMiu || "",
    r.analysis,
  ]);

  const csvContent =
    [header, ...rows].map((e) => e.join(",")).join("\n");

  const blob = new Blob([csvContent], { type: "text/csv;charset=utf-8;" });
  const link = document.createElement("a");
  link.href = URL.createObjectURL(blob);
  link.setAttribute("download", `${filename}.csv`);
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
}
</script>
