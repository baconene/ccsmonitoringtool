import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\GradeLevelController::index
 * @see app/Http/Controllers/GradeLevelController.php:13
 * @route '/api/grade-levels'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/api/grade-levels',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\GradeLevelController::index
 * @see app/Http/Controllers/GradeLevelController.php:13
 * @route '/api/grade-levels'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\GradeLevelController::index
 * @see app/Http/Controllers/GradeLevelController.php:13
 * @route '/api/grade-levels'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\GradeLevelController::index
 * @see app/Http/Controllers/GradeLevelController.php:13
 * @route '/api/grade-levels'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\GradeLevelController::index
 * @see app/Http/Controllers/GradeLevelController.php:13
 * @route '/api/grade-levels'
 */
    const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: index.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\GradeLevelController::index
 * @see app/Http/Controllers/GradeLevelController.php:13
 * @route '/api/grade-levels'
 */
        indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: index.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\GradeLevelController::index
 * @see app/Http/Controllers/GradeLevelController.php:13
 * @route '/api/grade-levels'
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
const GradeLevelController = { index }

export default GradeLevelController