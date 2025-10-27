import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\StudentAssignmentController::showByStudentActivity
 * @see app/Http/Controllers/StudentAssignmentController.php:23
 * @route '/student/assignments/{studentActivity}'
 */
export const showByStudentActivity = (args: { studentActivity: number | { id: number } } | [studentActivity: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: showByStudentActivity.url(args, options),
    method: 'get',
})

showByStudentActivity.definition = {
    methods: ["get","head"],
    url: '/student/assignments/{studentActivity}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\StudentAssignmentController::showByStudentActivity
 * @see app/Http/Controllers/StudentAssignmentController.php:23
 * @route '/student/assignments/{studentActivity}'
 */
showByStudentActivity.url = (args: { studentActivity: number | { id: number } } | [studentActivity: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return showByStudentActivity.definition.url
            .replace('{studentActivity}', parsedArgs.studentActivity.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\StudentAssignmentController::showByStudentActivity
 * @see app/Http/Controllers/StudentAssignmentController.php:23
 * @route '/student/assignments/{studentActivity}'
 */
showByStudentActivity.get = (args: { studentActivity: number | { id: number } } | [studentActivity: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: showByStudentActivity.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\StudentAssignmentController::showByStudentActivity
 * @see app/Http/Controllers/StudentAssignmentController.php:23
 * @route '/student/assignments/{studentActivity}'
 */
showByStudentActivity.head = (args: { studentActivity: number | { id: number } } | [studentActivity: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: showByStudentActivity.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\StudentAssignmentController::showByStudentActivity
 * @see app/Http/Controllers/StudentAssignmentController.php:23
 * @route '/student/assignments/{studentActivity}'
 */
    const showByStudentActivityForm = (args: { studentActivity: number | { id: number } } | [studentActivity: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: showByStudentActivity.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\StudentAssignmentController::showByStudentActivity
 * @see app/Http/Controllers/StudentAssignmentController.php:23
 * @route '/student/assignments/{studentActivity}'
 */
        showByStudentActivityForm.get = (args: { studentActivity: number | { id: number } } | [studentActivity: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: showByStudentActivity.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\StudentAssignmentController::showByStudentActivity
 * @see app/Http/Controllers/StudentAssignmentController.php:23
 * @route '/student/assignments/{studentActivity}'
 */
        showByStudentActivityForm.head = (args: { studentActivity: number | { id: number } } | [studentActivity: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: showByStudentActivity.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    showByStudentActivity.form = showByStudentActivityForm
/**
* @see \App\Http\Controllers\StudentAssignmentController::start
 * @see app/Http/Controllers/StudentAssignmentController.php:42
 * @route '/student/assignment/start/{activity}'
 */
export const start = (args: { activity: string | number } | [activity: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: start.url(args, options),
    method: 'get',
})

start.definition = {
    methods: ["get","head"],
    url: '/student/assignment/start/{activity}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\StudentAssignmentController::start
 * @see app/Http/Controllers/StudentAssignmentController.php:42
 * @route '/student/assignment/start/{activity}'
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
* @see \App\Http\Controllers\StudentAssignmentController::start
 * @see app/Http/Controllers/StudentAssignmentController.php:42
 * @route '/student/assignment/start/{activity}'
 */
start.get = (args: { activity: string | number } | [activity: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: start.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\StudentAssignmentController::start
 * @see app/Http/Controllers/StudentAssignmentController.php:42
 * @route '/student/assignment/start/{activity}'
 */
start.head = (args: { activity: string | number } | [activity: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: start.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\StudentAssignmentController::start
 * @see app/Http/Controllers/StudentAssignmentController.php:42
 * @route '/student/assignment/start/{activity}'
 */
    const startForm = (args: { activity: string | number } | [activity: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: start.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\StudentAssignmentController::start
 * @see app/Http/Controllers/StudentAssignmentController.php:42
 * @route '/student/assignment/start/{activity}'
 */
        startForm.get = (args: { activity: string | number } | [activity: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: start.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\StudentAssignmentController::start
 * @see app/Http/Controllers/StudentAssignmentController.php:42
 * @route '/student/assignment/start/{activity}'
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
* @see \App\Http\Controllers\StudentAssignmentController::show
 * @see app/Http/Controllers/StudentAssignmentController.php:161
 * @route '/student/assignments/{assignment}/old'
 */
export const show = (args: { assignment: number | { id: number } } | [assignment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/student/assignments/{assignment}/old',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\StudentAssignmentController::show
 * @see app/Http/Controllers/StudentAssignmentController.php:161
 * @route '/student/assignments/{assignment}/old'
 */
show.url = (args: { assignment: number | { id: number } } | [assignment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return show.definition.url
            .replace('{assignment}', parsedArgs.assignment.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\StudentAssignmentController::show
 * @see app/Http/Controllers/StudentAssignmentController.php:161
 * @route '/student/assignments/{assignment}/old'
 */
show.get = (args: { assignment: number | { id: number } } | [assignment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\StudentAssignmentController::show
 * @see app/Http/Controllers/StudentAssignmentController.php:161
 * @route '/student/assignments/{assignment}/old'
 */
show.head = (args: { assignment: number | { id: number } } | [assignment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\StudentAssignmentController::show
 * @see app/Http/Controllers/StudentAssignmentController.php:161
 * @route '/student/assignments/{assignment}/old'
 */
    const showForm = (args: { assignment: number | { id: number } } | [assignment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: show.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\StudentAssignmentController::show
 * @see app/Http/Controllers/StudentAssignmentController.php:161
 * @route '/student/assignments/{assignment}/old'
 */
        showForm.get = (args: { assignment: number | { id: number } } | [assignment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: show.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\StudentAssignmentController::show
 * @see app/Http/Controllers/StudentAssignmentController.php:161
 * @route '/student/assignments/{assignment}/old'
 */
        showForm.head = (args: { assignment: number | { id: number } } | [assignment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
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
* @see \App\Http\Controllers\StudentAssignmentController::uploadFile
 * @see app/Http/Controllers/StudentAssignmentController.php:387
 * @route '/student/assignments/{assignment}/upload'
 */
export const uploadFile = (args: { assignment: number | { id: number } } | [assignment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: uploadFile.url(args, options),
    method: 'post',
})

uploadFile.definition = {
    methods: ["post"],
    url: '/student/assignments/{assignment}/upload',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\StudentAssignmentController::uploadFile
 * @see app/Http/Controllers/StudentAssignmentController.php:387
 * @route '/student/assignments/{assignment}/upload'
 */
uploadFile.url = (args: { assignment: number | { id: number } } | [assignment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return uploadFile.definition.url
            .replace('{assignment}', parsedArgs.assignment.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\StudentAssignmentController::uploadFile
 * @see app/Http/Controllers/StudentAssignmentController.php:387
 * @route '/student/assignments/{assignment}/upload'
 */
uploadFile.post = (args: { assignment: number | { id: number } } | [assignment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: uploadFile.url(args, options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\StudentAssignmentController::uploadFile
 * @see app/Http/Controllers/StudentAssignmentController.php:387
 * @route '/student/assignments/{assignment}/upload'
 */
    const uploadFileForm = (args: { assignment: number | { id: number } } | [assignment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: uploadFile.url(args, options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\StudentAssignmentController::uploadFile
 * @see app/Http/Controllers/StudentAssignmentController.php:387
 * @route '/student/assignments/{assignment}/upload'
 */
        uploadFileForm.post = (args: { assignment: number | { id: number } } | [assignment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: uploadFile.url(args, options),
            method: 'post',
        })
    
    uploadFile.form = uploadFileForm
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
const StudentAssignmentController = { showByStudentActivity, start, show, saveAnswer, uploadFile, submit }

export default StudentAssignmentController