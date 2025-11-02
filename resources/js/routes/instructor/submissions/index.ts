import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\Instructor\StudentSubmissionController::show
 * @see app/Http/Controllers/Instructor/StudentSubmissionController.php:18
 * @route '/instructor/submissions/{submission}'
 */
export const show = (args: { submission: number | { id: number } } | [submission: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/instructor/submissions/{submission}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Instructor\StudentSubmissionController::show
 * @see app/Http/Controllers/Instructor/StudentSubmissionController.php:18
 * @route '/instructor/submissions/{submission}'
 */
show.url = (args: { submission: number | { id: number } } | [submission: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { submission: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { submission: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    submission: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        submission: typeof args.submission === 'object'
                ? args.submission.id
                : args.submission,
                }

    return show.definition.url
            .replace('{submission}', parsedArgs.submission.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Instructor\StudentSubmissionController::show
 * @see app/Http/Controllers/Instructor/StudentSubmissionController.php:18
 * @route '/instructor/submissions/{submission}'
 */
show.get = (args: { submission: number | { id: number } } | [submission: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Instructor\StudentSubmissionController::show
 * @see app/Http/Controllers/Instructor/StudentSubmissionController.php:18
 * @route '/instructor/submissions/{submission}'
 */
show.head = (args: { submission: number | { id: number } } | [submission: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\Instructor\StudentSubmissionController::show
 * @see app/Http/Controllers/Instructor/StudentSubmissionController.php:18
 * @route '/instructor/submissions/{submission}'
 */
    const showForm = (args: { submission: number | { id: number } } | [submission: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: show.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\Instructor\StudentSubmissionController::show
 * @see app/Http/Controllers/Instructor/StudentSubmissionController.php:18
 * @route '/instructor/submissions/{submission}'
 */
        showForm.get = (args: { submission: number | { id: number } } | [submission: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: show.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\Instructor\StudentSubmissionController::show
 * @see app/Http/Controllers/Instructor/StudentSubmissionController.php:18
 * @route '/instructor/submissions/{submission}'
 */
        showForm.head = (args: { submission: number | { id: number } } | [submission: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: show.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    show.form = showForm
/**
* @see \App\Http\Controllers\Instructor\StudentSubmissionController::grade
 * @see app/Http/Controllers/Instructor/StudentSubmissionController.php:71
 * @route '/instructor/submissions/{submission}/grade'
 */
export const grade = (args: { submission: number | { id: number } } | [submission: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: grade.url(args, options),
    method: 'post',
})

grade.definition = {
    methods: ["post"],
    url: '/instructor/submissions/{submission}/grade',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Instructor\StudentSubmissionController::grade
 * @see app/Http/Controllers/Instructor/StudentSubmissionController.php:71
 * @route '/instructor/submissions/{submission}/grade'
 */
grade.url = (args: { submission: number | { id: number } } | [submission: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { submission: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { submission: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    submission: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        submission: typeof args.submission === 'object'
                ? args.submission.id
                : args.submission,
                }

    return grade.definition.url
            .replace('{submission}', parsedArgs.submission.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Instructor\StudentSubmissionController::grade
 * @see app/Http/Controllers/Instructor/StudentSubmissionController.php:71
 * @route '/instructor/submissions/{submission}/grade'
 */
grade.post = (args: { submission: number | { id: number } } | [submission: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: grade.url(args, options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\Instructor\StudentSubmissionController::grade
 * @see app/Http/Controllers/Instructor/StudentSubmissionController.php:71
 * @route '/instructor/submissions/{submission}/grade'
 */
    const gradeForm = (args: { submission: number | { id: number } } | [submission: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: grade.url(args, options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\Instructor\StudentSubmissionController::grade
 * @see app/Http/Controllers/Instructor/StudentSubmissionController.php:71
 * @route '/instructor/submissions/{submission}/grade'
 */
        gradeForm.post = (args: { submission: number | { id: number } } | [submission: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: grade.url(args, options),
            method: 'post',
        })
    
    grade.form = gradeForm
const submissions = {
    show: Object.assign(show, show),
grade: Object.assign(grade, grade),
}

export default submissions