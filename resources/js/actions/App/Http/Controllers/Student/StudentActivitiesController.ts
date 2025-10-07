import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Student\StudentActivitiesController::index
 * @see app/Http/Controllers/Student/StudentActivitiesController.php:27
 * @route '/student/activities'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/student/activities',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Student\StudentActivitiesController::index
 * @see app/Http/Controllers/Student/StudentActivitiesController.php:27
 * @route '/student/activities'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Student\StudentActivitiesController::index
 * @see app/Http/Controllers/Student/StudentActivitiesController.php:27
 * @route '/student/activities'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Student\StudentActivitiesController::index
 * @see app/Http/Controllers/Student/StudentActivitiesController.php:27
 * @route '/student/activities'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\Student\StudentActivitiesController::index
 * @see app/Http/Controllers/Student/StudentActivitiesController.php:27
 * @route '/student/activities'
 */
    const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: index.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\Student\StudentActivitiesController::index
 * @see app/Http/Controllers/Student/StudentActivitiesController.php:27
 * @route '/student/activities'
 */
        indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: index.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\Student\StudentActivitiesController::index
 * @see app/Http/Controllers/Student/StudentActivitiesController.php:27
 * @route '/student/activities'
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
* @see \App\Http\Controllers\Student\StudentActivitiesController::show
 * @see app/Http/Controllers/Student/StudentActivitiesController.php:118
 * @route '/student/activities/{activity}'
 */
export const show = (args: { activity: string | number } | [activity: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/student/activities/{activity}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Student\StudentActivitiesController::show
 * @see app/Http/Controllers/Student/StudentActivitiesController.php:118
 * @route '/student/activities/{activity}'
 */
show.url = (args: { activity: string | number } | [activity: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return show.definition.url
            .replace('{activity}', parsedArgs.activity.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Student\StudentActivitiesController::show
 * @see app/Http/Controllers/Student/StudentActivitiesController.php:118
 * @route '/student/activities/{activity}'
 */
show.get = (args: { activity: string | number } | [activity: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Student\StudentActivitiesController::show
 * @see app/Http/Controllers/Student/StudentActivitiesController.php:118
 * @route '/student/activities/{activity}'
 */
show.head = (args: { activity: string | number } | [activity: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\Student\StudentActivitiesController::show
 * @see app/Http/Controllers/Student/StudentActivitiesController.php:118
 * @route '/student/activities/{activity}'
 */
    const showForm = (args: { activity: string | number } | [activity: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: show.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\Student\StudentActivitiesController::show
 * @see app/Http/Controllers/Student/StudentActivitiesController.php:118
 * @route '/student/activities/{activity}'
 */
        showForm.get = (args: { activity: string | number } | [activity: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: show.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\Student\StudentActivitiesController::show
 * @see app/Http/Controllers/Student/StudentActivitiesController.php:118
 * @route '/student/activities/{activity}'
 */
        showForm.head = (args: { activity: string | number } | [activity: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: show.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    show.form = showForm
const StudentActivitiesController = { index, show }

export default StudentActivitiesController