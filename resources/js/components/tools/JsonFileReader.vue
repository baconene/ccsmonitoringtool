<template> 
  <div>
    <label class="block mb-2 text-sm font-medium">Upload JSON File</label>
    <input type="file" accept=".json" @change="readFile" class="border p-2 rounded w-full" />
  </div>
</template>

<script setup>
const emit = defineEmits(['json-loaded']) 
function readFile(event) {
  const file = event.target.files[0]
  if (!file) return

  const reader = new FileReader()
  reader.onload = (e) => {
    try {
      const parsed = JSON.parse(e.target.result)
      emit('json-loaded', Array.isArray(parsed) ? parsed : [parsed])
    } catch (err) {
      console.error('Invalid JSON:', err)
      alert('Invalid JSON file.')
    }
  }

  reader.readAsText(file)
}
</script>
