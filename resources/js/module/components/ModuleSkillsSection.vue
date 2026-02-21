<template>
  <div class="mb-6">
    <div class="flex items-center justify-between mb-3">
      <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
        Skill Assessment Parameters
      </h3>
      <button
        type="button"
        class="px-3 py-1.5 text-xs font-medium rounded-lg bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50"
        @click="startCreate"
        :disabled="loading"
      >
        Add Skill
      </button>
    </div>

    <div v-if="loading" class="text-sm text-gray-500 dark:text-gray-400">
      Loading skills...
    </div>

    <div v-else-if="skills.length === 0" class="text-sm text-gray-500 dark:text-gray-400">
      No skills defined yet for this module. Use "Add Skill" to configure
      the skills that will be used in student assessment.
    </div>

    <div v-else class="space-y-3">
      <div
        v-for="skill in skills"
        :key="skill.id"
        class="border border-gray-200 dark:border-gray-600 rounded-lg p-3 flex items-start justify-between gap-3 bg-gray-50/60 dark:bg-gray-900/40"
      >
        <div class="flex-1 min-w-0">
          <div class="flex items-center gap-2 mb-1">
            <span class="font-semibold text-sm text-gray-900 dark:text-gray-100 truncate">
              {{ skill.name }}
            </span>
            <span
              class="text-[10px] px-2 py-0.5 rounded-full bg-indigo-100 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-300"
            >
              {{ skill.difficulty_level }} · Bloom: {{ skill.bloom_level || 'n/a' }}
            </span>
          </div>
          <p class="text-xs text-gray-600 dark:text-gray-400 line-clamp-2 mb-2">
            {{ skill.description || 'No description provided.' }}
          </p>
          <div class="flex flex-wrap items-center gap-3 text-[11px] text-gray-500 dark:text-gray-400">
            <span>
              Weight: <span class="font-semibold">{{ skill.weight ?? 1 }}%</span>
            </span>
            <span>
              Threshold: <span class="font-semibold">{{ skill.competency_threshold }}%</span>
            </span>
            <span v-if="(skill.tags || []).length" class="inline-flex items-center gap-1">
              <span class="text-gray-400">Tags:</span>
              <span
                v-for="tag in skill.tags"
                :key="tag"
                class="px-1.5 py-0.5 rounded-full bg-gray-200 dark:bg-gray-700 text-[10px]"
              >
                {{ tag }}
              </span>
            </span>
          </div>
        </div>

        <div class="flex flex-col items-end gap-1">
          <button
            type="button"
            class="text-xs px-2 py-1 rounded bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-200"
            @click="startEdit(skill)"
          >
            Edit
          </button>
          <button
            type="button"
            class="text-xs px-2 py-1 rounded bg-white dark:bg-gray-800 border border-red-200 dark:border-red-700 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30"
            @click="removeSkill(skill)"
          >
            Remove
          </button>
        </div>
      </div>
    </div>

    <!-- Simple inline form for create/edit -->
    <div v-if="editing" class="mt-4 border border-blue-200 dark:border-blue-700 rounded-lg p-4 bg-blue-50/60 dark:bg-blue-900/20 space-y-3">
      <div class="flex items-center justify-between mb-1">
        <h4 class="text-sm font-semibold text-blue-900 dark:text-blue-100">
          {{ form.id ? 'Edit Skill' : 'Add Skill' }}
        </h4>
        <button
          type="button"
          class="text-xs text-blue-700 dark:text-blue-300 hover:underline"
          @click="cancelEdit"
        >
          Cancel
        </button>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
        <div class="space-y-1">
          <label class="block font-medium text-gray-700 dark:text-gray-200">Name</label>
          <input
            v-model="form.name"
            type="text"
            class="w-full rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-2 py-1.5 text-xs text-gray-900 dark:text-gray-100"
          />
        </div>
        <div class="space-y-1">
          <label class="block font-medium text-gray-700 dark:text-gray-200">Difficulty</label>
          <select
            v-model="form.difficulty_level"
            class="w-full rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-2 py-1.5 text-xs text-gray-900 dark:text-gray-100"
          >
            <option value="basic">Basic</option>
            <option value="intermediate">Intermediate</option>
            <option value="advanced">Advanced</option>
            <option value="expert">Expert</option>
          </select>
        </div>
        <div class="space-y-1">
          <label class="block font-medium text-gray-700 dark:text-gray-200">Weight (%)</label>
          <input
            v-model.number="form.weight"
            type="number"
            min="0"
            max="100"
            step="0.1"
            class="w-full rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-2 py-1.5 text-xs text-gray-900 dark:text-gray-100"
          />
        </div>
        <div class="space-y-1">
          <label class="block font-medium text-gray-700 dark:text-gray-200">Threshold (%)</label>
          <input
            v-model.number="form.competency_threshold"
            type="number"
            min="0"
            max="100"
            step="0.1"
            class="w-full rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-2 py-1.5 text-xs text-gray-900 dark:text-gray-100"
          />
        </div>
        <div class="space-y-1 sm:col-span-2">
          <label class="block font-medium text-gray-700 dark:text-gray-200">Bloom Level</label>
          <select
            v-model="form.bloom_level"
            class="w-full rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-2 py-1.5 text-xs text-gray-900 dark:text-gray-100"
          >
            <option :value="null">Not set</option>
            <option value="remember">Remember</option>
            <option value="understand">Understand</option>
            <option value="apply">Apply</option>
            <option value="analyze">Analyze</option>
            <option value="evaluate">Evaluate</option>
            <option value="create">Create</option>
          </select>
        </div>
      </div>

      <div class="space-y-1 text-xs mt-2">
        <label class="block font-medium text-gray-700 dark:text-gray-200">Description</label>
        <textarea
          v-model="form.description"
          rows="2"
          class="w-full rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-2 py-1.5 text-xs text-gray-900 dark:text-gray-100"
        />
      </div>

      <div class="space-y-1 text-xs mt-2">
        <label class="block font-medium text-gray-700 dark:text-gray-200">Tags (comma separated)</label>
        <input
          v-model="tagsInput"
          type="text"
          class="w-full rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-2 py-1.5 text-xs text-gray-900 dark:text-gray-100"
        />
      </div>

      <div class="mt-3 flex justify-end gap-2">
        <button
          type="button"
          class="px-3 py-1.5 text-xs rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700"
          @click="cancelEdit"
        >
          Cancel
        </button>
        <button
          type="button"
          class="px-3 py-1.5 text-xs rounded-lg bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50"
          :disabled="saving || !form.name"
          @click="saveSkill"
        >
          {{ saving ? 'Saving...' : 'Save' }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, watch, onMounted } from 'vue'
import axios from 'axios'

interface Skill {
  id: number
  module_id: number
  name: string
  description?: string | null
  difficulty_level: string
  weight: number | null
  competency_threshold: number
  bloom_level: string | null
  tags?: string[]
}

const props = defineProps<{
  moduleId: number
}>()

const skills = ref<Skill[]>([])
const loading = ref(false)
const saving = ref(false)
const editing = ref(false)

const form = ref<Partial<Skill>>({
  name: '',
  description: '',
  difficulty_level: 'intermediate',
  weight: 1,
  competency_threshold: 70,
  bloom_level: null,
  tags: [],
})

const tagsInput = ref('')

async function loadSkills() {
  if (!props.moduleId) return
  loading.value = true
  try {
    const { data } = await axios.get(`/modules/${props.moduleId}/skills`)
    skills.value = data.skills || []
  } catch (e) {
    console.error('Failed to load skills for module', e)
  } finally {
    loading.value = false
  }
}

function startCreate() {
  editing.value = true
  form.value = {
    id: undefined,
    module_id: props.moduleId,
    name: '',
    description: '',
    difficulty_level: 'intermediate',
    weight: 1,
    competency_threshold: 70,
    bloom_level: null,
    tags: [],
  }
  tagsInput.value = ''
}

function startEdit(skill: Skill) {
  editing.value = true
  form.value = { ...skill }
  tagsInput.value = (skill.tags || []).join(', ')
}

function cancelEdit() {
  editing.value = false
}

async function saveSkill() {
  if (!form.value.name) return

  saving.value = true
  try {
    const payload: any = {
      name: form.value.name,
      description: form.value.description,
      difficulty_level: form.value.difficulty_level,
      weight: form.value.weight,
      competency_threshold: form.value.competency_threshold,
      bloom_level: form.value.bloom_level,
      tags: tagsInput.value
        .split(',')
        .map(t => t.trim())
        .filter(Boolean),
    }

    if (form.value.id) {
      await axios.put(`/skills/${form.value.id}`, payload)
    } else {
      await axios.post(`/modules/${props.moduleId}/skills`, payload)
    }

    editing.value = false
    await loadSkills()
  } catch (e) {
    console.error('Failed to save skill', e)
  } finally {
    saving.value = false
  }
}

async function removeSkill(skill: Skill) {
  if (!confirm(`Remove skill "${skill.name}" from this module?`)) return
  try {
    await axios.delete(`/skills/${skill.id}`)
    await loadSkills()
  } catch (e) {
    console.error('Failed to delete skill', e)
  }
}

watch(
  () => props.moduleId,
  () => {
    loadSkills()
    editing.value = false
  },
  { immediate: true }
)

onMounted(loadSkills)
</script>
