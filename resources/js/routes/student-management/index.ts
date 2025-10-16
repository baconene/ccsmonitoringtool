import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../wayfinder'
import course from './course'
import student from './student'
/**
* @see \App\Http\Controllers\Instructor\StudentManagementController::index
 * @see app/Http/Controllers/Instructor/StudentManagementController.php:22
 * @route '/student-management'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/student-management',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Instructor\StudentManagementController::index
 * @see app/Http/Controllers/Instructor/StudentManagementController.php:22
 * @route '/student-management'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Instructor\StudentManagementController::index
 * @see app/Http/Controllers/Instructor/StudentManagementController.php:22
 * @route '/student-management'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Instructor\StudentManagementController::index
 * @see app/Http/Controllers/Instructor/StudentManagementController.php:22
 * @route '/student-management'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\Instructor\StudentManagementController::index
 * @see app/Http/Controllers/Instructor/StudentManagementController.php:22
 * @route '/student-management'
 */
    const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: index.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\Instructor\StudentManagementController::index
 * @see app/Http/Controllers/Instructor/StudentManagementController.php:22
 * @route '/student-management'
 */
        indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: index.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\Instructor\StudentManagementController::index
 * @see app/Http/Controllers/Instructor/StudentManagementController.php:22
 * @route '/student-management'
 */
        indexForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: index.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    index.form = indexForm
/**
* @see \App\Http\Controllers\Instructor\StudentManagementController::statistics
 * @see app/Http/Controllers/Instructor/StudentManagementController.php:376
 * @route '/student-management/statistics'
 */
export const statistics = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: statistics.url(options),
    method: 'get',
})

statistics.definition = {
    methods: ["get","head"],
    url: '/student-management/statistics',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Instructor\StudentManagementController::statistics
 * @see app/Http/Controllers/Instructor/StudentManagementController.php:376
 * @route '/student-management/statistics'
 */
statistics.url = (options?: RouteQueryOptions) => {
    return statistics.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Instructor\StudentManagementController::statistics
 * @see app/Http/Controllers/Instructor/StudentManagementController.php:376
 * @route '/student-management/statistics'
 */
statistics.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: statistics.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Instructor\StudentManagementController::statistics
 * @see app/Http/Controllers/Instructor/StudentManagementController.php:376
 * @route '/student-management/statistics'
 */
statistics.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: statistics.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\Instructor\StudentManagementController::statistics
 * @see app/Http/Controllers/Instructor/StudentManagementController.php:376
 * @route '/student-management/statistics'
 */
    const statisticsForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: statistics.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\Instructor\StudentManagementController::statistics
 * @see app/Http/Controllers/Instructor/StudentManagementController.php:376
 * @route '/student-management/statistics'
 */
        statisticsForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: statistics.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\Instructor\StudentManagementController::statistics
 * @see app/Http/Controllers/Instructor/StudentManagementController.php:376
 * @route '/student-management/statistics'
 */
        statisticsForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: statistics.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    statistics.form = statisticsForm
/**
* @see \App\Http\Controllers\Instructor\StudentManagementController::exportMethod
 * @see app/Http/Controllers/Instructor/StudentManagementController.php:265
 * @route '/student-management/course/{course}/export'
 */
export const exportMethod = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: exportMethod.url(args, options),
    method: 'get',
})

exportMethod.definition = {
    methods: ["get","head"],
    url: '/student-management/course/{course}/export',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Instructor\StudentManagementController::exportMethod
 * @see app/Http/Controllers/Instructor/StudentManagementController.php:265
 * @route '/student-management/course/{course}/export'
 */
exportMethod.url = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { course: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { course: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    course: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        course: typeof args.course === 'object'
                ? args.course.id
                : args.course,
                }

    return exportMethod.definition.url
            .replace('{course}', parsedArgs.course.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Instructor\StudentManagementController::exportMethod
 * @see app/Http/Controllers/Instructor/StudentManagementController.php:265
 * @route '/student-management/course/{course}/export'
 */
exportMethod.get = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: exportMethod.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Instructor\StudentManagementController::exportMethod
 * @see app/Http/Controllers/Instructor/StudentManagementController.php:265
 * @route '/student-management/course/{course}/export'
 */
exportMethod.head = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: exportMethod.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\Instructor\StudentManagementController::exportMethod
 * @see app/Http/Controllers/Instructor/StudentManagementController.php:265
 * @route '/student-management/course/{course}/export'
 */
    const exportMethodForm = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: exportMethod.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\Instructor\StudentManagementController::exportMethod
 * @see app/Http/Controllers/Instructor/StudentManagementController.php:265
 * @route '/student-management/course/{course}/export'
 */
        exportMethodForm.get = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: exportMethod.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\Instructor\StudentManagementController::exportMethod
 * @see app/Http/Controllers/Instructor/StudentManagementController.php:265
 * @route '/student-management/course/{course}/export'
 */
        exportMethodForm.head = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: exportMethod.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    exportMethod.form = exportMethodForm
const studentManagement = {
    index: Object.assign(index, index),
statistics: Object.assign(statistics, statistics),
course: Object.assign(course, course),
student: Object.assign(student, student),
export: Object.assign(exportMethod, exportMethod),
}

export default studentManagement