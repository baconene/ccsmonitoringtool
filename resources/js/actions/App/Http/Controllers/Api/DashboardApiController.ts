import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../wayfinder'
import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\DashboardApiController::getStats
 * @see app/Http/Controllers/Api/DashboardApiController.php:19
 * @route '/api/dashboard/stats'
 */
export const getStats = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getStats.url(options),
    method: 'get',
})

getStats.definition = {
    methods: ["get","head"],
    url: '/api/dashboard/stats',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\DashboardApiController::getStats
 * @see app/Http/Controllers/Api/DashboardApiController.php:19
 * @route '/api/dashboard/stats'
 */
getStats.url = (options?: RouteQueryOptions) => {
    return getStats.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\DashboardApiController::getStats
 * @see app/Http/Controllers/Api/DashboardApiController.php:19
 * @route '/api/dashboard/stats'
 */
getStats.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getStats.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Api\DashboardApiController::getStats
 * @see app/Http/Controllers/Api/DashboardApiController.php:19
 * @route '/api/dashboard/stats'
 */
getStats.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getStats.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\Api\DashboardApiController::getStats
 * @see app/Http/Controllers/Api/DashboardApiController.php:19
 * @route '/api/dashboard/stats'
 */
    const getStatsForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: getStats.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\Api\DashboardApiController::getStats
 * @see app/Http/Controllers/Api/DashboardApiController.php:19
 * @route '/api/dashboard/stats'
 */
        getStatsForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: getStats.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\Api\DashboardApiController::getStats
 * @see app/Http/Controllers/Api/DashboardApiController.php:19
 * @route '/api/dashboard/stats'
 */
        getStatsForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: getStats.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    getStats.form = getStatsForm
/**
* @see \App\Http\Controllers\Api\DashboardApiController::getInstructorProfile
 * @see app/Http/Controllers/Api/DashboardApiController.php:58
 * @route '/api/instructor/profile'
 */
export const getInstructorProfile = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getInstructorProfile.url(options),
    method: 'get',
})

getInstructorProfile.definition = {
    methods: ["get","head"],
    url: '/api/instructor/profile',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\DashboardApiController::getInstructorProfile
 * @see app/Http/Controllers/Api/DashboardApiController.php:58
 * @route '/api/instructor/profile'
 */
getInstructorProfile.url = (options?: RouteQueryOptions) => {
    return getInstructorProfile.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\DashboardApiController::getInstructorProfile
 * @see app/Http/Controllers/Api/DashboardApiController.php:58
 * @route '/api/instructor/profile'
 */
getInstructorProfile.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getInstructorProfile.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Api\DashboardApiController::getInstructorProfile
 * @see app/Http/Controllers/Api/DashboardApiController.php:58
 * @route '/api/instructor/profile'
 */
getInstructorProfile.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getInstructorProfile.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\Api\DashboardApiController::getInstructorProfile
 * @see app/Http/Controllers/Api/DashboardApiController.php:58
 * @route '/api/instructor/profile'
 */
    const getInstructorProfileForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: getInstructorProfile.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\Api\DashboardApiController::getInstructorProfile
 * @see app/Http/Controllers/Api/DashboardApiController.php:58
 * @route '/api/instructor/profile'
 */
        getInstructorProfileForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: getInstructorProfile.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\Api\DashboardApiController::getInstructorProfile
 * @see app/Http/Controllers/Api/DashboardApiController.php:58
 * @route '/api/instructor/profile'
 */
        getInstructorProfileForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: getInstructorProfile.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    getInstructorProfile.form = getInstructorProfileForm
const DashboardApiController = { getStats, getInstructorProfile }

export default DashboardApiController