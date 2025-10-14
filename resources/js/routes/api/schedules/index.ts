import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../wayfinder'
import participants from './participants'
/**
* @see \App\Http\Controllers\Api\ScheduleController::range
 * @see app/Http/Controllers/Api/ScheduleController.php:104
 * @route '/api/schedules/range'
 */
export const range = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: range.url(options),
    method: 'get',
})

range.definition = {
    methods: ["get","head"],
    url: '/api/schedules/range',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\ScheduleController::range
 * @see app/Http/Controllers/Api/ScheduleController.php:104
 * @route '/api/schedules/range'
 */
range.url = (options?: RouteQueryOptions) => {
    return range.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScheduleController::range
 * @see app/Http/Controllers/Api/ScheduleController.php:104
 * @route '/api/schedules/range'
 */
range.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: range.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Api\ScheduleController::range
 * @see app/Http/Controllers/Api/ScheduleController.php:104
 * @route '/api/schedules/range'
 */
range.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: range.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\Api\ScheduleController::range
 * @see app/Http/Controllers/Api/ScheduleController.php:104
 * @route '/api/schedules/range'
 */
    const rangeForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: range.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\Api\ScheduleController::range
 * @see app/Http/Controllers/Api/ScheduleController.php:104
 * @route '/api/schedules/range'
 */
        rangeForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: range.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\Api\ScheduleController::range
 * @see app/Http/Controllers/Api/ScheduleController.php:104
 * @route '/api/schedules/range'
 */
        rangeForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: range.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    range.form = rangeForm
/**
* @see \App\Http\Controllers\Api\ScheduleController::checkConflicts
 * @see app/Http/Controllers/Api/ScheduleController.php:365
 * @route '/api/schedules/check-conflicts'
 */
export const checkConflicts = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: checkConflicts.url(options),
    method: 'post',
})

checkConflicts.definition = {
    methods: ["post"],
    url: '/api/schedules/check-conflicts',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\ScheduleController::checkConflicts
 * @see app/Http/Controllers/Api/ScheduleController.php:365
 * @route '/api/schedules/check-conflicts'
 */
checkConflicts.url = (options?: RouteQueryOptions) => {
    return checkConflicts.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScheduleController::checkConflicts
 * @see app/Http/Controllers/Api/ScheduleController.php:365
 * @route '/api/schedules/check-conflicts'
 */
checkConflicts.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: checkConflicts.url(options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\Api\ScheduleController::checkConflicts
 * @see app/Http/Controllers/Api/ScheduleController.php:365
 * @route '/api/schedules/check-conflicts'
 */
    const checkConflictsForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: checkConflicts.url(options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\Api\ScheduleController::checkConflicts
 * @see app/Http/Controllers/Api/ScheduleController.php:365
 * @route '/api/schedules/check-conflicts'
 */
        checkConflictsForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: checkConflicts.url(options),
            method: 'post',
        })
    
    checkConflicts.form = checkConflictsForm
const schedules = {
    range: Object.assign(range, range),
checkConflicts: Object.assign(checkConflicts, checkConflicts),
participants: Object.assign(participants, participants),
}

export default schedules