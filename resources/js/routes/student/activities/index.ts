import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\Student\StudentActivityController::markComplete
 * @see app/Http/Controllers/Student/StudentActivityController.php:18
 * @route '/student/activities/{activity}/mark-complete'
 */
export const markComplete = (args: { activity: string | number } | [activity: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: markComplete.url(args, options),
    method: 'post',
})

markComplete.definition = {
    methods: ["post"],
    url: '/student/activities/{activity}/mark-complete',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Student\StudentActivityController::markComplete
 * @see app/Http/Controllers/Student/StudentActivityController.php:18
 * @route '/student/activities/{activity}/mark-complete'
 */
markComplete.url = (args: { activity: string | number } | [activity: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { activity: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    activity: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        activity: args.activity,
                }

    return markComplete.definition.url
            .replace('{activity}', parsedArgs.activity.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Student\StudentActivityController::markComplete
 * @see app/Http/Controllers/Student/StudentActivityController.php:18
 * @route '/student/activities/{activity}/mark-complete'
 */
markComplete.post = (args: { activity: string | number } | [activity: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: markComplete.url(args, options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\Student\StudentActivityController::markComplete
 * @see app/Http/Controllers/Student/StudentActivityController.php:18
 * @route '/student/activities/{activity}/mark-complete'
 */
    const markCompleteForm = (args: { activity: string | number } | [activity: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: markComplete.url(args, options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\Student\StudentActivityController::markComplete
 * @see app/Http/Controllers/Student/StudentActivityController.php:18
 * @route '/student/activities/{activity}/mark-complete'
 */
        markCompleteForm.post = (args: { activity: string | number } | [activity: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: markComplete.url(args, options),
            method: 'post',
        })
    
    markComplete.form = markCompleteForm
/**
* @see \App\Http\Controllers\Student\StudentActivityResultsController::results
 * @see app/Http/Controllers/Student/StudentActivityResultsController.php:25
 * @route '/student/activities/{studentActivity}/results'
 */
export const results = (args: { studentActivity: number | { id: number } } | [studentActivity: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: results.url(args, options),
    method: 'get',
})

results.definition = {
    methods: ["get","head"],
    url: '/student/activities/{studentActivity}/results',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Student\StudentActivityResultsController::results
 * @see app/Http/Controllers/Student/StudentActivityResultsController.php:25
 * @route '/student/activities/{studentActivity}/results'
 */
results.url = (args: { studentActivity: number | { id: number } } | [studentActivity: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { studentActivity: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { studentActivity: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    studentActivity: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        studentActivity: typeof args.studentActivity === 'object'
                ? args.studentActivity.id
                : args.studentActivity,
                }

    return results.definition.url
            .replace('{studentActivity}', parsedArgs.studentActivity.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Student\StudentActivityResultsController::results
 * @see app/Http/Controllers/Student/StudentActivityResultsController.php:25
 * @route '/student/activities/{studentActivity}/results'
 */
results.get = (args: { studentActivity: number | { id: number } } | [studentActivity: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: results.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Student\StudentActivityResultsController::results
 * @see app/Http/Controllers/Student/StudentActivityResultsController.php:25
 * @route '/student/activities/{studentActivity}/results'
 */
results.head = (args: { studentActivity: number | { id: number } } | [studentActivity: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: results.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\Student\StudentActivityResultsController::results
 * @see app/Http/Controllers/Student/StudentActivityResultsController.php:25
 * @route '/student/activities/{studentActivity}/results'
 */
    const resultsForm = (args: { studentActivity: number | { id: number } } | [studentActivity: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: results.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\Student\StudentActivityResultsController::results
 * @see app/Http/Controllers/Student/StudentActivityResultsController.php:25
 * @route '/student/activities/{studentActivity}/results'
 */
        resultsForm.get = (args: { studentActivity: number | { id: number } } | [studentActivity: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: results.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\Student\StudentActivityResultsController::results
 * @see app/Http/Controllers/Student/StudentActivityResultsController.php:25
 * @route '/student/activities/{studentActivity}/results'
 */
        resultsForm.head = (args: { studentActivity: number | { id: number } } | [studentActivity: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: results.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    results.form = resultsForm
const activities = {
    results: Object.assign(results, results),
}

export default activities