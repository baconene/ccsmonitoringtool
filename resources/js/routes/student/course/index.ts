import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../wayfinder'
import report611039 from './report'
/**
* @see \App\Http\Controllers\GradeController::report
 * @see app/Http/Controllers/GradeController.php:81
 * @route '/student/{student}/report/course/{course}'
 */
export const report = (args: { student: string | number, course: string | number } | [student: string | number, course: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: report.url(args, options),
    method: 'get',
})

report.definition = {
    methods: ["get","head"],
    url: '/student/{student}/report/course/{course}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\GradeController::report
 * @see app/Http/Controllers/GradeController.php:81
 * @route '/student/{student}/report/course/{course}'
 */
report.url = (args: { student: string | number, course: string | number } | [student: string | number, course: string | number ], options?: RouteQueryOptions) => {
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

    return report.definition.url
            .replace('{student}', parsedArgs.student.toString())
            .replace('{course}', parsedArgs.course.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\GradeController::report
 * @see app/Http/Controllers/GradeController.php:81
 * @route '/student/{student}/report/course/{course}'
 */
report.get = (args: { student: string | number, course: string | number } | [student: string | number, course: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: report.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\GradeController::report
 * @see app/Http/Controllers/GradeController.php:81
 * @route '/student/{student}/report/course/{course}'
 */
report.head = (args: { student: string | number, course: string | number } | [student: string | number, course: string | number ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: report.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\GradeController::report
 * @see app/Http/Controllers/GradeController.php:81
 * @route '/student/{student}/report/course/{course}'
 */
    const reportForm = (args: { student: string | number, course: string | number } | [student: string | number, course: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: report.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\GradeController::report
 * @see app/Http/Controllers/GradeController.php:81
 * @route '/student/{student}/report/course/{course}'
 */
        reportForm.get = (args: { student: string | number, course: string | number } | [student: string | number, course: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: report.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\GradeController::report
 * @see app/Http/Controllers/GradeController.php:81
 * @route '/student/{student}/report/course/{course}'
 */
        reportForm.head = (args: { student: string | number, course: string | number } | [student: string | number, course: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: report.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    report.form = reportForm
const course = {
    report: Object.assign(report, report611039),
}

export default course