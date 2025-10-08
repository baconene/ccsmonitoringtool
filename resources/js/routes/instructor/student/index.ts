import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\GradeController::detail
 * @see app/Http/Controllers/GradeController.php:0
 * @route '/instructor/student/{student}/detail'
 */
export const detail = (args: { student: string | number } | [student: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: detail.url(args, options),
    method: 'get',
})

detail.definition = {
    methods: ["get","head"],
    url: '/instructor/student/{student}/detail',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\GradeController::detail
 * @see app/Http/Controllers/GradeController.php:0
 * @route '/instructor/student/{student}/detail'
 */
detail.url = (args: { student: string | number } | [student: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { student: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    student: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        student: args.student,
                }

    return detail.definition.url
            .replace('{student}', parsedArgs.student.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\GradeController::detail
 * @see app/Http/Controllers/GradeController.php:0
 * @route '/instructor/student/{student}/detail'
 */
detail.get = (args: { student: string | number } | [student: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: detail.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\GradeController::detail
 * @see app/Http/Controllers/GradeController.php:0
 * @route '/instructor/student/{student}/detail'
 */
detail.head = (args: { student: string | number } | [student: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: detail.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\GradeController::detail
 * @see app/Http/Controllers/GradeController.php:0
 * @route '/instructor/student/{student}/detail'
 */
    const detailForm = (args: { student: string | number } | [student: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: detail.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\GradeController::detail
 * @see app/Http/Controllers/GradeController.php:0
 * @route '/instructor/student/{student}/detail'
 */
        detailForm.get = (args: { student: string | number } | [student: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: detail.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\GradeController::detail
 * @see app/Http/Controllers/GradeController.php:0
 * @route '/instructor/student/{student}/detail'
 */
        detailForm.head = (args: { student: string | number } | [student: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: detail.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    detail.form = detailForm
const student = {
    detail: Object.assign(detail, detail),
}

export default student