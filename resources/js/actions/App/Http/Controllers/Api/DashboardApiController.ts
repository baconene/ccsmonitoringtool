import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\DashboardApiController::getInstructorProfile
 * @see app/Http/Controllers/Api/DashboardApiController.php:198
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
 * @see app/Http/Controllers/Api/DashboardApiController.php:198
 * @route '/api/instructor/profile'
 */
getInstructorProfile.url = (options?: RouteQueryOptions) => {
    return getInstructorProfile.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\DashboardApiController::getInstructorProfile
 * @see app/Http/Controllers/Api/DashboardApiController.php:198
 * @route '/api/instructor/profile'
 */
getInstructorProfile.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getInstructorProfile.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Api\DashboardApiController::getInstructorProfile
 * @see app/Http/Controllers/Api/DashboardApiController.php:198
 * @route '/api/instructor/profile'
 */
getInstructorProfile.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getInstructorProfile.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\Api\DashboardApiController::getInstructorProfile
 * @see app/Http/Controllers/Api/DashboardApiController.php:198
 * @route '/api/instructor/profile'
 */
    const getInstructorProfileForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: getInstructorProfile.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\Api\DashboardApiController::getInstructorProfile
 * @see app/Http/Controllers/Api/DashboardApiController.php:198
 * @route '/api/instructor/profile'
 */
        getInstructorProfileForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: getInstructorProfile.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\Api\DashboardApiController::getInstructorProfile
 * @see app/Http/Controllers/Api/DashboardApiController.php:198
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
/**
* @see \App\Http\Controllers\Api\DashboardApiController::getStats
 * @see app/Http/Controllers/Api/DashboardApiController.php:25
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
 * @see app/Http/Controllers/Api/DashboardApiController.php:25
 * @route '/api/dashboard/stats'
 */
getStats.url = (options?: RouteQueryOptions) => {
    return getStats.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\DashboardApiController::getStats
 * @see app/Http/Controllers/Api/DashboardApiController.php:25
 * @route '/api/dashboard/stats'
 */
getStats.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getStats.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Api\DashboardApiController::getStats
 * @see app/Http/Controllers/Api/DashboardApiController.php:25
 * @route '/api/dashboard/stats'
 */
getStats.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getStats.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\Api\DashboardApiController::getStats
 * @see app/Http/Controllers/Api/DashboardApiController.php:25
 * @route '/api/dashboard/stats'
 */
    const getStatsForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: getStats.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\Api\DashboardApiController::getStats
 * @see app/Http/Controllers/Api/DashboardApiController.php:25
 * @route '/api/dashboard/stats'
 */
        getStatsForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: getStats.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\Api\DashboardApiController::getStats
 * @see app/Http/Controllers/Api/DashboardApiController.php:25
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
* @see \App\Http\Controllers\Api\DashboardApiController::getStudentData
 * @see app/Http/Controllers/Api/DashboardApiController.php:238
 * @route '/api/dashboard/student-data'
 */
export const getStudentData = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getStudentData.url(options),
    method: 'get',
})

getStudentData.definition = {
    methods: ["get","head"],
    url: '/api/dashboard/student-data',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\DashboardApiController::getStudentData
 * @see app/Http/Controllers/Api/DashboardApiController.php:238
 * @route '/api/dashboard/student-data'
 */
getStudentData.url = (options?: RouteQueryOptions) => {
    return getStudentData.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\DashboardApiController::getStudentData
 * @see app/Http/Controllers/Api/DashboardApiController.php:238
 * @route '/api/dashboard/student-data'
 */
getStudentData.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getStudentData.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Api\DashboardApiController::getStudentData
 * @see app/Http/Controllers/Api/DashboardApiController.php:238
 * @route '/api/dashboard/student-data'
 */
getStudentData.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getStudentData.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\Api\DashboardApiController::getStudentData
 * @see app/Http/Controllers/Api/DashboardApiController.php:238
 * @route '/api/dashboard/student-data'
 */
    const getStudentDataForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: getStudentData.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\Api\DashboardApiController::getStudentData
 * @see app/Http/Controllers/Api/DashboardApiController.php:238
 * @route '/api/dashboard/student-data'
 */
        getStudentDataForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: getStudentData.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\Api\DashboardApiController::getStudentData
 * @see app/Http/Controllers/Api/DashboardApiController.php:238
 * @route '/api/dashboard/student-data'
 */
        getStudentDataForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: getStudentData.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    getStudentData.form = getStudentDataForm
/**
* @see \App\Http\Controllers\Api\DashboardApiController::getInstructorData
 * @see app/Http/Controllers/Api/DashboardApiController.php:391
 * @route '/api/dashboard/instructor-data'
 */
export const getInstructorData = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getInstructorData.url(options),
    method: 'get',
})

getInstructorData.definition = {
    methods: ["get","head"],
    url: '/api/dashboard/instructor-data',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\DashboardApiController::getInstructorData
 * @see app/Http/Controllers/Api/DashboardApiController.php:391
 * @route '/api/dashboard/instructor-data'
 */
getInstructorData.url = (options?: RouteQueryOptions) => {
    return getInstructorData.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\DashboardApiController::getInstructorData
 * @see app/Http/Controllers/Api/DashboardApiController.php:391
 * @route '/api/dashboard/instructor-data'
 */
getInstructorData.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getInstructorData.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Api\DashboardApiController::getInstructorData
 * @see app/Http/Controllers/Api/DashboardApiController.php:391
 * @route '/api/dashboard/instructor-data'
 */
getInstructorData.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getInstructorData.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\Api\DashboardApiController::getInstructorData
 * @see app/Http/Controllers/Api/DashboardApiController.php:391
 * @route '/api/dashboard/instructor-data'
 */
    const getInstructorDataForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: getInstructorData.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\Api\DashboardApiController::getInstructorData
 * @see app/Http/Controllers/Api/DashboardApiController.php:391
 * @route '/api/dashboard/instructor-data'
 */
        getInstructorDataForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: getInstructorData.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\Api\DashboardApiController::getInstructorData
 * @see app/Http/Controllers/Api/DashboardApiController.php:391
 * @route '/api/dashboard/instructor-data'
 */
        getInstructorDataForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: getInstructorData.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    getInstructorData.form = getInstructorDataForm
/**
* @see \App\Http\Controllers\Api\DashboardApiController::getAdminStats
 * @see app/Http/Controllers/Api/DashboardApiController.php:462
 * @route '/api/dashboard/admin-stats'
 */
export const getAdminStats = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getAdminStats.url(options),
    method: 'get',
})

getAdminStats.definition = {
    methods: ["get","head"],
    url: '/api/dashboard/admin-stats',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\DashboardApiController::getAdminStats
 * @see app/Http/Controllers/Api/DashboardApiController.php:462
 * @route '/api/dashboard/admin-stats'
 */
getAdminStats.url = (options?: RouteQueryOptions) => {
    return getAdminStats.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\DashboardApiController::getAdminStats
 * @see app/Http/Controllers/Api/DashboardApiController.php:462
 * @route '/api/dashboard/admin-stats'
 */
getAdminStats.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getAdminStats.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Api\DashboardApiController::getAdminStats
 * @see app/Http/Controllers/Api/DashboardApiController.php:462
 * @route '/api/dashboard/admin-stats'
 */
getAdminStats.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getAdminStats.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\Api\DashboardApiController::getAdminStats
 * @see app/Http/Controllers/Api/DashboardApiController.php:462
 * @route '/api/dashboard/admin-stats'
 */
    const getAdminStatsForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: getAdminStats.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\Api\DashboardApiController::getAdminStats
 * @see app/Http/Controllers/Api/DashboardApiController.php:462
 * @route '/api/dashboard/admin-stats'
 */
        getAdminStatsForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: getAdminStats.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\Api\DashboardApiController::getAdminStats
 * @see app/Http/Controllers/Api/DashboardApiController.php:462
 * @route '/api/dashboard/admin-stats'
 */
        getAdminStatsForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: getAdminStats.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    getAdminStats.form = getAdminStatsForm
/**
* @see \App\Http\Controllers\Api\DashboardApiController::getAdminCourses
 * @see app/Http/Controllers/Api/DashboardApiController.php:519
 * @route '/api/dashboard/admin-courses'
 */
export const getAdminCourses = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getAdminCourses.url(options),
    method: 'get',
})

getAdminCourses.definition = {
    methods: ["get","head"],
    url: '/api/dashboard/admin-courses',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\DashboardApiController::getAdminCourses
 * @see app/Http/Controllers/Api/DashboardApiController.php:519
 * @route '/api/dashboard/admin-courses'
 */
getAdminCourses.url = (options?: RouteQueryOptions) => {
    return getAdminCourses.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\DashboardApiController::getAdminCourses
 * @see app/Http/Controllers/Api/DashboardApiController.php:519
 * @route '/api/dashboard/admin-courses'
 */
getAdminCourses.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getAdminCourses.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Api\DashboardApiController::getAdminCourses
 * @see app/Http/Controllers/Api/DashboardApiController.php:519
 * @route '/api/dashboard/admin-courses'
 */
getAdminCourses.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getAdminCourses.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\Api\DashboardApiController::getAdminCourses
 * @see app/Http/Controllers/Api/DashboardApiController.php:519
 * @route '/api/dashboard/admin-courses'
 */
    const getAdminCoursesForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: getAdminCourses.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\Api\DashboardApiController::getAdminCourses
 * @see app/Http/Controllers/Api/DashboardApiController.php:519
 * @route '/api/dashboard/admin-courses'
 */
        getAdminCoursesForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: getAdminCourses.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\Api\DashboardApiController::getAdminCourses
 * @see app/Http/Controllers/Api/DashboardApiController.php:519
 * @route '/api/dashboard/admin-courses'
 */
        getAdminCoursesForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: getAdminCourses.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    getAdminCourses.form = getAdminCoursesForm
/**
* @see \App\Http\Controllers\Api\DashboardApiController::getAdminActivities
 * @see app/Http/Controllers/Api/DashboardApiController.php:563
 * @route '/api/dashboard/admin-activities'
 */
export const getAdminActivities = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getAdminActivities.url(options),
    method: 'get',
})

getAdminActivities.definition = {
    methods: ["get","head"],
    url: '/api/dashboard/admin-activities',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\DashboardApiController::getAdminActivities
 * @see app/Http/Controllers/Api/DashboardApiController.php:563
 * @route '/api/dashboard/admin-activities'
 */
getAdminActivities.url = (options?: RouteQueryOptions) => {
    return getAdminActivities.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\DashboardApiController::getAdminActivities
 * @see app/Http/Controllers/Api/DashboardApiController.php:563
 * @route '/api/dashboard/admin-activities'
 */
getAdminActivities.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getAdminActivities.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Api\DashboardApiController::getAdminActivities
 * @see app/Http/Controllers/Api/DashboardApiController.php:563
 * @route '/api/dashboard/admin-activities'
 */
getAdminActivities.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getAdminActivities.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\Api\DashboardApiController::getAdminActivities
 * @see app/Http/Controllers/Api/DashboardApiController.php:563
 * @route '/api/dashboard/admin-activities'
 */
    const getAdminActivitiesForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: getAdminActivities.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\Api\DashboardApiController::getAdminActivities
 * @see app/Http/Controllers/Api/DashboardApiController.php:563
 * @route '/api/dashboard/admin-activities'
 */
        getAdminActivitiesForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: getAdminActivities.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\Api\DashboardApiController::getAdminActivities
 * @see app/Http/Controllers/Api/DashboardApiController.php:563
 * @route '/api/dashboard/admin-activities'
 */
        getAdminActivitiesForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: getAdminActivities.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    getAdminActivities.form = getAdminActivitiesForm
const DashboardApiController = { getInstructorProfile, getStats, getStudentData, getInstructorData, getAdminStats, getAdminCourses, getAdminActivities }

export default DashboardApiController