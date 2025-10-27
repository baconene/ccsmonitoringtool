import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Student\StudentActivityResultsController::show
 * @see app/Http/Controllers/Student/StudentActivityResultsController.php:25
 * @route '/student/activities/{studentActivity}/results'
 */
const showbaca534d3d58558b2ff765c764754a9c = (args: { studentActivity: number | { id: number } } | [studentActivity: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: showbaca534d3d58558b2ff765c764754a9c.url(args, options),
    method: 'get',
})

showbaca534d3d58558b2ff765c764754a9c.definition = {
    methods: ["get","head"],
    url: '/student/activities/{studentActivity}/results',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Student\StudentActivityResultsController::show
 * @see app/Http/Controllers/Student/StudentActivityResultsController.php:25
 * @route '/student/activities/{studentActivity}/results'
 */
showbaca534d3d58558b2ff765c764754a9c.url = (args: { studentActivity: number | { id: number } } | [studentActivity: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return showbaca534d3d58558b2ff765c764754a9c.definition.url
            .replace('{studentActivity}', parsedArgs.studentActivity.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Student\StudentActivityResultsController::show
 * @see app/Http/Controllers/Student/StudentActivityResultsController.php:25
 * @route '/student/activities/{studentActivity}/results'
 */
showbaca534d3d58558b2ff765c764754a9c.get = (args: { studentActivity: number | { id: number } } | [studentActivity: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: showbaca534d3d58558b2ff765c764754a9c.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Student\StudentActivityResultsController::show
 * @see app/Http/Controllers/Student/StudentActivityResultsController.php:25
 * @route '/student/activities/{studentActivity}/results'
 */
showbaca534d3d58558b2ff765c764754a9c.head = (args: { studentActivity: number | { id: number } } | [studentActivity: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: showbaca534d3d58558b2ff765c764754a9c.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\Student\StudentActivityResultsController::show
 * @see app/Http/Controllers/Student/StudentActivityResultsController.php:25
 * @route '/student/activities/{studentActivity}/results'
 */
    const showbaca534d3d58558b2ff765c764754a9cForm = (args: { studentActivity: number | { id: number } } | [studentActivity: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: showbaca534d3d58558b2ff765c764754a9c.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\Student\StudentActivityResultsController::show
 * @see app/Http/Controllers/Student/StudentActivityResultsController.php:25
 * @route '/student/activities/{studentActivity}/results'
 */
        showbaca534d3d58558b2ff765c764754a9cForm.get = (args: { studentActivity: number | { id: number } } | [studentActivity: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: showbaca534d3d58558b2ff765c764754a9c.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\Student\StudentActivityResultsController::show
 * @see app/Http/Controllers/Student/StudentActivityResultsController.php:25
 * @route '/student/activities/{studentActivity}/results'
 */
        showbaca534d3d58558b2ff765c764754a9cForm.head = (args: { studentActivity: number | { id: number } } | [studentActivity: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: showbaca534d3d58558b2ff765c764754a9c.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    showbaca534d3d58558b2ff765c764754a9c.form = showbaca534d3d58558b2ff765c764754a9cForm
    /**
* @see \App\Http\Controllers\Student\StudentActivityResultsController::show
 * @see app/Http/Controllers/Student/StudentActivityResultsController.php:25
 * @route '/student/quiz/{studentActivity}/results'
 */
const show73821bd0393b4e11be59167d35b3a9e3 = (args: { studentActivity: number | { id: number } } | [studentActivity: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show73821bd0393b4e11be59167d35b3a9e3.url(args, options),
    method: 'get',
})

show73821bd0393b4e11be59167d35b3a9e3.definition = {
    methods: ["get","head"],
    url: '/student/quiz/{studentActivity}/results',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Student\StudentActivityResultsController::show
 * @see app/Http/Controllers/Student/StudentActivityResultsController.php:25
 * @route '/student/quiz/{studentActivity}/results'
 */
show73821bd0393b4e11be59167d35b3a9e3.url = (args: { studentActivity: number | { id: number } } | [studentActivity: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return show73821bd0393b4e11be59167d35b3a9e3.definition.url
            .replace('{studentActivity}', parsedArgs.studentActivity.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Student\StudentActivityResultsController::show
 * @see app/Http/Controllers/Student/StudentActivityResultsController.php:25
 * @route '/student/quiz/{studentActivity}/results'
 */
show73821bd0393b4e11be59167d35b3a9e3.get = (args: { studentActivity: number | { id: number } } | [studentActivity: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show73821bd0393b4e11be59167d35b3a9e3.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Student\StudentActivityResultsController::show
 * @see app/Http/Controllers/Student/StudentActivityResultsController.php:25
 * @route '/student/quiz/{studentActivity}/results'
 */
show73821bd0393b4e11be59167d35b3a9e3.head = (args: { studentActivity: number | { id: number } } | [studentActivity: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show73821bd0393b4e11be59167d35b3a9e3.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\Student\StudentActivityResultsController::show
 * @see app/Http/Controllers/Student/StudentActivityResultsController.php:25
 * @route '/student/quiz/{studentActivity}/results'
 */
    const show73821bd0393b4e11be59167d35b3a9e3Form = (args: { studentActivity: number | { id: number } } | [studentActivity: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: show73821bd0393b4e11be59167d35b3a9e3.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\Student\StudentActivityResultsController::show
 * @see app/Http/Controllers/Student/StudentActivityResultsController.php:25
 * @route '/student/quiz/{studentActivity}/results'
 */
        show73821bd0393b4e11be59167d35b3a9e3Form.get = (args: { studentActivity: number | { id: number } } | [studentActivity: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: show73821bd0393b4e11be59167d35b3a9e3.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\Student\StudentActivityResultsController::show
 * @see app/Http/Controllers/Student/StudentActivityResultsController.php:25
 * @route '/student/quiz/{studentActivity}/results'
 */
        show73821bd0393b4e11be59167d35b3a9e3Form.head = (args: { studentActivity: number | { id: number } } | [studentActivity: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: show73821bd0393b4e11be59167d35b3a9e3.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    show73821bd0393b4e11be59167d35b3a9e3.form = show73821bd0393b4e11be59167d35b3a9e3Form
    /**
* @see \App\Http\Controllers\Student\StudentActivityResultsController::show
 * @see app/Http/Controllers/Student/StudentActivityResultsController.php:25
 * @route '/student/project/{studentActivity}/results'
 */
const show96687a4eec09b8272240e2d9cc362940 = (args: { studentActivity: number | { id: number } } | [studentActivity: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show96687a4eec09b8272240e2d9cc362940.url(args, options),
    method: 'get',
})

show96687a4eec09b8272240e2d9cc362940.definition = {
    methods: ["get","head"],
    url: '/student/project/{studentActivity}/results',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Student\StudentActivityResultsController::show
 * @see app/Http/Controllers/Student/StudentActivityResultsController.php:25
 * @route '/student/project/{studentActivity}/results'
 */
show96687a4eec09b8272240e2d9cc362940.url = (args: { studentActivity: number | { id: number } } | [studentActivity: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return show96687a4eec09b8272240e2d9cc362940.definition.url
            .replace('{studentActivity}', parsedArgs.studentActivity.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Student\StudentActivityResultsController::show
 * @see app/Http/Controllers/Student/StudentActivityResultsController.php:25
 * @route '/student/project/{studentActivity}/results'
 */
show96687a4eec09b8272240e2d9cc362940.get = (args: { studentActivity: number | { id: number } } | [studentActivity: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show96687a4eec09b8272240e2d9cc362940.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Student\StudentActivityResultsController::show
 * @see app/Http/Controllers/Student/StudentActivityResultsController.php:25
 * @route '/student/project/{studentActivity}/results'
 */
show96687a4eec09b8272240e2d9cc362940.head = (args: { studentActivity: number | { id: number } } | [studentActivity: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show96687a4eec09b8272240e2d9cc362940.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\Student\StudentActivityResultsController::show
 * @see app/Http/Controllers/Student/StudentActivityResultsController.php:25
 * @route '/student/project/{studentActivity}/results'
 */
    const show96687a4eec09b8272240e2d9cc362940Form = (args: { studentActivity: number | { id: number } } | [studentActivity: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: show96687a4eec09b8272240e2d9cc362940.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\Student\StudentActivityResultsController::show
 * @see app/Http/Controllers/Student/StudentActivityResultsController.php:25
 * @route '/student/project/{studentActivity}/results'
 */
        show96687a4eec09b8272240e2d9cc362940Form.get = (args: { studentActivity: number | { id: number } } | [studentActivity: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: show96687a4eec09b8272240e2d9cc362940.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\Student\StudentActivityResultsController::show
 * @see app/Http/Controllers/Student/StudentActivityResultsController.php:25
 * @route '/student/project/{studentActivity}/results'
 */
        show96687a4eec09b8272240e2d9cc362940Form.head = (args: { studentActivity: number | { id: number } } | [studentActivity: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: show96687a4eec09b8272240e2d9cc362940.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    show96687a4eec09b8272240e2d9cc362940.form = show96687a4eec09b8272240e2d9cc362940Form
    /**
* @see \App\Http\Controllers\Student\StudentActivityResultsController::show
 * @see app/Http/Controllers/Student/StudentActivityResultsController.php:25
 * @route '/student/assessment/{studentActivity}/results'
 */
const show40b6a0a116a7037e5f3b67d6447be1a4 = (args: { studentActivity: number | { id: number } } | [studentActivity: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show40b6a0a116a7037e5f3b67d6447be1a4.url(args, options),
    method: 'get',
})

show40b6a0a116a7037e5f3b67d6447be1a4.definition = {
    methods: ["get","head"],
    url: '/student/assessment/{studentActivity}/results',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Student\StudentActivityResultsController::show
 * @see app/Http/Controllers/Student/StudentActivityResultsController.php:25
 * @route '/student/assessment/{studentActivity}/results'
 */
show40b6a0a116a7037e5f3b67d6447be1a4.url = (args: { studentActivity: number | { id: number } } | [studentActivity: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return show40b6a0a116a7037e5f3b67d6447be1a4.definition.url
            .replace('{studentActivity}', parsedArgs.studentActivity.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Student\StudentActivityResultsController::show
 * @see app/Http/Controllers/Student/StudentActivityResultsController.php:25
 * @route '/student/assessment/{studentActivity}/results'
 */
show40b6a0a116a7037e5f3b67d6447be1a4.get = (args: { studentActivity: number | { id: number } } | [studentActivity: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show40b6a0a116a7037e5f3b67d6447be1a4.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Student\StudentActivityResultsController::show
 * @see app/Http/Controllers/Student/StudentActivityResultsController.php:25
 * @route '/student/assessment/{studentActivity}/results'
 */
show40b6a0a116a7037e5f3b67d6447be1a4.head = (args: { studentActivity: number | { id: number } } | [studentActivity: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show40b6a0a116a7037e5f3b67d6447be1a4.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\Student\StudentActivityResultsController::show
 * @see app/Http/Controllers/Student/StudentActivityResultsController.php:25
 * @route '/student/assessment/{studentActivity}/results'
 */
    const show40b6a0a116a7037e5f3b67d6447be1a4Form = (args: { studentActivity: number | { id: number } } | [studentActivity: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: show40b6a0a116a7037e5f3b67d6447be1a4.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\Student\StudentActivityResultsController::show
 * @see app/Http/Controllers/Student/StudentActivityResultsController.php:25
 * @route '/student/assessment/{studentActivity}/results'
 */
        show40b6a0a116a7037e5f3b67d6447be1a4Form.get = (args: { studentActivity: number | { id: number } } | [studentActivity: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: show40b6a0a116a7037e5f3b67d6447be1a4.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\Student\StudentActivityResultsController::show
 * @see app/Http/Controllers/Student/StudentActivityResultsController.php:25
 * @route '/student/assessment/{studentActivity}/results'
 */
        show40b6a0a116a7037e5f3b67d6447be1a4Form.head = (args: { studentActivity: number | { id: number } } | [studentActivity: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: show40b6a0a116a7037e5f3b67d6447be1a4.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    show40b6a0a116a7037e5f3b67d6447be1a4.form = show40b6a0a116a7037e5f3b67d6447be1a4Form

export const show = {
    '/student/activities/{studentActivity}/results': showbaca534d3d58558b2ff765c764754a9c,
    '/student/quiz/{studentActivity}/results': show73821bd0393b4e11be59167d35b3a9e3,
    '/student/project/{studentActivity}/results': show96687a4eec09b8272240e2d9cc362940,
    '/student/assessment/{studentActivity}/results': show40b6a0a116a7037e5f3b67d6447be1a4,
}

const StudentActivityResultsController = { show }

export default StudentActivityResultsController