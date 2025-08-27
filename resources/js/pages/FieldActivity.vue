<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import TableWithFilter from '@/components/table/TableWithFilter.vue';
const props = defineProps({
    externalData: {
        type: Object,
        default: () => ({}),
    },
})
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Field Activity',
        href: '/field-activity',
    },
];


 
</script>

<template>

    <Head title="Field Activity Reports" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-2 overflow-x-auto">
        

            <div class="w-full px-2 sm:px-6 lg:px-12 mt-12">
                <div class="max-w-7xl mx-auto"> 
                    <TableWithFilter />
                </div>
            </div>

        </div>

    </AppLayout>

</template>
