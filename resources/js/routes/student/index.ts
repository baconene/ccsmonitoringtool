import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../wayfinder'
import courses from './courses'
import quiz from './quiz'
import report611039 from './report'
import course from './course'
/**
 * @see routes/web.php:78
 * @route '/student-dashboard'
 */
export const dashboard = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: dashboard.url(options),
    method: 'get',
})

dashboard.definition = {
    methods: ["get","head"],
    url: '/student-dashboard',
} satisfies RouteDefinition<["get","head"]>

/**
 * @see routes/web.php:78
 * @route '/student-dashboard'
 */
dashboard.url = (options?: RouteQueryOptions) => {
    return dashboard.definition.url + queryParams(options)
}

/**
 * @see routes/web.php:78
 * @route '/student-dashboard'
 */
dashboard.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: dashboard.url(options),
    method: 'get',
})
/**
 * @see routes/web.php:78
 * @route '/student-dashboard'
 */
dashboard.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: dashboard.url(options),
    method: 'head',
})

    /**
 * @see routes/web.php:78
 * @route '/student-dashboard'
 */
    const dashboardForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: dashboard.url(options),
        method: 'get',
    })

            /**
 * @see routes/web.php:78
 * @route '/student-dashboard'
 */
        dashboardForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: dashboard.url(options),
            method: 'get',
        })
            /**
 * @see routes/web.php:78
 * @route '/student-dashboard'
 */
        dashboardForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: dashboard.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    dashboard.form = dashboardForm
/**
 * @see routes/web.php:149
 * @route '/student/{id}/details'
 */
export const details = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: details.url(args, options),
    method: 'get',
})

details.definition = {
    methods: ["get","head"],
    url: '/student/{id}/details',
} satisfies RouteDefinition<["get","head"]>

/**
 * @see routes/web.php:149
 * @route '/student/{id}/details'
 */
details.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { id: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    id: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        id: args.id,
                }

    return details.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
 * @see routes/web.php:149
 * @route '/student/{id}/details'
 */
details.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: details.url(args, options),
    method: 'get',
})
/**
 * @see routes/web.php:149
 * @route '/student/{id}/details'
 */
details.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: details.url(args, options),
    method: 'head',
})

    /**
 * @see routes/web.php:149
 * @route '/student/{id}/details'
 */
    const detailsForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: details.url(args, options),
        method: 'get',
    })

            /**
 * @see routes/web.php:149
 * @route '/student/{id}/details'
 */
        detailsForm.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: details.url(args, options),
            method: 'get',
        })
            /**
 * @see routes/web.php:149
 * @route '/student/{id}/details'
 */
        detailsForm.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: details.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    details.form = detailsForm
/**
* @see \App\Http\Controllers\Student\StudentCourseController::activities
 * @see app/Http/Controllers/Student/StudentCourseController.php:426
 * @route '/student/activities'
 */
export const activities = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: activities.url(options),
    method: 'get',
})

activities.definition = {
    methods: ["get","head"],
    url: '/student/activities',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Student\StudentCourseController::activities
 * @see app/Http/Controllers/Student/StudentCourseController.php:426
 * @route '/student/activities'
 */
activities.url = (options?: RouteQueryOptions) => {
    return activities.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Student\StudentCourseController::activities
 * @see app/Http/Controllers/Student/StudentCourseController.php:426
 * @route '/student/activities'
 */
activities.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: activities.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Student\StudentCourseController::activities
 * @see app/Http/Controllers/Student/StudentCourseController.php:426
 * @route '/student/activities'
 */
activities.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: activities.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\Student\StudentCourseController::activities
 * @see app/Http/Controllers/Student/StudentCourseController.php:426
 * @route '/student/activities'
 */
    const activitiesForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: activities.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\Student\StudentCourseController::activities
 * @see app/Http/Controllers/Student/StudentCourseController.php:426
 * @route '/student/activities'
 */
        activitiesForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: activities.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\Student\StudentCourseController::activities
 * @see app/Http/Controllers/Student/StudentCourseController.php:426
 * @route '/student/activities'
 */
        activitiesForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: activities.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    activities.form = activitiesForm
/**
* @see \App\Http\Controllers\GradeController::report
 * @see app/Http/Controllers/GradeController.php:28
 * @route '/student/report'
 */
export const report = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: report.url(options),
    method: 'get',
})

report.definition = {
    methods: ["get","head"],
    url: '/student/report',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\GradeController::report
 * @see app/Http/Controllers/GradeController.php:28
 * @route '/student/report'
 */
report.url = (options?: RouteQueryOptions) => {
    return report.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\GradeController::report
 * @see app/Http/Controllers/GradeController.php:28
 * @route '/student/report'
 */
report.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: report.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\GradeController::report
 * @see app/Http/Controllers/GradeController.php:28
 * @route '/student/report'
 */
report.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: report.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\GradeController::report
 * @see app/Http/Controllers/GradeController.php:28
 * @route '/student/report'
 */
    const reportForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: report.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\GradeController::report
 * @see app/Http/Controllers/GradeController.php:28
 * @route '/student/report'
 */
        reportForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: report.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\GradeController::report
 * @see app/Http/Controllers/GradeController.php:28
 * @route '/student/report'
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
const student = {
    dashboard: Object.assign(dashboard, dashboard),
details: Object.assign(details, details),
courses: Object.assign(courses, courses),
activities: Object.assign(activities, activities),
quiz: Object.assign(quiz, quiz),
report: Object.assign(report, report611039),
course: Object.assign(course, course),
}

export default student