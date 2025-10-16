import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../wayfinder'
/**
* @see \App\Http\Controllers\GradeSettingsController::index
 * @see app/Http/Controllers/GradeSettingsController.php:18
 * @route '/grade-settings'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/grade-settings',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\GradeSettingsController::index
 * @see app/Http/Controllers/GradeSettingsController.php:18
 * @route '/grade-settings'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\GradeSettingsController::index
 * @see app/Http/Controllers/GradeSettingsController.php:18
 * @route '/grade-settings'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\GradeSettingsController::index
 * @see app/Http/Controllers/GradeSettingsController.php:18
 * @route '/grade-settings'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\GradeSettingsController::index
 * @see app/Http/Controllers/GradeSettingsController.php:18
 * @route '/grade-settings'
 */
    const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: index.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\GradeSettingsController::index
 * @see app/Http/Controllers/GradeSettingsController.php:18
 * @route '/grade-settings'
 */
        indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: index.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\GradeSettingsController::index
 * @see app/Http/Controllers/GradeSettingsController.php:18
 * @route '/grade-settings'
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
* @see \App\Http\Controllers\GradeSettingsController::moduleComponents
 * @see app/Http/Controllers/GradeSettingsController.php:104
 * @route '/grade-settings/module-components'
 */
export const moduleComponents = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: moduleComponents.url(options),
    method: 'post',
})

moduleComponents.definition = {
    methods: ["post"],
    url: '/grade-settings/module-components',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\GradeSettingsController::moduleComponents
 * @see app/Http/Controllers/GradeSettingsController.php:104
 * @route '/grade-settings/module-components'
 */
moduleComponents.url = (options?: RouteQueryOptions) => {
    return moduleComponents.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\GradeSettingsController::moduleComponents
 * @see app/Http/Controllers/GradeSettingsController.php:104
 * @route '/grade-settings/module-components'
 */
moduleComponents.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: moduleComponents.url(options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\GradeSettingsController::moduleComponents
 * @see app/Http/Controllers/GradeSettingsController.php:104
 * @route '/grade-settings/module-components'
 */
    const moduleComponentsForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: moduleComponents.url(options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\GradeSettingsController::moduleComponents
 * @see app/Http/Controllers/GradeSettingsController.php:104
 * @route '/grade-settings/module-components'
 */
        moduleComponentsForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: moduleComponents.url(options),
            method: 'post',
        })
    
    moduleComponents.form = moduleComponentsForm
/**
* @see \App\Http\Controllers\GradeSettingsController::activityTypes
 * @see app/Http/Controllers/GradeSettingsController.php:169
 * @route '/grade-settings/activity-types'
 */
export const activityTypes = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: activityTypes.url(options),
    method: 'post',
})

activityTypes.definition = {
    methods: ["post"],
    url: '/grade-settings/activity-types',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\GradeSettingsController::activityTypes
 * @see app/Http/Controllers/GradeSettingsController.php:169
 * @route '/grade-settings/activity-types'
 */
activityTypes.url = (options?: RouteQueryOptions) => {
    return activityTypes.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\GradeSettingsController::activityTypes
 * @see app/Http/Controllers/GradeSettingsController.php:169
 * @route '/grade-settings/activity-types'
 */
activityTypes.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: activityTypes.url(options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\GradeSettingsController::activityTypes
 * @see app/Http/Controllers/GradeSettingsController.php:169
 * @route '/grade-settings/activity-types'
 */
    const activityTypesForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: activityTypes.url(options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\GradeSettingsController::activityTypes
 * @see app/Http/Controllers/GradeSettingsController.php:169
 * @route '/grade-settings/activity-types'
 */
        activityTypesForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: activityTypes.url(options),
            method: 'post',
        })
    
    activityTypes.form = activityTypesForm
/**
* @see \App\Http\Controllers\GradeSettingsController::reset
 * @see app/Http/Controllers/GradeSettingsController.php:229
 * @route '/grade-settings/reset'
 */
export const reset = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: reset.url(options),
    method: 'post',
})

reset.definition = {
    methods: ["post"],
    url: '/grade-settings/reset',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\GradeSettingsController::reset
 * @see app/Http/Controllers/GradeSettingsController.php:229
 * @route '/grade-settings/reset'
 */
reset.url = (options?: RouteQueryOptions) => {
    return reset.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\GradeSettingsController::reset
 * @see app/Http/Controllers/GradeSettingsController.php:229
 * @route '/grade-settings/reset'
 */
reset.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: reset.url(options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\GradeSettingsController::reset
 * @see app/Http/Controllers/GradeSettingsController.php:229
 * @route '/grade-settings/reset'
 */
    const resetForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: reset.url(options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\GradeSettingsController::reset
 * @see app/Http/Controllers/GradeSettingsController.php:229
 * @route '/grade-settings/reset'
 */
        resetForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: reset.url(options),
            method: 'post',
        })
    
    reset.form = resetForm
/**
* @see \App\Http\Controllers\GradeSettingsController::deleteCourse
 * @see app/Http/Controllers/GradeSettingsController.php:272
 * @route '/grade-settings/course'
 */
export const deleteCourse = (options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: deleteCourse.url(options),
    method: 'delete',
})

deleteCourse.definition = {
    methods: ["delete"],
    url: '/grade-settings/course',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\GradeSettingsController::deleteCourse
 * @see app/Http/Controllers/GradeSettingsController.php:272
 * @route '/grade-settings/course'
 */
deleteCourse.url = (options?: RouteQueryOptions) => {
    return deleteCourse.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\GradeSettingsController::deleteCourse
 * @see app/Http/Controllers/GradeSettingsController.php:272
 * @route '/grade-settings/course'
 */
deleteCourse.delete = (options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: deleteCourse.url(options),
    method: 'delete',
})

    /**
* @see \App\Http\Controllers\GradeSettingsController::deleteCourse
 * @see app/Http/Controllers/GradeSettingsController.php:272
 * @route '/grade-settings/course'
 */
    const deleteCourseForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: deleteCourse.url({
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'DELETE',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\GradeSettingsController::deleteCourse
 * @see app/Http/Controllers/GradeSettingsController.php:272
 * @route '/grade-settings/course'
 */
        deleteCourseForm.delete = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: deleteCourse.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'DELETE',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    deleteCourse.form = deleteCourseForm
const gradeSettings = {
    index: Object.assign(index, index),
moduleComponents: Object.assign(moduleComponents, moduleComponents),
activityTypes: Object.assign(activityTypes, activityTypes),
reset: Object.assign(reset, reset),
deleteCourse: Object.assign(deleteCourse, deleteCourse),
}

export default gradeSettings