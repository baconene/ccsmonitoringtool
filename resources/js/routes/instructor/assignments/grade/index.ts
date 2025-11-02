import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\Instructor\AssignmentGradingController::question
 * @see app/Http/Controllers/Instructor/AssignmentGradingController.php:152
 * @route '/instructor/assignments/{assignment}/grade/{progress}/question'
 */
export const question = (args: { assignment: number | { id: number }, progress: string | number | { id: string | number } } | [assignment: number | { id: number }, progress: string | number | { id: string | number } ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: question.url(args, options),
    method: 'post',
})

question.definition = {
    methods: ["post"],
    url: '/instructor/assignments/{assignment}/grade/{progress}/question',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Instructor\AssignmentGradingController::question
 * @see app/Http/Controllers/Instructor/AssignmentGradingController.php:152
 * @route '/instructor/assignments/{assignment}/grade/{progress}/question'
 */
question.url = (args: { assignment: number | { id: number }, progress: string | number | { id: string | number } } | [assignment: number | { id: number }, progress: string | number | { id: string | number } ], options?: RouteQueryOptions) => {
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
                                progress: typeof args.progress === 'object'
                ? args.progress.id
                : args.progress,
                }

    return question.definition.url
            .replace('{assignment}', parsedArgs.assignment.toString())
            .replace('{progress}', parsedArgs.progress.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Instructor\AssignmentGradingController::question
 * @see app/Http/Controllers/Instructor/AssignmentGradingController.php:152
 * @route '/instructor/assignments/{assignment}/grade/{progress}/question'
 */
question.post = (args: { assignment: number | { id: number }, progress: string | number | { id: string | number } } | [assignment: number | { id: number }, progress: string | number | { id: string | number } ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: question.url(args, options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\Instructor\AssignmentGradingController::question
 * @see app/Http/Controllers/Instructor/AssignmentGradingController.php:152
 * @route '/instructor/assignments/{assignment}/grade/{progress}/question'
 */
    const questionForm = (args: { assignment: number | { id: number }, progress: string | number | { id: string | number } } | [assignment: number | { id: number }, progress: string | number | { id: string | number } ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: question.url(args, options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\Instructor\AssignmentGradingController::question
 * @see app/Http/Controllers/Instructor/AssignmentGradingController.php:152
 * @route '/instructor/assignments/{assignment}/grade/{progress}/question'
 */
        questionForm.post = (args: { assignment: number | { id: number }, progress: string | number | { id: string | number } } | [assignment: number | { id: number }, progress: string | number | { id: string | number } ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: question.url(args, options),
            method: 'post',
        })
    
    question.form = questionForm
/**
* @see \App\Http\Controllers\Instructor\AssignmentGradingController::submit
 * @see app/Http/Controllers/Instructor/AssignmentGradingController.php:180
 * @route '/instructor/assignments/{assignment}/grade/{progress}/submit'
 */
export const submit = (args: { assignment: number | { id: number }, progress: string | number | { id: string | number } } | [assignment: number | { id: number }, progress: string | number | { id: string | number } ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: submit.url(args, options),
    method: 'post',
})

submit.definition = {
    methods: ["post"],
    url: '/instructor/assignments/{assignment}/grade/{progress}/submit',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Instructor\AssignmentGradingController::submit
 * @see app/Http/Controllers/Instructor/AssignmentGradingController.php:180
 * @route '/instructor/assignments/{assignment}/grade/{progress}/submit'
 */
submit.url = (args: { assignment: number | { id: number }, progress: string | number | { id: string | number } } | [assignment: number | { id: number }, progress: string | number | { id: string | number } ], options?: RouteQueryOptions) => {
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
                                progress: typeof args.progress === 'object'
                ? args.progress.id
                : args.progress,
                }

    return submit.definition.url
            .replace('{assignment}', parsedArgs.assignment.toString())
            .replace('{progress}', parsedArgs.progress.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Instructor\AssignmentGradingController::submit
 * @see app/Http/Controllers/Instructor/AssignmentGradingController.php:180
 * @route '/instructor/assignments/{assignment}/grade/{progress}/submit'
 */
submit.post = (args: { assignment: number | { id: number }, progress: string | number | { id: string | number } } | [assignment: number | { id: number }, progress: string | number | { id: string | number } ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: submit.url(args, options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\Instructor\AssignmentGradingController::submit
 * @see app/Http/Controllers/Instructor/AssignmentGradingController.php:180
 * @route '/instructor/assignments/{assignment}/grade/{progress}/submit'
 */
    const submitForm = (args: { assignment: number | { id: number }, progress: string | number | { id: string | number } } | [assignment: number | { id: number }, progress: string | number | { id: string | number } ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: submit.url(args, options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\Instructor\AssignmentGradingController::submit
 * @see app/Http/Controllers/Instructor/AssignmentGradingController.php:180
 * @route '/instructor/assignments/{assignment}/grade/{progress}/submit'
 */
        submitForm.post = (args: { assignment: number | { id: number }, progress: string | number | { id: string | number } } | [assignment: number | { id: number }, progress: string | number | { id: string | number } ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: submit.url(args, options),
            method: 'post',
        })
    
    submit.form = submitForm
const grade = {
    question: Object.assign(question, question),
submit: Object.assign(submit, submit),
}

export default grade