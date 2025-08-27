<script setup lang="ts">
import { ref, computed } from 'vue'
import { Link } from '@inertiajs/vue3'

interface ToolItem {
  title: string
  href: string
}

// Tool data defined inside the component
const tools = ref<ToolItem[]>([
  { title: 'JSON File Reader', href: '/tools/jsonFileReader' },
  { title: 'JSON Comparator', href: '/tools/json-comparator' },
  { title: 'Open Ai Analyzer', href: '/tools/open-ai-analyzer' },
  { title: 'Interval Generator', href: '/tools/intervalGenerator' },
  { title: 'Neptune File Analyzer', href: '/tools/neptuneFileAnalyzer' },
  { title: 'Regex Tester', href: '/tools/regex-tester' },
])

const search = ref('')
const showDropdown = ref(false)

const filteredTools = computed(() =>
  tools.value.filter(tool =>
    tool.title.toLowerCase().includes(search.value.toLowerCase())
  )
)

function handleFocus() {
  showDropdown.value = true
}

function handleBlur() {
  setTimeout(() => (showDropdown.value = false), 150)
}
</script>

<template>
  <div class="flex justify-end items-center gap-3">
    <span class="text-sm font-medium text-gray-600">
      What kind of tool are you looking for?
    </span>

    <div class="relative w-full max-w-sm">
      <input
        v-model="search"
        @focus="handleFocus"
        @blur="handleBlur"
        type="text"
        placeholder="Search tools..."
        class="w-full rounded-lg border border-gray-300 px-4 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
      />

      <div
        v-if="showDropdown && filteredTools.length"
        class="absolute z-50 mt-1 w-full rounded-md border border-gray-200 bg-white shadow-lg max-h-60 overflow-auto"
      >
        <ul>
          <li
            v-for="tool in filteredTools"
            :key="tool.title"
          >
            <Link
              :href="tool.href"
              class="block px-4 py-2 text-sm hover:bg-gray-100"
            >
              {{ tool.title }}
            </Link>
          </li>
        </ul>
      </div>

      <div
        v-if="showDropdown && search && filteredTools.length === 0"
        class="absolute z-50 mt-1 w-full rounded-md border border-gray-200 bg-white shadow-lg px-4 py-2 text-sm text-gray-500"
      >
        No tools found.
      </div>
    </div>
  </div>
</template>
