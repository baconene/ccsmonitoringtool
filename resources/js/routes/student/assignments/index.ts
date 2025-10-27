import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../wayfinder'
import showBac614 from './show'
/**
* @see \App\Http\Controllers\StudentAssignmentController::show
 * @see app/Http/Controllers/StudentAssignmentController.php:23
 * @route '/student/assignments/{studentActivity}'
 */
export const show = (args: { studentActivity: number | { id: number } } | [studentActivity: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/student/assignments/{studentActivity}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\StudentAssignmentController::show
 * @see app/Http/Controllers/StudentAssignmentController.php:23
 * @route '/student/assignments/{studentActivity}'
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
* @see \App\Http\Controllers\StudentAssignmentController::show
 * @see app/Http/Controllers/StudentAssignmentController.php:23
 * @route '/student/assignments/{studentActivity}'
 */
show.get = (args: { studentActivity: number | { id: number } } | [studentActivity: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\StudentAssignmentController::show
 * @see app/Http/Controllers/StudentAssignmentController.php:23
 * @route '/student/assignments/{studentActivity}'
 */
show.head = (args: { studentActivity: number | { id: number } } | [studentActivity: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\StudentAssignmentController::show
 * @see app/Http/Controllers/StudentAssignmentController.php:23
 * @route '/student/assignments/{studentActivity}'
 */
    const showForm = (args: { studentActivity: number | { id: number } } | [studentActivity: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: show.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\StudentAssignmentController::show
 * @see app/Http/Controllers/StudentAssignmentController.php:23
 * @route '/student/assignments/{studentActivity}'
 */
        showForm.get = (args: { studentActivity: number | { id: number } } | [studentActivity: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: show.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\StudentAssignmentController::show
 * @see app/Http/Controllers/StudentAssignmentController.php:23
 * @route '/student/assignments/{studentActivity}'
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
* @see \App\Http\Controllers\StudentAssignmentController::saveAnswer
 * @see app/Http/Controllers/StudentAssignmentController.php:262
 * @route '/student/assignments/{assignment}/answers'
 */
export const saveAnswer = (args: { assignment: number | { id: number } } | [assignment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: saveAnswer.url(args, options),
    method: 'post',
})

saveAnswer.definition = {
    methods: ["post"],
    url: '/student/assignments/{assignment}/answers',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\StudentAssignmentController::saveAnswer
 * @see app/Http/Controllers/StudentAssignmentController.php:262
 * @route '/student/assignments/{assignment}/answers'
 */
saveAnswer.url = (args: { assignment: number | { id: number } } | [assignment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return saveAnswer.definition.url
            .replace('{assignment}', parsedArgs.assignment.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\StudentAssignmentController::saveAnswer
 * @see app/Http/Controllers/StudentAssignmentController.php:262
 * @route '/student/assignments/{assignment}/answers'
 */
saveAnswer.post = (args: { assignment: number | { id: number } } | [assignment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: saveAnswer.url(args, options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\StudentAssignmentController::saveAnswer
 * @see app/Http/Controllers/StudentAssignmentController.php:262
 * @route '/student/assignments/{assignment}/answers'
 */
    const saveAnswerForm = (args: { assignment: number | { id: number } } | [assignment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: saveAnswer.url(args, options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\StudentAssignmentController::saveAnswer
 * @see app/Http/Controllers/StudentAssignmentController.php:262
 * @route '/student/assignments/{assignment}/answers'
 */
        saveAnswerForm.post = (args: { assignment: number | { id: number } } | [assignment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: saveAnswer.url(args, options),
            method: 'post',
        })
    
    saveAnswer.form = saveAnswerForm
/**
* @see \App\Http\Controllers\StudentAssignmentController::upload
 * @see app/Http/Controllers/StudentAssignmentController.php:387
 * @route '/student/assignments/{assignment}/upload'
 */
export const upload = (args: { assignment: number | { id: number } } | [assignment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: upload.url(args, options),
    method: 'post',
})

upload.definition = {
    methods: ["post"],
    url: '/student/assignments/{assignment}/upload',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\StudentAssignmentController::upload
 * @see app/Http/Controllers/StudentAssignmentController.php:387
 * @route '/student/assignments/{assignment}/upload'
 */
upload.url = (args: { assignment: number | { id: number } } | [assignment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return upload.definition.url
            .replace('{assignment}', parsedArgs.assignment.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\StudentAssignmentController::upload
 * @see app/Http/Controllers/StudentAssignmentController.php:387
 * @route '/student/assignments/{assignment}/upload'
 */
upload.post = (args: { assignment: number | { id: number } } | [assignment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: upload.url(args, options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\StudentAssignmentController::upload
 * @see app/Http/Controllers/StudentAssignmentController.php:387
 * @route '/student/assignments/{assignment}/upload'
 */
    const uploadForm = (args: { assignment: number | { id: number } } | [assignment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: upload.url(args, options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\StudentAssignmentController::upload
 * @see app/Http/Controllers/StudentAssignmentController.php:387
 * @route '/student/assignments/{assignment}/upload'
 */
        uploadForm.post = (args: { assignment: number | { id: number } } | [assignment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: upload.url(args, options),
            method: 'post',
        })
    
    upload.form = uploadForm
/**
* @see \App\Http\Controllers\StudentAssignmentController::submit
 * @see app/Http/Controllers/StudentAssignmentController.php:444
 * @route '/student/assignments/{assignment}/submit'
 */
export const submit = (args: { assignment: number | { id: number } } | [assignment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: submit.url(args, options),
    method: 'post',
})

submit.definition = {
    methods: ["post"],
    url: '/student/assignments/{assignment}/submit',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\StudentAssignmentController::submit
 * @see app/Http/Controllers/StudentAssignmentController.php:444
 * @route '/student/assignments/{assignment}/submit'
 */
submit.url = (args: { assignment: number | { id: number } } | [assignment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return submit.definition.url
            .replace('{assignment}', parsedArgs.assignment.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\StudentAssignmentController::submit
 * @see app/Http/Controllers/StudentAssignmentController.php:444
 * @route '/student/assignments/{assignment}/submit'
 */
submit.post = (args: { assignment: number | { id: number } } | [assignment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: submit.url(args, options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\StudentAssignmentController::submit
 * @see app/Http/Controllers/StudentAssignmentController.php:444
 * @route '/student/assignments/{assignment}/submit'
 */
    const submitForm = (args: { assignment: number | { id: number } } | [assignment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: submit.url(args, options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\StudentAssignmentController::submit
 * @see app/Http/Controllers/StudentAssignmentController.php:444
 * @route '/student/assignments/{assignment}/submit'
 */
        submitForm.post = (args: { assignment: number | { id: number } } | [assignment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: submit.url(args, options),
            method: 'post',
        })
    
    submit.form = submitForm
const assignments = {
    show: Object.assign(show, showBac614),
saveAnswer: Object.assign(saveAnswer, saveAnswer),
upload: Object.assign(upload, upload),
submit: Object.assign(submit, submit),
}

export default assignments