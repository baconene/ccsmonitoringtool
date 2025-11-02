import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../wayfinder'
import submissions3f87f7 from './submissions'
import grade from './grade'
/**
* @see \App\Http\Controllers\Instructor\AssignmentGradingController::submissions
 * @see app/Http/Controllers/Instructor/AssignmentGradingController.php:23
 * @route '/instructor/assignments/{assignment}/submissions'
 */
export const submissions = (args: { assignment: number | { id: number } } | [assignment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: submissions.url(args, options),
    method: 'get',
})

submissions.definition = {
    methods: ["get","head"],
    url: '/instructor/assignments/{assignment}/submissions',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Instructor\AssignmentGradingController::submissions
 * @see app/Http/Controllers/Instructor/AssignmentGradingController.php:23
 * @route '/instructor/assignments/{assignment}/submissions'
 */
submissions.url = (args: { assignment: number | { id: number } } | [assignment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return submissions.definition.url
            .replace('{assignment}', parsedArgs.assignment.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Instructor\AssignmentGradingController::submissions
 * @see app/Http/Controllers/Instructor/AssignmentGradingController.php:23
 * @route '/instructor/assignments/{assignment}/submissions'
 */
submissions.get = (args: { assignment: number | { id: number } } | [assignment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: submissions.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Instructor\AssignmentGradingController::submissions
 * @see app/Http/Controllers/Instructor/AssignmentGradingController.php:23
 * @route '/instructor/assignments/{assignment}/submissions'
 */
submissions.head = (args: { assignment: number | { id: number } } | [assignment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: submissions.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\Instructor\AssignmentGradingController::submissions
 * @see app/Http/Controllers/Instructor/AssignmentGradingController.php:23
 * @route '/instructor/assignments/{assignment}/submissions'
 */
    const submissionsForm = (args: { assignment: number | { id: number } } | [assignment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: submissions.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\Instructor\AssignmentGradingController::submissions
 * @see app/Http/Controllers/Instructor/AssignmentGradingController.php:23
 * @route '/instructor/assignments/{assignment}/submissions'
 */
        submissionsForm.get = (args: { assignment: number | { id: number } } | [assignment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: submissions.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\Instructor\AssignmentGradingController::submissions
 * @see app/Http/Controllers/Instructor/AssignmentGradingController.php:23
 * @route '/instructor/assignments/{assignment}/submissions'
 */
        submissionsForm.head = (args: { assignment: number | { id: number } } | [assignment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: submissions.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    submissions.form = submissionsForm
const assignments = {
    submissions: Object.assign(submissions, submissions3f87f7),
grade: Object.assign(grade, grade),
}

export default assignments