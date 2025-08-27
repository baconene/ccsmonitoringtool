<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head } from '@inertiajs/vue3';

import AppLayout from '@/layouts/AppLayout.vue';
import JsonComparator from '@/components/tools/JsonComparator.vue';
import SearchBar from '@/components/forms/SearchBar.vue';
import { type BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'JSON Comparator',
    href: '/tools/json-comparator',
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
      <JsonComparator />
    </div>
  </AppLayout>
</template>
