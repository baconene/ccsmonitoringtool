import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../wayfinder'
import pdf81d01d from './pdf'
import csvC085d9 from './csv'
/**
* @see \App\Http\Controllers\GradeController::pdf
 * @see app/Http/Controllers/GradeController.php:485
 * @route '/student/report/pdf'
 */
export const pdf = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: pdf.url(options),
    method: 'get',
})

pdf.definition = {
    methods: ["get","head"],
    url: '/student/report/pdf',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\GradeController::pdf
 * @see app/Http/Controllers/GradeController.php:485
 * @route '/student/report/pdf'
 */
pdf.url = (options?: RouteQueryOptions) => {
    return pdf.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\GradeController::pdf
 * @see app/Http/Controllers/GradeController.php:485
 * @route '/student/report/pdf'
 */
pdf.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: pdf.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\GradeController::pdf
 * @see app/Http/Controllers/GradeController.php:485
 * @route '/student/report/pdf'
 */
pdf.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: pdf.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\GradeController::pdf
 * @see app/Http/Controllers/GradeController.php:485
 * @route '/student/report/pdf'
 */
    const pdfForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: pdf.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\GradeController::pdf
 * @see app/Http/Controllers/GradeController.php:485
 * @route '/student/report/pdf'
 */
        pdfForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: pdf.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\GradeController::pdf
 * @see app/Http/Controllers/GradeController.php:485
 * @route '/student/report/pdf'
 */
        pdfForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: pdf.url({
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
 * @see app/Http/Controllers/GradeController.php:502
 * @route '/student/report/csv'
 */
export const csv = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: csv.url(options),
    method: 'get',
})

csv.definition = {
    methods: ["get","head"],
    url: '/student/report/csv',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\GradeController::csv
 * @see app/Http/Controllers/GradeController.php:502
 * @route '/student/report/csv'
 */
csv.url = (options?: RouteQueryOptions) => {
    return csv.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\GradeController::csv
 * @see app/Http/Controllers/GradeController.php:502
 * @route '/student/report/csv'
 */
csv.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: csv.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\GradeController::csv
 * @see app/Http/Controllers/GradeController.php:502
 * @route '/student/report/csv'
 */
csv.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: csv.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\GradeController::csv
 * @see app/Http/Controllers/GradeController.php:502
 * @route '/student/report/csv'
 */
    const csvForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: csv.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\GradeController::csv
 * @see app/Http/Controllers/GradeController.php:502
 * @route '/student/report/csv'
 */
        csvForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: csv.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\GradeController::csv
 * @see app/Http/Controllers/GradeController.php:502
 * @route '/student/report/csv'
 */
        csvForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: csv.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    csv.form = csvForm
const report = {
    pdf: Object.assign(pdf, pdf81d01d),
csv: Object.assign(csv, csvC085d9),
}

export default report