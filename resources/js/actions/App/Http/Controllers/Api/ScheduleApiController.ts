import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\ScheduleApiController::index
 * @see app/Http/Controllers/Api/ScheduleApiController.php:16
 * @route '/api/schedule'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/api/schedule',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\ScheduleApiController::index
 * @see app/Http/Controllers/Api/ScheduleApiController.php:16
 * @route '/api/schedule'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScheduleApiController::index
 * @see app/Http/Controllers/Api/ScheduleApiController.php:16
 * @route '/api/schedule'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Api\ScheduleApiController::index
 * @see app/Http/Controllers/Api/ScheduleApiController.php:16
 * @route '/api/schedule'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\Api\ScheduleApiController::index
 * @see app/Http/Controllers/Api/ScheduleApiController.php:16
 * @route '/api/schedule'
 */
    const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: index.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\Api\ScheduleApiController::index
 * @see app/Http/Controllers/Api/ScheduleApiController.php:16
 * @route '/api/schedule'
 */
        indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: index.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\Api\ScheduleApiController::index
 * @see app/Http/Controllers/Api/ScheduleApiController.php:16
 * @route '/api/schedule'
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
* @see \App\Http\Controllers\Api\ScheduleApiController::upcoming
 * @see app/Http/Controllers/Api/ScheduleApiController.php:37
 * @route '/api/schedule/upcoming'
 */
export const upcoming = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: upcoming.url(options),
    method: 'get',
})

upcoming.definition = {
    methods: ["get","head"],
    url: '/api/schedule/upcoming',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\ScheduleApiController::upcoming
 * @see app/Http/Controllers/Api/ScheduleApiController.php:37
 * @route '/api/schedule/upcoming'
 */
upcoming.url = (options?: RouteQueryOptions) => {
    return upcoming.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScheduleApiController::upcoming
 * @see app/Http/Controllers/Api/ScheduleApiController.php:37
 * @route '/api/schedule/upcoming'
 */
upcoming.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: upcoming.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Api\ScheduleApiController::upcoming
 * @see app/Http/Controllers/Api/ScheduleApiController.php:37
 * @route '/api/schedule/upcoming'
 */
upcoming.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: upcoming.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\Api\ScheduleApiController::upcoming
 * @see app/Http/Controllers/Api/ScheduleApiController.php:37
 * @route '/api/schedule/upcoming'
 */
    const upcomingForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: upcoming.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\Api\ScheduleApiController::upcoming
 * @see app/Http/Controllers/Api/ScheduleApiController.php:37
 * @route '/api/schedule/upcoming'
 */
        upcomingForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: upcoming.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\Api\ScheduleApiController::upcoming
 * @see app/Http/Controllers/Api/ScheduleApiController.php:37
 * @route '/api/schedule/upcoming'
 */
        upcomingForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: upcoming.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    upcoming.form = upcomingForm
const ScheduleApiController = { index, upcoming }

export default ScheduleApiController