<template>
  <div class="w-full h-full">
    <canvas ref="chartCanvas"></canvas>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, watch, PropType } from 'vue'
import {
  Chart as ChartJS,
  RadarController,
  CategoryScale,
  LinearScale,
  RadialLinearScale,
  PointElement,
  LineElement,
  Filler,
  Tooltip,
  Legend,
  ChartOptions,
} from 'chart.js'

// Register ChartJS components
ChartJS.register(
  RadarController,
  CategoryScale,
  LinearScale,
  RadialLinearScale,
  PointElement,
  LineElement,
  Filler,
  Tooltip,
  Legend
)

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
    type: Array as PropType<string[]>,
    required: true,
  },
  datasets: {
    type: Array as PropType<Dataset[]>,
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
  const gridColor = isDark ? '#374151' : '#e5e7eb'

  const options: ChartOptions<'radar'> = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      legend: {
        display: true,
        position: 'top' as const,
        labels: {
          color: textColor,
          usePointStyle: true,
          padding: 20,
          font: {
            size: 14,
            weight: 'bold',
          },
        },
      },
      tooltip: {
        backgroundColor: 'rgba(0, 0, 0, 0.8)',
        padding: 12,
        titleColor: '#fff',
        bodyColor: '#fff',
        borderColor: '#fff',
        borderWidth: 1,
        usePointStyle: true,
        callbacks: {
          label: function (context) {
            let label = context.dataset.label || ''
            if (label) {
              label += ': '
            }
            if (context.parsed.r !== null) {
              label += context.parsed.r.toFixed(1) + '%'
            }
            return label
          },
        },
      },
    },
    scales: {
      r: {
        max: 100,
        min: 0,
        ticks: {
          color: textColor,
          font: {
            size: 12,
          },
          callback: function (value) {
            return value as string
          },
        },
        grid: {
          color: gridColor,
          display: true,
        },
        pointLabels: {
          color: textColor,
          font: {
            size: 13,
            weight: 'normal',
          },
          padding: 15,
        },
      },
    },
  }

  chartInstance = new ChartJS(ctx, {
    type: 'radar',
    data: {
      labels: props.labels,
      datasets: props.datasets.map(dataset => ({
        ...dataset,
        tension: 0.4,
        fill: true,
        pointRadius: 5,
        pointHoverRadius: 7,
        pointBackgroundColor: dataset.borderColor,
        pointBorderColor: '#fff',
        pointBorderWidth: 2,
      })),
    },
    options,
  })
}
</script>

<style scoped>
/* Ensure canvas fills container */
canvas {
  max-width: 100% !important;
}
</style>
