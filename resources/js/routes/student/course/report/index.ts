import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\GradeController::pdf
 * @see app/Http/Controllers/GradeController.php:519
 * @route '/student/{student}/report/course/{course}/pdf'
 */
export const pdf = (args: { student: string | number, course: string | number } | [student: string | number, course: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: pdf.url(args, options),
    method: 'get',
})

pdf.definition = {
    methods: ["get","head"],
    url: '/student/{student}/report/course/{course}/pdf',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\GradeController::pdf
 * @see app/Http/Controllers/GradeController.php:519
 * @route '/student/{student}/report/course/{course}/pdf'
 */
pdf.url = (args: { student: string | number, course: string | number } | [student: string | number, course: string | number ], options?: RouteQueryOptions) => {
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

    return pdf.definition.url
            .replace('{student}', parsedArgs.student.toString())
            .replace('{course}', parsedArgs.course.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\GradeController::pdf
 * @see app/Http/Controllers/GradeController.php:519
 * @route '/student/{student}/report/course/{course}/pdf'
 */
pdf.get = (args: { student: string | number, course: string | number } | [student: string | number, course: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: pdf.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\GradeController::pdf
 * @see app/Http/Controllers/GradeController.php:519
 * @route '/student/{student}/report/course/{course}/pdf'
 */
pdf.head = (args: { student: string | number, course: string | number } | [student: string | number, course: string | number ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: pdf.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\GradeController::pdf
 * @see app/Http/Controllers/GradeController.php:519
 * @route '/student/{student}/report/course/{course}/pdf'
 */
    const pdfForm = (args: { student: string | number, course: string | number } | [student: string | number, course: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: pdf.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\GradeController::pdf
 * @see app/Http/Controllers/GradeController.php:519
 * @route '/student/{student}/report/course/{course}/pdf'
 */
        pdfForm.get = (args: { student: string | number, course: string | number } | [student: string | number, course: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: pdf.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\GradeController::pdf
 * @see app/Http/Controllers/GradeController.php:519
 * @route '/student/{student}/report/course/{course}/pdf'
 */
        pdfForm.head = (args: { student: string | number, course: string | number } | [student: string | number, course: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: pdf.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    pdf.form = pdfForm
/**
* @see \App\Http\Controllers\GradeController::csv
 * @see app/Http/Controllers/GradeController.php:529
 * @route '/student/{student}/report/course/{course}/csv'
 */
export const csv = (args: { student: string | number, course: string | number } | [student: string | number, course: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: csv.url(args, options),
    method: 'get',
})

csv.definition = {
    methods: ["get","head"],
    url: '/student/{student}/report/course/{course}/csv',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\GradeController::csv
 * @see app/Http/Controllers/GradeController.php:529
 * @route '/student/{student}/report/course/{course}/csv'
 */
csv.url = (args: { student: string | number, course: string | number } | [student: string | number, course: string | number ], options?: RouteQueryOptions) => {
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

    return csv.definition.url
            .replace('{student}', parsedArgs.student.toString())
            .replace('{course}', parsedArgs.course.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\GradeController::csv
 * @see app/Http/Controllers/GradeController.php:529
 * @route '/student/{student}/report/course/{course}/csv'
 */
csv.get = (args: { student: string | number, course: string | number } | [student: string | number, course: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: csv.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\GradeController::csv
 * @see app/Http/Controllers/GradeController.php:529
 * @route '/student/{student}/report/course/{course}/csv'
 */
csv.head = (args: { student: string | number, course: string | number } | [student: string | number, course: string | number ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: csv.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\GradeController::csv
 * @see app/Http/Controllers/GradeController.php:529
 * @route '/student/{student}/report/course/{course}/csv'
 */
    const csvForm = (args: { student: string | number, course: string | number } | [student: string | number, course: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: csv.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\GradeController::csv
 * @see app/Http/Controllers/GradeController.php:529
 * @route '/student/{student}/report/course/{course}/csv'
 */
        csvForm.get = (args: { student: string | number, course: string | number } | [student: string | number, course: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: csv.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\GradeController::csv
 * @see app/Http/Controllers/GradeController.php:529
 * @route '/student/{student}/report/course/{course}/csv'
 */
        csvForm.head = (args: { student: string | number, course: string | number } | [student: string | number, course: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: csv.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    csv.form = csvForm
const report = {
    pdf: Object.assign(pdf, pdf),
csv: Object.assign(csv, csv),
}

export default report