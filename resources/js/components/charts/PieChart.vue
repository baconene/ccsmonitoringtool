<template>
    <div class="w-full bg-white rounded-xl shadow p-4 text-center">
        <div v-if="props.results.length === 0" class="text-gray-500 py-8">
            NO FIELD ACTIVITY FOR THE GIVEN DATE RANGE
        </div>
        <div v-else-if="onlyEmptyTaskTypes" class="text-gray-500 py-8">
            NO TASK TYPE
        </div>
        <div v-else class="w-full max-w-full h-auto overflow-x-auto">
            <Pie :data="chartData" :options="chartOptions" />
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'
import { Pie } from 'vue-chartjs'
import {
    Chart as ChartJS,
    Title,
    Tooltip,
    Legend,
    ArcElement
} from 'chart.js'

ChartJS.register(Title, Tooltip, Legend, ArcElement)

const props = defineProps({
    results: {
        type: Array,
        required: true
    }
})

// Count the field task types
const taskTypeCounts = computed(() => {
    const counts = {}
    for (const item of props.results) {
        if (item.fieldTaskType) {
            counts[item.fieldTaskType] = (counts[item.fieldTaskType] || 0) + 1
        }
    }
    return counts
})

// Show "NO TASK TYPE" if there are results but none have a valid fieldTaskType
const onlyEmptyTaskTypes = computed(() => {
    return props.results.length > 0 && Object.keys(taskTypeCounts.value).length === 0
})
// Prepare data for chart
const chartData = computed(() => {
    const labels = Object.keys(taskTypeCounts.value)
    const data = Object.values(taskTypeCounts.value)

    return {
        labels,
        datasets: [
            {
                label: 'Field Task Type Count',
                backgroundColor:  pastelColors,
                data
            }
        ]
    }
})

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            position: 'left',
            labels: {
                font: {
                    size: 14
                },
                boxWidth: 10
            }
        },
        title: {
            display: false
        }
    }
}

function generatePastelColor() {
  const hue = Math.floor(Math.random() * 360); // Random hue (0–359)
  const saturation = 70; // Pastel-like saturation
  const lightness = 80; // Pastel-like lightness
  return `hsl(${hue}, ${saturation}%, ${lightness}%)`;
}

// Generate N pastel colors
function generatePastelColors(count) {
  return Array.from({ length: count }, generatePastelColor);
}

// Example data setup
const data = [12, 19, 3, 5, 2, 3, 8]; // Example dataset values
const pastelColors = generatePastelColors(data.length);

 

</script>

<style scoped>
canvas {
    width: 100% !important;
    height: 100% !important;
}
</style>
