<template>
  <Head title="Grade Report" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
        Grade Report
      </h2>
    </template>

    <div class="h-[calc(100vh-140px)] flex gap-0 bg-gray-50 dark:bg-gray-900">
      <!-- Left Sidebar -->
      <div class="w-80 bg-white dark:bg-gray-800 border-r dark:border-gray-700 overflow-y-auto flex flex-col">
        <!-- Sticky Header & Course Selector -->
        <div class="sticky top-0 bg-white dark:bg-gray-800 z-20 border-b dark:border-gray-700 p-6 space-y-4">
          <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Courses</h3>
          
          <!-- Selected Course Display / Search Input -->
          <div class="relative">
            <input
              v-model="searchQuery"
              @focus="showCourseList = true"
              @blur="setTimeout(() => { if (!searchQuery) showCourseList = false }, 250)"
              type="text"
              :placeholder="getSelectedCourseName()"
              :value="searchQuery || getSelectedCourseName()"
              class="w-full px-4 py-2 border-2 border-blue-400 dark:border-blue-500 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
            
            <!-- Dropdown List -->
            <div v-show="showCourseList || searchQuery" class="absolute top-full left-0 right-0 mt-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg shadow-lg z-30 max-h-96 overflow-y-auto">
              <!-- Individual Courses -->
              <button
                v-for="course in filteredCourses"
                :key="course.id"
                @click="selectCourse(course.id); showCourseList = false; searchQuery = ''"
                class="w-full text-left px-4 py-3 hover:bg-blue-50 dark:hover:bg-blue-900/20 border-b dark:border-gray-700 last:border-b-0 transition-colors flex justify-between items-center"
              >
                <div class="flex-1 min-w-0">
                  <h4 class="font-medium text-gray-900 dark:text-gray-100 truncate text-sm">{{ course.title }}</h4>
                </div>
                <div class="ml-3 flex items-center gap-1 text-sm font-bold">
                  <span class="text-gray-400 dark:text-gray-500">|</span>
                  <span class="text-blue-600 dark:text-blue-400 w-8 text-right">{{ getCourseLetterGrade(course.id) }}</span>
                  <span class="text-gray-400 dark:text-gray-500">|</span>
                </div>
              </button>

              <!-- All Courses Option -->
              <button
                @click="selectCourse('all'); showCourseList = false; searchQuery = ''"
                class="w-full text-left px-4 py-3 hover:bg-green-50 dark:hover:bg-green-900/20 transition-colors flex justify-between items-center bg-gray-50 dark:bg-gray-700/50"
              >
                <div class="flex-1">
                  <h4 class="font-medium text-gray-900 dark:text-gray-100 text-sm">All Courses</h4>
                  <p class="text-xs text-gray-500 dark:text-gray-400">Complete summary</p>
                </div>
              </button>
            </div>
          </div>
          
          <!-- Export Buttons -->
          <div class="space-y-2">
            <button
              @click="exportPDF"
              class="w-full px-3 py-2 text-xs font-medium rounded-md text-white bg-red-600 hover:bg-red-700"
            >
              <DocumentArrowDownIcon class="h-3 w-3 inline mr-1" />PDF
            </button>
            <button
              @click="exportCSV"
              class="w-full px-3 py-2 text-xs font-medium rounded-md text-gray-700 dark:text-gray-200 bg-gray-100 dark:bg-gray-700"
            >
              <DocumentArrowDownIcon class="h-3 w-3 inline mr-1" />CSV
            </button>
          </div>
        </div>

        <!-- Scrollable Summary Section -->
        <div class="flex-1 overflow-y-auto px-6 py-6">
          <!-- Summary -->
          <div v-if="gradeData" class="space-y-3">
            <h4 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Summary</h4>
            <div class="space-y-2 text-sm">
              <div class="flex justify-between">
                <span class="text-gray-600 dark:text-gray-400">{{ selectedCourseId === 'all' ? 'GPA' : 'Grade' }}:</span>
                <span class="font-bold text-gray-900 dark:text-gray-100">
                  {{ selectedCourseId === 'all' ? gradeData.overall_gpa : gradeData.overall_grade }}{{ selectedCourseId !== 'all' ? '%' : '' }}
                </span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600 dark:text-gray-400">Modules:</span>
                <span class="font-bold text-gray-900 dark:text-gray-100">
                  {{ selectedCourseId === 'all' ? (gradeData.total_courses || 0) : getCompletedModules(gradeData.modules) }}
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Main Content -->
      <div class="flex-1 overflow-y-auto">
        <div class="max-w-6xl mx-auto px-6 py-6">
          <!-- Loading -->
          <div v-if="loading" class="flex items-center justify-center h-64">
            <div class="text-center">
              <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 dark:border-blue-400 mx-auto mb-2"></div>
              <p class="text-gray-600 dark:text-gray-300 text-sm">Loading report...</p>
            </div>
          </div>

          <!-- Content -->
          <div v-else-if="gradeData" class="space-y-6">
        <!-- Overall Grade Summary -->
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border dark:border-gray-700">
          <div class="p-6">
            <div class="flex justify-between items-center mb-4">
              <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                {{ selectedCourseId === 'all' ? 'Overall Performance' : (gradeData.course?.title || 'Course Report') }}
              </h3>
              <div class="flex flex-wrap gap-2">
                <button
                  @click="exportPDF"
                  class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-red-600 hover:bg-red-700 dark:bg-red-700 dark:hover:bg-red-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 dark:focus:ring-offset-gray-800 transition-colors duration-200"
                >
                  <DocumentArrowDownIcon class="h-4 w-4 mr-1" />
                  Export PDF
                </button>
                <button
                  @click="exportCSV"
                  class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 text-sm leading-4 font-medium rounded-md text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-gray-800 transition-colors duration-200"
                >
                  <DocumentArrowDownIcon class="h-4 w-4 mr-1" />
                  Export CSV
                </button>
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
              <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg border dark:border-blue-800/30">
                <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                  {{ selectedCourseId === 'all' ? gradeData.overall_gpa : gradeData.overall_grade }}{{ selectedCourseId === 'all' ? '' : '%' }}
                </div>
                <div class="text-sm text-gray-600 dark:text-gray-300">
                  {{ selectedCourseId === 'all' ? 'Overall GPA' : 'Course Grade' }}
                </div>
              </div>
              <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg border dark:border-green-800/30">
                <div class="text-2xl font-bold text-green-600 dark:text-green-400">
                  {{ selectedCourseId === 'all' ? (gradeData.total_courses || 0) : getCompletedModules(gradeData.modules) }}
                </div>
                <div class="text-sm text-gray-600 dark:text-gray-300">
                  {{ selectedCourseId === 'all' ? 'Total Courses' : 'Completed Modules' }}
                </div>
              </div>
              <div class="bg-yellow-50 dark:bg-yellow-900/20 p-4 rounded-lg border dark:border-yellow-800/30">
                <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">
                  {{ selectedCourseId === 'all' ? calculateTotalActivities().completed : (gradeData.activity_summary?.completed || 0) }}
                </div>
                <div class="text-sm text-gray-600 dark:text-gray-300">
                  {{ selectedCourseId === 'all' ? 'Total Completed' : 'Activities Done' }}
                </div>
              </div>
              <div class="bg-purple-50 dark:bg-purple-900/20 p-4 rounded-lg border dark:border-purple-800/30">
                <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">
                  {{ selectedCourseId === 'all' ? (gradeData.overall_letter_grade || 'N/A') : (gradeData.overall_letter_grade || 'N/A') }}
                </div>
                <div class="text-sm text-gray-600 dark:text-gray-300">Letter Grade</div>
              </div>
            </div>

            <!-- Grade Calculation Explanation (for single course) -->
            <div v-if="selectedCourseId !== 'all' && gradeData.modules && gradeData.modules.length > 0" class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 border dark:border-gray-600">
              <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-3">How Your Course Grade is Calculated:</h4>
              <div class="space-y-2">
                <div class="text-sm text-gray-700 dark:text-gray-300">
                  <span class="font-medium">Course Grade ({{ gradeData.overall_grade || 0 }}%)</span> = Weighted average of all modules
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-xs">
                  <div
                    v-for="module in gradeData.modules"
                    :key="module.module_id"
                    class="flex justify-between items-center bg-white dark:bg-gray-800 rounded px-2 py-1 border dark:border-gray-600"
                  >
                    <span class="text-gray-600 dark:text-gray-400">{{ module.module_title || `Module ${module.module_id}` }}:</span>
                    <span class="font-medium text-gray-900 dark:text-gray-100">{{ module.module_score || 0 }}% × {{ module.module_weight || (100 / gradeData.modules.length).toFixed(1) }}%</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Single Course Details -->
        <div v-if="selectedCourseId !== 'all'" class="space-y-6">
          <!-- Module Performance -->
          <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border dark:border-gray-700">
            <div class="p-6">
              <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Module Performance</h4>
              <div class="space-y-3">
                <div
                  v-for="module in (gradeData.modules || [])"
                  :key="module.module_id"
                  class="border border-gray-200 dark:border-gray-600 rounded-lg p-4 bg-gray-50 dark:bg-gray-700/30"
                >
                  <div class="flex justify-between items-center mb-2">
                    <h5 class="font-medium text-gray-900 dark:text-gray-100">{{ module.module_title }}</h5>
                    <span
                      :class="[
                        'px-2 py-1 rounded-full text-xs font-medium',
                        getStatusClass(module.completion_status)
                      ]"
                    >
                      {{ (module.completion_status || 'unknown').replace('_', ' ').toUpperCase() }}
                    </span>
                  </div>
                  <div class="flex justify-between items-center mb-2">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Grade:</span>
                    <span class="font-medium text-gray-900 dark:text-gray-100">{{ module.module_score || 'N/A' }}{{ module.module_score ? '%' : '' }} ({{ module.module_letter_grade || 'N/A' }})</span>
                  </div>
                  <div class="bg-gray-200 dark:bg-gray-700 rounded-full h-2 mb-3">
                    <div
                      class="bg-blue-600 h-2 rounded-full transition-all duration-300"
                      :style="{ width: `${module.module_score || 0}%` }"
                    ></div>
                  </div>
                  
                  <!-- Toggle Button for Calculation Details -->
                  <button
                    @click="toggleModuleDetails(module.module_id)"
                    class="flex items-center gap-2 text-sm text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 font-medium mb-2 transition-colors"
                  >
                    <component :is="expandedModules[module.module_id] ? ChevronUpIcon : ChevronDownIcon" class="w-4 h-4" />
                    {{ expandedModules[module.module_id] ? 'Hide' : 'Show' }} Calculation Details
                  </button>

                  <!-- Module Performance Calculation Details (Collapsible) -->
                  <div v-show="expandedModules[module.module_id]" class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-3 border dark:border-blue-800/30">
                    <h6 class="text-xs font-semibold text-blue-900 dark:text-blue-100 mb-3">Module Grade Calculation:</h6>
                    
                    <!-- Dynamic Weight Notice -->
                    <div v-if="!module.has_lessons || !module.has_activities" class="bg-amber-50 dark:bg-amber-900/20 border border-amber-300 dark:border-amber-700 rounded p-2 mb-2 text-xs">
                      <span class="text-amber-800 dark:text-amber-200">
                        ⚠️ Weights adjusted: 
                        <span v-if="!module.has_lessons">No lessons in module</span>
                        <span v-else-if="!module.has_activities">No activities in module</span>
                      </span>
                    </div>
                    
                    <!-- Formula Display -->
                    <div class="bg-white dark:bg-gray-800 rounded p-2 mb-3 text-xs border dark:border-gray-600">
                      <div class="font-medium text-gray-800 dark:text-gray-200 mb-1">
                        {{ module.module_score || 0 }}% = (Lessons × {{ module.lesson_weight_used || 0 }}%) + (Activities × {{ module.activity_weight_used || 0 }}%)
                      </div>
                      <div class="text-gray-600 dark:text-gray-400">
                        = ({{ module.lesson_score || 0 }}% × {{ module.lesson_weight_used || 0 }}%) + ({{ module.activity_score || 0 }}% × {{ module.activity_weight_used || 0 }}%)
                      </div>
                      <div class="text-gray-600 dark:text-gray-400">
                        = {{ module.lesson_contribution || 0 }}% + {{ module.activity_contribution || 0 }}%
                      </div>
                    </div>

                    <!-- Component Breakdown -->
                    <div class="grid grid-cols-2 gap-3 text-xs">
                      <!-- Lessons Component -->
                      <div 
                        :class="[
                          'rounded p-2 border',
                          module.has_lessons 
                            ? 'bg-green-50 dark:bg-green-900/20 border-green-800/30' 
                            : 'bg-gray-50 dark:bg-gray-800/20 border-gray-600 opacity-50'
                        ]"
                      >
                        <div :class="module.has_lessons ? 'text-green-800 dark:text-green-200' : 'text-gray-600 dark:text-gray-400'" class="font-medium mb-1">
                          📚 Lessons ({{ module.lesson_weight_used || 0 }}%)
                        </div>
                        <div :class="module.has_lessons ? 'text-green-700 dark:text-green-300' : 'text-gray-500 dark:text-gray-500'">
                          Score: {{ module.lesson_score || 0 }}%
                        </div>
                        <div :class="module.has_lessons ? 'text-green-700 dark:text-green-300' : 'text-gray-500 dark:text-gray-500'">
                          Contributes: {{ module.lesson_contribution || 0 }}%
                        </div>
                      </div>

                      <!-- Activities Component -->
                      <div 
                        :class="[
                          'rounded p-2 border',
                          module.has_activities 
                            ? 'bg-orange-50 dark:bg-orange-900/20 border-orange-800/30' 
                            : 'bg-gray-50 dark:bg-gray-800/20 border-gray-600 opacity-50'
                        ]"
                      >
                        <div :class="module.has_activities ? 'text-orange-800 dark:text-orange-200' : 'text-gray-600 dark:text-gray-400'" class="font-medium mb-1">
                          📝 Activities ({{ module.activity_weight_used || 0 }}%)
                        </div>
                        <div :class="module.has_activities ? 'text-orange-700 dark:text-orange-300' : 'text-gray-500 dark:text-gray-500'">
                          Score: {{ module.activity_score || 0 }}%
                        </div>
                        <div :class="module.has_activities ? 'text-orange-700 dark:text-orange-300' : 'text-gray-500 dark:text-gray-500'">
                          Contributes: {{ module.activity_contribution || 0 }}%
                        </div>
                      </div>
                    </div>

                    <!-- Activity Details -->
                    <div v-if="module.activities && module.activities.length > 0" class="mt-3 pt-2 border-t border-blue-200">
                      <div class="text-xs font-medium text-blue-800 mb-2">Activity Breakdown:</div>
                      <div class="grid grid-cols-2 gap-2 text-xs">
                        <div>
                          <span class="text-blue-700 font-medium">Completed:</span>
                          <span class="text-blue-900">{{ module.activities.filter(a => a.status === 'completed').length }}/{{ module.activities.length }}</span>
                        </div>
                        <div>
                          <span class="text-blue-700 font-medium">Progress:</span>
                          <span class="text-blue-900">{{ Math.round((module.activities.filter(a => a.status === 'completed').length / module.activities.length) * 100) }}%</span>
                        </div>
                      </div>
                      
                      <!-- Activity Type Breakdown -->
                      <div class="mt-2 flex flex-wrap gap-1">
                        <template v-for="activityType in getUniqueActivityTypes(module.activities)" :key="activityType">
                          <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-blue-100 text-blue-800">
                            {{ activityType }}: {{ getActivityTypeCount(module.activities, activityType) }}
                          </span>
                        </template>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Activity Details -->
          <div class="bg-white shadow-sm rounded-lg">
            <div class="p-6">
              <h4 class="text-lg font-medium text-gray-900 mb-4">Activity Breakdown</h4>
              <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                  <thead class="bg-gray-50">
                    <tr>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Activity
                      </th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Type
                      </th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Status
                      </th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Score
                      </th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Due Date
                      </th>
                    </tr>
                  </thead>
                  <tbody class="bg-white divide-y divide-gray-200">
                    <template v-for="module in (gradeData.modules || [])" :key="module.module_id">
                      <tr
                        v-for="activity in (module.activities || [])"
                        :key="activity.activity_id"
                        :class="activity.is_overdue ? 'bg-red-50' : ''"
                      >
                        <td class="px-6 py-4 whitespace-nowrap">
                          <div class="text-sm font-medium text-gray-900">
                            {{ activity.activity_title }}
                          </div>
                          <div class="text-sm text-gray-500">
                            {{ module.module_title }}
                          </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                          <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                            {{ activity.activity_type }}
                          </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                          <span
                            :class="[
                              'px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full',
                              getActivityStatusClass(activity.status, activity.is_overdue)
                            ]"
                          >
                            {{ (activity.status || 'unknown').replace('_', ' ').toUpperCase() }}
                            <span v-if="activity.is_overdue" class="ml-1">⚠️</span>
                          </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                          {{ activity.percentage_score !== null ? `${activity.percentage_score}%` : 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                          {{ activity.due_date ? formatDate(activity.due_date) : 'No due date' }}
                        </td>
                      </tr>
                    </template>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

        <!-- Complete Report View -->
        <div v-else class="space-y-6">
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div
              v-for="course in (gradeData.courses || [])"
              :key="course.course?.id"
              class="bg-white shadow-lg rounded-xl border border-gray-200 hover:shadow-xl transition-shadow duration-300 flex flex-col"
            >
              <!-- Course Header -->
              <div :class="[getCourseColorClasses(course.course?.id).gradient, 'text-white p-4 rounded-t-xl h-28 flex items-center justify-between']">
                <h4 class="text-lg font-bold line-clamp-2 flex-1 pr-4">{{ course.course?.title || 'Unknown Course' }}</h4>
                <div class="text-right flex-shrink-0">
                  <div class="text-3xl font-bold">{{ course.overall_grade || 0 }}%</div>
                  <div class="text-sm font-medium">{{ course.overall_letter_grade || 'N/A' }}</div>
                </div>
              </div>

              <!-- Course Stats -->
              <div class="p-6 flex-1 overflow-y-auto">
                <div class="grid grid-cols-3 gap-4 mb-6">
                  <div class="text-center">
                    <div class="bg-green-50 rounded-lg p-3">
                      <div class="text-2xl font-bold text-green-600">{{ getCompletedModules(course.modules) }}</div>
                      <div class="text-xs text-green-700 font-medium">Modules</div>
                      <div class="text-xs text-gray-500">Completed</div>
                    </div>
                  </div>
                  <div class="text-center">
                    <div class="bg-blue-50 rounded-lg p-3">
                      <div class="text-2xl font-bold text-blue-600">{{ course.activity_summary?.completed || 0 }}</div>
                      <div class="text-xs text-blue-700 font-medium">Activities</div>
                      <div class="text-xs text-gray-500">Done</div>
                    </div>
                  </div>
                  <div class="text-center">
                    <div class="bg-purple-50 rounded-lg p-3">
                      <div class="text-sm font-bold text-purple-600">{{ (course.completion_status || 'unknown').replace('_', ' ').toUpperCase() }}</div>
                      <div class="text-xs text-purple-700 font-medium">Status</div>
                    </div>
                  </div>
                </div>

                <!-- Module Performance Details -->
                <div class="space-y-3">
                  <h5 class="font-semibold text-gray-900 text-sm">Module Performance</h5>
                  <div class="space-y-2">
                    <div
                      v-for="module in (course.modules || [])"
                      :key="module.module_id"
                      class="bg-gray-50 rounded-lg p-3"
                    >
                      <div class="flex justify-between items-center mb-2">
                        <span class="text-sm font-medium text-gray-900">{{ module.module_title }}</span>
                        <div class="flex items-center space-x-2">
                          <span class="text-sm font-bold text-gray-900">{{ module.module_score || 0 }}%</span>
                          <span
                            :class="[
                              'px-2 py-1 rounded-full text-xs font-medium',
                              getStatusClass(module.completion_status)
                            ]"
                          >
                            {{ module.completion_status ? module.completion_status.replace('_', ' ').toUpperCase() : 'UNKNOWN' }}
                          </span>
                        </div>
                      </div>
                      <div class="bg-gray-200 rounded-full h-2 mb-2">
                        <div
                          class="bg-blue-500 h-2 rounded-full transition-all duration-500"
                          :style="{ width: `${module.module_score || 0}%` }"
                        ></div>
                      </div>
                      <!-- Module Calculation Details (Hidden) -->
                    </div>
                  </div>
                </div>
              </div>

              <!-- Course Action - Fixed at Bottom -->
              <div class="border-t border-gray-200 p-4">
                <button
                  @click="selectCourse(course.course?.id)"
                  :class="[getCourseColorClasses(course.course?.id).button, 'w-full text-white py-2 px-4 rounded-lg font-medium transition-colors duration-200']"
                >
                  View Detailed Report
                </button>
              </div>
            </div>
          </div>
        </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { DocumentArrowDownIcon } from '@heroicons/vue/24/outline'
import { ChevronDownIcon, ChevronUpIcon } from '@heroicons/vue/24/solid'

// Props
const props = defineProps({
  availableCourses: Array,
  courseGrades: Object,
  completeReport: Object,
  selectedCourse: String
})

// State
const selectedCourseId = ref(props.selectedCourse || 'all')
const gradeData = ref(props.courseGrades || props.completeReport || null)
const loading = ref(false)
const expandedModules = ref({})
const searchQuery = ref('')
const showCourseList = ref(false)

// Computed
const filteredCourses = computed(() => {
  if (!searchQuery.value.trim()) {
    return props.availableCourses || []
  }
  const query = searchQuery.value.toLowerCase()
  return (props.availableCourses || []).filter(course =>
    course.title.toLowerCase().includes(query) ||
    (course.description && course.description.toLowerCase().includes(query))
  )
})

const breadcrumbs = computed(() => {
  const items = [
    { title: 'Home', href: '/' },
    { title: 'Dashboard', href: '/student-dashboard' },
    { title: 'Grade Report', href: '/student/report' }
  ]
  
  if (selectedCourseId.value !== 'all') {
    const selectedCourse = props.availableCourses?.find(c => c.id === selectedCourseId.value)
    if (selectedCourse) {
      items.push({ title: selectedCourse.title, href: undefined })
    }
  }
  
  return items
}) // Track which modules have calculation details expanded

// Methods
const toggleModuleDetails = (moduleId) => {
  expandedModules.value[moduleId] = !expandedModules.value[moduleId]
}

const selectCourse = (courseId) => {
  if (selectedCourseId.value === courseId) return
  
  selectedCourseId.value = courseId
  loading.value = true
  
  router.get('/student/report', { course_id: courseId === 'all' ? null : courseId }, {
    preserveState: true,
    onSuccess: (page) => {
      gradeData.value = courseId === 'all' ? page.props.completeReport : page.props.courseGrades
      loading.value = false
    },
    onError: () => {
      loading.value = false
    }
  })
}

const exportPDF = () => {
  const url = selectedCourseId.value === 'all' 
    ? '/student/report/pdf/complete'
    : `/student/report/pdf?course_id=${selectedCourseId.value}`
  
  window.open(url, '_blank')
}

const exportCSV = () => {
  const url = selectedCourseId.value === 'all'
    ? '/student/report/csv/complete'
    : `/student/report/csv?course_id=${selectedCourseId.value}`
  
  window.open(url, '_blank')
}

const getStatusClass = (status) => {
  switch (status) {
    case 'completed':
      return 'bg-green-100 text-green-800'
    case 'in_progress':
      return 'bg-yellow-100 text-yellow-800'
    case 'not_started':
      return 'bg-gray-100 text-gray-800'
    default:
      return 'bg-gray-100 text-gray-800'
  }
}

const getActivityStatusClass = (status, isOverdue) => {
  if (isOverdue) {
    return 'bg-red-100 text-red-800'
  }
  
  switch (status) {
    case 'completed':
      return 'bg-green-100 text-green-800'
    case 'in_progress':
      return 'bg-yellow-100 text-yellow-800'
    case 'not_started':
      return 'bg-gray-100 text-gray-800'
    default:
      return 'bg-gray-100 text-gray-800'
  }
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

const calculateTotalActivities = () => {
  if (!gradeData.value?.courses) return { completed: 0, total: 0 }
  
  return gradeData.value.courses.reduce((acc, course) => ({
    completed: acc.completed + (course.activity_summary?.completed || 0),
    total: acc.total + (course.activity_summary?.total || 0)
  }), { completed: 0, total: 0 })
}

const getCompletedModules = (modules) => {
  if (!modules || !Array.isArray(modules)) return 0
  return modules.filter(module => module.is_completed).length
}

// Color palettes for course headers
const colorPalettes = [
  { gradient: 'bg-gradient-to-r from-blue-500 to-blue-600', button: 'bg-blue-600 hover:bg-blue-700' },
  { gradient: 'bg-gradient-to-r from-purple-500 to-purple-600', button: 'bg-purple-600 hover:bg-purple-700' },
  { gradient: 'bg-gradient-to-r from-indigo-500 to-indigo-600', button: 'bg-indigo-600 hover:bg-indigo-700' },
  { gradient: 'bg-gradient-to-r from-pink-500 to-pink-600', button: 'bg-pink-600 hover:bg-pink-700' },
  { gradient: 'bg-gradient-to-r from-teal-500 to-teal-600', button: 'bg-teal-600 hover:bg-teal-700' },
  { gradient: 'bg-gradient-to-r from-cyan-500 to-cyan-600', button: 'bg-cyan-600 hover:bg-cyan-700' },
  { gradient: 'bg-gradient-to-r from-emerald-500 to-emerald-600', button: 'bg-emerald-600 hover:bg-emerald-700' },
  { gradient: 'bg-gradient-to-r from-rose-500 to-rose-600', button: 'bg-rose-600 hover:bg-rose-700' }
]

const getCourseColorClasses = (courseId) => {
  if (!courseId) return colorPalettes[0]
  const hash = courseId.toString().split('').reduce((acc, char) => acc + char.charCodeAt(0), 0)
  const index = hash % colorPalettes.length
  return colorPalettes[index]
}

const getCourseLetterGrade = (courseId) => {
  if (selectedCourseId.value === courseId && gradeData.value) {
    return gradeData.value.overall_letter_grade || 'N/A'
  }
  return 'N/A'
}

const getSelectedCourseName = () => {
  if (selectedCourseId.value === 'all') {
    return 'All Courses'
  }
  const course = props.availableCourses.find(c => c.id === selectedCourseId.value)
  return course?.title || 'Select a course'
}

const calculateModuleAverageScore = (activities) => {
  if (!activities || activities.length === 0) return 0
  const completedActivities = activities.filter(a => a.percentage_score !== null && a.percentage_score !== undefined)
  if (completedActivities.length === 0) return 0
  const total = completedActivities.reduce((sum, activity) => sum + (activity.percentage_score || 0), 0)
  return Math.round(total / completedActivities.length)
}

const getUniqueActivityTypes = (activities) => {
  if (!activities || activities.length === 0) return []
  return [...new Set(activities.map(a => a.activity_type))].filter(Boolean)
}

const getActivityTypeCount = (activities, type) => {
  if (!activities || activities.length === 0) return 0
  return activities.filter(a => a.activity_type === type).length
}

onMounted(() => {
  // If 'all' is selected (default), load the complete report
  if (selectedCourseId.value === 'all') {
    selectCourse('all')
  }
})
</script>