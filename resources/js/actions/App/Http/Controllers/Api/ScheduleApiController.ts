import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\ScheduleApiController::index
 * @see app/Http/Controllers/Api/ScheduleApiController.php:21
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
 * @see app/Http/Controllers/Api/ScheduleApiController.php:21
 * @route '/api/schedule'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScheduleApiController::index
 * @see app/Http/Controllers/Api/ScheduleApiController.php:21
 * @route '/api/schedule'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Api\ScheduleApiController::index
 * @see app/Http/Controllers/Api/ScheduleApiController.php:21
 * @route '/api/schedule'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\Api\ScheduleApiController::index
 * @see app/Http/Controllers/Api/ScheduleApiController.php:21
 * @route '/api/schedule'
 */
    const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: index.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\Api\ScheduleApiController::index
 * @see app/Http/Controllers/Api/ScheduleApiController.php:21
 * @route '/api/schedule'
 */
        indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: index.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\Api\ScheduleApiController::index
 * @see app/Http/Controllers/Api/ScheduleApiController.php:21
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
 * @see app/Http/Controllers/Api/ScheduleApiController.php:42
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
 * @see app/Http/Controllers/Api/ScheduleApiController.php:42
 * @route '/api/schedule/upcoming'
 */
upcoming.url = (options?: RouteQueryOptions) => {
    return upcoming.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScheduleApiController::upcoming
 * @see app/Http/Controllers/Api/ScheduleApiController.php:42
 * @route '/api/schedule/upcoming'
 */
upcoming.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: upcoming.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Api\ScheduleApiController::upcoming
 * @see app/Http/Controllers/Api/ScheduleApiController.php:42
 * @route '/api/schedule/upcoming'
 */
upcoming.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: upcoming.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\Api\ScheduleApiController::upcoming
 * @see app/Http/Controllers/Api/ScheduleApiController.php:42
 * @route '/api/schedule/upcoming'
 */
    const upcomingForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: upcoming.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\Api\ScheduleApiController::upcoming
 * @see app/Http/Controllers/Api/ScheduleApiController.php:42
 * @route '/api/schedule/upcoming'
 */
        upcomingForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: upcoming.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\Api\ScheduleApiController::upcoming
 * @see app/Http/Controllers/Api/ScheduleApiController.php:42
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
/**
* @see \App\Http\Controllers\Api\ScheduleApiController::update
 * @see app/Http/Controllers/Api/ScheduleApiController.php:72
 * @route '/api/schedules/{schedule}'
 */
export const update = (args: { schedule: number | { id: number } } | [schedule: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/api/schedules/{schedule}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\Api\ScheduleApiController::update
 * @see app/Http/Controllers/Api/ScheduleApiController.php:72
 * @route '/api/schedules/{schedule}'
 */
update.url = (args: { schedule: number | { id: number } } | [schedule: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { schedule: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { schedule: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    schedule: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        schedule: typeof args.schedule === 'object'
                ? args.schedule.id
                : args.schedule,
                }

    return update.definition.url
            .replace('{schedule}', parsedArgs.schedule.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScheduleApiController::update
 * @see app/Http/Controllers/Api/ScheduleApiController.php:72
 * @route '/api/schedules/{schedule}'
 */
update.put = (args: { schedule: number | { id: number } } | [schedule: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

    /**
* @see \App\Http\Controllers\Api\ScheduleApiController::update
 * @see app/Http/Controllers/Api/ScheduleApiController.php:72
 * @route '/api/schedules/{schedule}'
 */
    const updateForm = (args: { schedule: number | { id: number } } | [schedule: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: update.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'PUT',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\Api\ScheduleApiController::update
 * @see app/Http/Controllers/Api/ScheduleApiController.php:72
 * @route '/api/schedules/{schedule}'
 */
        updateForm.put = (args: { schedule: number | { id: number } } | [schedule: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: update.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'PUT',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    update.form = updateForm
const ScheduleApiController = { index, upcoming, update }

export default ScheduleApiController