import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
 * @see routes/web.php:575
 * @route '/student/assignment/{studentActivity}/results'
 */
export const results = (args: { studentActivity: number | { id: number } } | [studentActivity: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: results.url(args, options),
    method: 'get',
})

results.definition = {
    methods: ["get","head"],
    url: '/student/assignment/{studentActivity}/results',
} satisfies RouteDefinition<["get","head"]>

/**
 * @see routes/web.php:575
 * @route '/student/assignment/{studentActivity}/results'
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
 * @see routes/web.php:575
 * @route '/student/assignment/{studentActivity}/results'
 */
results.get = (args: { studentActivity: number | { id: number } } | [studentActivity: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: results.url(args, options),
    method: 'get',
})
/**
 * @see routes/web.php:575
 * @route '/student/assignment/{studentActivity}/results'
 */
results.head = (args: { studentActivity: number | { id: number } } | [studentActivity: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: results.url(args, options),
    method: 'head',
})

    /**
 * @see routes/web.php:575
 * @route '/student/assignment/{studentActivity}/results'
 */
    const resultsForm = (args: { studentActivity: number | { id: number } } | [studentActivity: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: results.url(args, options),
        method: 'get',
    })

            /**
 * @see routes/web.php:575
 * @route '/student/assignment/{studentActivity}/results'
 */
        resultsForm.get = (args: { studentActivity: number | { id: number } } | [studentActivity: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: results.url(args, options),
            method: 'get',
        })
            /**
 * @see routes/web.php:575
 * @route '/student/assignment/{studentActivity}/results'
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
/**
* @see \App\Http\Controllers\StudentAssignmentController::start
 * @see app/Http/Controllers/StudentAssignmentController.php:42
 * @route '/student/assignment/start/{activity}'
 */
export const start = (args: { activity: string | number } | [activity: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: start.url(args, options),
    method: 'get',
})

start.definition = {
    methods: ["get","head"],
    url: '/student/assignment/start/{activity}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\StudentAssignmentController::start
 * @see app/Http/Controllers/StudentAssignmentController.php:42
 * @route '/student/assignment/start/{activity}'
 */
start.url = (args: { activity: string | number } | [activity: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return start.definition.url
            .replace('{activity}', parsedArgs.activity.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\StudentAssignmentController::start
 * @see app/Http/Controllers/StudentAssignmentController.php:42
 * @route '/student/assignment/start/{activity}'
 */
start.get = (args: { activity: string | number } | [activity: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: start.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\StudentAssignmentController::start
 * @see app/Http/Controllers/StudentAssignmentController.php:42
 * @route '/student/assignment/start/{activity}'
 */
start.head = (args: { activity: string | number } | [activity: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: start.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\StudentAssignmentController::start
 * @see app/Http/Controllers/StudentAssignmentController.php:42
 * @route '/student/assignment/start/{activity}'
 */
    const startForm = (args: { activity: string | number } | [activity: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: start.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\StudentAssignmentController::start
 * @see app/Http/Controllers/StudentAssignmentController.php:42
 * @route '/student/assignment/start/{activity}'
 */
        startForm.get = (args: { activity: string | number } | [activity: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: start.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\StudentAssignmentController::start
 * @see app/Http/Controllers/StudentAssignmentController.php:42
 * @route '/student/assignment/start/{activity}'
 */
        startForm.head = (args: { activity: string | number } | [activity: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: start.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    start.form = startForm
const assignment = {
    results: Object.assign(results, results),
start: Object.assign(start, start),
}

export default assignment