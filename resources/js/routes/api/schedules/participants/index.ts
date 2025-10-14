import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\ScheduleController::status
 * @see app/Http/Controllers/Api/ScheduleController.php:435
 * @route '/api/schedules/{scheduleId}/participants/{userId}/status'
 */
export const status = (args: { scheduleId: string | number, userId: string | number } | [scheduleId: string | number, userId: string | number ], options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: status.url(args, options),
    method: 'patch',
})

status.definition = {
    methods: ["patch"],
    url: '/api/schedules/{scheduleId}/participants/{userId}/status',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Http\Controllers\Api\ScheduleController::status
 * @see app/Http/Controllers/Api/ScheduleController.php:435
 * @route '/api/schedules/{scheduleId}/participants/{userId}/status'
 */
status.url = (args: { scheduleId: string | number, userId: string | number } | [scheduleId: string | number, userId: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
                    scheduleId: args[0],
                    userId: args[1],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        scheduleId: args.scheduleId,
                                userId: args.userId,
                }

    return status.definition.url
            .replace('{scheduleId}', parsedArgs.scheduleId.toString())
            .replace('{userId}', parsedArgs.userId.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScheduleController::status
 * @see app/Http/Controllers/Api/ScheduleController.php:435
 * @route '/api/schedules/{scheduleId}/participants/{userId}/status'
 */
status.patch = (args: { scheduleId: string | number, userId: string | number } | [scheduleId: string | number, userId: string | number ], options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: status.url(args, options),
    method: 'patch',
})

    /**
* @see \App\Http\Controllers\Api\ScheduleController::status
 * @see app/Http/Controllers/Api/ScheduleController.php:435
 * @route '/api/schedules/{scheduleId}/participants/{userId}/status'
 */
    const statusForm = (args: { scheduleId: string | number, userId: string | number } | [scheduleId: string | number, userId: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: status.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'PATCH',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\Api\ScheduleController::status
 * @see app/Http/Controllers/Api/ScheduleController.php:435
 * @route '/api/schedules/{scheduleId}/participants/{userId}/status'
 */
        statusForm.patch = (args: { scheduleId: string | number, userId: string | number } | [scheduleId: string | number, userId: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: status.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'PATCH',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    status.form = statusForm
const participants = {
    status: Object.assign(status, status),
}

export default participants