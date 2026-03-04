import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\AdminConfigurationController::index
 * @see app/Http/Controllers/AdminConfigurationController.php:17
 * @route '/admin/configuration'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/admin/configuration',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\AdminConfigurationController::index
 * @see app/Http/Controllers/AdminConfigurationController.php:17
 * @route '/admin/configuration'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\AdminConfigurationController::index
 * @see app/Http/Controllers/AdminConfigurationController.php:17
 * @route '/admin/configuration'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\AdminConfigurationController::index
 * @see app/Http/Controllers/AdminConfigurationController.php:17
 * @route '/admin/configuration'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\AdminConfigurationController::index
 * @see app/Http/Controllers/AdminConfigurationController.php:17
 * @route '/admin/configuration'
 */
    const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: index.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\AdminConfigurationController::index
 * @see app/Http/Controllers/AdminConfigurationController.php:17
 * @route '/admin/configuration'
 */
        indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: index.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\AdminConfigurationController::index
 * @see app/Http/Controllers/AdminConfigurationController.php:17
 * @route '/admin/configuration'
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
const configuration = {
    index: Object.assign(index, index),
}

export default configuration