import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
 * @see routes/web.php:562
 * @route '/student/assessment'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/student/assessment',
} satisfies RouteDefinition<["get","head"]>

/**
 * @see routes/web.php:562
 * @route '/student/assessment'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
 * @see routes/web.php:562
 * @route '/student/assessment'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
 * @see routes/web.php:562
 * @route '/student/assessment'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

    /**
 * @see routes/web.php:562
 * @route '/student/assessment'
 */
    const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: index.url(options),
        method: 'get',
    })

            /**
 * @see routes/web.php:562
 * @route '/student/assessment'
 */
        indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: index.url(options),
            method: 'get',
        })
            /**
 * @see routes/web.php:562
 * @route '/student/assessment'
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
* @see \App\Http\Controllers\Student\StudentActivityResultsController::results
 * @see app/Http/Controllers/Student/StudentActivityResultsController.php:25
 * @route '/student/assessment/{studentActivity}/results'
 */
export const results = (args: { studentActivity: number | { id: number } } | [studentActivity: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: results.url(args, options),
    method: 'get',
})

results.definition = {
    methods: ["get","head"],
    url: '/student/assessment/{studentActivity}/results',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Student\StudentActivityResultsController::results
 * @see app/Http/Controllers/Student/StudentActivityResultsController.php:25
 * @route '/student/assessment/{studentActivity}/results'
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
 * @route '/student/assessment/{studentActivity}/results'
 */
results.get = (args: { studentActivity: number | { id: number } } | [studentActivity: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: results.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Student\StudentActivityResultsController::results
 * @see app/Http/Controllers/Student/StudentActivityResultsController.php:25
 * @route '/student/assessment/{studentActivity}/results'
 */
results.head = (args: { studentActivity: number | { id: number } } | [studentActivity: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: results.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\Student\StudentActivityResultsController::results
 * @see app/Http/Controllers/Student/StudentActivityResultsController.php:25
 * @route '/student/assessment/{studentActivity}/results'
 */
    const resultsForm = (args: { studentActivity: number | { id: number } } | [studentActivity: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: results.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\Student\StudentActivityResultsController::results
 * @see app/Http/Controllers/Student/StudentActivityResultsController.php:25
 * @route '/student/assessment/{studentActivity}/results'
 */
        resultsForm.get = (args: { studentActivity: number | { id: number } } | [studentActivity: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: results.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\Student\StudentActivityResultsController::results
 * @see app/Http/Controllers/Student/StudentActivityResultsController.php:25
 * @route '/student/assessment/{studentActivity}/results'
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
const assessment = {
    index: Object.assign(index, index),
results: Object.assign(results, results),
}

export default assessment