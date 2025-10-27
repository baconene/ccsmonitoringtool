import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Instructor\AssignmentGradingController::submissions
 * @see app/Http/Controllers/Instructor/AssignmentGradingController.php:20
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
 * @see app/Http/Controllers/Instructor/AssignmentGradingController.php:20
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
 * @see app/Http/Controllers/Instructor/AssignmentGradingController.php:20
 * @route '/instructor/assignments/{assignment}/submissions'
 */
submissions.get = (args: { assignment: number | { id: number } } | [assignment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: submissions.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Instructor\AssignmentGradingController::submissions
 * @see app/Http/Controllers/Instructor/AssignmentGradingController.php:20
 * @route '/instructor/assignments/{assignment}/submissions'
 */
submissions.head = (args: { assignment: number | { id: number } } | [assignment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: submissions.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\Instructor\AssignmentGradingController::submissions
 * @see app/Http/Controllers/Instructor/AssignmentGradingController.php:20
 * @route '/instructor/assignments/{assignment}/submissions'
 */
    const submissionsForm = (args: { assignment: number | { id: number } } | [assignment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: submissions.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\Instructor\AssignmentGradingController::submissions
 * @see app/Http/Controllers/Instructor/AssignmentGradingController.php:20
 * @route '/instructor/assignments/{assignment}/submissions'
 */
        submissionsForm.get = (args: { assignment: number | { id: number } } | [assignment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: submissions.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\Instructor\AssignmentGradingController::submissions
 * @see app/Http/Controllers/Instructor/AssignmentGradingController.php:20
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
/**
* @see \App\Http\Controllers\Instructor\AssignmentGradingController::viewSubmission
 * @see app/Http/Controllers/Instructor/AssignmentGradingController.php:118
 * @route '/instructor/assignments/{assignment}/submissions/{progress}'
 */
export const viewSubmission = (args: { assignment: number | { id: number }, progress: string | number } | [assignment: number | { id: number }, progress: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: viewSubmission.url(args, options),
    method: 'get',
})

viewSubmission.definition = {
    methods: ["get","head"],
    url: '/instructor/assignments/{assignment}/submissions/{progress}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Instructor\AssignmentGradingController::viewSubmission
 * @see app/Http/Controllers/Instructor/AssignmentGradingController.php:118
 * @route '/instructor/assignments/{assignment}/submissions/{progress}'
 */
viewSubmission.url = (args: { assignment: number | { id: number }, progress: string | number } | [assignment: number | { id: number }, progress: string | number ], options?: RouteQueryOptions) => {
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

    return viewSubmission.definition.url
            .replace('{assignment}', parsedArgs.assignment.toString())
            .replace('{progress}', parsedArgs.progress.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Instructor\AssignmentGradingController::viewSubmission
 * @see app/Http/Controllers/Instructor/AssignmentGradingController.php:118
 * @route '/instructor/assignments/{assignment}/submissions/{progress}'
 */
viewSubmission.get = (args: { assignment: number | { id: number }, progress: string | number } | [assignment: number | { id: number }, progress: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: viewSubmission.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Instructor\AssignmentGradingController::viewSubmission
 * @see app/Http/Controllers/Instructor/AssignmentGradingController.php:118
 * @route '/instructor/assignments/{assignment}/submissions/{progress}'
 */
viewSubmission.head = (args: { assignment: number | { id: number }, progress: string | number } | [assignment: number | { id: number }, progress: string | number ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: viewSubmission.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\Instructor\AssignmentGradingController::viewSubmission
 * @see app/Http/Controllers/Instructor/AssignmentGradingController.php:118
 * @route '/instructor/assignments/{assignment}/submissions/{progress}'
 */
    const viewSubmissionForm = (args: { assignment: number | { id: number }, progress: string | number } | [assignment: number | { id: number }, progress: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: viewSubmission.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\Instructor\AssignmentGradingController::viewSubmission
 * @see app/Http/Controllers/Instructor/AssignmentGradingController.php:118
 * @route '/instructor/assignments/{assignment}/submissions/{progress}'
 */
        viewSubmissionForm.get = (args: { assignment: number | { id: number }, progress: string | number } | [assignment: number | { id: number }, progress: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: viewSubmission.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\Instructor\AssignmentGradingController::viewSubmission
 * @see app/Http/Controllers/Instructor/AssignmentGradingController.php:118
 * @route '/instructor/assignments/{assignment}/submissions/{progress}'
 */
        viewSubmissionForm.head = (args: { assignment: number | { id: number }, progress: string | number } | [assignment: number | { id: number }, progress: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: viewSubmission.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    viewSubmission.form = viewSubmissionForm
/**
* @see \App\Http\Controllers\Instructor\AssignmentGradingController::gradeQuestion
 * @see app/Http/Controllers/Instructor/AssignmentGradingController.php:149
 * @route '/instructor/assignments/{assignment}/grade/{progress}/question'
 */
export const gradeQuestion = (args: { assignment: number | { id: number }, progress: string | number } | [assignment: number | { id: number }, progress: string | number ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: gradeQuestion.url(args, options),
    method: 'post',
})

gradeQuestion.definition = {
    methods: ["post"],
    url: '/instructor/assignments/{assignment}/grade/{progress}/question',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Instructor\AssignmentGradingController::gradeQuestion
 * @see app/Http/Controllers/Instructor/AssignmentGradingController.php:149
 * @route '/instructor/assignments/{assignment}/grade/{progress}/question'
 */
gradeQuestion.url = (args: { assignment: number | { id: number }, progress: string | number } | [assignment: number | { id: number }, progress: string | number ], options?: RouteQueryOptions) => {
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

    return gradeQuestion.definition.url
            .replace('{assignment}', parsedArgs.assignment.toString())
            .replace('{progress}', parsedArgs.progress.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Instructor\AssignmentGradingController::gradeQuestion
 * @see app/Http/Controllers/Instructor/AssignmentGradingController.php:149
 * @route '/instructor/assignments/{assignment}/grade/{progress}/question'
 */
gradeQuestion.post = (args: { assignment: number | { id: number }, progress: string | number } | [assignment: number | { id: number }, progress: string | number ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: gradeQuestion.url(args, options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\Instructor\AssignmentGradingController::gradeQuestion
 * @see app/Http/Controllers/Instructor/AssignmentGradingController.php:149
 * @route '/instructor/assignments/{assignment}/grade/{progress}/question'
 */
    const gradeQuestionForm = (args: { assignment: number | { id: number }, progress: string | number } | [assignment: number | { id: number }, progress: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: gradeQuestion.url(args, options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\Instructor\AssignmentGradingController::gradeQuestion
 * @see app/Http/Controllers/Instructor/AssignmentGradingController.php:149
 * @route '/instructor/assignments/{assignment}/grade/{progress}/question'
 */
        gradeQuestionForm.post = (args: { assignment: number | { id: number }, progress: string | number } | [assignment: number | { id: number }, progress: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: gradeQuestion.url(args, options),
            method: 'post',
        })
    
    gradeQuestion.form = gradeQuestionForm
/**
* @see \App\Http\Controllers\Instructor\AssignmentGradingController::submitGrade
 * @see app/Http/Controllers/Instructor/AssignmentGradingController.php:177
 * @route '/instructor/assignments/{assignment}/grade/{progress}/submit'
 */
export const submitGrade = (args: { assignment: number | { id: number }, progress: string | number } | [assignment: number | { id: number }, progress: string | number ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: submitGrade.url(args, options),
    method: 'post',
})

submitGrade.definition = {
    methods: ["post"],
    url: '/instructor/assignments/{assignment}/grade/{progress}/submit',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Instructor\AssignmentGradingController::submitGrade
 * @see app/Http/Controllers/Instructor/AssignmentGradingController.php:177
 * @route '/instructor/assignments/{assignment}/grade/{progress}/submit'
 */
submitGrade.url = (args: { assignment: number | { id: number }, progress: string | number } | [assignment: number | { id: number }, progress: string | number ], options?: RouteQueryOptions) => {
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

    return submitGrade.definition.url
            .replace('{assignment}', parsedArgs.assignment.toString())
            .replace('{progress}', parsedArgs.progress.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Instructor\AssignmentGradingController::submitGrade
 * @see app/Http/Controllers/Instructor/AssignmentGradingController.php:177
 * @route '/instructor/assignments/{assignment}/grade/{progress}/submit'
 */
submitGrade.post = (args: { assignment: number | { id: number }, progress: string | number } | [assignment: number | { id: number }, progress: string | number ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: submitGrade.url(args, options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\Instructor\AssignmentGradingController::submitGrade
 * @see app/Http/Controllers/Instructor/AssignmentGradingController.php:177
 * @route '/instructor/assignments/{assignment}/grade/{progress}/submit'
 */
    const submitGradeForm = (args: { assignment: number | { id: number }, progress: string | number } | [assignment: number | { id: number }, progress: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: submitGrade.url(args, options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\Instructor\AssignmentGradingController::submitGrade
 * @see app/Http/Controllers/Instructor/AssignmentGradingController.php:177
 * @route '/instructor/assignments/{assignment}/grade/{progress}/submit'
 */
        submitGradeForm.post = (args: { assignment: number | { id: number }, progress: string | number } | [assignment: number | { id: number }, progress: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: submitGrade.url(args, options),
            method: 'post',
        })
    
    submitGrade.form = submitGradeForm
const AssignmentGradingController = { submissions, viewSubmission, gradeQuestion, submitGrade }

export default AssignmentGradingController