import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
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
* @see \App\Http\Controllers\Student\StudentQuizController::submitAnswer
 * @see app/Http/Controllers/Student/StudentQuizController.php:130
 * @route '/student/quiz/{progress}/answer'
 */
export const submitAnswer = (args: { progress: string | number } | [progress: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: submitAnswer.url(args, options),
    method: 'post',
})

submitAnswer.definition = {
    methods: ["post"],
    url: '/student/quiz/{progress}/answer',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Student\StudentQuizController::submitAnswer
 * @see app/Http/Controllers/Student/StudentQuizController.php:130
 * @route '/student/quiz/{progress}/answer'
 */
submitAnswer.url = (args: { progress: string | number } | [progress: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return submitAnswer.definition.url
            .replace('{progress}', parsedArgs.progress.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Student\StudentQuizController::submitAnswer
 * @see app/Http/Controllers/Student/StudentQuizController.php:130
 * @route '/student/quiz/{progress}/answer'
 */
submitAnswer.post = (args: { progress: string | number } | [progress: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: submitAnswer.url(args, options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\Student\StudentQuizController::submitAnswer
 * @see app/Http/Controllers/Student/StudentQuizController.php:130
 * @route '/student/quiz/{progress}/answer'
 */
    const submitAnswerForm = (args: { progress: string | number } | [progress: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: submitAnswer.url(args, options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\Student\StudentQuizController::submitAnswer
 * @see app/Http/Controllers/Student/StudentQuizController.php:130
 * @route '/student/quiz/{progress}/answer'
 */
        submitAnswerForm.post = (args: { progress: string | number } | [progress: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: submitAnswer.url(args, options),
            method: 'post',
        })
    
    submitAnswer.form = submitAnswerForm
/**
* @see \App\Http\Controllers\Student\StudentQuizController::submit
 * @see app/Http/Controllers/Student/StudentQuizController.php:201
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
 * @see app/Http/Controllers/Student/StudentQuizController.php:201
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
 * @see app/Http/Controllers/Student/StudentQuizController.php:201
 * @route '/student/quiz/{progress}/submit'
 */
submit.post = (args: { progress: string | number } | [progress: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: submit.url(args, options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\Student\StudentQuizController::submit
 * @see app/Http/Controllers/Student/StudentQuizController.php:201
 * @route '/student/quiz/{progress}/submit'
 */
    const submitForm = (args: { progress: string | number } | [progress: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: submit.url(args, options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\Student\StudentQuizController::submit
 * @see app/Http/Controllers/Student/StudentQuizController.php:201
 * @route '/student/quiz/{progress}/submit'
 */
        submitForm.post = (args: { progress: string | number } | [progress: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: submit.url(args, options),
            method: 'post',
        })
    
    submit.form = submitForm
/**
* @see \App\Http\Controllers\Student\StudentQuizController::getProgress
 * @see app/Http/Controllers/Student/StudentQuizController.php:327
 * @route '/student/quiz/{activity}/progress'
 */
export const getProgress = (args: { activity: string | number } | [activity: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getProgress.url(args, options),
    method: 'get',
})

getProgress.definition = {
    methods: ["get","head"],
    url: '/student/quiz/{activity}/progress',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Student\StudentQuizController::getProgress
 * @see app/Http/Controllers/Student/StudentQuizController.php:327
 * @route '/student/quiz/{activity}/progress'
 */
getProgress.url = (args: { activity: string | number } | [activity: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return getProgress.definition.url
            .replace('{activity}', parsedArgs.activity.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Student\StudentQuizController::getProgress
 * @see app/Http/Controllers/Student/StudentQuizController.php:327
 * @route '/student/quiz/{activity}/progress'
 */
getProgress.get = (args: { activity: string | number } | [activity: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getProgress.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Student\StudentQuizController::getProgress
 * @see app/Http/Controllers/Student/StudentQuizController.php:327
 * @route '/student/quiz/{activity}/progress'
 */
getProgress.head = (args: { activity: string | number } | [activity: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getProgress.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\Student\StudentQuizController::getProgress
 * @see app/Http/Controllers/Student/StudentQuizController.php:327
 * @route '/student/quiz/{activity}/progress'
 */
    const getProgressForm = (args: { activity: string | number } | [activity: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: getProgress.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\Student\StudentQuizController::getProgress
 * @see app/Http/Controllers/Student/StudentQuizController.php:327
 * @route '/student/quiz/{activity}/progress'
 */
        getProgressForm.get = (args: { activity: string | number } | [activity: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: getProgress.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\Student\StudentQuizController::getProgress
 * @see app/Http/Controllers/Student/StudentQuizController.php:327
 * @route '/student/quiz/{activity}/progress'
 */
        getProgressForm.head = (args: { activity: string | number } | [activity: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: getProgress.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    getProgress.form = getProgressForm
const StudentQuizController = { show, start, submitAnswer, submit, getProgress }

export default StudentQuizController