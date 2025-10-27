import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\Student\StudentQuizController::show
 * @see app/Http/Controllers/Student/StudentQuizController.php:20
 * @route '/student/quizs/{studentActivity}'
 */
export const show = (args: { studentActivity: number | { id: number } } | [studentActivity: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/student/quizs/{studentActivity}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Student\StudentQuizController::show
 * @see app/Http/Controllers/Student/StudentQuizController.php:20
 * @route '/student/quizs/{studentActivity}'
 */
show.url = (args: { studentActivity: number | { id: number } } | [studentActivity: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { studentActivity: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { studentActivity: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    studentActivity: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        studentActivity: typeof args.studentActivity === 'object'
                ? args.studentActivity.id
                : args.studentActivity,
                }

    return show.definition.url
            .replace('{studentActivity}', parsedArgs.studentActivity.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Student\StudentQuizController::show
 * @see app/Http/Controllers/Student/StudentQuizController.php:20
 * @route '/student/quizs/{studentActivity}'
 */
show.get = (args: { studentActivity: number | { id: number } } | [studentActivity: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Student\StudentQuizController::show
 * @see app/Http/Controllers/Student/StudentQuizController.php:20
 * @route '/student/quizs/{studentActivity}'
 */
show.head = (args: { studentActivity: number | { id: number } } | [studentActivity: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\Student\StudentQuizController::show
 * @see app/Http/Controllers/Student/StudentQuizController.php:20
 * @route '/student/quizs/{studentActivity}'
 */
    const showForm = (args: { studentActivity: number | { id: number } } | [studentActivity: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: show.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\Student\StudentQuizController::show
 * @see app/Http/Controllers/Student/StudentQuizController.php:20
 * @route '/student/quizs/{studentActivity}'
 */
        showForm.get = (args: { studentActivity: number | { id: number } } | [studentActivity: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: show.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\Student\StudentQuizController::show
 * @see app/Http/Controllers/Student/StudentQuizController.php:20
 * @route '/student/quizs/{studentActivity}'
 */
        showForm.head = (args: { studentActivity: number | { id: number } } | [studentActivity: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: show.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    show.form = showForm
const quizs = {
    show: Object.assign(show, show),
}

export default quizs