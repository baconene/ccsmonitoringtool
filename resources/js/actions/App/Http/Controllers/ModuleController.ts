import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\ModuleController::store
 * @see app/Http/Controllers/ModuleController.php:77
 * @route '/courses/{course}/modules'
 */
export const store = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/courses/{course}/modules',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\ModuleController::store
 * @see app/Http/Controllers/ModuleController.php:77
 * @route '/courses/{course}/modules'
 */
store.url = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { course: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { course: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    course: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        course: typeof args.course === 'object'
                ? args.course.id
                : args.course,
                }

    return store.definition.url
            .replace('{course}', parsedArgs.course.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ModuleController::store
 * @see app/Http/Controllers/ModuleController.php:77
 * @route '/courses/{course}/modules'
 */
store.post = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\ModuleController::store
 * @see app/Http/Controllers/ModuleController.php:77
 * @route '/courses/{course}/modules'
 */
    const storeForm = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: store.url(args, options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\ModuleController::store
 * @see app/Http/Controllers/ModuleController.php:77
 * @route '/courses/{course}/modules'
 */
        storeForm.post = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: store.url(args, options),
            method: 'post',
        })
    
    store.form = storeForm
/**
* @see \App\Http\Controllers\ModuleController::update
 * @see app/Http/Controllers/ModuleController.php:120
 * @route '/modules/{module}'
 */
export const update = (args: { module: number | { id: number } } | [module: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/modules/{module}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\ModuleController::update
 * @see app/Http/Controllers/ModuleController.php:120
 * @route '/modules/{module}'
 */
update.url = (args: { module: number | { id: number } } | [module: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { module: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { module: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    module: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        module: typeof args.module === 'object'
                ? args.module.id
                : args.module,
                }

    return update.definition.url
            .replace('{module}', parsedArgs.module.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ModuleController::update
 * @see app/Http/Controllers/ModuleController.php:120
 * @route '/modules/{module}'
 */
update.put = (args: { module: number | { id: number } } | [module: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

    /**
* @see \App\Http\Controllers\ModuleController::update
 * @see app/Http/Controllers/ModuleController.php:120
 * @route '/modules/{module}'
 */
    const updateForm = (args: { module: number | { id: number } } | [module: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: update.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'PUT',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\ModuleController::update
 * @see app/Http/Controllers/ModuleController.php:120
 * @route '/modules/{module}'
 */
        updateForm.put = (args: { module: number | { id: number } } | [module: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: update.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'PUT',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    update.form = updateForm
/**
* @see \App\Http\Controllers\ModuleController::destroy
 * @see app/Http/Controllers/ModuleController.php:140
 * @route '/modules/{module}'
 */
export const destroy = (args: { module: number | { id: number } } | [module: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/modules/{module}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\ModuleController::destroy
 * @see app/Http/Controllers/ModuleController.php:140
 * @route '/modules/{module}'
 */
destroy.url = (args: { module: number | { id: number } } | [module: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { module: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { module: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    module: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        module: typeof args.module === 'object'
                ? args.module.id
                : args.module,
                }

    return destroy.definition.url
            .replace('{module}', parsedArgs.module.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ModuleController::destroy
 * @see app/Http/Controllers/ModuleController.php:140
 * @route '/modules/{module}'
 */
destroy.delete = (args: { module: number | { id: number } } | [module: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

    /**
* @see \App\Http\Controllers\ModuleController::destroy
 * @see app/Http/Controllers/ModuleController.php:140
 * @route '/modules/{module}'
 */
    const destroyForm = (args: { module: number | { id: number } } | [module: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: destroy.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'DELETE',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\ModuleController::destroy
 * @see app/Http/Controllers/ModuleController.php:140
 * @route '/modules/{module}'
 */
        destroyForm.delete = (args: { module: number | { id: number } } | [module: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: destroy.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'DELETE',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    destroy.form = destroyForm
/**
* @see \App\Http\Controllers\ModuleController::index
 * @see app/Http/Controllers/ModuleController.php:14
 * @route '/modules/{module}/lessons'
 */
export const index = (args: { module: number | { id: number } } | [module: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(args, options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/modules/{module}/lessons',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ModuleController::index
 * @see app/Http/Controllers/ModuleController.php:14
 * @route '/modules/{module}/lessons'
 */
index.url = (args: { module: number | { id: number } } | [module: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { module: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { module: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    module: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        module: typeof args.module === 'object'
                ? args.module.id
                : args.module,
                }

    return index.definition.url
            .replace('{module}', parsedArgs.module.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ModuleController::index
 * @see app/Http/Controllers/ModuleController.php:14
 * @route '/modules/{module}/lessons'
 */
index.get = (args: { module: number | { id: number } } | [module: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\ModuleController::index
 * @see app/Http/Controllers/ModuleController.php:14
 * @route '/modules/{module}/lessons'
 */
index.head = (args: { module: number | { id: number } } | [module: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\ModuleController::index
 * @see app/Http/Controllers/ModuleController.php:14
 * @route '/modules/{module}/lessons'
 */
    const indexForm = (args: { module: number | { id: number } } | [module: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: index.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\ModuleController::index
 * @see app/Http/Controllers/ModuleController.php:14
 * @route '/modules/{module}/lessons'
 */
        indexForm.get = (args: { module: number | { id: number } } | [module: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: index.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\ModuleController::index
 * @see app/Http/Controllers/ModuleController.php:14
 * @route '/modules/{module}/lessons'
 */
        indexForm.head = (args: { module: number | { id: number } } | [module: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: index.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    index.form = indexForm
/**
* @see \App\Http\Controllers\ModuleController::addActivities
 * @see app/Http/Controllers/ModuleController.php:151
 * @route '/modules/{module}/activities'
 */
export const addActivities = (args: { module: number | { id: number } } | [module: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: addActivities.url(args, options),
    method: 'post',
})

addActivities.definition = {
    methods: ["post"],
    url: '/modules/{module}/activities',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\ModuleController::addActivities
 * @see app/Http/Controllers/ModuleController.php:151
 * @route '/modules/{module}/activities'
 */
addActivities.url = (args: { module: number | { id: number } } | [module: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { module: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { module: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    module: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        module: typeof args.module === 'object'
                ? args.module.id
                : args.module,
                }

    return addActivities.definition.url
            .replace('{module}', parsedArgs.module.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ModuleController::addActivities
 * @see app/Http/Controllers/ModuleController.php:151
 * @route '/modules/{module}/activities'
 */
addActivities.post = (args: { module: number | { id: number } } | [module: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: addActivities.url(args, options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\ModuleController::addActivities
 * @see app/Http/Controllers/ModuleController.php:151
 * @route '/modules/{module}/activities'
 */
    const addActivitiesForm = (args: { module: number | { id: number } } | [module: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: addActivities.url(args, options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\ModuleController::addActivities
 * @see app/Http/Controllers/ModuleController.php:151
 * @route '/modules/{module}/activities'
 */
        addActivitiesForm.post = (args: { module: number | { id: number } } | [module: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: addActivities.url(args, options),
            method: 'post',
        })
    
    addActivities.form = addActivitiesForm
/**
* @see \App\Http\Controllers\ModuleController::removeActivity
 * @see app/Http/Controllers/ModuleController.php:179
 * @route '/modules/{module}/activities/{activity}'
 */
export const removeActivity = (args: { module: number | { id: number }, activity: string | number } | [module: number | { id: number }, activity: string | number ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: removeActivity.url(args, options),
    method: 'delete',
})

removeActivity.definition = {
    methods: ["delete"],
    url: '/modules/{module}/activities/{activity}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\ModuleController::removeActivity
 * @see app/Http/Controllers/ModuleController.php:179
 * @route '/modules/{module}/activities/{activity}'
 */
removeActivity.url = (args: { module: number | { id: number }, activity: string | number } | [module: number | { id: number }, activity: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
                    module: args[0],
                    activity: args[1],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        module: typeof args.module === 'object'
                ? args.module.id
                : args.module,
                                activity: args.activity,
                }

    return removeActivity.definition.url
            .replace('{module}', parsedArgs.module.toString())
            .replace('{activity}', parsedArgs.activity.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ModuleController::removeActivity
 * @see app/Http/Controllers/ModuleController.php:179
 * @route '/modules/{module}/activities/{activity}'
 */
removeActivity.delete = (args: { module: number | { id: number }, activity: string | number } | [module: number | { id: number }, activity: string | number ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: removeActivity.url(args, options),
    method: 'delete',
})

    /**
* @see \App\Http\Controllers\ModuleController::removeActivity
 * @see app/Http/Controllers/ModuleController.php:179
 * @route '/modules/{module}/activities/{activity}'
 */
    const removeActivityForm = (args: { module: number | { id: number }, activity: string | number } | [module: number | { id: number }, activity: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: removeActivity.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'DELETE',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\ModuleController::removeActivity
 * @see app/Http/Controllers/ModuleController.php:179
 * @route '/modules/{module}/activities/{activity}'
 */
        removeActivityForm.delete = (args: { module: number | { id: number }, activity: string | number } | [module: number | { id: number }, activity: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: removeActivity.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'DELETE',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    removeActivity.form = removeActivityForm
/**
* @see \App\Http\Controllers\ModuleController::uploadDocuments
 * @see app/Http/Controllers/ModuleController.php:189
 * @route '/modules/{module}/documents'
 */
export const uploadDocuments = (args: { module: number | { id: number } } | [module: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: uploadDocuments.url(args, options),
    method: 'post',
})

uploadDocuments.definition = {
    methods: ["post"],
    url: '/modules/{module}/documents',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\ModuleController::uploadDocuments
 * @see app/Http/Controllers/ModuleController.php:189
 * @route '/modules/{module}/documents'
 */
uploadDocuments.url = (args: { module: number | { id: number } } | [module: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { module: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { module: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    module: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        module: typeof args.module === 'object'
                ? args.module.id
                : args.module,
                }

    return uploadDocuments.definition.url
            .replace('{module}', parsedArgs.module.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ModuleController::uploadDocuments
 * @see app/Http/Controllers/ModuleController.php:189
 * @route '/modules/{module}/documents'
 */
uploadDocuments.post = (args: { module: number | { id: number } } | [module: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: uploadDocuments.url(args, options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\ModuleController::uploadDocuments
 * @see app/Http/Controllers/ModuleController.php:189
 * @route '/modules/{module}/documents'
 */
    const uploadDocumentsForm = (args: { module: number | { id: number } } | [module: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: uploadDocuments.url(args, options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\ModuleController::uploadDocuments
 * @see app/Http/Controllers/ModuleController.php:189
 * @route '/modules/{module}/documents'
 */
        uploadDocumentsForm.post = (args: { module: number | { id: number } } | [module: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: uploadDocuments.url(args, options),
            method: 'post',
        })
    
    uploadDocuments.form = uploadDocumentsForm
const ModuleController = { store, update, destroy, index, addActivities, removeActivity, uploadDocuments }

export default ModuleController