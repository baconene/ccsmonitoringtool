<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head } from '@inertiajs/vue3';

import DynamicTable from '@/components/table/DynamicTable.vue';
import JsonFileReader from '@/components/tools/JsonFileReader.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import CounterCard from '@/components/cards/CounterCard.vue';
import { ArrowBigLeft, ArrowBigRight, CalendarCheck, ChartBarIcon, CircleAlertIcon, User } from 'lucide-vue-next';
import { type BreadcrumbItem } from '@/types';
import FullCard from '@/components/cards/FullCard.vue';
import { formatDateToYMD } from '@/components/utils/date';
import SearchBar from '@/components/forms/SearchBar.vue';
import JsonFileReaderLayout from '@/pages/JsonFileReaderLayout.vue';

const jsonData = ref<BillCycle[]>([]);

function handleJsonLoaded(data: never[]) {
    jsonData.value = data;
}

type BillCycle = {
    acct_id: string
    schedule_status: string;
    bill_cyc_cd: string;
    prev_win_dt: number;
    next_win_dt: number;
    // add more fields if needed
};
const props = defineProps({
    externalData: {
        type: Object,
        default: () => ({}),
    },
});
const uniqueAcctCount = computed(() => {
    const ids = jsonData.value.map(item => item.acct_id);
    const uniqueIds = new Set(ids);
    return uniqueIds.size;
});
const not_scheduled = computed(() =>
    jsonData.value.filter(item => item.schedule_status?.trim() === 'NOT YET').length
);
const scheduled = computed(() =>
    jsonData.value.filter(item => item.schedule_status?.trim() === 'IS SCHEDULE').length
);
const prev_win_dt = computed(() =>
    formatDateToYMD(jsonData.value[0]?.prev_win_dt).toString()
);
const next_win_dt = computed(() =>
    formatDateToYMD(jsonData.value[0]?.next_win_dt).toString()
);
const firstBillCycle = computed(() => jsonData.value[0]?.bill_cyc_cd ?? '');
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Tools',
        href: '/toolsDashboard',
    },
];
 
</script>

<template>

    <Head title="Field Activity Reports" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex items-center justify-end space-x-2 w-full px-4 py-2">
           
            <SearchBar  />
        </div>
        <div class="w-full p-6 space-y-6">
         SELECT A TOOL
        </div>
        </AppLayout> 
</template>
