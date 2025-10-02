<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import PlaceholderPattern from '../components/PlaceholderPattern.vue';
import ModuleButton from '@/course/ModuleButton.vue';
import { ref } from 'vue';
import ModuleDetails from '@/course/ModuleDetails.vue';
import RelatedDocumentContainer from '@/course/RelatedDocumentContainer.vue';
 

const content = ref('<p>Hello <b>world</b> 👋</p>');
 

const docs = ref([
  { name: "Project Plan.pdf", type: "pdf" },
  { name: "Report.docx", type: "word" },
  { name: "Budget.xlsx", type: "excel" },
  { name: "SitePhoto.png", type: "image" },
]);


</script>

<template>
    <Head title="Dashboard" />

    <AppLayout  >
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <div class="grid auto-rows-min gap-4 md:grid-cols-3">
                <div
                    class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border"
                >
                   <ModuleButton seq="1" text="Introduction to Vue.js" :showEye="true" />
                </div>
                <div
                    class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border"
                > 
  <div class="mt-4">
    <strong>Bound Content:</strong>
    <pre>{{ content }}</pre>
  </div>
                </div>
                <div
                    class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border"
                >
                 
                </div>
            </div>
            <div
                class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border"
            >
                <PlaceholderPattern />
            </div>
        </div>
    </AppLayout>
</template>
