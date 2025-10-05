import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Student\StudentCourseController::index
 * @see app/Http/Controllers/Student/StudentCourseController.php:18
 * @route '/student/courses'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/student/courses',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Student\StudentCourseController::index
 * @see app/Http/Controllers/Student/StudentCourseController.php:18
 * @route '/student/courses'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Student\StudentCourseController::index
 * @see app/Http/Controllers/Student/StudentCourseController.php:18
 * @route '/student/courses'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Student\StudentCourseController::index
 * @see app/Http/Controllers/Student/StudentCourseController.php:18
 * @route '/student/courses'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\Student\StudentCourseController::index
 * @see app/Http/Controllers/Student/StudentCourseController.php:18
 * @route '/student/courses'
 */
    const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: index.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\Student\StudentCourseController::index
 * @see app/Http/Controllers/Student/StudentCourseController.php:18
 * @route '/student/courses'
 */
        indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: index.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\Student\StudentCourseController::index
 * @see app/Http/Controllers/Student/StudentCourseController.php:18
 * @route '/student/courses'
 */
        indexForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: index.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    index.form = indexForm
/**
* @see \App\Http\Controllers\Student\StudentCourseController::show
 * @see app/Http/Controllers/Student/StudentCourseController.php:60
 * @route '/student/courses/{course}'
 */
export const show = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/student/courses/{course}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Student\StudentCourseController::show
 * @see app/Http/Controllers/Student/StudentCourseController.php:60
 * @route '/student/courses/{course}'
 */
show.url = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { course: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { course: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    course: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        course: typeof args.course === 'object'
                ? args.course.id
                : args.course,
                }

    return show.definition.url
            .replace('{course}', parsedArgs.course.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Student\StudentCourseController::show
 * @see app/Http/Controllers/Student/StudentCourseController.php:60
 * @route '/student/courses/{course}'
 */
show.get = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Student\StudentCourseController::show
 * @see app/Http/Controllers/Student/StudentCourseController.php:60
 * @route '/student/courses/{course}'
 */
show.head = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\Student\StudentCourseController::show
 * @see app/Http/Controllers/Student/StudentCourseController.php:60
 * @route '/student/courses/{course}'
 */
    const showForm = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: show.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\Student\StudentCourseController::show
 * @see app/Http/Controllers/Student/StudentCourseController.php:60
 * @route '/student/courses/{course}'
 */
        showForm.get = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: show.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\Student\StudentCourseController::show
 * @see app/Http/Controllers/Student/StudentCourseController.php:60
 * @route '/student/courses/{course}'
 */
        showForm.head = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
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
* @see \App\Http\Controllers\Student\StudentCourseController::completeLesson
 * @see app/Http/Controllers/Student/StudentCourseController.php:120
 * @route '/student/courses/{course}/lessons/{lessonId}/complete'
 */
export const completeLesson = (args: { course: number | { id: number }, lessonId: string | number } | [course: number | { id: number }, lessonId: string | number ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: completeLesson.url(args, options),
    method: 'post',
})

completeLesson.definition = {
    methods: ["post"],
    url: '/student/courses/{course}/lessons/{lessonId}/complete',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Student\StudentCourseController::completeLesson
 * @see app/Http/Controllers/Student/StudentCourseController.php:120
 * @route '/student/courses/{course}/lessons/{lessonId}/complete'
 */
completeLesson.url = (args: { course: number | { id: number }, lessonId: string | number } | [course: number | { id: number }, lessonId: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
                    course: args[0],
                    lessonId: args[1],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        course: typeof args.course === 'object'
                ? args.course.id
                : args.course,
                                lessonId: args.lessonId,
                }

    return completeLesson.definition.url
            .replace('{course}', parsedArgs.course.toString())
            .replace('{lessonId}', parsedArgs.lessonId.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Student\StudentCourseController::completeLesson
 * @see app/Http/Controllers/Student/StudentCourseController.php:120
 * @route '/student/courses/{course}/lessons/{lessonId}/complete'
 */
completeLesson.post = (args: { course: number | { id: number }, lessonId: string | number } | [course: number | { id: number }, lessonId: string | number ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: completeLesson.url(args, options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\Student\StudentCourseController::completeLesson
 * @see app/Http/Controllers/Student/StudentCourseController.php:120
 * @route '/student/courses/{course}/lessons/{lessonId}/complete'
 */
    const completeLessonForm = (args: { course: number | { id: number }, lessonId: string | number } | [course: number | { id: number }, lessonId: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: completeLesson.url(args, options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\Student\StudentCourseController::completeLesson
 * @see app/Http/Controllers/Student/StudentCourseController.php:120
 * @route '/student/courses/{course}/lessons/{lessonId}/complete'
 */
        completeLessonForm.post = (args: { course: number | { id: number }, lessonId: string | number } | [course: number | { id: number }, lessonId: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: completeLesson.url(args, options),
            method: 'post',
        })
    
    completeLesson.form = completeLessonForm
/**
* @see \App\Http\Controllers\Student\StudentCourseController::getLessons
 * @see app/Http/Controllers/Student/StudentCourseController.php:173
 * @route '/student/courses/{course}/lessons'
 */
export const getLessons = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getLessons.url(args, options),
    method: 'get',
})

getLessons.definition = {
    methods: ["get","head"],
    url: '/student/courses/{course}/lessons',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Student\StudentCourseController::getLessons
 * @see app/Http/Controllers/Student/StudentCourseController.php:173
 * @route '/student/courses/{course}/lessons'
 */
getLessons.url = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { course: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { course: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    course: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        course: typeof args.course === 'object'
                ? args.course.id
                : args.course,
                }

    return getLessons.definition.url
            .replace('{course}', parsedArgs.course.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Student\StudentCourseController::getLessons
 * @see app/Http/Controllers/Student/StudentCourseController.php:173
 * @route '/student/courses/{course}/lessons'
 */
getLessons.get = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getLessons.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Student\StudentCourseController::getLessons
 * @see app/Http/Controllers/Student/StudentCourseController.php:173
 * @route '/student/courses/{course}/lessons'
 */
getLessons.head = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getLessons.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\Student\StudentCourseController::getLessons
 * @see app/Http/Controllers/Student/StudentCourseController.php:173
 * @route '/student/courses/{course}/lessons'
 */
    const getLessonsForm = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: getLessons.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\Student\StudentCourseController::getLessons
 * @see app/Http/Controllers/Student/StudentCourseController.php:173
 * @route '/student/courses/{course}/lessons'
 */
        getLessonsForm.get = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: getLessons.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\Student\StudentCourseController::getLessons
 * @see app/Http/Controllers/Student/StudentCourseController.php:173
 * @route '/student/courses/{course}/lessons'
 */
        getLessonsForm.head = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: getLessons.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    getLessons.form = getLessonsForm
const StudentCourseController = { index, show, completeLesson, getLessons }

export default StudentCourseController