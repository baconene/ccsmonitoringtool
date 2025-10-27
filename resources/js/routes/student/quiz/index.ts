import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\Student\StudentActivityResultsController::results
 * @see app/Http/Controllers/Student/StudentActivityResultsController.php:25
 * @route '/student/quiz/{studentActivity}/results'
 */
export const results = (args: { studentActivity: number | { id: number } } | [studentActivity: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: results.url(args, options),
    method: 'get',
})

results.definition = {
    methods: ["get","head"],
    url: '/student/quiz/{studentActivity}/results',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Student\StudentActivityResultsController::results
 * @see app/Http/Controllers/Student/StudentActivityResultsController.php:25
 * @route '/student/quiz/{studentActivity}/results'
 */
results.url = (args: { studentActivity: number | { id: number } } | [studentActivity: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return results.definition.url
            .replace('{studentActivity}', parsedArgs.studentActivity.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Student\StudentActivityResultsController::results
 * @see app/Http/Controllers/Student/StudentActivityResultsController.php:25
 * @route '/student/quiz/{studentActivity}/results'
 */
results.get = (args: { studentActivity: number | { id: number } } | [studentActivity: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: results.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Student\StudentActivityResultsController::results
 * @see app/Http/Controllers/Student/StudentActivityResultsController.php:25
 * @route '/student/quiz/{studentActivity}/results'
 */
results.head = (args: { studentActivity: number | { id: number } } | [studentActivity: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: results.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\Student\StudentActivityResultsController::results
 * @see app/Http/Controllers/Student/StudentActivityResultsController.php:25
 * @route '/student/quiz/{studentActivity}/results'
 */
    const resultsForm = (args: { studentActivity: number | { id: number } } | [studentActivity: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: results.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\Student\StudentActivityResultsController::results
 * @see app/Http/Controllers/Student/StudentActivityResultsController.php:25
 * @route '/student/quiz/{studentActivity}/results'
 */
        resultsForm.get = (args: { studentActivity: number | { id: number } } | [studentActivity: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: results.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\Student\StudentActivityResultsController::results
 * @see app/Http/Controllers/Student/StudentActivityResultsController.php:25
 * @route '/student/quiz/{studentActivity}/results'
 */
        resultsForm.head = (args: { studentActivity: number | { id: number } } | [studentActivity: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: results.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    results.form = resultsForm
/**
* @see \App\Http\Controllers\Student\StudentQuizController::start
 * @see app/Http/Controllers/Student/StudentQuizController.php:35
 * @route '/student/quiz/start/{activity}'
 */
export const start = (args: { activity: string | number } | [activity: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: start.url(args, options),
    method: 'get',
})

start.definition = {
    methods: ["get","head"],
    url: '/student/quiz/start/{activity}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Student\StudentQuizController::start
 * @see app/Http/Controllers/Student/StudentQuizController.php:35
 * @route '/student/quiz/start/{activity}'
 */
start.url = (args: { activity: string | number } | [activity: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { activity: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    activity: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        activity: args.activity,
                }

    return start.definition.url
            .replace('{activity}', parsedArgs.activity.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Student\StudentQuizController::start
 * @see app/Http/Controllers/Student/StudentQuizController.php:35
 * @route '/student/quiz/start/{activity}'
 */
start.get = (args: { activity: string | number } | [activity: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: start.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Student\StudentQuizController::start
 * @see app/Http/Controllers/Student/StudentQuizController.php:35
 * @route '/student/quiz/start/{activity}'
 */
start.head = (args: { activity: string | number } | [activity: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: start.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\Student\StudentQuizController::start
 * @see app/Http/Controllers/Student/StudentQuizController.php:35
 * @route '/student/quiz/start/{activity}'
 */
    const startForm = (args: { activity: string | number } | [activity: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: start.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\Student\StudentQuizController::start
 * @see app/Http/Controllers/Student/StudentQuizController.php:35
 * @route '/student/quiz/start/{activity}'
 */
        startForm.get = (args: { activity: string | number } | [activity: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: start.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\Student\StudentQuizController::start
 * @see app/Http/Controllers/Student/StudentQuizController.php:35
 * @route '/student/quiz/start/{activity}'
 */
        startForm.head = (args: { activity: string | number } | [activity: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: start.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    start.form = startForm
/**
* @see \App\Http\Controllers\Student\StudentQuizController::answer
 * @see app/Http/Controllers/Student/StudentQuizController.php:115
 * @route '/student/quiz/{progress}/answer'
 */
export const answer = (args: { progress: string | number } | [progress: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: answer.url(args, options),
    method: 'post',
})

answer.definition = {
    methods: ["post"],
    url: '/student/quiz/{progress}/answer',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Student\StudentQuizController::answer
 * @see app/Http/Controllers/Student/StudentQuizController.php:115
 * @route '/student/quiz/{progress}/answer'
 */
answer.url = (args: { progress: string | number } | [progress: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { progress: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    progress: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        progress: args.progress,
                }

    return answer.definition.url
            .replace('{progress}', parsedArgs.progress.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Student\StudentQuizController::answer
 * @see app/Http/Controllers/Student/StudentQuizController.php:115
 * @route '/student/quiz/{progress}/answer'
 */
answer.post = (args: { progress: string | number } | [progress: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: answer.url(args, options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\Student\StudentQuizController::answer
 * @see app/Http/Controllers/Student/StudentQuizController.php:115
 * @route '/student/quiz/{progress}/answer'
 */
    const answerForm = (args: { progress: string | number } | [progress: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: answer.url(args, options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\Student\StudentQuizController::answer
 * @see app/Http/Controllers/Student/StudentQuizController.php:115
 * @route '/student/quiz/{progress}/answer'
 */
        answerForm.post = (args: { progress: string | number } | [progress: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: answer.url(args, options),
            method: 'post',
        })
    
    answer.form = answerForm
/**
* @see \App\Http\Controllers\Student\StudentQuizController::submit
 * @see app/Http/Controllers/Student/StudentQuizController.php:186
 * @route '/student/quiz/{progress}/submit'
 */
export const submit = (args: { progress: string | number } | [progress: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: submit.url(args, options),
    method: 'post',
})

submit.definition = {
    methods: ["post"],
    url: '/student/quiz/{progress}/submit',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Student\StudentQuizController::submit
 * @see app/Http/Controllers/Student/StudentQuizController.php:186
 * @route '/student/quiz/{progress}/submit'
 */
submit.url = (args: { progress: string | number } | [progress: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { progress: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    progress: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        progress: args.progress,
                }

    return submit.definition.url
            .replace('{progress}', parsedArgs.progress.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Student\StudentQuizController::submit
 * @see app/Http/Controllers/Student/StudentQuizController.php:186
 * @route '/student/quiz/{progress}/submit'
 */
submit.post = (args: { progress: string | number } | [progress: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: submit.url(args, options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\Student\StudentQuizController::submit
 * @see app/Http/Controllers/Student/StudentQuizController.php:186
 * @route '/student/quiz/{progress}/submit'
 */
    const submitForm = (args: { progress: string | number } | [progress: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: submit.url(args, options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\Student\StudentQuizController::submit
 * @see app/Http/Controllers/Student/StudentQuizController.php:186
 * @route '/student/quiz/{progress}/submit'
 */
        submitForm.post = (args: { progress: string | number } | [progress: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: submit.url(args, options),
            method: 'post',
        })
    
    submit.form = submitForm
/**
* @see \App\Http\Controllers\Student\StudentQuizController::progress
 * @see app/Http/Controllers/Student/StudentQuizController.php:312
 * @route '/student/quiz/{activity}/progress'
 */
export const progress = (args: { activity: string | number } | [activity: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: progress.url(args, options),
    method: 'get',
})

progress.definition = {
    methods: ["get","head"],
    url: '/student/quiz/{activity}/progress',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Student\StudentQuizController::progress
 * @see app/Http/Controllers/Student/StudentQuizController.php:312
 * @route '/student/quiz/{activity}/progress'
 */
progress.url = (args: { activity: string | number } | [activity: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { activity: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    activity: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        activity: args.activity,
                }

    return progress.definition.url
            .replace('{activity}', parsedArgs.activity.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Student\StudentQuizController::progress
 * @see app/Http/Controllers/Student/StudentQuizController.php:312
 * @route '/student/quiz/{activity}/progress'
 */
progress.get = (args: { activity: string | number } | [activity: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: progress.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Student\StudentQuizController::progress
 * @see app/Http/Controllers/Student/StudentQuizController.php:312
 * @route '/student/quiz/{activity}/progress'
 */
progress.head = (args: { activity: string | number } | [activity: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: progress.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\Student\StudentQuizController::progress
 * @see app/Http/Controllers/Student/StudentQuizController.php:312
 * @route '/student/quiz/{activity}/progress'
 */
    const progressForm = (args: { activity: string | number } | [activity: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: progress.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\Student\StudentQuizController::progress
 * @see app/Http/Controllers/Student/StudentQuizController.php:312
 * @route '/student/quiz/{activity}/progress'
 */
        progressForm.get = (args: { activity: string | number } | [activity: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: progress.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\Student\StudentQuizController::progress
 * @see app/Http/Controllers/Student/StudentQuizController.php:312
 * @route '/student/quiz/{activity}/progress'
 */
        progressForm.head = (args: { activity: string | number } | [activity: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: progress.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    progress.form = progressForm
const quiz = {
    results: Object.assign(results, results),
start: Object.assign(start, start),
answer: Object.assign(answer, answer),
submit: Object.assign(submit, submit),
progress: Object.assign(progress, progress),
}

export default quiz