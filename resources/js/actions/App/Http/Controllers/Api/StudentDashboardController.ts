import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\StudentDashboardController::getDashboardData
 * @see app/Http/Controllers/Api/StudentDashboardController.php:18
 * @route '/api/dashboard/student-data'
 */
export const getDashboardData = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getDashboardData.url(options),
    method: 'get',
})

getDashboardData.definition = {
    methods: ["get","head"],
    url: '/api/dashboard/student-data',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\StudentDashboardController::getDashboardData
 * @see app/Http/Controllers/Api/StudentDashboardController.php:18
 * @route '/api/dashboard/student-data'
 */
getDashboardData.url = (options?: RouteQueryOptions) => {
    return getDashboardData.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\StudentDashboardController::getDashboardData
 * @see app/Http/Controllers/Api/StudentDashboardController.php:18
 * @route '/api/dashboard/student-data'
 */
getDashboardData.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getDashboardData.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Api\StudentDashboardController::getDashboardData
 * @see app/Http/Controllers/Api/StudentDashboardController.php:18
 * @route '/api/dashboard/student-data'
 */
getDashboardData.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getDashboardData.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\Api\StudentDashboardController::getDashboardData
 * @see app/Http/Controllers/Api/StudentDashboardController.php:18
 * @route '/api/dashboard/student-data'
 */
    const getDashboardDataForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: getDashboardData.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\Api\StudentDashboardController::getDashboardData
 * @see app/Http/Controllers/Api/StudentDashboardController.php:18
 * @route '/api/dashboard/student-data'
 */
        getDashboardDataForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: getDashboardData.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\Api\StudentDashboardController::getDashboardData
 * @see app/Http/Controllers/Api/StudentDashboardController.php:18
 * @route '/api/dashboard/student-data'
 */
        getDashboardDataForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: getDashboardData.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    getDashboardData.form = getDashboardDataForm
const StudentDashboardController = { getDashboardData }

export default StudentDashboardController