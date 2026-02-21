import { ref, computed, Ref } from 'vue'
import axios, { AxiosError } from 'axios'

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

interface SkillAssessment {
  id: number
  skill_id: number
  skill_name: string
  normalized_score: number
  final_score: number
  mastery_level: string
  status: string
  consistency_score: number
  attempt_count: number
  improvement_factor: number
  days_late: number
}

export function useAssessment() {
  const assessment: Ref<Assessment | null> = ref(null)
  const skillAssessments: Ref<SkillAssessment[]> = ref([])
  const isLoading = ref(false)
  const error: Ref<string | null> = ref(null)
  const lastUpdated: Ref<Date | null> = ref(null)

  /**
   * Fetch comprehensive assessment for the current student
   */
  const fetchAssessment = async () => {
    isLoading.value = true
    error.value = null
    try {
      const response = await axios.get('/api/student/assessment')
      assessment.value = response.data.data
      lastUpdated.value = new Date()
      return response.data.data
    } catch (err) {
      const axiosError = err as AxiosError
      error.value = axiosError.message || 'Failed to fetch assessment'
      console.error('Assessment fetch error:', err)
      return null
    } finally {
      isLoading.value = false
    }
  }

  /**
   * Fetch skill assessments for current student
   */
  const fetchSkillAssessments = async () => {
    isLoading.value = true
    error.value = null
    try {
      const response = await axios.get('/api/student/skills/assessments')
      skillAssessments.value = response.data.data
      return response.data.data
    } catch (err) {
      const axiosError = err as AxiosError
      error.value = axiosError.message || 'Failed to fetch skill assessments'
      console.error('Skill assessments fetch error:', err)
      return []
    } finally {
      isLoading.value = false
    }
  }

  /**
   * Fetch strengths for current student
   */
  const fetchStrengths = async () => {
    try {
      const response = await axios.get('/api/student/strengths')
      return response.data.data
    } catch (err) {
      console.error('Strengths fetch error:', err)
      return []
    }
  }

  /**
   * Fetch weaknesses for current student
   */
  const fetchWeaknesses = async () => {
    try {
      const response = await axios.get('/api/student/weaknesses')
      return response.data.data
    } catch (err) {
      console.error('Weaknesses fetch error:', err)
      return []
    }
  }

  /**
   * Fetch radar chart data for current student
   */
  const fetchRadarData = async () => {
    try {
      const response = await axios.get('/api/student/assessment/radar')
      return response.data.data
    } catch (err) {
      console.error('Radar data fetch error:', err)
      return { labels: [], datasets: [] }
    }
  }

  /**
   * Get assessment for a specific student (admin only)
   */
  const fetchStudentAssessment = async (studentId: number) => {
    isLoading.value = true
    error.value = null
    try {
      const response = await axios.get(`/api/admin/student/${studentId}/assessment`)
      return response.data.data
    } catch (err) {
      const axiosError = err as AxiosError
      error.value = axiosError.message || 'Failed to fetch student assessment'
      console.error('Student assessment fetch error:', err)
      return null
    } finally {
      isLoading.value = false
    }
  }

  /**
   * Recalculate assessments for all students in a course
   */
  const recalculateCourseAssessments = async (courseId: number) => {
    isLoading.value = true
    error.value = null
    try {
      const response = await axios.post(`/api/admin/course/${courseId}/recalculate-assessments`)
      return response.data
    } catch (err) {
      const axiosError = err as AxiosError
      error.value = axiosError.message || 'Failed to recalculate assessments'
      console.error('Recalculate error:', err)
      return null
    } finally {
      isLoading.value = false
    }
  }

  /**
   * Compare assessments of multiple students
   */
  const compareStudentAssessments = async (studentIds: number[]) => {
    isLoading.value = true
    error.value = null
    try {
      const response = await axios.post('/api/admin/assessment/compare', {
        student_ids: studentIds,
      })
      return response.data.data
    } catch (err) {
      const axiosError = err as AxiosError
      error.value = axiosError.message || 'Failed to compare assessments'
      console.error('Compare assessments error:', err)
      return []
    } finally {
      isLoading.value = false
    }
  }

  /**
   * Clear all assessment data from state
   */
  const clearAssessment = () => {
    assessment.value = null
    skillAssessments.value = []
    error.value = null
  }

  /**
   * Computed properties
   */
  const hasAssessment = computed(() => assessment.value !== null)

  const overallScore = computed(() => assessment.value?.overall_score ?? 0)

  const readinessLevel = computed(() => assessment.value?.readiness_level ?? 'Unknown')

  const strengths = computed(() => assessment.value?.strengths ?? [])

  const weaknesses = computed(() => assessment.value?.weaknesses ?? [])

  const courseCount = computed(() => assessment.value?.courses?.length ?? 0)

  const masteredSkills = computed(
    () => skillAssessments.value.filter(s => s.mastery_level === 'met' || s.mastery_level === 'exceeds').length
  )

  const skillGaps = computed(() => skillAssessments.value.filter(s => s.mastery_level === 'not_met').length)

  const averageConsistency = computed(() => {
    if (skillAssessments.value.length === 0) return 0
    const sum = skillAssessments.value.reduce((acc, s) => acc + s.consistency_score, 0)
    return (sum / skillAssessments.value.length).toFixed(2)
  })

  return {
    // State
    assessment,
    skillAssessments,
    isLoading,
    error,
    lastUpdated,

    // Methods
    fetchAssessment,
    fetchSkillAssessments,
    fetchStrengths,
    fetchWeaknesses,
    fetchRadarData,
    fetchStudentAssessment,
    recalculateCourseAssessments,
    compareStudentAssessments,
    clearAssessment,

    // Computed
    hasAssessment,
    overallScore,
    readinessLevel,
    strengths,
    weaknesses,
    courseCount,
    masteredSkills,
    skillGaps,
    averageConsistency,
  }
}

export type { Assessment, SkillAssessment }
