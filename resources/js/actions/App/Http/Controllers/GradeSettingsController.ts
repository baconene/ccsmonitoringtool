import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../wayfinder'
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
* @see \App\Http\Controllers\GradeSettingsController::updateModuleComponents
 * @see app/Http/Controllers/GradeSettingsController.php:104
 * @route '/grade-settings/module-components'
 */
export const updateModuleComponents = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: updateModuleComponents.url(options),
    method: 'post',
})

updateModuleComponents.definition = {
    methods: ["post"],
    url: '/grade-settings/module-components',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\GradeSettingsController::updateModuleComponents
 * @see app/Http/Controllers/GradeSettingsController.php:104
 * @route '/grade-settings/module-components'
 */
updateModuleComponents.url = (options?: RouteQueryOptions) => {
    return updateModuleComponents.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\GradeSettingsController::updateModuleComponents
 * @see app/Http/Controllers/GradeSettingsController.php:104
 * @route '/grade-settings/module-components'
 */
updateModuleComponents.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: updateModuleComponents.url(options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\GradeSettingsController::updateModuleComponents
 * @see app/Http/Controllers/GradeSettingsController.php:104
 * @route '/grade-settings/module-components'
 */
    const updateModuleComponentsForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: updateModuleComponents.url(options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\GradeSettingsController::updateModuleComponents
 * @see app/Http/Controllers/GradeSettingsController.php:104
 * @route '/grade-settings/module-components'
 */
        updateModuleComponentsForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: updateModuleComponents.url(options),
            method: 'post',
        })
    
    updateModuleComponents.form = updateModuleComponentsForm
/**
* @see \App\Http\Controllers\GradeSettingsController::updateActivityTypes
 * @see app/Http/Controllers/GradeSettingsController.php:169
 * @route '/grade-settings/activity-types'
 */
export const updateActivityTypes = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: updateActivityTypes.url(options),
    method: 'post',
})

updateActivityTypes.definition = {
    methods: ["post"],
    url: '/grade-settings/activity-types',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\GradeSettingsController::updateActivityTypes
 * @see app/Http/Controllers/GradeSettingsController.php:169
 * @route '/grade-settings/activity-types'
 */
updateActivityTypes.url = (options?: RouteQueryOptions) => {
    return updateActivityTypes.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\GradeSettingsController::updateActivityTypes
 * @see app/Http/Controllers/GradeSettingsController.php:169
 * @route '/grade-settings/activity-types'
 */
updateActivityTypes.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: updateActivityTypes.url(options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\GradeSettingsController::updateActivityTypes
 * @see app/Http/Controllers/GradeSettingsController.php:169
 * @route '/grade-settings/activity-types'
 */
    const updateActivityTypesForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: updateActivityTypes.url(options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\GradeSettingsController::updateActivityTypes
 * @see app/Http/Controllers/GradeSettingsController.php:169
 * @route '/grade-settings/activity-types'
 */
        updateActivityTypesForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: updateActivityTypes.url(options),
            method: 'post',
        })
    
    updateActivityTypes.form = updateActivityTypesForm
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
* @see \App\Http\Controllers\GradeSettingsController::deleteCourseSettings
 * @see app/Http/Controllers/GradeSettingsController.php:272
 * @route '/grade-settings/course'
 */
export const deleteCourseSettings = (options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: deleteCourseSettings.url(options),
    method: 'delete',
})

deleteCourseSettings.definition = {
    methods: ["delete"],
    url: '/grade-settings/course',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\GradeSettingsController::deleteCourseSettings
 * @see app/Http/Controllers/GradeSettingsController.php:272
 * @route '/grade-settings/course'
 */
deleteCourseSettings.url = (options?: RouteQueryOptions) => {
    return deleteCourseSettings.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\GradeSettingsController::deleteCourseSettings
 * @see app/Http/Controllers/GradeSettingsController.php:272
 * @route '/grade-settings/course'
 */
deleteCourseSettings.delete = (options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: deleteCourseSettings.url(options),
    method: 'delete',
})

    /**
* @see \App\Http\Controllers\GradeSettingsController::deleteCourseSettings
 * @see app/Http/Controllers/GradeSettingsController.php:272
 * @route '/grade-settings/course'
 */
    const deleteCourseSettingsForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: deleteCourseSettings.url({
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'DELETE',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\GradeSettingsController::deleteCourseSettings
 * @see app/Http/Controllers/GradeSettingsController.php:272
 * @route '/grade-settings/course'
 */
        deleteCourseSettingsForm.delete = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: deleteCourseSettings.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'DELETE',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    deleteCourseSettings.form = deleteCourseSettingsForm
const GradeSettingsController = { index, updateModuleComponents, updateActivityTypes, reset, deleteCourseSettings }

export default GradeSettingsController