<script setup lang="ts"> 
import { ref, computed } from "vue"
import { Head } from "@inertiajs/vue3"

import AppLayout from "@/layouts/AppLayout.vue"
import SearchBar from "@/components/forms/SearchBar.vue"
import { type BreadcrumbItem } from "@/types" 
import NeptuneScalarFileAnalyzer from "@/components/device/NeptuneScalarFileAnalyzer.vue"
import TeserractIMG from "@/components/device/TeserractIMG.vue"

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: "Neptune File Analyzer",
    href: "/tools/neptuneFileAnalyzer",
  },
]

// Tabs
const tabs: { label: string; value: "xml" | "exp" }[] = [
  { label: "Neptune ERROR XML File Analyzer", value: "xml" },
  { label: "Neptune EXP and AMI IMD Counter", value: "exp" },
]

const activeTab = ref<"xml" | "exp">("xml")

// Map active tab to component
const activeComponent = computed(() =>
  activeTab.value === "xml"
    ? NeptuneScalarFileAnalyzer
    : TeserractIMG //NeptuneScalarEXPFileAnalyzer
)

function selectTab(tab: "xml" | "exp") {
  activeTab.value = tab
}
</script>

<template>
  <Head :title="breadcrumbs[0].title" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex items-center justify-end space-x-2 w-full px-4 py-2">
      <SearchBar />
    </div>

    <div class="w-full p-6 space-y-6">
      <!-- Minimalist Tabs -->
      <div class="flex gap-6 border-b">
        <button
          v-for="tab in tabs"
          :key="tab.value"
          @click="selectTab(tab.value)"
          class="pb-2 text-sm font-medium transition-colors border-b-2"
          :class="activeTab === tab.value
            ? 'border-blue-500 text-blue-600'
            : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
        >
          {{ tab.label }}
        </button>
      </div>

      <!-- Dynamic Component -->
      <component :is="activeComponent" />
    </div>
  </AppLayout>
</template>

