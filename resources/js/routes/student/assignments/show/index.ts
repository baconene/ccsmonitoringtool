import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\StudentAssignmentController::old
 * @see app/Http/Controllers/StudentAssignmentController.php:161
 * @route '/student/assignments/{assignment}/old'
 */
export const old = (args: { assignment: number | { id: number } } | [assignment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: old.url(args, options),
    method: 'get',
})

old.definition = {
    methods: ["get","head"],
    url: '/student/assignments/{assignment}/old',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\StudentAssignmentController::old
 * @see app/Http/Controllers/StudentAssignmentController.php:161
 * @route '/student/assignments/{assignment}/old'
 */
old.url = (args: { assignment: number | { id: number } } | [assignment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { assignment: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { assignment: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    assignment: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        assignment: typeof args.assignment === 'object'
                ? args.assignment.id
                : args.assignment,
                }

    return old.definition.url
            .replace('{assignment}', parsedArgs.assignment.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\StudentAssignmentController::old
 * @see app/Http/Controllers/StudentAssignmentController.php:161
 * @route '/student/assignments/{assignment}/old'
 */
old.get = (args: { assignment: number | { id: number } } | [assignment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: old.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\StudentAssignmentController::old
 * @see app/Http/Controllers/StudentAssignmentController.php:161
 * @route '/student/assignments/{assignment}/old'
 */
old.head = (args: { assignment: number | { id: number } } | [assignment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: old.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\StudentAssignmentController::old
 * @see app/Http/Controllers/StudentAssignmentController.php:161
 * @route '/student/assignments/{assignment}/old'
 */
    const oldForm = (args: { assignment: number | { id: number } } | [assignment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: old.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\StudentAssignmentController::old
 * @see app/Http/Controllers/StudentAssignmentController.php:161
 * @route '/student/assignments/{assignment}/old'
 */
        oldForm.get = (args: { assignment: number | { id: number } } | [assignment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: old.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\StudentAssignmentController::old
 * @see app/Http/Controllers/StudentAssignmentController.php:161
 * @route '/student/assignments/{assignment}/old'
 */
        oldForm.head = (args: { assignment: number | { id: number } } | [assignment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: old.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    old.form = oldForm
const show = {
    old: Object.assign(old, old),
}

export default show