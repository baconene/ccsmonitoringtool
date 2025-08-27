<script setup lang="ts">
import { ref, computed, nextTick } from 'vue'
import Chart from 'chart.js/auto'

type RecordData = {
  acct_id: string
  sa_id: string
  [key: string]: any
}

const records1 = ref<RecordData[]>([])
const records2 = ref<RecordData[]>([])
const file1Name = ref('')
const file2Name = ref('')
const fileInput1 = ref<HTMLInputElement | null>(null)
const fileInput2 = ref<HTMLInputElement | null>(null)
const scrollContainer = ref<HTMLElement | null>(null)

const searchAcct = ref('')
const filters = ref<Record<string, string>>({})
const diffOnly = ref(false)
const sortKey = ref<string | null>(null)
const sortAsc = ref(true)
const columns = ref<string[]>([])
const aiAnalysisMap = ref(new Map<string, string>())
const chartCanvas = ref<HTMLCanvasElement | null>(null)
let chartInstance: Chart | null = null

const getKey = (r: RecordData) => `${r.acct_id}-${r.sa_id}`

const recordMap1 = computed(() => {
  const m = new Map()
  records1.value.forEach(r => m.set(getKey(r), r))
  return m
})
const recordMap2 = computed(() => {
  const m = new Map()
  records2.value.forEach(r => m.set(getKey(r), r))
  return m
})

const allKeys = computed(() => {
  const keys = new Set([
    ...records1.value.map(getKey),
    ...records2.value.map(getKey)
  ])
  return Array.from(keys)
})

const filteredKeys = computed(() => {
  let keys = allKeys.value.filter(k => {
    const acct = k.split('-')[0]
    if (searchAcct.value && !acct.includes(searchAcct.value)) return false
    return columns.value.every(key => {
      const val1 = recordMap1.value.get(k)?.[key]
      const val2 = recordMap2.value.get(k)?.[key]
      const f = filters.value[key] || ''
      return !f || val1?.toString().includes(f) || val2?.toString().includes(f)
    })
  })

  if (diffOnly.value) {
    keys = keys.filter(k => {
      const rec1 = recordMap1.value.get(k)
      const rec2 = recordMap2.value.get(k)
      if (!rec1 || !rec2) return true
      return columns.value.some(c => rec1[c] !== rec2[c])
    })
  }

  if (sortKey.value) {
    keys.sort((a, b) => {
      const valA = recordMap1.value.get(a)?.[sortKey.value!] ?? recordMap2.value.get(a)?.[sortKey.value!] ?? ''
      const valB = recordMap1.value.get(b)?.[sortKey.value!] ?? recordMap2.value.get(b)?.[sortKey.value!] ?? ''
      return sortAsc.value
        ? String(valA).localeCompare(String(valB))
        : String(valB).localeCompare(String(valA))
    })
  }

  return keys
})

const summary = computed(() => {
  const acctSet = new Set<string>()
  const saSet = new Set<string>()
  const labelCount = new Map<string, number>()

  for (const k of allKeys.value) {
    const [acct, sa] = k.split('-')
    acctSet.add(acct)
    saSet.add(sa)
    const msg = aiAnalysisMap.value.get(k)
    if (msg) labelCount.set(msg, (labelCount.get(msg) ?? 0) + 1)
  }

  return {
    uniqueAcctIds: acctSet.size,
    uniqueSAIds: saSet.size,
    labels: Array.from(labelCount.entries())
  }
})

function exportCSV() {
  const rows: string[] = []
  const header = ['acct_id + sa_id', ...columns.value, 'AI Analysis']
  rows.push(header.join(','))

  filteredKeys.value.forEach(k => {
    const rec1 = recordMap1.value.get(k)
    const rec2 = recordMap2.value.get(k)
    const row = [
      k,
      ...columns.value.map(col => (rec1?.[col] ?? rec2?.[col] ?? '')),
      aiAnalysisMap.value.get(k) ?? ''
    ]
    rows.push(row.map(val => `"${String(val).replace(/"/g, '""')}"`).join(','))
  })

  const blob = new Blob([rows.join('\n')], { type: 'text/csv;charset=utf-8;' })
  const url = URL.createObjectURL(blob)
  const link = document.createElement('a')
  link.href = url
  link.setAttribute('download', 'comparison.csv')
  document.body.appendChild(link)
  link.click()
  document.body.removeChild(link)
}

function handleFileUpload(event: Event, target: 'file1' | 'file2') {
  const input = event.target as HTMLInputElement
  const files = input.files
  if (!files?.length) return

  const reader = new FileReader()
  reader.onload = () => {
    try {
      const json = JSON.parse(reader.result as string) as RecordData[]
      if (target === 'file1') {
        records1.value = json
        file1Name.value = files[0].name
      } else {
        file2Name.value = files[0].name
        records2.value = json.map(rec => {
          const key = getKey(rec)
          const old = recordMap1.value.get(key)
          let ai = ''

          if (!old) {
            ai = 'NEW ACCOUNT AFTER SNAPSHOT'
          } else {
            const bsChanged = rec.bs_count > old.bs_count
            const prevWindowPassed = new Date(rec.prev_win_dt) < new Date()
            const longActiveSA = Number(rec.number_of_days_sa_active) > 14
            const changed = columns.value.some(k => old?.[k] !== rec[k])

            if (bsChanged) ai = 'CUSTOMER GOT BILLED'
            else if (!changed) ai = 'NO BILL GENERATED'
            else if (longActiveSA && prevWindowPassed) ai = 'WAS NOT BILLED ON EARLIEST WINDOW'
            else if (!longActiveSA) ai = 'NO ACTION NEEDED'
          }

          aiAnalysisMap.value.set(key, ai)
          return { ...rec, ai_analysis: ai }
        })

        // Backfill analysis for records only in file1
        records1.value.forEach(rec => {
          const k = getKey(rec)
          if (!recordMap2.value.has(k)) {
            aiAnalysisMap.value.set(k, 'SA HAS BEEN BILLED')
          }
        })
      }

      if (!columns.value.length) {
        columns.value = Object.keys(json[0] || {}).filter(k => !['acct_id', 'sa_id'].includes(k))
      }

      updateChart()

      nextTick(() => {
        if (scrollContainer.value) {
          scrollContainer.value.scrollTop = scrollContainer.value.scrollHeight
        }
      })
    } catch (e) {
      alert('Invalid JSON')
    }
  }
  reader.readAsText(files[0])
}

function updateChart() {
  if (!chartCanvas.value) return

  if (chartInstance) chartInstance.destroy()

  const labels = summary.value.labels.map(([label]) => label)
  const counts = summary.value.labels.map(([, count]) => count)

  chartInstance = new Chart(chartCanvas.value, {
    type: 'bar',
    data: {
      labels,
      datasets: [{
        label: 'AI Analysis Count',
        data: counts,
        backgroundColor: '#6366F1'
      }]
    },
    options: {
      indexAxis: 'y',
      responsive: true,
      plugins: {
        legend: { display: false }
      }
    }
  })
}

const toggleSort = (col: string) => {
  if (sortKey.value === col) {
    sortAsc.value = !sortAsc.value
  } else {
    sortKey.value = col
    sortAsc.value = true
  }
}
</script>

<template>
  <div class="p-4 space-y-4">
    <!-- Controls -->
    <div class="flex flex-wrap justify-between items-center gap-4">
      <div class="flex items-center gap-2">
        <input ref="fileInput1" type="file" class="hidden" @change="e => handleFileUpload(e, 'file1')" />
        <button @click="() => fileInput1?.click()" class="px-3 py-1 bg-gray-200 rounded">Upload Previous Snapshot</button>
        <span v-if="file1Name">{{ file1Name }}</span>
        <button v-if="file1Name" class="ml-1 text-red-500" @click="() => { records1 = []; file1Name = '' }">×</button>

        <input ref="fileInput2" type="file" class="hidden" @change="e => handleFileUpload(e, 'file2')" />
        <button @click="() => fileInput2?.click()" class="px-3 py-1 bg-gray-200 rounded">Upload Current Snapshot</button>
        <span v-if="file2Name">{{ file2Name }}</span>
        <button v-if="file2Name" class="ml-1 text-red-500" @click="() => { records2 = []; file2Name = '' }">×</button>
      </div>

      <div class="flex items-center gap-2">
        <label>Search Acct ID:</label>
        <input v-model="searchAcct" class="border rounded px-2 py-1" placeholder="Enter Acct ID..." />
      </div>

      <div class="flex gap-2">
        <button class="bg-blue-500 text-white px-4 py-1 rounded" @click="exportCSV">Export CSV</button>
        <label class="flex items-center gap-2">
          <input type="checkbox" v-model="diffOnly" />
          Show Diff Only
        </label>
      </div>
    </div>

    <!-- Summary -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
      <div class="bg-white shadow rounded p-4">
        <div class="text-sm text-gray-500">Unique Acct IDs</div>
        <div class="text-2xl font-bold">{{ summary.uniqueAcctIds }}</div>
      </div>
      <div class="bg-white shadow rounded p-4">
        <div class="text-sm text-gray-500">Unique SA IDs</div>
        <div class="text-2xl font-bold">{{ summary.uniqueSAIds }}</div>
      </div>
      <div class="bg-white shadow rounded p-4">
        <canvas ref="chartCanvas" height="100"></canvas>
      </div>
    </div>

    <!-- Table -->
    <div class="overflow-y-auto border rounded max-h-[70vh]" ref="scrollContainer">
      <table class="min-w-full border-collapse">
        <thead>
          <tr class="bg-gray-100 sticky top-0 shadow-sm z-10">
            <th class="border p-2">Acct ID</th>
            <th class="border p-2">SA ID</th>
            <th v-for="col in columns" :key="col" class="border p-2">
              <div class="flex items-center gap-1">
                <span class="font-semibold">{{ col }}</span>
                <input v-model="filters[col]" class="text-xs px-1 py-0.5 border rounded" :placeholder="'Filter...'" />
                <button class="text-xs" @click="toggleSort(col)">⇅</button>
              </div>
            </th>
            <th class="border p-2">AI Analysis</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="acct in filteredKeys"
            :key="acct"
            :class="{
              'bg-green-100': ['No Action Needed', 'SA HAS BEEN BILLED'].includes(aiAnalysisMap.get(acct) || '')
            }"
          >
            <td class="border px-2 py-1 font-mono whitespace-nowrap">{{ acct.split('-')[0] }}</td>
            <td class="border px-2 py-1 font-mono whitespace-nowrap">{{ acct.split('-')[1] }}</td>
            <td
              v-for="col in columns"
              :key="col"
              :class="{
                'border px-3 py-1 whitespace-nowrap': true,
                'bg-red-50': recordMap1.get(acct)?.[col] !== recordMap2.get(acct)?.[col]
              }"
            >
              <span class="block text-gray-500" v-if="recordMap1.get(acct)">
                {{ recordMap1.get(acct)?.[col] ?? '—' }}
              </span>
              <span class="block text-blue-600" v-if="recordMap2.get(acct)">
                {{ recordMap2.get(acct)?.[col] ?? '—' }}
              </span>
            </td>
            <td class="border px-2 py-1 text-sm text-purple-700">
              {{ aiAnalysisMap.get(acct) ?? '—' }}
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>
