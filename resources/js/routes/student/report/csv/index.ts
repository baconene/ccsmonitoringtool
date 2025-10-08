import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\GradeController::complete
 * @see app/Http/Controllers/GradeController.php:511
 * @route '/student/report/csv/complete'
 */
export const complete = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: complete.url(options),
    method: 'get',
})

complete.definition = {
    methods: ["get","head"],
    url: '/student/report/csv/complete',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\GradeController::complete
 * @see app/Http/Controllers/GradeController.php:511
 * @route '/student/report/csv/complete'
 */
complete.url = (options?: RouteQueryOptions) => {
    return complete.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\GradeController::complete
 * @see app/Http/Controllers/GradeController.php:511
 * @route '/student/report/csv/complete'
 */
complete.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: complete.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\GradeController::complete
 * @see app/Http/Controllers/GradeController.php:511
 * @route '/student/report/csv/complete'
 */
complete.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: complete.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\GradeController::complete
 * @see app/Http/Controllers/GradeController.php:511
 * @route '/student/report/csv/complete'
 */
    const completeForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: complete.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\GradeController::complete
 * @see app/Http/Controllers/GradeController.php:511
 * @route '/student/report/csv/complete'
 */
        completeForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: complete.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\GradeController::complete
 * @see app/Http/Controllers/GradeController.php:511
 * @route '/student/report/csv/complete'
 */
        completeForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: complete.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    complete.form = completeForm
const csv = {
    complete: Object.assign(complete, complete),
}

export default csv