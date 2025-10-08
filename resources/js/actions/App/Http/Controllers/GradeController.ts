import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\GradeController::studentReport
 * @see app/Http/Controllers/GradeController.php:28
 * @route '/student/report'
 */
export const studentReport = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: studentReport.url(options),
    method: 'get',
})

studentReport.definition = {
    methods: ["get","head"],
    url: '/student/report',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\GradeController::studentReport
 * @see app/Http/Controllers/GradeController.php:28
 * @route '/student/report'
 */
studentReport.url = (options?: RouteQueryOptions) => {
    return studentReport.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\GradeController::studentReport
 * @see app/Http/Controllers/GradeController.php:28
 * @route '/student/report'
 */
studentReport.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: studentReport.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\GradeController::studentReport
 * @see app/Http/Controllers/GradeController.php:28
 * @route '/student/report'
 */
studentReport.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: studentReport.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\GradeController::studentReport
 * @see app/Http/Controllers/GradeController.php:28
 * @route '/student/report'
 */
    const studentReportForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: studentReport.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\GradeController::studentReport
 * @see app/Http/Controllers/GradeController.php:28
 * @route '/student/report'
 */
        studentReportForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: studentReport.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\GradeController::studentReport
 * @see app/Http/Controllers/GradeController.php:28
 * @route '/student/report'
 */
        studentReportForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: studentReport.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    studentReport.form = studentReportForm
/**
* @see \App\Http\Controllers\GradeController::studentCourseReport
 * @see app/Http/Controllers/GradeController.php:81
 * @route '/student/{student}/report/course/{course}'
 */
export const studentCourseReport = (args: { student: string | number, course: string | number } | [student: string | number, course: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: studentCourseReport.url(args, options),
    method: 'get',
})

studentCourseReport.definition = {
    methods: ["get","head"],
    url: '/student/{student}/report/course/{course}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\GradeController::studentCourseReport
 * @see app/Http/Controllers/GradeController.php:81
 * @route '/student/{student}/report/course/{course}'
 */
studentCourseReport.url = (args: { student: string | number, course: string | number } | [student: string | number, course: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
                    student: args[0],
                    course: args[1],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        student: args.student,
                                course: args.course,
                }

    return studentCourseReport.definition.url
            .replace('{student}', parsedArgs.student.toString())
            .replace('{course}', parsedArgs.course.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\GradeController::studentCourseReport
 * @see app/Http/Controllers/GradeController.php:81
 * @route '/student/{student}/report/course/{course}'
 */
studentCourseReport.get = (args: { student: string | number, course: string | number } | [student: string | number, course: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: studentCourseReport.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\GradeController::studentCourseReport
 * @see app/Http/Controllers/GradeController.php:81
 * @route '/student/{student}/report/course/{course}'
 */
studentCourseReport.head = (args: { student: string | number, course: string | number } | [student: string | number, course: string | number ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: studentCourseReport.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\GradeController::studentCourseReport
 * @see app/Http/Controllers/GradeController.php:81
 * @route '/student/{student}/report/course/{course}'
 */
    const studentCourseReportForm = (args: { student: string | number, course: string | number } | [student: string | number, course: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: studentCourseReport.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\GradeController::studentCourseReport
 * @see app/Http/Controllers/GradeController.php:81
 * @route '/student/{student}/report/course/{course}'
 */
        studentCourseReportForm.get = (args: { student: string | number, course: string | number } | [student: string | number, course: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: studentCourseReport.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\GradeController::studentCourseReport
 * @see app/Http/Controllers/GradeController.php:81
 * @route '/student/{student}/report/course/{course}'
 */
        studentCourseReportForm.head = (args: { student: string | number, course: string | number } | [student: string | number, course: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: studentCourseReport.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    studentCourseReport.form = studentCourseReportForm
/**
* @see \App\Http\Controllers\GradeController::studentReportPDF
 * @see app/Http/Controllers/GradeController.php:485
 * @route '/student/report/pdf'
 */
export const studentReportPDF = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: studentReportPDF.url(options),
    method: 'get',
})

studentReportPDF.definition = {
    methods: ["get","head"],
    url: '/student/report/pdf',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\GradeController::studentReportPDF
 * @see app/Http/Controllers/GradeController.php:485
 * @route '/student/report/pdf'
 */
studentReportPDF.url = (options?: RouteQueryOptions) => {
    return studentReportPDF.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\GradeController::studentReportPDF
 * @see app/Http/Controllers/GradeController.php:485
 * @route '/student/report/pdf'
 */
studentReportPDF.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: studentReportPDF.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\GradeController::studentReportPDF
 * @see app/Http/Controllers/GradeController.php:485
 * @route '/student/report/pdf'
 */
studentReportPDF.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: studentReportPDF.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\GradeController::studentReportPDF
 * @see app/Http/Controllers/GradeController.php:485
 * @route '/student/report/pdf'
 */
    const studentReportPDFForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: studentReportPDF.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\GradeController::studentReportPDF
 * @see app/Http/Controllers/GradeController.php:485
 * @route '/student/report/pdf'
 */
        studentReportPDFForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: studentReportPDF.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\GradeController::studentReportPDF
 * @see app/Http/Controllers/GradeController.php:485
 * @route '/student/report/pdf'
 */
        studentReportPDFForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: studentReportPDF.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    studentReportPDF.form = studentReportPDFForm
/**
* @see \App\Http\Controllers\GradeController::studentCompleteReportPDF
 * @see app/Http/Controllers/GradeController.php:494
 * @route '/student/report/pdf/complete'
 */
export const studentCompleteReportPDF = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: studentCompleteReportPDF.url(options),
    method: 'get',
})

studentCompleteReportPDF.definition = {
    methods: ["get","head"],
    url: '/student/report/pdf/complete',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\GradeController::studentCompleteReportPDF
 * @see app/Http/Controllers/GradeController.php:494
 * @route '/student/report/pdf/complete'
 */
studentCompleteReportPDF.url = (options?: RouteQueryOptions) => {
    return studentCompleteReportPDF.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\GradeController::studentCompleteReportPDF
 * @see app/Http/Controllers/GradeController.php:494
 * @route '/student/report/pdf/complete'
 */
studentCompleteReportPDF.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: studentCompleteReportPDF.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\GradeController::studentCompleteReportPDF
 * @see app/Http/Controllers/GradeController.php:494
 * @route '/student/report/pdf/complete'
 */
studentCompleteReportPDF.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: studentCompleteReportPDF.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\GradeController::studentCompleteReportPDF
 * @see app/Http/Controllers/GradeController.php:494
 * @route '/student/report/pdf/complete'
 */
    const studentCompleteReportPDFForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: studentCompleteReportPDF.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\GradeController::studentCompleteReportPDF
 * @see app/Http/Controllers/GradeController.php:494
 * @route '/student/report/pdf/complete'
 */
        studentCompleteReportPDFForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: studentCompleteReportPDF.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\GradeController::studentCompleteReportPDF
 * @see app/Http/Controllers/GradeController.php:494
 * @route '/student/report/pdf/complete'
 */
        studentCompleteReportPDFForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: studentCompleteReportPDF.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    studentCompleteReportPDF.form = studentCompleteReportPDFForm
/**
* @see \App\Http\Controllers\GradeController::studentReportCSV
 * @see app/Http/Controllers/GradeController.php:502
 * @route '/student/report/csv'
 */
export const studentReportCSV = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: studentReportCSV.url(options),
    method: 'get',
})

studentReportCSV.definition = {
    methods: ["get","head"],
    url: '/student/report/csv',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\GradeController::studentReportCSV
 * @see app/Http/Controllers/GradeController.php:502
 * @route '/student/report/csv'
 */
studentReportCSV.url = (options?: RouteQueryOptions) => {
    return studentReportCSV.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\GradeController::studentReportCSV
 * @see app/Http/Controllers/GradeController.php:502
 * @route '/student/report/csv'
 */
studentReportCSV.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: studentReportCSV.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\GradeController::studentReportCSV
 * @see app/Http/Controllers/GradeController.php:502
 * @route '/student/report/csv'
 */
studentReportCSV.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: studentReportCSV.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\GradeController::studentReportCSV
 * @see app/Http/Controllers/GradeController.php:502
 * @route '/student/report/csv'
 */
    const studentReportCSVForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: studentReportCSV.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\GradeController::studentReportCSV
 * @see app/Http/Controllers/GradeController.php:502
 * @route '/student/report/csv'
 */
        studentReportCSVForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: studentReportCSV.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\GradeController::studentReportCSV
 * @see app/Http/Controllers/GradeController.php:502
 * @route '/student/report/csv'
 */
        studentReportCSVForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: studentReportCSV.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    studentReportCSV.form = studentReportCSVForm
/**
* @see \App\Http\Controllers\GradeController::studentCompleteReportCSV
 * @see app/Http/Controllers/GradeController.php:511
 * @route '/student/report/csv/complete'
 */
export const studentCompleteReportCSV = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: studentCompleteReportCSV.url(options),
    method: 'get',
})

studentCompleteReportCSV.definition = {
    methods: ["get","head"],
    url: '/student/report/csv/complete',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\GradeController::studentCompleteReportCSV
 * @see app/Http/Controllers/GradeController.php:511
 * @route '/student/report/csv/complete'
 */
studentCompleteReportCSV.url = (options?: RouteQueryOptions) => {
    return studentCompleteReportCSV.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\GradeController::studentCompleteReportCSV
 * @see app/Http/Controllers/GradeController.php:511
 * @route '/student/report/csv/complete'
 */
studentCompleteReportCSV.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: studentCompleteReportCSV.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\GradeController::studentCompleteReportCSV
 * @see app/Http/Controllers/GradeController.php:511
 * @route '/student/report/csv/complete'
 */
studentCompleteReportCSV.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: studentCompleteReportCSV.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\GradeController::studentCompleteReportCSV
 * @see app/Http/Controllers/GradeController.php:511
 * @route '/student/report/csv/complete'
 */
    const studentCompleteReportCSVForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: studentCompleteReportCSV.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\GradeController::studentCompleteReportCSV
 * @see app/Http/Controllers/GradeController.php:511
 * @route '/student/report/csv/complete'
 */
        studentCompleteReportCSVForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: studentCompleteReportCSV.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\GradeController::studentCompleteReportCSV
 * @see app/Http/Controllers/GradeController.php:511
 * @route '/student/report/csv/complete'
 */
        studentCompleteReportCSVForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: studentCompleteReportCSV.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    studentCompleteReportCSV.form = studentCompleteReportCSVForm
/**
* @see \App\Http\Controllers\GradeController::studentCourseReportPDF
 * @see app/Http/Controllers/GradeController.php:519
 * @route '/student/{student}/report/course/{course}/pdf'
 */
export const studentCourseReportPDF = (args: { student: string | number, course: string | number } | [student: string | number, course: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: studentCourseReportPDF.url(args, options),
    method: 'get',
})

studentCourseReportPDF.definition = {
    methods: ["get","head"],
    url: '/student/{student}/report/course/{course}/pdf',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\GradeController::studentCourseReportPDF
 * @see app/Http/Controllers/GradeController.php:519
 * @route '/student/{student}/report/course/{course}/pdf'
 */
studentCourseReportPDF.url = (args: { student: string | number, course: string | number } | [student: string | number, course: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
                    student: args[0],
                    course: args[1],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        student: args.student,
                                course: args.course,
                }

    return studentCourseReportPDF.definition.url
            .replace('{student}', parsedArgs.student.toString())
            .replace('{course}', parsedArgs.course.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\GradeController::studentCourseReportPDF
 * @see app/Http/Controllers/GradeController.php:519
 * @route '/student/{student}/report/course/{course}/pdf'
 */
studentCourseReportPDF.get = (args: { student: string | number, course: string | number } | [student: string | number, course: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: studentCourseReportPDF.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\GradeController::studentCourseReportPDF
 * @see app/Http/Controllers/GradeController.php:519
 * @route '/student/{student}/report/course/{course}/pdf'
 */
studentCourseReportPDF.head = (args: { student: string | number, course: string | number } | [student: string | number, course: string | number ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: studentCourseReportPDF.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\GradeController::studentCourseReportPDF
 * @see app/Http/Controllers/GradeController.php:519
 * @route '/student/{student}/report/course/{course}/pdf'
 */
    const studentCourseReportPDFForm = (args: { student: string | number, course: string | number } | [student: string | number, course: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: studentCourseReportPDF.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\GradeController::studentCourseReportPDF
 * @see app/Http/Controllers/GradeController.php:519
 * @route '/student/{student}/report/course/{course}/pdf'
 */
        studentCourseReportPDFForm.get = (args: { student: string | number, course: string | number } | [student: string | number, course: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: studentCourseReportPDF.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\GradeController::studentCourseReportPDF
 * @see app/Http/Controllers/GradeController.php:519
 * @route '/student/{student}/report/course/{course}/pdf'
 */
        studentCourseReportPDFForm.head = (args: { student: string | number, course: string | number } | [student: string | number, course: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: studentCourseReportPDF.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    studentCourseReportPDF.form = studentCourseReportPDFForm
/**
* @see \App\Http\Controllers\GradeController::studentCourseReportCSV
 * @see app/Http/Controllers/GradeController.php:529
 * @route '/student/{student}/report/course/{course}/csv'
 */
export const studentCourseReportCSV = (args: { student: string | number, course: string | number } | [student: string | number, course: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: studentCourseReportCSV.url(args, options),
    method: 'get',
})

studentCourseReportCSV.definition = {
    methods: ["get","head"],
    url: '/student/{student}/report/course/{course}/csv',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\GradeController::studentCourseReportCSV
 * @see app/Http/Controllers/GradeController.php:529
 * @route '/student/{student}/report/course/{course}/csv'
 */
studentCourseReportCSV.url = (args: { student: string | number, course: string | number } | [student: string | number, course: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
                    student: args[0],
                    course: args[1],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        student: args.student,
                                course: args.course,
                }

    return studentCourseReportCSV.definition.url
            .replace('{student}', parsedArgs.student.toString())
            .replace('{course}', parsedArgs.course.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\GradeController::studentCourseReportCSV
 * @see app/Http/Controllers/GradeController.php:529
 * @route '/student/{student}/report/course/{course}/csv'
 */
studentCourseReportCSV.get = (args: { student: string | number, course: string | number } | [student: string | number, course: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: studentCourseReportCSV.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\GradeController::studentCourseReportCSV
 * @see app/Http/Controllers/GradeController.php:529
 * @route '/student/{student}/report/course/{course}/csv'
 */
studentCourseReportCSV.head = (args: { student: string | number, course: string | number } | [student: string | number, course: string | number ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: studentCourseReportCSV.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\GradeController::studentCourseReportCSV
 * @see app/Http/Controllers/GradeController.php:529
 * @route '/student/{student}/report/course/{course}/csv'
 */
    const studentCourseReportCSVForm = (args: { student: string | number, course: string | number } | [student: string | number, course: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: studentCourseReportCSV.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\GradeController::studentCourseReportCSV
 * @see app/Http/Controllers/GradeController.php:529
 * @route '/student/{student}/report/course/{course}/csv'
 */
        studentCourseReportCSVForm.get = (args: { student: string | number, course: string | number } | [student: string | number, course: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: studentCourseReportCSV.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\GradeController::studentCourseReportCSV
 * @see app/Http/Controllers/GradeController.php:529
 * @route '/student/{student}/report/course/{course}/csv'
 */
        studentCourseReportCSVForm.head = (args: { student: string | number, course: string | number } | [student: string | number, course: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: studentCourseReportCSV.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    studentCourseReportCSV.form = studentCourseReportCSVForm
/**
* @see \App\Http\Controllers\GradeController::instructorReport
 * @see app/Http/Controllers/GradeController.php:129
 * @route '/instructor/report'
 */
export const instructorReport = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: instructorReport.url(options),
    method: 'get',
})

instructorReport.definition = {
    methods: ["get","head"],
    url: '/instructor/report',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\GradeController::instructorReport
 * @see app/Http/Controllers/GradeController.php:129
 * @route '/instructor/report'
 */
instructorReport.url = (options?: RouteQueryOptions) => {
    return instructorReport.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\GradeController::instructorReport
 * @see app/Http/Controllers/GradeController.php:129
 * @route '/instructor/report'
 */
instructorReport.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: instructorReport.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\GradeController::instructorReport
 * @see app/Http/Controllers/GradeController.php:129
 * @route '/instructor/report'
 */
instructorReport.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: instructorReport.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\GradeController::instructorReport
 * @see app/Http/Controllers/GradeController.php:129
 * @route '/instructor/report'
 */
    const instructorReportForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: instructorReport.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\GradeController::instructorReport
 * @see app/Http/Controllers/GradeController.php:129
 * @route '/instructor/report'
 */
        instructorReportForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: instructorReport.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\GradeController::instructorReport
 * @see app/Http/Controllers/GradeController.php:129
 * @route '/instructor/report'
 */
        instructorReportForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: instructorReport.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    instructorReport.form = instructorReportForm
/**
* @see \App\Http\Controllers\GradeController::instructorReportPDF
 * @see app/Http/Controllers/GradeController.php:0
 * @route '/instructor/report/pdf'
 */
export const instructorReportPDF = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: instructorReportPDF.url(options),
    method: 'get',
})

instructorReportPDF.definition = {
    methods: ["get","head"],
    url: '/instructor/report/pdf',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\GradeController::instructorReportPDF
 * @see app/Http/Controllers/GradeController.php:0
 * @route '/instructor/report/pdf'
 */
instructorReportPDF.url = (options?: RouteQueryOptions) => {
    return instructorReportPDF.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\GradeController::instructorReportPDF
 * @see app/Http/Controllers/GradeController.php:0
 * @route '/instructor/report/pdf'
 */
instructorReportPDF.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: instructorReportPDF.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\GradeController::instructorReportPDF
 * @see app/Http/Controllers/GradeController.php:0
 * @route '/instructor/report/pdf'
 */
instructorReportPDF.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: instructorReportPDF.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\GradeController::instructorReportPDF
 * @see app/Http/Controllers/GradeController.php:0
 * @route '/instructor/report/pdf'
 */
    const instructorReportPDFForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: instructorReportPDF.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\GradeController::instructorReportPDF
 * @see app/Http/Controllers/GradeController.php:0
 * @route '/instructor/report/pdf'
 */
        instructorReportPDFForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: instructorReportPDF.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\GradeController::instructorReportPDF
 * @see app/Http/Controllers/GradeController.php:0
 * @route '/instructor/report/pdf'
 */
        instructorReportPDFForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: instructorReportPDF.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    instructorReportPDF.form = instructorReportPDFForm
/**
* @see \App\Http\Controllers\GradeController::instructorReportCSV
 * @see app/Http/Controllers/GradeController.php:0
 * @route '/instructor/report/csv'
 */
export const instructorReportCSV = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: instructorReportCSV.url(options),
    method: 'get',
})

instructorReportCSV.definition = {
    methods: ["get","head"],
    url: '/instructor/report/csv',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\GradeController::instructorReportCSV
 * @see app/Http/Controllers/GradeController.php:0
 * @route '/instructor/report/csv'
 */
instructorReportCSV.url = (options?: RouteQueryOptions) => {
    return instructorReportCSV.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\GradeController::instructorReportCSV
 * @see app/Http/Controllers/GradeController.php:0
 * @route '/instructor/report/csv'
 */
instructorReportCSV.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: instructorReportCSV.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\GradeController::instructorReportCSV
 * @see app/Http/Controllers/GradeController.php:0
 * @route '/instructor/report/csv'
 */
instructorReportCSV.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: instructorReportCSV.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\GradeController::instructorReportCSV
 * @see app/Http/Controllers/GradeController.php:0
 * @route '/instructor/report/csv'
 */
    const instructorReportCSVForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: instructorReportCSV.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\GradeController::instructorReportCSV
 * @see app/Http/Controllers/GradeController.php:0
 * @route '/instructor/report/csv'
 */
        instructorReportCSVForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: instructorReportCSV.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\GradeController::instructorReportCSV
 * @see app/Http/Controllers/GradeController.php:0
 * @route '/instructor/report/csv'
 */
        instructorReportCSVForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: instructorReportCSV.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    instructorReportCSV.form = instructorReportCSVForm
/**
* @see \App\Http\Controllers\GradeController::studentDetail
 * @see app/Http/Controllers/GradeController.php:0
 * @route '/instructor/student/{student}/detail'
 */
export const studentDetail = (args: { student: string | number } | [student: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: studentDetail.url(args, options),
    method: 'get',
})

studentDetail.definition = {
    methods: ["get","head"],
    url: '/instructor/student/{student}/detail',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\GradeController::studentDetail
 * @see app/Http/Controllers/GradeController.php:0
 * @route '/instructor/student/{student}/detail'
 */
studentDetail.url = (args: { student: string | number } | [student: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { student: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    student: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        student: args.student,
                }

    return studentDetail.definition.url
            .replace('{student}', parsedArgs.student.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\GradeController::studentDetail
 * @see app/Http/Controllers/GradeController.php:0
 * @route '/instructor/student/{student}/detail'
 */
studentDetail.get = (args: { student: string | number } | [student: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: studentDetail.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\GradeController::studentDetail
 * @see app/Http/Controllers/GradeController.php:0
 * @route '/instructor/student/{student}/detail'
 */
studentDetail.head = (args: { student: string | number } | [student: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: studentDetail.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\GradeController::studentDetail
 * @see app/Http/Controllers/GradeController.php:0
 * @route '/instructor/student/{student}/detail'
 */
    const studentDetailForm = (args: { student: string | number } | [student: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: studentDetail.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\GradeController::studentDetail
 * @see app/Http/Controllers/GradeController.php:0
 * @route '/instructor/student/{student}/detail'
 */
        studentDetailForm.get = (args: { student: string | number } | [student: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: studentDetail.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\GradeController::studentDetail
 * @see app/Http/Controllers/GradeController.php:0
 * @route '/instructor/student/{student}/detail'
 */
        studentDetailForm.head = (args: { student: string | number } | [student: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: studentDetail.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    studentDetail.form = studentDetailForm
const GradeController = { studentReport, studentCourseReport, studentReportPDF, studentCompleteReportPDF, studentReportCSV, studentCompleteReportCSV, studentCourseReportPDF, studentCourseReportCSV, instructorReport, instructorReportPDF, instructorReportCSV, studentDetail }

export default GradeController