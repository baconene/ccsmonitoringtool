import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\ActivityTypeController::index
 * @see app/Http/Controllers/Api/ActivityTypeController.php:14
 * @route '/api/activity-types'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/api/activity-types',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\ActivityTypeController::index
 * @see app/Http/Controllers/Api/ActivityTypeController.php:14
 * @route '/api/activity-types'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ActivityTypeController::index
 * @see app/Http/Controllers/Api/ActivityTypeController.php:14
 * @route '/api/activity-types'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Api\ActivityTypeController::index
 * @see app/Http/Controllers/Api/ActivityTypeController.php:14
 * @route '/api/activity-types'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\Api\ActivityTypeController::index
 * @see app/Http/Controllers/Api/ActivityTypeController.php:14
 * @route '/api/activity-types'
 */
    const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: index.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\Api\ActivityTypeController::index
 * @see app/Http/Controllers/Api/ActivityTypeController.php:14
 * @route '/api/activity-types'
 */
        indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: index.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\Api\ActivityTypeController::index
 * @see app/Http/Controllers/Api/ActivityTypeController.php:14
 * @route '/api/activity-types'
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
const ActivityTypeController = { index }

export default ActivityTypeController