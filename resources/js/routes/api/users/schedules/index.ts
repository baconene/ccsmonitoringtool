import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\ScheduleController::upcoming
 * @see app/Http/Controllers/Api/ScheduleController.php:23
 * @route '/api/users/{userId}/schedules/upcoming'
 */
export const upcoming = (args: { userId: string | number } | [userId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: upcoming.url(args, options),
    method: 'get',
})

upcoming.definition = {
    methods: ["get","head"],
    url: '/api/users/{userId}/schedules/upcoming',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\ScheduleController::upcoming
 * @see app/Http/Controllers/Api/ScheduleController.php:23
 * @route '/api/users/{userId}/schedules/upcoming'
 */
upcoming.url = (args: { userId: string | number } | [userId: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { userId: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    userId: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        userId: args.userId,
                }

    return upcoming.definition.url
            .replace('{userId}', parsedArgs.userId.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScheduleController::upcoming
 * @see app/Http/Controllers/Api/ScheduleController.php:23
 * @route '/api/users/{userId}/schedules/upcoming'
 */
upcoming.get = (args: { userId: string | number } | [userId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: upcoming.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Api\ScheduleController::upcoming
 * @see app/Http/Controllers/Api/ScheduleController.php:23
 * @route '/api/users/{userId}/schedules/upcoming'
 */
upcoming.head = (args: { userId: string | number } | [userId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: upcoming.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\Api\ScheduleController::upcoming
 * @see app/Http/Controllers/Api/ScheduleController.php:23
 * @route '/api/users/{userId}/schedules/upcoming'
 */
    const upcomingForm = (args: { userId: string | number } | [userId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: upcoming.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\Api\ScheduleController::upcoming
 * @see app/Http/Controllers/Api/ScheduleController.php:23
 * @route '/api/users/{userId}/schedules/upcoming'
 */
        upcomingForm.get = (args: { userId: string | number } | [userId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: upcoming.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\Api\ScheduleController::upcoming
 * @see app/Http/Controllers/Api/ScheduleController.php:23
 * @route '/api/users/{userId}/schedules/upcoming'
 */
        upcomingForm.head = (args: { userId: string | number } | [userId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: upcoming.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    upcoming.form = upcomingForm
const schedules = {
    upcoming: Object.assign(upcoming, upcoming),
}

export default schedules