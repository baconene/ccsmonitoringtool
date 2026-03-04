import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\AdminConfigurationController::index
 * @see app/Http/Controllers/AdminConfigurationController.php:17
 * @route '/admin/configuration'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/admin/configuration',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\AdminConfigurationController::index
 * @see app/Http/Controllers/AdminConfigurationController.php:17
 * @route '/admin/configuration'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\AdminConfigurationController::index
 * @see app/Http/Controllers/AdminConfigurationController.php:17
 * @route '/admin/configuration'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\AdminConfigurationController::index
 * @see app/Http/Controllers/AdminConfigurationController.php:17
 * @route '/admin/configuration'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\AdminConfigurationController::index
 * @see app/Http/Controllers/AdminConfigurationController.php:17
 * @route '/admin/configuration'
 */
    const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: index.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\AdminConfigurationController::index
 * @see app/Http/Controllers/AdminConfigurationController.php:17
 * @route '/admin/configuration'
 */
        indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: index.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\AdminConfigurationController::index
 * @see app/Http/Controllers/AdminConfigurationController.php:17
 * @route '/admin/configuration'
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
* @see \App\Http\Controllers\AdminConfigurationController::storeGradeLevel
 * @see app/Http/Controllers/AdminConfigurationController.php:35
 * @route '/admin/grade-levels'
 */
export const storeGradeLevel = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: storeGradeLevel.url(options),
    method: 'post',
})

storeGradeLevel.definition = {
    methods: ["post"],
    url: '/admin/grade-levels',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\AdminConfigurationController::storeGradeLevel
 * @see app/Http/Controllers/AdminConfigurationController.php:35
 * @route '/admin/grade-levels'
 */
storeGradeLevel.url = (options?: RouteQueryOptions) => {
    return storeGradeLevel.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\AdminConfigurationController::storeGradeLevel
 * @see app/Http/Controllers/AdminConfigurationController.php:35
 * @route '/admin/grade-levels'
 */
storeGradeLevel.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: storeGradeLevel.url(options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\AdminConfigurationController::storeGradeLevel
 * @see app/Http/Controllers/AdminConfigurationController.php:35
 * @route '/admin/grade-levels'
 */
    const storeGradeLevelForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: storeGradeLevel.url(options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\AdminConfigurationController::storeGradeLevel
 * @see app/Http/Controllers/AdminConfigurationController.php:35
 * @route '/admin/grade-levels'
 */
        storeGradeLevelForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: storeGradeLevel.url(options),
            method: 'post',
        })
    
    storeGradeLevel.form = storeGradeLevelForm
/**
* @see \App\Http\Controllers\AdminConfigurationController::updateGradeLevel
 * @see app/Http/Controllers/AdminConfigurationController.php:59
 * @route '/admin/grade-levels/{gradeLevel}'
 */
export const updateGradeLevel = (args: { gradeLevel: number | { id: number } } | [gradeLevel: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: updateGradeLevel.url(args, options),
    method: 'put',
})

updateGradeLevel.definition = {
    methods: ["put"],
    url: '/admin/grade-levels/{gradeLevel}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\AdminConfigurationController::updateGradeLevel
 * @see app/Http/Controllers/AdminConfigurationController.php:59
 * @route '/admin/grade-levels/{gradeLevel}'
 */
updateGradeLevel.url = (args: { gradeLevel: number | { id: number } } | [gradeLevel: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { gradeLevel: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { gradeLevel: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    gradeLevel: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        gradeLevel: typeof args.gradeLevel === 'object'
                ? args.gradeLevel.id
                : args.gradeLevel,
                }

    return updateGradeLevel.definition.url
            .replace('{gradeLevel}', parsedArgs.gradeLevel.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\AdminConfigurationController::updateGradeLevel
 * @see app/Http/Controllers/AdminConfigurationController.php:59
 * @route '/admin/grade-levels/{gradeLevel}'
 */
updateGradeLevel.put = (args: { gradeLevel: number | { id: number } } | [gradeLevel: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: updateGradeLevel.url(args, options),
    method: 'put',
})

    /**
* @see \App\Http\Controllers\AdminConfigurationController::updateGradeLevel
 * @see app/Http/Controllers/AdminConfigurationController.php:59
 * @route '/admin/grade-levels/{gradeLevel}'
 */
    const updateGradeLevelForm = (args: { gradeLevel: number | { id: number } } | [gradeLevel: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: updateGradeLevel.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'PUT',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\AdminConfigurationController::updateGradeLevel
 * @see app/Http/Controllers/AdminConfigurationController.php:59
 * @route '/admin/grade-levels/{gradeLevel}'
 */
        updateGradeLevelForm.put = (args: { gradeLevel: number | { id: number } } | [gradeLevel: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: updateGradeLevel.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'PUT',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    updateGradeLevel.form = updateGradeLevelForm
/**
* @see \App\Http\Controllers\AdminConfigurationController::destroyGradeLevel
 * @see app/Http/Controllers/AdminConfigurationController.php:83
 * @route '/admin/grade-levels/{gradeLevel}'
 */
export const destroyGradeLevel = (args: { gradeLevel: number | { id: number } } | [gradeLevel: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroyGradeLevel.url(args, options),
    method: 'delete',
})

destroyGradeLevel.definition = {
    methods: ["delete"],
    url: '/admin/grade-levels/{gradeLevel}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\AdminConfigurationController::destroyGradeLevel
 * @see app/Http/Controllers/AdminConfigurationController.php:83
 * @route '/admin/grade-levels/{gradeLevel}'
 */
destroyGradeLevel.url = (args: { gradeLevel: number | { id: number } } | [gradeLevel: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { gradeLevel: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { gradeLevel: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    gradeLevel: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        gradeLevel: typeof args.gradeLevel === 'object'
                ? args.gradeLevel.id
                : args.gradeLevel,
                }

    return destroyGradeLevel.definition.url
            .replace('{gradeLevel}', parsedArgs.gradeLevel.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\AdminConfigurationController::destroyGradeLevel
 * @see app/Http/Controllers/AdminConfigurationController.php:83
 * @route '/admin/grade-levels/{gradeLevel}'
 */
destroyGradeLevel.delete = (args: { gradeLevel: number | { id: number } } | [gradeLevel: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroyGradeLevel.url(args, options),
    method: 'delete',
})

    /**
* @see \App\Http\Controllers\AdminConfigurationController::destroyGradeLevel
 * @see app/Http/Controllers/AdminConfigurationController.php:83
 * @route '/admin/grade-levels/{gradeLevel}'
 */
    const destroyGradeLevelForm = (args: { gradeLevel: number | { id: number } } | [gradeLevel: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: destroyGradeLevel.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'DELETE',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\AdminConfigurationController::destroyGradeLevel
 * @see app/Http/Controllers/AdminConfigurationController.php:83
 * @route '/admin/grade-levels/{gradeLevel}'
 */
        destroyGradeLevelForm.delete = (args: { gradeLevel: number | { id: number } } | [gradeLevel: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: destroyGradeLevel.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'DELETE',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    destroyGradeLevel.form = destroyGradeLevelForm
/**
* @see \App\Http\Controllers\AdminConfigurationController::storeActivityType
 * @see app/Http/Controllers/AdminConfigurationController.php:103
 * @route '/admin/activity-types'
 */
export const storeActivityType = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: storeActivityType.url(options),
    method: 'post',
})

storeActivityType.definition = {
    methods: ["post"],
    url: '/admin/activity-types',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\AdminConfigurationController::storeActivityType
 * @see app/Http/Controllers/AdminConfigurationController.php:103
 * @route '/admin/activity-types'
 */
storeActivityType.url = (options?: RouteQueryOptions) => {
    return storeActivityType.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\AdminConfigurationController::storeActivityType
 * @see app/Http/Controllers/AdminConfigurationController.php:103
 * @route '/admin/activity-types'
 */
storeActivityType.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: storeActivityType.url(options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\AdminConfigurationController::storeActivityType
 * @see app/Http/Controllers/AdminConfigurationController.php:103
 * @route '/admin/activity-types'
 */
    const storeActivityTypeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: storeActivityType.url(options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\AdminConfigurationController::storeActivityType
 * @see app/Http/Controllers/AdminConfigurationController.php:103
 * @route '/admin/activity-types'
 */
        storeActivityTypeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: storeActivityType.url(options),
            method: 'post',
        })
    
    storeActivityType.form = storeActivityTypeForm
/**
* @see \App\Http\Controllers\AdminConfigurationController::updateActivityType
 * @see app/Http/Controllers/AdminConfigurationController.php:126
 * @route '/admin/activity-types/{activityType}'
 */
export const updateActivityType = (args: { activityType: number | { id: number } } | [activityType: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: updateActivityType.url(args, options),
    method: 'put',
})

updateActivityType.definition = {
    methods: ["put"],
    url: '/admin/activity-types/{activityType}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\AdminConfigurationController::updateActivityType
 * @see app/Http/Controllers/AdminConfigurationController.php:126
 * @route '/admin/activity-types/{activityType}'
 */
updateActivityType.url = (args: { activityType: number | { id: number } } | [activityType: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { activityType: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { activityType: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    activityType: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        activityType: typeof args.activityType === 'object'
                ? args.activityType.id
                : args.activityType,
                }

    return updateActivityType.definition.url
            .replace('{activityType}', parsedArgs.activityType.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\AdminConfigurationController::updateActivityType
 * @see app/Http/Controllers/AdminConfigurationController.php:126
 * @route '/admin/activity-types/{activityType}'
 */
updateActivityType.put = (args: { activityType: number | { id: number } } | [activityType: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: updateActivityType.url(args, options),
    method: 'put',
})

    /**
* @see \App\Http\Controllers\AdminConfigurationController::updateActivityType
 * @see app/Http/Controllers/AdminConfigurationController.php:126
 * @route '/admin/activity-types/{activityType}'
 */
    const updateActivityTypeForm = (args: { activityType: number | { id: number } } | [activityType: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: updateActivityType.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'PUT',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\AdminConfigurationController::updateActivityType
 * @see app/Http/Controllers/AdminConfigurationController.php:126
 * @route '/admin/activity-types/{activityType}'
 */
        updateActivityTypeForm.put = (args: { activityType: number | { id: number } } | [activityType: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: updateActivityType.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'PUT',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    updateActivityType.form = updateActivityTypeForm
/**
* @see \App\Http\Controllers\AdminConfigurationController::destroyActivityType
 * @see app/Http/Controllers/AdminConfigurationController.php:149
 * @route '/admin/activity-types/{activityType}'
 */
export const destroyActivityType = (args: { activityType: number | { id: number } } | [activityType: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroyActivityType.url(args, options),
    method: 'delete',
})

destroyActivityType.definition = {
    methods: ["delete"],
    url: '/admin/activity-types/{activityType}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\AdminConfigurationController::destroyActivityType
 * @see app/Http/Controllers/AdminConfigurationController.php:149
 * @route '/admin/activity-types/{activityType}'
 */
destroyActivityType.url = (args: { activityType: number | { id: number } } | [activityType: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { activityType: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { activityType: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    activityType: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        activityType: typeof args.activityType === 'object'
                ? args.activityType.id
                : args.activityType,
                }

    return destroyActivityType.definition.url
            .replace('{activityType}', parsedArgs.activityType.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\AdminConfigurationController::destroyActivityType
 * @see app/Http/Controllers/AdminConfigurationController.php:149
 * @route '/admin/activity-types/{activityType}'
 */
destroyActivityType.delete = (args: { activityType: number | { id: number } } | [activityType: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroyActivityType.url(args, options),
    method: 'delete',
})

    /**
* @see \App\Http\Controllers\AdminConfigurationController::destroyActivityType
 * @see app/Http/Controllers/AdminConfigurationController.php:149
 * @route '/admin/activity-types/{activityType}'
 */
    const destroyActivityTypeForm = (args: { activityType: number | { id: number } } | [activityType: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: destroyActivityType.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'DELETE',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\AdminConfigurationController::destroyActivityType
 * @see app/Http/Controllers/AdminConfigurationController.php:149
 * @route '/admin/activity-types/{activityType}'
 */
        destroyActivityTypeForm.delete = (args: { activityType: number | { id: number } } | [activityType: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: destroyActivityType.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'DELETE',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    destroyActivityType.form = destroyActivityTypeForm
/**
* @see \App\Http\Controllers\AdminConfigurationController::storeQuestionType
 * @see app/Http/Controllers/AdminConfigurationController.php:169
 * @route '/admin/question-types'
 */
export const storeQuestionType = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: storeQuestionType.url(options),
    method: 'post',
})

storeQuestionType.definition = {
    methods: ["post"],
    url: '/admin/question-types',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\AdminConfigurationController::storeQuestionType
 * @see app/Http/Controllers/AdminConfigurationController.php:169
 * @route '/admin/question-types'
 */
storeQuestionType.url = (options?: RouteQueryOptions) => {
    return storeQuestionType.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\AdminConfigurationController::storeQuestionType
 * @see app/Http/Controllers/AdminConfigurationController.php:169
 * @route '/admin/question-types'
 */
storeQuestionType.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: storeQuestionType.url(options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\AdminConfigurationController::storeQuestionType
 * @see app/Http/Controllers/AdminConfigurationController.php:169
 * @route '/admin/question-types'
 */
    const storeQuestionTypeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: storeQuestionType.url(options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\AdminConfigurationController::storeQuestionType
 * @see app/Http/Controllers/AdminConfigurationController.php:169
 * @route '/admin/question-types'
 */
        storeQuestionTypeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: storeQuestionType.url(options),
            method: 'post',
        })
    
    storeQuestionType.form = storeQuestionTypeForm
/**
* @see \App\Http\Controllers\AdminConfigurationController::updateQuestionType
 * @see app/Http/Controllers/AdminConfigurationController.php:191
 * @route '/admin/question-types/{questionType}'
 */
export const updateQuestionType = (args: { questionType: number | { id: number } } | [questionType: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: updateQuestionType.url(args, options),
    method: 'put',
})

updateQuestionType.definition = {
    methods: ["put"],
    url: '/admin/question-types/{questionType}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\AdminConfigurationController::updateQuestionType
 * @see app/Http/Controllers/AdminConfigurationController.php:191
 * @route '/admin/question-types/{questionType}'
 */
updateQuestionType.url = (args: { questionType: number | { id: number } } | [questionType: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { questionType: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { questionType: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    questionType: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        questionType: typeof args.questionType === 'object'
                ? args.questionType.id
                : args.questionType,
                }

    return updateQuestionType.definition.url
            .replace('{questionType}', parsedArgs.questionType.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\AdminConfigurationController::updateQuestionType
 * @see app/Http/Controllers/AdminConfigurationController.php:191
 * @route '/admin/question-types/{questionType}'
 */
updateQuestionType.put = (args: { questionType: number | { id: number } } | [questionType: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: updateQuestionType.url(args, options),
    method: 'put',
})

    /**
* @see \App\Http\Controllers\AdminConfigurationController::updateQuestionType
 * @see app/Http/Controllers/AdminConfigurationController.php:191
 * @route '/admin/question-types/{questionType}'
 */
    const updateQuestionTypeForm = (args: { questionType: number | { id: number } } | [questionType: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: updateQuestionType.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'PUT',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\AdminConfigurationController::updateQuestionType
 * @see app/Http/Controllers/AdminConfigurationController.php:191
 * @route '/admin/question-types/{questionType}'
 */
        updateQuestionTypeForm.put = (args: { questionType: number | { id: number } } | [questionType: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: updateQuestionType.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'PUT',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    updateQuestionType.form = updateQuestionTypeForm
/**
* @see \App\Http\Controllers\AdminConfigurationController::destroyQuestionType
 * @see app/Http/Controllers/AdminConfigurationController.php:213
 * @route '/admin/question-types/{questionType}'
 */
export const destroyQuestionType = (args: { questionType: number | { id: number } } | [questionType: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroyQuestionType.url(args, options),
    method: 'delete',
})

destroyQuestionType.definition = {
    methods: ["delete"],
    url: '/admin/question-types/{questionType}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\AdminConfigurationController::destroyQuestionType
 * @see app/Http/Controllers/AdminConfigurationController.php:213
 * @route '/admin/question-types/{questionType}'
 */
destroyQuestionType.url = (args: { questionType: number | { id: number } } | [questionType: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { questionType: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { questionType: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    questionType: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        questionType: typeof args.questionType === 'object'
                ? args.questionType.id
                : args.questionType,
                }

    return destroyQuestionType.definition.url
            .replace('{questionType}', parsedArgs.questionType.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\AdminConfigurationController::destroyQuestionType
 * @see app/Http/Controllers/AdminConfigurationController.php:213
 * @route '/admin/question-types/{questionType}'
 */
destroyQuestionType.delete = (args: { questionType: number | { id: number } } | [questionType: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroyQuestionType.url(args, options),
    method: 'delete',
})

    /**
* @see \App\Http\Controllers\AdminConfigurationController::destroyQuestionType
 * @see app/Http/Controllers/AdminConfigurationController.php:213
 * @route '/admin/question-types/{questionType}'
 */
    const destroyQuestionTypeForm = (args: { questionType: number | { id: number } } | [questionType: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: destroyQuestionType.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'DELETE',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\AdminConfigurationController::destroyQuestionType
 * @see app/Http/Controllers/AdminConfigurationController.php:213
 * @route '/admin/question-types/{questionType}'
 */
        destroyQuestionTypeForm.delete = (args: { questionType: number | { id: number } } | [questionType: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: destroyQuestionType.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'DELETE',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    destroyQuestionType.form = destroyQuestionTypeForm
const AdminConfigurationController = { index, storeGradeLevel, updateGradeLevel, destroyGradeLevel, storeActivityType, updateActivityType, destroyActivityType, storeQuestionType, updateQuestionType, destroyQuestionType }

export default AdminConfigurationController