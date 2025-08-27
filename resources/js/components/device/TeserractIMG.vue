<script setup lang="ts">
import { ref, onMounted, onBeforeUnmount } from "vue"
import Tesseract from "tesseract.js"

const pastedImage = ref<string | null>(null)
const extractedText = ref<string>("")
const isProcessing = ref(false)

function handlePaste(event: ClipboardEvent) {
  const items = event.clipboardData?.items
  if (!items) return

  for (let i = 0; i < items.length; i++) {
    const item = items[i]
    if (item.type.indexOf("image") !== -1) {
      const blob = item.getAsFile()
      if (blob) {
        const url = URL.createObjectURL(blob)
        pastedImage.value = url
        processImage(blob)
      }
    }
  }
}

async function processImage(blob: Blob) {
  isProcessing.value = true
  extractedText.value = ""

  try {
    const { data } = await Tesseract.recognize(blob, "eng")
    extractedText.value = data.text.trim()
  } catch (err) {
    extractedText.value = "❌ Error extracting text"
    console.error(err)
  } finally {
    isProcessing.value = false
  }
}

onMounted(() => {
  window.addEventListener("paste", handlePaste)
})
onBeforeUnmount(() => {
  window.removeEventListener("paste", handlePaste)
})
</script>

<template>
  <div class="p-6 max-w-3xl mx-auto space-y-4">
    <h2 class="text-xl font-bold">📋 Paste Image to Analyze</h2>

    <div
      class="border-2 border-dashed rounded-xl p-6 text-center bg-gray-50"
    >
      <p class="text-gray-600">Press <kbd class="px-2 py-1 bg-gray-200 rounded">Ctrl</kbd> + <kbd class="px-2 py-1 bg-gray-200 rounded">V</kbd> to paste an image</p>
    </div>

    <!-- Preview Image -->
    <div v-if="pastedImage" class="mt-4">
      <h3 class="font-semibold">Preview:</h3>
      <img :src="pastedImage" alt="Pasted" class="max-h-80 rounded-lg shadow" />
    </div>

    <!-- OCR Result -->
    <div v-if="pastedImage" class="mt-4">
      <h3 class="font-semibold">Extracted Text:</h3>
      <div
        class="p-4 bg-gray-100 rounded-lg whitespace-pre-wrap font-mono text-sm"
      >
        <span v-if="isProcessing">⏳ Processing...</span>
        <span v-else>{{ extractedText || "No text found" }}</span>
      </div>
    </div>
  </div>
</template>
