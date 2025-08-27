<template>
  <div class="flex w-full h-screen overflow-hidden">
    <!-- Left Form -->
    <div class="w-1/3 bg-white p-6 border-r border-gray-300 sticky top-0 h-screen overflow-y-auto">
      <h2 class="text-xl font-bold mb-4">Consumption Form</h2>
      <form @submit.prevent="generateXML" class="space-y-4">
        <div>
          <label class="block font-medium">MIUID</label>
          <input v-model="form.miu_id" type="text" class="border p-2 w-full rounded" required />
        </div>

        <div>
          <label class="block font-medium">Meter Number</label>
          <input v-model="form.meter_number" type="text" class="border p-2 w-full rounded" required />
        </div>

        <div>
          <label class="block font-medium">Frequency</label>
          <select v-model="form.frequency" class="border p-2 w-full rounded">
            <option value="D">Daily (24 hourly readings per day)</option>
            <option value="H">Hourly (every hour in range)</option>
          </select>
        </div>

        <div>
          <label class="block font-medium">From Date</label>
          <input v-model="form.from_date" type="date" class="border p-2 w-full rounded" required />
        </div>

        <div>
          <label class="block font-medium">To Date</label>
          <input v-model="form.to_date" type="date" class="border p-2 w-full rounded" required />
        </div>

        <div class="flex gap-2">
          <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">
            Generate XML
          </button>
          <button v-if="xmlOutput" @click="exportXML" type="button" class="bg-green-500 text-white px-4 py-2 rounded">
            Export XML
          </button>
        </div>
      </form>
    </div>

    <!-- Right Output -->
    <div class="w-2/3 p-6 overflow-y-auto h-screen bg-gray-50">
      <h2 class="text-xl font-bold mb-4">Generated XML</h2>
      <div v-if="xmlOutput">
        <pre class="bg-gray-900 text-green-400 p-4 rounded overflow-x-auto whitespace-pre">{{ xmlOutput }}</pre>
      </div>
      <div v-else class="text-gray-500 italic">
        Fill out the form on the left and click "Generate XML" to see the output here.
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const form = ref({
  miu_id: '',
  meter_number: '',
  frequency: 'D',
  from_date: '',
  to_date: ''
})

const xmlOutput = ref('')

function getRandomConsumption(min = 12, max = 125) {
  return Math.floor(Math.random() * (max - min + 1)) + min
}

function makeLocalDate(dateStr, hour = 0, minute = 0, second = 0) {
  const [y, m, d] = dateStr.split('-').map(Number)
  return new Date(y, m - 1, d, hour, minute, second)
}

function formatForCCS(dt) {
  const pad = (n) => String(n).padStart(2, '0')
  return `${dt.getFullYear()}-${pad(dt.getMonth() + 1)}-${pad(dt.getDate())}-${pad(dt.getHours())}.${pad(dt.getMinutes())}.${pad(dt.getSeconds())}`
}

function escapeXml(unsafe) {
  if (unsafe == null) return ''
  return String(unsafe)
    .replaceAll('&', '&amp;')
    .replaceAll('<', '&lt;')
    .replaceAll('>', '&gt;')
    .replaceAll('"', '&quot;')
    .replaceAll("'", '&apos;')
}

function generateXML() {
  if (!form.value.from_date || !form.value.to_date) {
    alert('Please provide both From Date and To Date.')
    return
  }

  const fromCheck = makeLocalDate(form.value.from_date, 0, 0, 0)
  const toCheck = makeLocalDate(form.value.to_date, 0, 0, 0)

  if (fromCheck.getTime() > toCheck.getTime()) {
    alert('From Date must be before or equal to To Date.')
    return
  }

  const entries = []

  if (form.value.frequency === 'H') {
    const start = makeLocalDate(form.value.from_date, 0, 0, 0)
    const end = makeLocalDate(form.value.to_date, 23, 0, 0)
    for (let dt = new Date(start); dt <= end; dt.setHours(dt.getHours() + 1)) {
      const c = getRandomConsumption()
      entries.push({
        date: new Date(dt),
        xml: `        <consumption_history>
            <reading_date>${formatForCCS(dt)}</reading_date>
            <consumption>${c}</consumption>
            <consumption_with_multiplier>${(c / 10).toFixed(1)}</consumption_with_multiplier>
        </consumption_history>`
      })
    }
  } else {
    const startDay = makeLocalDate(form.value.from_date, 0, 0, 0)
    const endDay = makeLocalDate(form.value.to_date, 0, 0, 0)
    for (let day = new Date(startDay); day <= endDay; day.setDate(day.getDate() + 1)) {
      const c = getRandomConsumption()
      entries.push({
        date: new Date(day),
        xml: `        <consumption_history>
            <reading_date>${formatForCCS(day)}</reading_date>
            <consumption>${c}</consumption>
            <consumption_with_multiplier>${(c / 10).toFixed(1)}</consumption_with_multiplier>
        </consumption_history>`
      })
    }
    const extraDay = new Date(endDay)
    extraDay.setDate(extraDay.getDate() + 1)
    const cExtra = getRandomConsumption()
    entries.push({
      date: extraDay,
      xml: `        <consumption_history>
            <reading_date>${formatForCCS(extraDay)}</reading_date>
            <consumption>${cExtra}</consumption>
            <consumption_with_multiplier>${(cExtra / 10).toFixed(1)}</consumption_with_multiplier>
        </consumption_history>`
    })
  }

  entries.sort((a, b) => b.date - a.date)

  const histories = entries.map(e => e.xml).join("\n")

  xmlOutput.value = `<responseDetails>
    <site_id>05096</site_id>
    <endpoints>
        <miu_id>${escapeXml(form.value.miu_id)}</miu_id>
        <meter_number>${escapeXml(form.value.meter_number)}</meter_number>
${histories}
    </endpoints>
</responseDetails>`
}

function exportXML() {
  const blob = new Blob([xmlOutput.value], { type: 'application/xml' })
  const url = URL.createObjectURL(blob)
  const link = document.createElement('a')
  link.href = url
  link.download = `consumption_${form.value.from_date}_to_${form.value.to_date}.xml`
  document.body.appendChild(link)
  link.click()
  document.body.removeChild(link)
  URL.revokeObjectURL(url)
}
</script>

<style scoped>
pre {
  white-space: pre-wrap;
  word-break: break-word;
}
</style>
