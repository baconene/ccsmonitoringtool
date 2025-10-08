import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../wayfinder'
import report611039 from './report'
import student from './student'
/**
* @see \App\Http\Controllers\GradeController::report
 * @see app/Http/Controllers/GradeController.php:129
 * @route '/instructor/report'
 */
export const report = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: report.url(options),
    method: 'get',
})

report.definition = {
    methods: ["get","head"],
    url: '/instructor/report',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\GradeController::report
 * @see app/Http/Controllers/GradeController.php:129
 * @route '/instructor/report'
 */
report.url = (options?: RouteQueryOptions) => {
    return report.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\GradeController::report
 * @see app/Http/Controllers/GradeController.php:129
 * @route '/instructor/report'
 */
report.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: report.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\GradeController::report
 * @see app/Http/Controllers/GradeController.php:129
 * @route '/instructor/report'
 */
report.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: report.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\GradeController::report
 * @see app/Http/Controllers/GradeController.php:129
 * @route '/instructor/report'
 */
    const reportForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: report.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\GradeController::report
 * @see app/Http/Controllers/GradeController.php:129
 * @route '/instructor/report'
 */
        reportForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: report.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\GradeController::report
 * @see app/Http/Controllers/GradeController.php:129
 * @route '/instructor/report'
 */
        reportForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: report.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    report.form = reportForm
const instructor = {
    report: Object.assign(report, report611039),
student: Object.assign(student, student),
}

export default instructor