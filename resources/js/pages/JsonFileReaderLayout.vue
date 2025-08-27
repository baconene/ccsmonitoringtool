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
        title: 'JsonFileReader',
        href: '/tools/jsonFileReader',
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
            <!-- JSON Upload -->
            <JsonFileReader @json-loaded="handleJsonLoaded" /><!-- Stats Cards -->
            <div class="w-full px-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 w-full">

                    <!-- Empty columns for 1st, 2nd, and 3rd -->
                    <div lass="space-y-4">
                        <CounterCard :number="firstBillCycle" text="Current Cyle">
                            <template #icon>
                                <CircleAlertIcon class="w-8 h-4" />
                            </template>
                        </CounterCard>
                        <CounterCard :number="uniqueAcctCount" text="Number of Accounts">
                            <template #icon>
                                <User class="w-8 h-4" />
                            </template>
                        </CounterCard>


                    </div>
                    <div lass="space-y-4">
                        <FullCard :number="jsonData.length" text="Total Number of Water and Sewer SA without BSEG">

                        </FullCard>
                    </div>
                    <div class="space-y-4">
                        <CounterCard :number="not_scheduled" text="Not Scheduled for Billing">
                            <template #icon>
                                <CalendarCheck class="w-8 h-4" />
                            </template>
                        </CounterCard>

                        <CounterCard :number="scheduled" text="Scheduled for Billing" textClass="text-red-500">
                            <template #icon>
                                <CalendarCheck class="w-8 h-4" />
                            </template>
                        </CounterCard>
                    </div>

                    <!-- 4th Column with all CounterCards stacked vertically -->
                    <div class="space-y-4">


                        <CounterCard :number="prev_win_dt" text="Previous Bill Window">
                            <template #icon>
                                <ArrowBigLeft class="w-8 h-4" />
                            </template>
                        </CounterCard>

                        <CounterCard :number="next_win_dt" text="Next Bill Window">
                            <template #icon>
                                <ArrowBigRight class="w-8 h-4" />
                            </template>
                        </CounterCard>

                    </div>

                </div>
            </div>
        </div>
        <div class="w-full p-6 space-y-6">

            <!-- Table Output -->
            <DynamicTable v-if="jsonData.length" :rows="jsonData" />
        </div>
    </AppLayout>
</template>
