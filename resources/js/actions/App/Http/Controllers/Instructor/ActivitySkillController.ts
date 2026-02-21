import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Instructor\ActivitySkillController::index
 * @see app/Http/Controllers/Instructor/ActivitySkillController.php:16
 * @route '/activities/{activity}/skills'
 */
export const index = (args: { activity: number | { id: number } } | [activity: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(args, options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/activities/{activity}/skills',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Instructor\ActivitySkillController::index
 * @see app/Http/Controllers/Instructor/ActivitySkillController.php:16
 * @route '/activities/{activity}/skills'
 */
index.url = (args: { activity: number | { id: number } } | [activity: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { activity: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { activity: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    activity: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        activity: typeof args.activity === 'object'
                ? args.activity.id
                : args.activity,
                }

    return index.definition.url
            .replace('{activity}', parsedArgs.activity.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Instructor\ActivitySkillController::index
 * @see app/Http/Controllers/Instructor/ActivitySkillController.php:16
 * @route '/activities/{activity}/skills'
 */
index.get = (args: { activity: number | { id: number } } | [activity: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Instructor\ActivitySkillController::index
 * @see app/Http/Controllers/Instructor/ActivitySkillController.php:16
 * @route '/activities/{activity}/skills'
 */
index.head = (args: { activity: number | { id: number } } | [activity: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\Instructor\ActivitySkillController::index
 * @see app/Http/Controllers/Instructor/ActivitySkillController.php:16
 * @route '/activities/{activity}/skills'
 */
    const indexForm = (args: { activity: number | { id: number } } | [activity: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: index.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\Instructor\ActivitySkillController::index
 * @see app/Http/Controllers/Instructor/ActivitySkillController.php:16
 * @route '/activities/{activity}/skills'
 */
        indexForm.get = (args: { activity: number | { id: number } } | [activity: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: index.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\Instructor\ActivitySkillController::index
 * @see app/Http/Controllers/Instructor/ActivitySkillController.php:16
 * @route '/activities/{activity}/skills'
 */
        indexForm.head = (args: { activity: number | { id: number } } | [activity: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
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
* @see \App\Http\Controllers\Instructor\ActivitySkillController::store
 * @see app/Http/Controllers/Instructor/ActivitySkillController.php:56
 * @route '/activities/{activity}/skills'
 */
export const store = (args: { activity: number | { id: number } } | [activity: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/activities/{activity}/skills',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Instructor\ActivitySkillController::store
 * @see app/Http/Controllers/Instructor/ActivitySkillController.php:56
 * @route '/activities/{activity}/skills'
 */
store.url = (args: { activity: number | { id: number } } | [activity: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { activity: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { activity: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    activity: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        activity: typeof args.activity === 'object'
                ? args.activity.id
                : args.activity,
                }

    return store.definition.url
            .replace('{activity}', parsedArgs.activity.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Instructor\ActivitySkillController::store
 * @see app/Http/Controllers/Instructor/ActivitySkillController.php:56
 * @route '/activities/{activity}/skills'
 */
store.post = (args: { activity: number | { id: number } } | [activity: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\Instructor\ActivitySkillController::store
 * @see app/Http/Controllers/Instructor/ActivitySkillController.php:56
 * @route '/activities/{activity}/skills'
 */
    const storeForm = (args: { activity: number | { id: number } } | [activity: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: store.url(args, options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\Instructor\ActivitySkillController::store
 * @see app/Http/Controllers/Instructor/ActivitySkillController.php:56
 * @route '/activities/{activity}/skills'
 */
        storeForm.post = (args: { activity: number | { id: number } } | [activity: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: store.url(args, options),
            method: 'post',
        })
    
    store.form = storeForm
/**
* @see \App\Http\Controllers\Instructor\ActivitySkillController::update
 * @see app/Http/Controllers/Instructor/ActivitySkillController.php:84
 * @route '/activities/{activity}/skills/{skill}'
 */
export const update = (args: { activity: number | { id: number }, skill: number | { id: number } } | [activity: number | { id: number }, skill: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/activities/{activity}/skills/{skill}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\Instructor\ActivitySkillController::update
 * @see app/Http/Controllers/Instructor/ActivitySkillController.php:84
 * @route '/activities/{activity}/skills/{skill}'
 */
update.url = (args: { activity: number | { id: number }, skill: number | { id: number } } | [activity: number | { id: number }, skill: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
                    activity: args[0],
                    skill: args[1],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        activity: typeof args.activity === 'object'
                ? args.activity.id
                : args.activity,
                                skill: typeof args.skill === 'object'
                ? args.skill.id
                : args.skill,
                }

    return update.definition.url
            .replace('{activity}', parsedArgs.activity.toString())
            .replace('{skill}', parsedArgs.skill.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Instructor\ActivitySkillController::update
 * @see app/Http/Controllers/Instructor/ActivitySkillController.php:84
 * @route '/activities/{activity}/skills/{skill}'
 */
update.put = (args: { activity: number | { id: number }, skill: number | { id: number } } | [activity: number | { id: number }, skill: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

    /**
* @see \App\Http\Controllers\Instructor\ActivitySkillController::update
 * @see app/Http/Controllers/Instructor/ActivitySkillController.php:84
 * @route '/activities/{activity}/skills/{skill}'
 */
    const updateForm = (args: { activity: number | { id: number }, skill: number | { id: number } } | [activity: number | { id: number }, skill: number | { id: number } ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: update.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'PUT',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\Instructor\ActivitySkillController::update
 * @see app/Http/Controllers/Instructor/ActivitySkillController.php:84
 * @route '/activities/{activity}/skills/{skill}'
 */
        updateForm.put = (args: { activity: number | { id: number }, skill: number | { id: number } } | [activity: number | { id: number }, skill: number | { id: number } ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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
* @see \App\Http\Controllers\Instructor\ActivitySkillController::destroy
 * @see app/Http/Controllers/Instructor/ActivitySkillController.php:104
 * @route '/activities/{activity}/skills/{skill}'
 */
export const destroy = (args: { activity: number | { id: number }, skill: number | { id: number } } | [activity: number | { id: number }, skill: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/activities/{activity}/skills/{skill}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Instructor\ActivitySkillController::destroy
 * @see app/Http/Controllers/Instructor/ActivitySkillController.php:104
 * @route '/activities/{activity}/skills/{skill}'
 */
destroy.url = (args: { activity: number | { id: number }, skill: number | { id: number } } | [activity: number | { id: number }, skill: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
                    activity: args[0],
                    skill: args[1],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        activity: typeof args.activity === 'object'
                ? args.activity.id
                : args.activity,
                                skill: typeof args.skill === 'object'
                ? args.skill.id
                : args.skill,
                }

    return destroy.definition.url
            .replace('{activity}', parsedArgs.activity.toString())
            .replace('{skill}', parsedArgs.skill.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Instructor\ActivitySkillController::destroy
 * @see app/Http/Controllers/Instructor/ActivitySkillController.php:104
 * @route '/activities/{activity}/skills/{skill}'
 */
destroy.delete = (args: { activity: number | { id: number }, skill: number | { id: number } } | [activity: number | { id: number }, skill: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

    /**
* @see \App\Http\Controllers\Instructor\ActivitySkillController::destroy
 * @see app/Http/Controllers/Instructor/ActivitySkillController.php:104
 * @route '/activities/{activity}/skills/{skill}'
 */
    const destroyForm = (args: { activity: number | { id: number }, skill: number | { id: number } } | [activity: number | { id: number }, skill: number | { id: number } ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: destroy.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'DELETE',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\Instructor\ActivitySkillController::destroy
 * @see app/Http/Controllers/Instructor/ActivitySkillController.php:104
 * @route '/activities/{activity}/skills/{skill}'
 */
        destroyForm.delete = (args: { activity: number | { id: number }, skill: number | { id: number } } | [activity: number | { id: number }, skill: number | { id: number } ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: destroy.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'DELETE',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    destroy.form = destroyForm
const ActivitySkillController = { index, store, update, destroy }

export default ActivitySkillController