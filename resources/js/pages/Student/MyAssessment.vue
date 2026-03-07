<template>
  <Head title="My Assessment" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
        My Skill Stats
      </h2>
    </template>

    <!-- Loading State -->
    <div v-if="isLoading" class="flex items-center justify-center h-screen">
      <div class="text-center">
        <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-blue-100 dark:bg-blue-900 animate-spin">
          <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
          </svg>
        </div>
        <p class="mt-4 text-gray-600 dark:text-gray-400">Loading assessment data...</p>
      </div>
    </div>

    <!-- Main Content -->
    <div v-else class="space-y-6 p-6">
      <div class="border-b border-gray-200 dark:border-gray-700">
        <nav class="-mb-px flex gap-6">
          <button
            @click="activeTab = 'skills'"
            :class="activeTab === 'skills'
              ? 'border-blue-500 text-blue-600 dark:text-blue-400'
              : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200'"
            class="whitespace-nowrap border-b-2 py-3 px-1 text-sm font-medium transition-colors"
          >
            Skills
          </button>
          <button
            @click="activeTab = 'assessment'"
            :class="activeTab === 'assessment'
              ? 'border-blue-500 text-blue-600 dark:text-blue-400'
              : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200'"
            class="whitespace-nowrap border-b-2 py-3 px-1 text-sm font-medium transition-colors"
          >
            Assessment
          </button>
        </nav>
      </div>

      <div v-if="activeTab === 'skills'" class="space-y-6">
      <!-- Top Stats Row -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Overall Score / Rank -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 flex flex-col justify-between">
          <div>
            <h3 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Overall Skill Rating</h3>
            <div class="flex items-center justify-between mb-4">
              <div>
                <p class="text-4xl font-extrabold text-blue-600 dark:text-blue-400 tracking-tight">
                  {{ assessment.overall_score }}%
                </p>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Your current season score</p>
              </div>
              <div
                :class="getReadinessLevelColor(assessment.readiness_level)"
                class="w-20 h-20 rounded-full flex flex-col items-center justify-center text-white text-xs font-semibold text-center px-1"
              >
                <span class="text-2xl font-bold leading-none mb-1">{{ getReadinessLevelIcon(assessment.readiness_level) }}</span>
                <span class="leading-tight">{{ assessment.readiness_level }}</span>
              </div>
            </div>
          </div>
          <!-- Simple XP bar style progress -->
          <div>
            <div class="flex justify-between text-xs mb-1 text-gray-500 dark:text-gray-400">
              <span>0%</span>
              <span>100%</span>
            </div>
            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2 overflow-hidden">
              <div
                class="h-2 rounded-full bg-gradient-to-r from-blue-500 via-indigo-500 to-emerald-500 transition-all duration-500"
                :style="{ width: Math.min(assessment.overall_score, 100) + '%' }"
              ></div>
            </div>
          </div>
        </div>

        <!-- Quick Stats -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
          <h3 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-3">Quick Stats</h3>
          <div class="grid grid-cols-2 gap-3 text-sm">
            <div class="bg-blue-50 dark:bg-blue-900/20 rounded-md p-3 flex flex-col justify-between">
              <span class="text-xs uppercase tracking-wide text-blue-600 dark:text-blue-300">Courses</span>
              <span class="mt-1 text-xl font-bold text-blue-700 dark:text-blue-200">{{ assessment.summary.total_courses }}</span>
              <span class="mt-1 text-[11px] text-gray-500 dark:text-gray-400">Active seasons</span>
            </div>
            <div class="bg-green-50 dark:bg-green-900/20 rounded-md p-3 flex flex-col justify-between">
              <span class="text-xs uppercase tracking-wide text-green-600 dark:text-green-300">Strong Skills</span>
              <span class="mt-1 text-xl font-bold text-green-700 dark:text-green-200">{{ assessment.summary.strengths_count }}</span>
              <span class="mt-1 text-[11px] text-gray-500 dark:text-gray-400">Top performing</span>
            </div>
            <div class="bg-amber-50 dark:bg-amber-900/20 rounded-md p-3 flex flex-col justify-between">
              <span class="text-xs uppercase tracking-wide text-amber-600 dark:text-amber-300">Needs Work</span>
              <span class="mt-1 text-xl font-bold text-amber-700 dark:text-amber-200">{{ assessment.summary.weaknesses_count }}</span>
              <span class="mt-1 text-[11px] text-gray-500 dark:text-gray-400">Training targets</span>
            </div>
            <div class="bg-indigo-50 dark:bg-indigo-900/20 rounded-md p-3 flex flex-col justify-between">
              <span class="text-xs uppercase tracking-wide text-indigo-600 dark:text-indigo-300">Avg Skill %</span>
              <span class="mt-1 text-xl font-bold text-indigo-700 dark:text-indigo-200">{{ assessment.summary.average_skill_score }}%</span>
              <span class="mt-1 text-[11px] text-gray-500 dark:text-gray-400">Overall accuracy</span>
            </div>
          </div>
        </div>

        <!-- Rank Legend -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
          <h3 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-3">Rank Legend</h3>
          <div class="space-y-1.5 text-xs">
            <div class="flex items-center gap-2">
              <span class="w-3 h-3 rounded bg-red-500"></span>
              <span class="text-gray-700 dark:text-gray-300">Not Ready &mdash; just starting out</span>
            </div>
            <div class="flex items-center gap-2">
              <span class="w-3 h-3 rounded bg-orange-500"></span>
              <span class="text-gray-700 dark:text-gray-300">Developing &mdash; leveling up</span>
            </div>
            <div class="flex items-center gap-2">
              <span class="w-3 h-3 rounded bg-blue-500"></span>
              <span class="text-gray-700 dark:text-gray-300">Proficient &mdash; game ready</span>
            </div>
            <div class="flex items-center gap-2">
              <span class="w-3 h-3 rounded bg-green-500"></span>
              <span class="text-gray-700 dark:text-gray-300">Advanced &mdash; pro status</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Radar Chart + Improvement Summary -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Radar Chart -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 p-6">
          <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-6">Skill Radar</h3>
          <div
            v-if="assessment.radar_chart.labels && assessment.radar_chart.labels.length > 0"
            class="h-96 flex items-center justify-center"
          >
            <RadarChart :labels="assessment.radar_chart.labels" :datasets="assessment.radar_chart.datasets" />
          </div>
          <div v-else class="flex items-center justify-center h-96 bg-gray-50 dark:bg-gray-900 rounded">
            <p class="text-gray-500 dark:text-gray-400">No module performance data available yet</p>
          </div>
        </div>

        <!-- Compact Areas for Improvement Chart -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 p-6">
          <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Weak Spots (Summary)</h3>
          <div v-if="assessment.weaknesses && assessment.weaknesses.length > 0" class="h-64">
            <BarChart :labels="weaknessChartLabels" :datasets="weaknessChartDatasets" />
          </div>
          <div v-else class="flex items-center justify-center h-64 bg-gray-50 dark:bg-gray-900 rounded">
            <p class="text-gray-500 dark:text-gray-400">No areas for improvement identified yet</p>
          </div>
        </div>
      </div>

      <!-- Strengths Section -->
      <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-6 flex items-center gap-2">
          <span class="text-green-600 dark:text-green-400">✓</span>
          Top Skills
        </h3>
        <div v-if="assessment.strengths && assessment.strengths.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
          <div
            v-for="(strength, index) in assessment.strengths"
            :key="index"
            class="bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4"
          >
            <div class="flex justify-between items-start mb-2">
              <h4 class="font-semibold text-gray-900 dark:text-gray-100">{{ strength.skill_name }}</h4>
              <span class="text-lg font-bold text-green-600 dark:text-green-400">{{ strength.score }}%</span>
            </div>
            <div class="flex gap-2 flex-wrap">
              <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/40 text-green-800 dark:text-green-300">
                {{ strength.difficulty }}
              </span>
              <span v-for="tag in (strength.tags || [])" :key="tag" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/40 text-blue-800 dark:text-blue-300">
                {{ tag }}
              </span>
            </div>
          </div>
        </div>
        <div v-else class="text-center py-8">
          <p class="text-gray-600 dark:text-gray-400">Play more activities to unlock your top skills.</p>
        </div>
      </div>

      <!-- Weaknesses Section -->
      <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 flex items-center gap-2">
            <span class="text-orange-600 dark:text-orange-400">!</span>
            Training Targets
          </h3>
          <button
            type="button"
            class="inline-flex items-center px-3 py-1 text-xs font-medium rounded-full border border-orange-300 dark:border-orange-700 text-orange-700 dark:text-orange-300 hover:bg-orange-50 dark:hover:bg-orange-900/30 transition-colors"
            @click="showWeaknesses = !showWeaknesses"
          >
            <span class="mr-1">{{ showWeaknesses ? 'Hide' : 'Show' }}</span>
            <span>{{ showWeaknesses ? '▴' : '▾' }}</span>
          </button>
        </div>

        <div v-if="assessment.weaknesses && assessment.weaknesses.length > 0 && showWeaknesses" class="space-y-4">
          <!-- Weakness Cards -->
          <div class="space-y-4">
            <div
              v-for="(weakness, index) in assessment.weaknesses"
              :key="index"
              class="bg-gradient-to-br from-orange-50 to-red-50 dark:from-orange-900/20 dark:to-red-900/20 border border-orange-200 dark:border-orange-800 rounded-lg p-4"
            >
              <div class="flex justify-between items-start mb-3">
                <div>
                  <h4 class="font-semibold text-gray-900 dark:text-gray-100">{{ weakness.skill_name }}</h4>
                  <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    Current Score: {{ weakness.score }}% | Threshold: {{ weakness.threshold }}%
                  </p>
                </div>
                <div class="text-right">
                  <p class="text-sm font-semibold text-orange-600 dark:text-orange-400">Gap: {{ weakness.gap }}%</p>
                </div>
              </div>

              <!-- Progress Bar -->
              <div class="mb-4">
                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                  <div
                    :style="{ width: weakness.score + '%' }"
                    class="bg-gradient-to-r from-orange-500 to-red-500 h-2 rounded-full transition-all duration-300"
                  ></div>
                </div>
              </div>

              <!-- Recommendations -->
              <div
                v-if="weakness.recommendations && weakness.recommendations.length > 0"
                class="bg-white dark:bg-gray-900/40 rounded p-3"
              >
                <p class="text-xs font-semibold text-gray-700 dark:text-gray-300 mb-2">Recommendations:</p>
                <ul class="text-sm space-y-1">
                  <li
                    v-for="(rec, i) in weakness.recommendations"
                    :key="i"
                    class="flex gap-2 text-gray-600 dark:text-gray-400"
                  >
                    <span class="text-orange-500 font-bold">•</span>
                    {{ rec }}
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </div>
        <div v-else class="text-center py-8">
          <p class="text-gray-600 dark:text-gray-400">Excellent! No training targets for now &mdash; keep your streak going.</p>
        </div>
      </div>

      <!-- Course Breakdown -->
      <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-6">Course Stats</h3>
        <div v-if="assessment.courses && assessment.courses.length > 0" class="space-y-6">
          <div
            v-for="course in assessment.courses"
            :key="course.course_id"
            class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 overflow-x-auto"
          >
            <div class="flex items-center justify-between mb-3">
              <h4 class="font-semibold text-gray-900 dark:text-gray-100">{{ course.course_name }}</h4>
              <span class="text-sm font-semibold text-blue-600 dark:text-blue-400">Overall: {{ course.score }}%</span>
            </div>

            <table class="min-w-full text-sm divide-y divide-gray-200 dark:divide-gray-700">
              <thead class="bg-gray-50 dark:bg-gray-900/60">
                <tr>
                  <th scope="col" class="px-3 py-2 text-left font-semibold text-gray-700 dark:text-gray-200">Module</th>
                  <th scope="col" class="px-3 py-2 text-right font-semibold text-gray-700 dark:text-gray-200">Score</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                <tr v-for="module in course.modules" :key="module.module_id" class="bg-white dark:bg-gray-900/40">
                  <td class="px-3 py-2 text-gray-800 dark:text-gray-100">{{ module.module_name }}</td>
                  <td class="px-3 py-2 text-right text-gray-800 dark:text-gray-100">{{ module.score }}%</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Last Updated -->
      <div class="text-center text-sm text-gray-600 dark:text-gray-400">
        <p>Last updated: {{ formatDate(assessment.assessment_date) }}</p>
      </div>
      </div>

      <div v-else class="space-y-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700">
          <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">Assessment Details</h3>
          <p class="text-sm text-gray-600 dark:text-gray-400">Instructor/admin assessment notes and observations for your courses.</p>
        </div>

        <div v-if="isAssessmentDetailsLoading" class="bg-white dark:bg-gray-800 rounded-lg shadow p-8 text-center">
          <p class="text-gray-600 dark:text-gray-400">Loading assessment details...</p>
        </div>

        <div v-else-if="assessmentDetailsError" class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
          <p class="text-sm text-red-700 dark:text-red-300">{{ assessmentDetailsError }}</p>
        </div>

        <div v-else-if="assessmentDetails.length > 0" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
          <button
            v-for="detail in assessmentDetails"
            :key="detail.id"
            type="button"
            @click="openAssessmentModal(detail)"
            class="group text-left bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-4 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-lg hover:border-blue-300 dark:hover:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500"
          >
            <div class="flex items-start justify-between gap-3">
              <p class="text-xs text-gray-500 dark:text-gray-400">{{ formatDate(detail.created_at) }}</p>
              <span class="text-[11px] px-2 py-1 rounded-full bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">
                {{ getCourseDisplayName(detail) }}
              </span>
            </div>

            <p class="mt-3 text-sm text-gray-800 dark:text-gray-100 line-clamp-4">{{ detail.description }}</p>

            <p v-if="detail.course_mostyle" class="mt-3 text-xs text-gray-500 dark:text-gray-400">
              Style: {{ detail.course_mostyle }}
            </p>

            <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
              Assessed by: {{ getAssessorDisplay(detail) }}
            </p>

            <div class="mt-3 flex items-center justify-between">
              <p class="text-xs text-blue-600 dark:text-blue-400 opacity-0 group-hover:opacity-100 transition-opacity">Click to view details</p>
              <span
                class="inline-flex items-center gap-1 text-xs"
                :class="isLiked(detail.id) ? 'text-red-600 dark:text-red-400' : 'text-gray-500 dark:text-gray-400'"
              >
                <Heart class="w-3.5 h-3.5" :fill="isLiked(detail.id) ? 'currentColor' : 'none'" />
                {{ isLiked(detail.id) ? 'Liked' : 'Like' }}
              </span>
            </div>
          </button>
        </div>

        <div v-else class="bg-white dark:bg-gray-800 rounded-lg shadow p-8 text-center">
          <p class="text-gray-600 dark:text-gray-400">No assessment details available yet.</p>
        </div>
      </div>

      <div
        v-if="showAssessmentModal && selectedAssessmentDetail"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4"
      >
        <div class="w-full max-w-2xl bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-200 dark:border-gray-700">
          <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200 dark:border-gray-700">
            <div>
              <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Assessment Detail</h3>
              <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ getCourseDisplayName(selectedAssessmentDetail) }}</p>
            </div>
            <button @click="closeAssessmentModal" class="p-1 rounded hover:bg-gray-100 dark:hover:bg-gray-700">
              <X class="w-5 h-5 text-gray-500" />
            </button>
          </div>

          <div class="p-5 space-y-4">
            <p class="text-xs text-gray-500 dark:text-gray-400">Created: {{ formatDate(selectedAssessmentDetail.created_at) }}</p>

            <p class="text-sm text-gray-800 dark:text-gray-100 leading-relaxed">
              {{ selectedAssessmentDetail.description }}
            </p>

            <p v-if="selectedAssessmentDetail.course_mostyle" class="text-sm text-gray-600 dark:text-gray-300">
              <span class="font-medium">Style:</span> {{ selectedAssessmentDetail.course_mostyle }}
            </p>

            <p class="text-sm text-gray-600 dark:text-gray-300">
              <span class="font-medium">Assessed by:</span> {{ getAssessorDisplay(selectedAssessmentDetail) }}
            </p>
          </div>

          <div class="px-5 py-4 border-t border-gray-200 dark:border-gray-700 flex items-center justify-between">
            <button
              type="button"
              @click="toggleLikeAssessment(selectedAssessmentDetail.id)"
              class="inline-flex items-center gap-2 px-3 py-2 rounded-lg border text-sm font-medium transition-colors"
              :class="isLiked(selectedAssessmentDetail.id)
                ? 'border-red-200 bg-red-50 text-red-700 dark:border-red-700 dark:bg-red-900/20 dark:text-red-300'
                : 'border-gray-300 text-gray-700 dark:border-gray-600 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700'"
            >
              <Heart class="w-4 h-4" :fill="isLiked(selectedAssessmentDetail.id) ? 'currentColor' : 'none'" />
              {{ isLiked(selectedAssessmentDetail.id) ? 'Liked' : 'Like' }}
            </button>

            <button
              @click="closeAssessmentModal"
              class="px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 text-sm font-medium text-gray-700 dark:text-gray-200"
            >
              Close
            </button>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import RadarChart from '@/components/Charts/RadarChart.vue'
import BarChart from '@/components/Charts/BarChart.vue'
import { ref, onMounted, computed } from 'vue'
import { Heart, X } from 'lucide-vue-next'
import axios from 'axios'
import type { BreadcrumbItemType } from '@/types'

interface Assessment {
  student_id: number
  student_name: string
  overall_score: number
  readiness_level: string
  assessment_date: string
  courses: any[]
  strengths: any[]
  weaknesses: any[]
  radar_chart: {
    labels: string[]
    datasets: any[]
  }
  summary: {
    total_courses: number
    strengths_count: number
    weaknesses_count: number
    average_skill_score: number
  }
}

interface AssessmentDetail {
  id: number
  student_id: number
  course_id: number
  assessed_by_user_id: number | null
  description: string
  course_mostyle: string | null
  created_at: string
  updated_at: string
  course?: {
    id: number
    title?: string | null
    name?: string | null
  }
  assessed_by?: {
    id: number
    name?: string | null
    email?: string | null
  } | null
}

const isLoading = ref(true)
const showWeaknesses = ref(false)
const activeTab = ref<'skills' | 'assessment'>('skills')
const isAssessmentDetailsLoading = ref(false)
const assessmentDetailsError = ref<string | null>(null)
const assessmentDetails = ref<AssessmentDetail[]>([])
const showAssessmentModal = ref(false)
const selectedAssessmentDetail = ref<AssessmentDetail | null>(null)
const likedAssessmentIds = ref<number[]>([])

const LIKE_STORAGE_KEY = 'student-assessment-liked-cards'
const assessment = ref<Assessment>({
  student_id: 0,
  student_name: '',
  overall_score: 0,
  readiness_level: '',
  assessment_date: '',
  courses: [],
  strengths: [],
  weaknesses: [],
  radar_chart: { labels: [], datasets: [] },
  summary: {
    total_courses: 0,
    strengths_count: 0,
    weaknesses_count: 0,
    average_skill_score: 0
  }
})

const breadcrumbs: BreadcrumbItemType[] = [
  { title: 'Dashboard', href: '/dashboard' },
  { title: 'My Assessment', href: '/student/assessment' },
]

const weaknessChartLabels = computed(() =>
  assessment.value.weaknesses.map((w: any) => w.skill_name)
)

const weaknessChartDatasets = computed(() => [
  {
    label: 'Skill Score',
    data: assessment.value.weaknesses.map((w: any) => w.score),
    borderColor: 'rgba(249, 115, 22, 1)',
    backgroundColor: 'rgba(249, 115, 22, 0.3)',
    borderWidth: 1,
  },
])

const getCourseDisplayName = (detail: AssessmentDetail) => {
  return detail.course?.title || detail.course?.name || `Course #${detail.course_id}`
}

const getAssessorDisplay = (detail: AssessmentDetail) => {
  if (detail.assessed_by?.name) {
    return `${detail.assessed_by.name} (#${detail.assessed_by.id})`
  }

  if (detail.assessed_by_user_id) {
    return `User #${detail.assessed_by_user_id}`
  }

  return 'Unknown assessor'
}

const isLiked = (assessmentId: number) => likedAssessmentIds.value.includes(assessmentId)

const loadLikedAssessments = () => {
  try {
    const saved = localStorage.getItem(LIKE_STORAGE_KEY)
    if (!saved) {
      return
    }

    const parsed = JSON.parse(saved)
    if (Array.isArray(parsed)) {
      likedAssessmentIds.value = parsed.filter((id) => typeof id === 'number')
    }
  } catch (error) {
    console.error('Failed to load liked assessments:', error)
  }
}

const persistLikedAssessments = () => {
  localStorage.setItem(LIKE_STORAGE_KEY, JSON.stringify(likedAssessmentIds.value))
}

const toggleLikeAssessment = (assessmentId: number) => {
  if (isLiked(assessmentId)) {
    likedAssessmentIds.value = likedAssessmentIds.value.filter((id) => id !== assessmentId)
  } else {
    likedAssessmentIds.value.push(assessmentId)
  }

  persistLikedAssessments()
}

const openAssessmentModal = (detail: AssessmentDetail) => {
  selectedAssessmentDetail.value = detail
  showAssessmentModal.value = true
}

const closeAssessmentModal = () => {
  showAssessmentModal.value = false
  selectedAssessmentDetail.value = null
}

onMounted(async () => {
  loadLikedAssessments()
  isAssessmentDetailsLoading.value = true

  const [assessmentResult, assessmentDetailsResult] = await Promise.allSettled([
    axios.get('/api/student/assessment'),
    axios.get('/api/student/assessment-details'),
  ])

  if (assessmentResult.status === 'fulfilled') {
    // API returns the assessment object directly (not wrapped in { data: ... })
    assessment.value = assessmentResult.value.data
  } else {
    console.error('Failed to load assessment:', assessmentResult.reason)
  }

  if (assessmentDetailsResult.status === 'fulfilled') {
    assessmentDetails.value = assessmentDetailsResult.value.data.assessments || []
    assessmentDetailsError.value = null
  } else {
    console.error('Failed to load assessment details:', assessmentDetailsResult.reason)
    assessmentDetailsError.value = 'Failed to load assessment details.'
  }

  isLoading.value = false
  isAssessmentDetailsLoading.value = false
})

const getReadinessLevelColor = (level: string) => {
  switch (level) {
    case 'Advanced':
      return 'bg-gradient-to-br from-green-500 to-emerald-600'
    case 'Proficient':
      return 'bg-gradient-to-br from-blue-500 to-blue-600'
    case 'Developing':
      return 'bg-gradient-to-br from-orange-500 to-orange-600'
    case 'Not Ready':
      return 'bg-gradient-to-br from-red-500 to-red-600'
    default:
      return 'bg-gray-500'
  }
}

const getReadinessLevelIcon = (level: string) => {
  switch (level) {
    case 'Advanced':
      return '★'
    case 'Proficient':
      return '✓'
    case 'Developing':
      return '↗'
    case 'Not Ready':
      return '⚠'
    default:
      return '?'
  }
}

const formatDate = (dateString: string) => {
  const date = new Date(dateString)
  return date.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}
</script>

<style scoped>
/* Smooth transitions */
:where(.grid, .space-y-4, .space-y-6) > * {
  animation: fadeInUp 0.3s ease-out forwards;
}

@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>
