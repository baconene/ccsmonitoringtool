<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head } from '@inertiajs/vue3';

import AppLayout from '@/layouts/AppLayout.vue'; 
import SearchBar from '@/components/forms/SearchBar.vue';
import { type BreadcrumbItem } from '@/types';
import OpenAiAnalyzer from '@/components/tools/OpenAiAnalyzer.vue';

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Open Ai Analyzer',
    href: '/tools/open-ai-analyzer',
  },
];
</script>

<template>
  <Head :title="breadcrumbs[0].title" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex items-center justify-end space-x-2 w-full px-4 py-2">
      <SearchBar />
    </div>

    <div class="w-full p-6 space-y-6">
      <OpenAiAnalyzer/>
    </div>
  </AppLayout>
</template>
