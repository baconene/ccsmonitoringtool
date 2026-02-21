import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Instructor\SkillManagementController::index
 * @see app/Http/Controllers/Instructor/SkillManagementController.php:16
 * @route '/modules/{module}/skills'
 */
export const index = (args: { module: number | { id: number } } | [module: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(args, options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/modules/{module}/skills',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Instructor\SkillManagementController::index
 * @see app/Http/Controllers/Instructor/SkillManagementController.php:16
 * @route '/modules/{module}/skills'
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
* @see \App\Http\Controllers\Instructor\SkillManagementController::index
 * @see app/Http/Controllers/Instructor/SkillManagementController.php:16
 * @route '/modules/{module}/skills'
 */
index.get = (args: { module: number | { id: number } } | [module: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Instructor\SkillManagementController::index
 * @see app/Http/Controllers/Instructor/SkillManagementController.php:16
 * @route '/modules/{module}/skills'
 */
index.head = (args: { module: number | { id: number } } | [module: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\Instructor\SkillManagementController::index
 * @see app/Http/Controllers/Instructor/SkillManagementController.php:16
 * @route '/modules/{module}/skills'
 */
    const indexForm = (args: { module: number | { id: number } } | [module: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: index.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\Instructor\SkillManagementController::index
 * @see app/Http/Controllers/Instructor/SkillManagementController.php:16
 * @route '/modules/{module}/skills'
 */
        indexForm.get = (args: { module: number | { id: number } } | [module: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: index.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\Instructor\SkillManagementController::index
 * @see app/Http/Controllers/Instructor/SkillManagementController.php:16
 * @route '/modules/{module}/skills'
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
* @see \App\Http\Controllers\Instructor\SkillManagementController::store
 * @see app/Http/Controllers/Instructor/SkillManagementController.php:45
 * @route '/modules/{module}/skills'
 */
export const store = (args: { module: number | { id: number } } | [module: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/modules/{module}/skills',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Instructor\SkillManagementController::store
 * @see app/Http/Controllers/Instructor/SkillManagementController.php:45
 * @route '/modules/{module}/skills'
 */
store.url = (args: { module: number | { id: number } } | [module: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return store.definition.url
            .replace('{module}', parsedArgs.module.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Instructor\SkillManagementController::store
 * @see app/Http/Controllers/Instructor/SkillManagementController.php:45
 * @route '/modules/{module}/skills'
 */
store.post = (args: { module: number | { id: number } } | [module: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\Instructor\SkillManagementController::store
 * @see app/Http/Controllers/Instructor/SkillManagementController.php:45
 * @route '/modules/{module}/skills'
 */
    const storeForm = (args: { module: number | { id: number } } | [module: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: store.url(args, options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\Instructor\SkillManagementController::store
 * @see app/Http/Controllers/Instructor/SkillManagementController.php:45
 * @route '/modules/{module}/skills'
 */
        storeForm.post = (args: { module: number | { id: number } } | [module: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: store.url(args, options),
            method: 'post',
        })
    
    store.form = storeForm
/**
* @see \App\Http\Controllers\Instructor\SkillManagementController::update
 * @see app/Http/Controllers/Instructor/SkillManagementController.php:73
 * @route '/skills/{skill}'
 */
export const update = (args: { skill: number | { id: number } } | [skill: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/skills/{skill}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\Instructor\SkillManagementController::update
 * @see app/Http/Controllers/Instructor/SkillManagementController.php:73
 * @route '/skills/{skill}'
 */
update.url = (args: { skill: number | { id: number } } | [skill: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { skill: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { skill: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    skill: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        skill: typeof args.skill === 'object'
                ? args.skill.id
                : args.skill,
                }

    return update.definition.url
            .replace('{skill}', parsedArgs.skill.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Instructor\SkillManagementController::update
 * @see app/Http/Controllers/Instructor/SkillManagementController.php:73
 * @route '/skills/{skill}'
 */
update.put = (args: { skill: number | { id: number } } | [skill: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

    /**
* @see \App\Http\Controllers\Instructor\SkillManagementController::update
 * @see app/Http/Controllers/Instructor/SkillManagementController.php:73
 * @route '/skills/{skill}'
 */
    const updateForm = (args: { skill: number | { id: number } } | [skill: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: update.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'PUT',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\Instructor\SkillManagementController::update
 * @see app/Http/Controllers/Instructor/SkillManagementController.php:73
 * @route '/skills/{skill}'
 */
        updateForm.put = (args: { skill: number | { id: number } } | [skill: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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
* @see \App\Http\Controllers\Instructor\SkillManagementController::destroy
 * @see app/Http/Controllers/Instructor/SkillManagementController.php:99
 * @route '/skills/{skill}'
 */
export const destroy = (args: { skill: number | { id: number } } | [skill: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/skills/{skill}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Instructor\SkillManagementController::destroy
 * @see app/Http/Controllers/Instructor/SkillManagementController.php:99
 * @route '/skills/{skill}'
 */
destroy.url = (args: { skill: number | { id: number } } | [skill: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { skill: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { skill: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    skill: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        skill: typeof args.skill === 'object'
                ? args.skill.id
                : args.skill,
                }

    return destroy.definition.url
            .replace('{skill}', parsedArgs.skill.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Instructor\SkillManagementController::destroy
 * @see app/Http/Controllers/Instructor/SkillManagementController.php:99
 * @route '/skills/{skill}'
 */
destroy.delete = (args: { skill: number | { id: number } } | [skill: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

    /**
* @see \App\Http\Controllers\Instructor\SkillManagementController::destroy
 * @see app/Http/Controllers/Instructor/SkillManagementController.php:99
 * @route '/skills/{skill}'
 */
    const destroyForm = (args: { skill: number | { id: number } } | [skill: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: destroy.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'DELETE',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\Instructor\SkillManagementController::destroy
 * @see app/Http/Controllers/Instructor/SkillManagementController.php:99
 * @route '/skills/{skill}'
 */
        destroyForm.delete = (args: { skill: number | { id: number } } | [skill: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: destroy.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'DELETE',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    destroy.form = destroyForm
const SkillManagementController = { index, store, update, destroy }

export default SkillManagementController