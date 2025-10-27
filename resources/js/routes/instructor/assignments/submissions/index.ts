import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\Instructor\AssignmentGradingController::view
 * @see app/Http/Controllers/Instructor/AssignmentGradingController.php:118
 * @route '/instructor/assignments/{assignment}/submissions/{progress}'
 */
export const view = (args: { assignment: number | { id: number }, progress: string | number } | [assignment: number | { id: number }, progress: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: view.url(args, options),
    method: 'get',
})

view.definition = {
    methods: ["get","head"],
    url: '/instructor/assignments/{assignment}/submissions/{progress}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Instructor\AssignmentGradingController::view
 * @see app/Http/Controllers/Instructor/AssignmentGradingController.php:118
 * @route '/instructor/assignments/{assignment}/submissions/{progress}'
 */
view.url = (args: { assignment: number | { id: number }, progress: string | number } | [assignment: number | { id: number }, progress: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
                    assignment: args[0],
                    progress: args[1],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        assignment: typeof args.assignment === 'object'
                ? args.assignment.id
                : args.assignment,
                                progress: args.progress,
                }

    return view.definition.url
            .replace('{assignment}', parsedArgs.assignment.toString())
            .replace('{progress}', parsedArgs.progress.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Instructor\AssignmentGradingController::view
 * @see app/Http/Controllers/Instructor/AssignmentGradingController.php:118
 * @route '/instructor/assignments/{assignment}/submissions/{progress}'
 */
view.get = (args: { assignment: number | { id: number }, progress: string | number } | [assignment: number | { id: number }, progress: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: view.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Instructor\AssignmentGradingController::view
 * @see app/Http/Controllers/Instructor/AssignmentGradingController.php:118
 * @route '/instructor/assignments/{assignment}/submissions/{progress}'
 */
view.head = (args: { assignment: number | { id: number }, progress: string | number } | [assignment: number | { id: number }, progress: string | number ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: view.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\Instructor\AssignmentGradingController::view
 * @see app/Http/Controllers/Instructor/AssignmentGradingController.php:118
 * @route '/instructor/assignments/{assignment}/submissions/{progress}'
 */
    const viewForm = (args: { assignment: number | { id: number }, progress: string | number } | [assignment: number | { id: number }, progress: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: view.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\Instructor\AssignmentGradingController::view
 * @see app/Http/Controllers/Instructor/AssignmentGradingController.php:118
 * @route '/instructor/assignments/{assignment}/submissions/{progress}'
 */
        viewForm.get = (args: { assignment: number | { id: number }, progress: string | number } | [assignment: number | { id: number }, progress: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: view.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\Instructor\AssignmentGradingController::view
 * @see app/Http/Controllers/Instructor/AssignmentGradingController.php:118
 * @route '/instructor/assignments/{assignment}/submissions/{progress}'
 */
        viewForm.head = (args: { assignment: number | { id: number }, progress: string | number } | [assignment: number | { id: number }, progress: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: view.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    view.form = viewForm
const submissions = {
    view: Object.assign(view, view),
}

export default submissions