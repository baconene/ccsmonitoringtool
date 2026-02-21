<template>
  <div class="w-full h-full">
    <canvas ref="chartCanvas"></canvas>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, watch, PropType } from 'vue'
import {
  Chart as ChartJS,
  BarController,
  CategoryScale,
  LinearScale,
  BarElement,
  Tooltip,
  Legend,
  ChartOptions,
} from 'chart.js'

// Register ChartJS components for vertical bar charts
ChartJS.register(BarController, CategoryScale, LinearScale, BarElement, Tooltip, Legend)

interface Dataset {
  label: string
  data: number[]
  borderColor: string
  backgroundColor: string
  borderWidth: number
}

interface Props {
  labels: string[]
  datasets: Dataset[]
}

const props = defineProps({
  labels: {
    type: Array as PropType<Props['labels']>,
    required: true,
  },
  datasets: {
    type: Array as PropType<Props['datasets']>,
    required: true,
  },
})

const chartCanvas = ref<HTMLCanvasElement | null>(null)
let chartInstance: ChartJS | null = null

onMounted(() => {
  if (chartCanvas.value && props.labels.length > 0 && props.datasets.length > 0) {
    initializeChart()
  }
})

watch(
  () => [props.labels, props.datasets],
  () => {
    if (chartCanvas.value && props.labels.length > 0 && props.datasets.length > 0) {
      if (chartInstance) {
        chartInstance.destroy()
      }
      initializeChart()
    }
  },
  { deep: true }
)

const initializeChart = () => {
  if (!chartCanvas.value) return

  const ctx = chartCanvas.value.getContext('2d')
  if (!ctx) return

  const isDark = document.documentElement.classList.contains('dark')
  const textColor = isDark ? '#d1d5db' : '#374151'
  const gridColor = isDark ? '#4b5563' : '#e5e7eb'

  const options: ChartOptions<'bar'> = {
    responsive: true,
    maintainAspectRatio: false,
    indexAxis: 'y',
    plugins: {
      legend: {
        display: true,
        position: 'top',
        labels: {
          color: textColor,
          font: {
            size: 13,
            weight: 600,
          },
        },
      },
      tooltip: {
        backgroundColor: 'rgba(0, 0, 0, 0.8)',
        padding: 10,
        titleColor: '#fff',
        bodyColor: '#fff',
        borderColor: '#fff',
        borderWidth: 1,
        callbacks: {
          label: function (context) {
            const label = context.dataset.label || 'Score'
            const value = context.parsed.y ?? 0
            return `${label}: ${value.toFixed(1)}%`
          },
        },
      },
    },
    scales: {
      x: {
        ticks: {
          color: textColor,
          font: {
            size: 11,
          },
        },
        grid: {
          display: false,
        },
      },
      y: {
        beginAtZero: true,
        max: 100,
        ticks: {
          color: textColor,
          callback: function (value) {
            return `${value}%`
          },
        },
        grid: {
          color: gridColor,
        },
      },
    },
  }

  chartInstance = new ChartJS(ctx, {
    type: 'bar',
    data: {
      labels: props.labels,
      datasets: props.datasets,
    },
    options,
  })
}
</script>

<style scoped>
canvas {
  max-width: 100% !important;
}
</style>
