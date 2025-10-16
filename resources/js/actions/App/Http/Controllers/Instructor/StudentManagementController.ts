import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Instructor\StudentManagementController::index
 * @see app/Http/Controllers/Instructor/StudentManagementController.php:22
 * @route '/student-management'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/student-management',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Instructor\StudentManagementController::index
 * @see app/Http/Controllers/Instructor/StudentManagementController.php:22
 * @route '/student-management'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Instructor\StudentManagementController::index
 * @see app/Http/Controllers/Instructor/StudentManagementController.php:22
 * @route '/student-management'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Instructor\StudentManagementController::index
 * @see app/Http/Controllers/Instructor/StudentManagementController.php:22
 * @route '/student-management'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\Instructor\StudentManagementController::index
 * @see app/Http/Controllers/Instructor/StudentManagementController.php:22
 * @route '/student-management'
 */
    const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: index.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\Instructor\StudentManagementController::index
 * @see app/Http/Controllers/Instructor/StudentManagementController.php:22
 * @route '/student-management'
 */
        indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: index.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\Instructor\StudentManagementController::index
 * @see app/Http/Controllers/Instructor/StudentManagementController.php:22
 * @route '/student-management'
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
* @see \App\Http\Controllers\Instructor\StudentManagementController::getStatistics
 * @see app/Http/Controllers/Instructor/StudentManagementController.php:376
 * @route '/student-management/statistics'
 */
export const getStatistics = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getStatistics.url(options),
    method: 'get',
})

getStatistics.definition = {
    methods: ["get","head"],
    url: '/student-management/statistics',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Instructor\StudentManagementController::getStatistics
 * @see app/Http/Controllers/Instructor/StudentManagementController.php:376
 * @route '/student-management/statistics'
 */
getStatistics.url = (options?: RouteQueryOptions) => {
    return getStatistics.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Instructor\StudentManagementController::getStatistics
 * @see app/Http/Controllers/Instructor/StudentManagementController.php:376
 * @route '/student-management/statistics'
 */
getStatistics.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getStatistics.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Instructor\StudentManagementController::getStatistics
 * @see app/Http/Controllers/Instructor/StudentManagementController.php:376
 * @route '/student-management/statistics'
 */
getStatistics.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getStatistics.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\Instructor\StudentManagementController::getStatistics
 * @see app/Http/Controllers/Instructor/StudentManagementController.php:376
 * @route '/student-management/statistics'
 */
    const getStatisticsForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: getStatistics.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\Instructor\StudentManagementController::getStatistics
 * @see app/Http/Controllers/Instructor/StudentManagementController.php:376
 * @route '/student-management/statistics'
 */
        getStatisticsForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: getStatistics.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\Instructor\StudentManagementController::getStatistics
 * @see app/Http/Controllers/Instructor/StudentManagementController.php:376
 * @route '/student-management/statistics'
 */
        getStatisticsForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: getStatistics.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    getStatistics.form = getStatisticsForm
/**
* @see \App\Http\Controllers\Instructor\StudentManagementController::getStudentsByCourse
 * @see app/Http/Controllers/Instructor/StudentManagementController.php:62
 * @route '/student-management/course/{course}/students'
 */
export const getStudentsByCourse = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getStudentsByCourse.url(args, options),
    method: 'get',
})

getStudentsByCourse.definition = {
    methods: ["get","head"],
    url: '/student-management/course/{course}/students',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Instructor\StudentManagementController::getStudentsByCourse
 * @see app/Http/Controllers/Instructor/StudentManagementController.php:62
 * @route '/student-management/course/{course}/students'
 */
getStudentsByCourse.url = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return getStudentsByCourse.definition.url
            .replace('{course}', parsedArgs.course.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Instructor\StudentManagementController::getStudentsByCourse
 * @see app/Http/Controllers/Instructor/StudentManagementController.php:62
 * @route '/student-management/course/{course}/students'
 */
getStudentsByCourse.get = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getStudentsByCourse.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Instructor\StudentManagementController::getStudentsByCourse
 * @see app/Http/Controllers/Instructor/StudentManagementController.php:62
 * @route '/student-management/course/{course}/students'
 */
getStudentsByCourse.head = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getStudentsByCourse.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\Instructor\StudentManagementController::getStudentsByCourse
 * @see app/Http/Controllers/Instructor/StudentManagementController.php:62
 * @route '/student-management/course/{course}/students'
 */
    const getStudentsByCourseForm = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: getStudentsByCourse.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\Instructor\StudentManagementController::getStudentsByCourse
 * @see app/Http/Controllers/Instructor/StudentManagementController.php:62
 * @route '/student-management/course/{course}/students'
 */
        getStudentsByCourseForm.get = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: getStudentsByCourse.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\Instructor\StudentManagementController::getStudentsByCourse
 * @see app/Http/Controllers/Instructor/StudentManagementController.php:62
 * @route '/student-management/course/{course}/students'
 */
        getStudentsByCourseForm.head = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: getStudentsByCourse.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    getStudentsByCourse.form = getStudentsByCourseForm
/**
* @see \App\Http\Controllers\Instructor\StudentManagementController::getStudentActivities
 * @see app/Http/Controllers/Instructor/StudentManagementController.php:183
 * @route '/student-management/course/{course}/student/{student}/activities'
 */
export const getStudentActivities = (args: { course: number | { id: number }, student: number | { id: number } } | [course: number | { id: number }, student: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getStudentActivities.url(args, options),
    method: 'get',
})

getStudentActivities.definition = {
    methods: ["get","head"],
    url: '/student-management/course/{course}/student/{student}/activities',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Instructor\StudentManagementController::getStudentActivities
 * @see app/Http/Controllers/Instructor/StudentManagementController.php:183
 * @route '/student-management/course/{course}/student/{student}/activities'
 */
getStudentActivities.url = (args: { course: number | { id: number }, student: number | { id: number } } | [course: number | { id: number }, student: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
                    course: args[0],
                    student: args[1],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        course: typeof args.course === 'object'
                ? args.course.id
                : args.course,
                                student: typeof args.student === 'object'
                ? args.student.id
                : args.student,
                }

    return getStudentActivities.definition.url
            .replace('{course}', parsedArgs.course.toString())
            .replace('{student}', parsedArgs.student.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Instructor\StudentManagementController::getStudentActivities
 * @see app/Http/Controllers/Instructor/StudentManagementController.php:183
 * @route '/student-management/course/{course}/student/{student}/activities'
 */
getStudentActivities.get = (args: { course: number | { id: number }, student: number | { id: number } } | [course: number | { id: number }, student: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getStudentActivities.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Instructor\StudentManagementController::getStudentActivities
 * @see app/Http/Controllers/Instructor/StudentManagementController.php:183
 * @route '/student-management/course/{course}/student/{student}/activities'
 */
getStudentActivities.head = (args: { course: number | { id: number }, student: number | { id: number } } | [course: number | { id: number }, student: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getStudentActivities.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\Instructor\StudentManagementController::getStudentActivities
 * @see app/Http/Controllers/Instructor/StudentManagementController.php:183
 * @route '/student-management/course/{course}/student/{student}/activities'
 */
    const getStudentActivitiesForm = (args: { course: number | { id: number }, student: number | { id: number } } | [course: number | { id: number }, student: number | { id: number } ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: getStudentActivities.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\Instructor\StudentManagementController::getStudentActivities
 * @see app/Http/Controllers/Instructor/StudentManagementController.php:183
 * @route '/student-management/course/{course}/student/{student}/activities'
 */
        getStudentActivitiesForm.get = (args: { course: number | { id: number }, student: number | { id: number } } | [course: number | { id: number }, student: number | { id: number } ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: getStudentActivities.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\Instructor\StudentManagementController::getStudentActivities
 * @see app/Http/Controllers/Instructor/StudentManagementController.php:183
 * @route '/student-management/course/{course}/student/{student}/activities'
 */
        getStudentActivitiesForm.head = (args: { course: number | { id: number }, student: number | { id: number } } | [course: number | { id: number }, student: number | { id: number } ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: getStudentActivities.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    getStudentActivities.form = getStudentActivitiesForm
/**
* @see \App\Http\Controllers\Instructor\StudentManagementController::exportReport
 * @see app/Http/Controllers/Instructor/StudentManagementController.php:265
 * @route '/student-management/course/{course}/export'
 */
export const exportReport = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: exportReport.url(args, options),
    method: 'get',
})

exportReport.definition = {
    methods: ["get","head"],
    url: '/student-management/course/{course}/export',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Instructor\StudentManagementController::exportReport
 * @see app/Http/Controllers/Instructor/StudentManagementController.php:265
 * @route '/student-management/course/{course}/export'
 */
exportReport.url = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return exportReport.definition.url
            .replace('{course}', parsedArgs.course.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Instructor\StudentManagementController::exportReport
 * @see app/Http/Controllers/Instructor/StudentManagementController.php:265
 * @route '/student-management/course/{course}/export'
 */
exportReport.get = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: exportReport.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Instructor\StudentManagementController::exportReport
 * @see app/Http/Controllers/Instructor/StudentManagementController.php:265
 * @route '/student-management/course/{course}/export'
 */
exportReport.head = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: exportReport.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\Instructor\StudentManagementController::exportReport
 * @see app/Http/Controllers/Instructor/StudentManagementController.php:265
 * @route '/student-management/course/{course}/export'
 */
    const exportReportForm = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: exportReport.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\Instructor\StudentManagementController::exportReport
 * @see app/Http/Controllers/Instructor/StudentManagementController.php:265
 * @route '/student-management/course/{course}/export'
 */
        exportReportForm.get = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: exportReport.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\Instructor\StudentManagementController::exportReport
 * @see app/Http/Controllers/Instructor/StudentManagementController.php:265
 * @route '/student-management/course/{course}/export'
 */
        exportReportForm.head = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: exportReport.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    exportReport.form = exportReportForm
const StudentManagementController = { index, getStatistics, getStudentsByCourse, getStudentActivities, exportReport }

export default StudentManagementController