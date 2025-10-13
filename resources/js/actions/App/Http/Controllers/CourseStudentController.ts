import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\CourseStudentController::index
 * @see app/Http/Controllers/CourseStudentController.php:27
 * @route '/courses/{course}/manage-students'
 */
export const index = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(args, options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/courses/{course}/manage-students',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\CourseStudentController::index
 * @see app/Http/Controllers/CourseStudentController.php:27
 * @route '/courses/{course}/manage-students'
 */
index.url = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return index.definition.url
            .replace('{course}', parsedArgs.course.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\CourseStudentController::index
 * @see app/Http/Controllers/CourseStudentController.php:27
 * @route '/courses/{course}/manage-students'
 */
index.get = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\CourseStudentController::index
 * @see app/Http/Controllers/CourseStudentController.php:27
 * @route '/courses/{course}/manage-students'
 */
index.head = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\CourseStudentController::index
 * @see app/Http/Controllers/CourseStudentController.php:27
 * @route '/courses/{course}/manage-students'
 */
    const indexForm = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: index.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\CourseStudentController::index
 * @see app/Http/Controllers/CourseStudentController.php:27
 * @route '/courses/{course}/manage-students'
 */
        indexForm.get = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: index.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\CourseStudentController::index
 * @see app/Http/Controllers/CourseStudentController.php:27
 * @route '/courses/{course}/manage-students'
 */
        indexForm.head = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: index.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    index.form = indexForm
/**
* @see \App\Http\Controllers\CourseStudentController::enrollStudents
 * @see app/Http/Controllers/CourseStudentController.php:98
 * @route '/courses/{course}/enroll-students'
 */
export const enrollStudents = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: enrollStudents.url(args, options),
    method: 'post',
})

enrollStudents.definition = {
    methods: ["post"],
    url: '/courses/{course}/enroll-students',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\CourseStudentController::enrollStudents
 * @see app/Http/Controllers/CourseStudentController.php:98
 * @route '/courses/{course}/enroll-students'
 */
enrollStudents.url = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return enrollStudents.definition.url
            .replace('{course}', parsedArgs.course.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\CourseStudentController::enrollStudents
 * @see app/Http/Controllers/CourseStudentController.php:98
 * @route '/courses/{course}/enroll-students'
 */
enrollStudents.post = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: enrollStudents.url(args, options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\CourseStudentController::enrollStudents
 * @see app/Http/Controllers/CourseStudentController.php:98
 * @route '/courses/{course}/enroll-students'
 */
    const enrollStudentsForm = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: enrollStudents.url(args, options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\CourseStudentController::enrollStudents
 * @see app/Http/Controllers/CourseStudentController.php:98
 * @route '/courses/{course}/enroll-students'
 */
        enrollStudentsForm.post = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: enrollStudents.url(args, options),
            method: 'post',
        })
    
    enrollStudents.form = enrollStudentsForm
/**
* @see \App\Http\Controllers\CourseStudentController::removeStudents
 * @see app/Http/Controllers/CourseStudentController.php:186
 * @route '/courses/{course}/remove-students'
 */
export const removeStudents = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: removeStudents.url(args, options),
    method: 'post',
})

removeStudents.definition = {
    methods: ["post"],
    url: '/courses/{course}/remove-students',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\CourseStudentController::removeStudents
 * @see app/Http/Controllers/CourseStudentController.php:186
 * @route '/courses/{course}/remove-students'
 */
removeStudents.url = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return removeStudents.definition.url
            .replace('{course}', parsedArgs.course.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\CourseStudentController::removeStudents
 * @see app/Http/Controllers/CourseStudentController.php:186
 * @route '/courses/{course}/remove-students'
 */
removeStudents.post = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: removeStudents.url(args, options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\CourseStudentController::removeStudents
 * @see app/Http/Controllers/CourseStudentController.php:186
 * @route '/courses/{course}/remove-students'
 */
    const removeStudentsForm = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: removeStudents.url(args, options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\CourseStudentController::removeStudents
 * @see app/Http/Controllers/CourseStudentController.php:186
 * @route '/courses/{course}/remove-students'
 */
        removeStudentsForm.post = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: removeStudents.url(args, options),
            method: 'post',
        })
    
    removeStudents.form = removeStudentsForm
/**
* @see \App\Http\Controllers\CourseStudentController::getEligibleStudents
 * @see app/Http/Controllers/CourseStudentController.php:254
 * @route '/courses/{course}/eligible-students'
 */
export const getEligibleStudents = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getEligibleStudents.url(args, options),
    method: 'get',
})

getEligibleStudents.definition = {
    methods: ["get","head"],
    url: '/courses/{course}/eligible-students',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\CourseStudentController::getEligibleStudents
 * @see app/Http/Controllers/CourseStudentController.php:254
 * @route '/courses/{course}/eligible-students'
 */
getEligibleStudents.url = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return getEligibleStudents.definition.url
            .replace('{course}', parsedArgs.course.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\CourseStudentController::getEligibleStudents
 * @see app/Http/Controllers/CourseStudentController.php:254
 * @route '/courses/{course}/eligible-students'
 */
getEligibleStudents.get = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getEligibleStudents.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\CourseStudentController::getEligibleStudents
 * @see app/Http/Controllers/CourseStudentController.php:254
 * @route '/courses/{course}/eligible-students'
 */
getEligibleStudents.head = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getEligibleStudents.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\CourseStudentController::getEligibleStudents
 * @see app/Http/Controllers/CourseStudentController.php:254
 * @route '/courses/{course}/eligible-students'
 */
    const getEligibleStudentsForm = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: getEligibleStudents.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\CourseStudentController::getEligibleStudents
 * @see app/Http/Controllers/CourseStudentController.php:254
 * @route '/courses/{course}/eligible-students'
 */
        getEligibleStudentsForm.get = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: getEligibleStudents.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\CourseStudentController::getEligibleStudents
 * @see app/Http/Controllers/CourseStudentController.php:254
 * @route '/courses/{course}/eligible-students'
 */
        getEligibleStudentsForm.head = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: getEligibleStudents.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    getEligibleStudents.form = getEligibleStudentsForm
/**
* @see \App\Http\Controllers\CourseStudentController::getEnrollmentStatistics
 * @see app/Http/Controllers/CourseStudentController.php:293
 * @route '/courses/{course}/enrollment-statistics'
 */
export const getEnrollmentStatistics = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getEnrollmentStatistics.url(args, options),
    method: 'get',
})

getEnrollmentStatistics.definition = {
    methods: ["get","head"],
    url: '/courses/{course}/enrollment-statistics',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\CourseStudentController::getEnrollmentStatistics
 * @see app/Http/Controllers/CourseStudentController.php:293
 * @route '/courses/{course}/enrollment-statistics'
 */
getEnrollmentStatistics.url = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return getEnrollmentStatistics.definition.url
            .replace('{course}', parsedArgs.course.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\CourseStudentController::getEnrollmentStatistics
 * @see app/Http/Controllers/CourseStudentController.php:293
 * @route '/courses/{course}/enrollment-statistics'
 */
getEnrollmentStatistics.get = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getEnrollmentStatistics.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\CourseStudentController::getEnrollmentStatistics
 * @see app/Http/Controllers/CourseStudentController.php:293
 * @route '/courses/{course}/enrollment-statistics'
 */
getEnrollmentStatistics.head = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getEnrollmentStatistics.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\CourseStudentController::getEnrollmentStatistics
 * @see app/Http/Controllers/CourseStudentController.php:293
 * @route '/courses/{course}/enrollment-statistics'
 */
    const getEnrollmentStatisticsForm = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: getEnrollmentStatistics.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\CourseStudentController::getEnrollmentStatistics
 * @see app/Http/Controllers/CourseStudentController.php:293
 * @route '/courses/{course}/enrollment-statistics'
 */
        getEnrollmentStatisticsForm.get = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: getEnrollmentStatistics.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\CourseStudentController::getEnrollmentStatistics
 * @see app/Http/Controllers/CourseStudentController.php:293
 * @route '/courses/{course}/enrollment-statistics'
 */
        getEnrollmentStatisticsForm.head = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: getEnrollmentStatistics.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    getEnrollmentStatistics.form = getEnrollmentStatisticsForm
/**
* @see \App\Http\Controllers\CourseStudentController::getCourseEnrollments
 * @see app/Http/Controllers/CourseStudentController.php:319
 * @route '/courses/{course}/enrollments'
 */
export const getCourseEnrollments = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getCourseEnrollments.url(args, options),
    method: 'get',
})

getCourseEnrollments.definition = {
    methods: ["get","head"],
    url: '/courses/{course}/enrollments',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\CourseStudentController::getCourseEnrollments
 * @see app/Http/Controllers/CourseStudentController.php:319
 * @route '/courses/{course}/enrollments'
 */
getCourseEnrollments.url = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return getCourseEnrollments.definition.url
            .replace('{course}', parsedArgs.course.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\CourseStudentController::getCourseEnrollments
 * @see app/Http/Controllers/CourseStudentController.php:319
 * @route '/courses/{course}/enrollments'
 */
getCourseEnrollments.get = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getCourseEnrollments.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\CourseStudentController::getCourseEnrollments
 * @see app/Http/Controllers/CourseStudentController.php:319
 * @route '/courses/{course}/enrollments'
 */
getCourseEnrollments.head = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getCourseEnrollments.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\CourseStudentController::getCourseEnrollments
 * @see app/Http/Controllers/CourseStudentController.php:319
 * @route '/courses/{course}/enrollments'
 */
    const getCourseEnrollmentsForm = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: getCourseEnrollments.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\CourseStudentController::getCourseEnrollments
 * @see app/Http/Controllers/CourseStudentController.php:319
 * @route '/courses/{course}/enrollments'
 */
        getCourseEnrollmentsForm.get = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: getCourseEnrollments.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\CourseStudentController::getCourseEnrollments
 * @see app/Http/Controllers/CourseStudentController.php:319
 * @route '/courses/{course}/enrollments'
 */
        getCourseEnrollmentsForm.head = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: getCourseEnrollments.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    getCourseEnrollments.form = getCourseEnrollmentsForm
/**
* @see \App\Http\Controllers\CourseStudentController::updateEnrollmentStatus
 * @see app/Http/Controllers/CourseStudentController.php:351
 * @route '/courses/{course}/enrollments/{studentUser}/status'
 */
export const updateEnrollmentStatus = (args: { course: number | { id: number }, studentUser: number | { id: number } } | [course: number | { id: number }, studentUser: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: updateEnrollmentStatus.url(args, options),
    method: 'put',
})

updateEnrollmentStatus.definition = {
    methods: ["put"],
    url: '/courses/{course}/enrollments/{studentUser}/status',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\CourseStudentController::updateEnrollmentStatus
 * @see app/Http/Controllers/CourseStudentController.php:351
 * @route '/courses/{course}/enrollments/{studentUser}/status'
 */
updateEnrollmentStatus.url = (args: { course: number | { id: number }, studentUser: number | { id: number } } | [course: number | { id: number }, studentUser: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
                    course: args[0],
                    studentUser: args[1],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        course: typeof args.course === 'object'
                ? args.course.id
                : args.course,
                                studentUser: typeof args.studentUser === 'object'
                ? args.studentUser.id
                : args.studentUser,
                }

    return updateEnrollmentStatus.definition.url
            .replace('{course}', parsedArgs.course.toString())
            .replace('{studentUser}', parsedArgs.studentUser.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\CourseStudentController::updateEnrollmentStatus
 * @see app/Http/Controllers/CourseStudentController.php:351
 * @route '/courses/{course}/enrollments/{studentUser}/status'
 */
updateEnrollmentStatus.put = (args: { course: number | { id: number }, studentUser: number | { id: number } } | [course: number | { id: number }, studentUser: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: updateEnrollmentStatus.url(args, options),
    method: 'put',
})

    /**
* @see \App\Http\Controllers\CourseStudentController::updateEnrollmentStatus
 * @see app/Http/Controllers/CourseStudentController.php:351
 * @route '/courses/{course}/enrollments/{studentUser}/status'
 */
    const updateEnrollmentStatusForm = (args: { course: number | { id: number }, studentUser: number | { id: number } } | [course: number | { id: number }, studentUser: number | { id: number } ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: updateEnrollmentStatus.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'PUT',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\CourseStudentController::updateEnrollmentStatus
 * @see app/Http/Controllers/CourseStudentController.php:351
 * @route '/courses/{course}/enrollments/{studentUser}/status'
 */
        updateEnrollmentStatusForm.put = (args: { course: number | { id: number }, studentUser: number | { id: number } } | [course: number | { id: number }, studentUser: number | { id: number } ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: updateEnrollmentStatus.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'PUT',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    updateEnrollmentStatus.form = updateEnrollmentStatusForm
const CourseStudentController = { index, enrollStudents, removeStudents, getEligibleStudents, getEnrollmentStatistics, getCourseEnrollments, updateEnrollmentStatus }

export default CourseStudentController