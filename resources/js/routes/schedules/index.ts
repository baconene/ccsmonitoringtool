import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../wayfinder'
/**
* @see \App\Http\Controllers\Api\ScheduleController::destroy
 * @see app/Http/Controllers/Api/ScheduleController.php:309
 * @route '/api/schedules/{schedule}'
 */
export const destroy = (args: { schedule: string | number } | [schedule: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/api/schedules/{schedule}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Api\ScheduleController::destroy
 * @see app/Http/Controllers/Api/ScheduleController.php:309
 * @route '/api/schedules/{schedule}'
 */
destroy.url = (args: { schedule: string | number } | [schedule: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { schedule: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    schedule: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        schedule: args.schedule,
                }

    return destroy.definition.url
            .replace('{schedule}', parsedArgs.schedule.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScheduleController::destroy
 * @see app/Http/Controllers/Api/ScheduleController.php:309
 * @route '/api/schedules/{schedule}'
 */
destroy.delete = (args: { schedule: string | number } | [schedule: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

    /**
* @see \App\Http\Controllers\Api\ScheduleController::destroy
 * @see app/Http/Controllers/Api/ScheduleController.php:309
 * @route '/api/schedules/{schedule}'
 */
    const destroyForm = (args: { schedule: string | number } | [schedule: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: destroy.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'DELETE',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\Api\ScheduleController::destroy
 * @see app/Http/Controllers/Api/ScheduleController.php:309
 * @route '/api/schedules/{schedule}'
 */
        destroyForm.delete = (args: { schedule: string | number } | [schedule: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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
* @see \App\Http\Controllers\Api\ScheduleController::index
 * @see app/Http/Controllers/Api/ScheduleController.php:0
 * @route '/api/schedules'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/api/schedules',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\ScheduleController::index
 * @see app/Http/Controllers/Api/ScheduleController.php:0
 * @route '/api/schedules'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScheduleController::index
 * @see app/Http/Controllers/Api/ScheduleController.php:0
 * @route '/api/schedules'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Api\ScheduleController::index
 * @see app/Http/Controllers/Api/ScheduleController.php:0
 * @route '/api/schedules'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\Api\ScheduleController::index
 * @see app/Http/Controllers/Api/ScheduleController.php:0
 * @route '/api/schedules'
 */
    const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: index.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\Api\ScheduleController::index
 * @see app/Http/Controllers/Api/ScheduleController.php:0
 * @route '/api/schedules'
 */
        indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: index.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\Api\ScheduleController::index
 * @see app/Http/Controllers/Api/ScheduleController.php:0
 * @route '/api/schedules'
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
* @see \App\Http\Controllers\Api\ScheduleController::store
 * @see app/Http/Controllers/Api/ScheduleController.php:177
 * @route '/api/schedules'
 */
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/api/schedules',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\ScheduleController::store
 * @see app/Http/Controllers/Api/ScheduleController.php:177
 * @route '/api/schedules'
 */
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScheduleController::store
 * @see app/Http/Controllers/Api/ScheduleController.php:177
 * @route '/api/schedules'
 */
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\Api\ScheduleController::store
 * @see app/Http/Controllers/Api/ScheduleController.php:177
 * @route '/api/schedules'
 */
    const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: store.url(options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\Api\ScheduleController::store
 * @see app/Http/Controllers/Api/ScheduleController.php:177
 * @route '/api/schedules'
 */
        storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: store.url(options),
            method: 'post',
        })
    
    store.form = storeForm
/**
* @see \App\Http\Controllers\Api\ScheduleController::show
 * @see app/Http/Controllers/Api/ScheduleController.php:334
 * @route '/api/schedules/{schedule}'
 */
export const show = (args: { schedule: string | number } | [schedule: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/api/schedules/{schedule}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\ScheduleController::show
 * @see app/Http/Controllers/Api/ScheduleController.php:334
 * @route '/api/schedules/{schedule}'
 */
show.url = (args: { schedule: string | number } | [schedule: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { schedule: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    schedule: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        schedule: args.schedule,
                }

    return show.definition.url
            .replace('{schedule}', parsedArgs.schedule.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScheduleController::show
 * @see app/Http/Controllers/Api/ScheduleController.php:334
 * @route '/api/schedules/{schedule}'
 */
show.get = (args: { schedule: string | number } | [schedule: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Api\ScheduleController::show
 * @see app/Http/Controllers/Api/ScheduleController.php:334
 * @route '/api/schedules/{schedule}'
 */
show.head = (args: { schedule: string | number } | [schedule: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\Api\ScheduleController::show
 * @see app/Http/Controllers/Api/ScheduleController.php:334
 * @route '/api/schedules/{schedule}'
 */
    const showForm = (args: { schedule: string | number } | [schedule: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: show.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\Api\ScheduleController::show
 * @see app/Http/Controllers/Api/ScheduleController.php:334
 * @route '/api/schedules/{schedule}'
 */
        showForm.get = (args: { schedule: string | number } | [schedule: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: show.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\Api\ScheduleController::show
 * @see app/Http/Controllers/Api/ScheduleController.php:334
 * @route '/api/schedules/{schedule}'
 */
        showForm.head = (args: { schedule: string | number } | [schedule: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: show.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    show.form = showForm
/**
* @see \App\Http\Controllers\Api\ScheduleController::update
 * @see app/Http/Controllers/Api/ScheduleController.php:258
 * @route '/api/schedules/{schedule}'
 */
export const update = (args: { schedule: string | number } | [schedule: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put","patch"],
    url: '/api/schedules/{schedule}',
} satisfies RouteDefinition<["put","patch"]>

/**
* @see \App\Http\Controllers\Api\ScheduleController::update
 * @see app/Http/Controllers/Api/ScheduleController.php:258
 * @route '/api/schedules/{schedule}'
 */
update.url = (args: { schedule: string | number } | [schedule: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { schedule: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    schedule: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        schedule: args.schedule,
                }

    return update.definition.url
            .replace('{schedule}', parsedArgs.schedule.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScheduleController::update
 * @see app/Http/Controllers/Api/ScheduleController.php:258
 * @route '/api/schedules/{schedule}'
 */
update.put = (args: { schedule: string | number } | [schedule: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})
/**
* @see \App\Http\Controllers\Api\ScheduleController::update
 * @see app/Http/Controllers/Api/ScheduleController.php:258
 * @route '/api/schedules/{schedule}'
 */
update.patch = (args: { schedule: string | number } | [schedule: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

    /**
* @see \App\Http\Controllers\Api\ScheduleController::update
 * @see app/Http/Controllers/Api/ScheduleController.php:258
 * @route '/api/schedules/{schedule}'
 */
    const updateForm = (args: { schedule: string | number } | [schedule: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: update.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'PUT',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\Api\ScheduleController::update
 * @see app/Http/Controllers/Api/ScheduleController.php:258
 * @route '/api/schedules/{schedule}'
 */
        updateForm.put = (args: { schedule: string | number } | [schedule: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: update.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'PUT',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
            /**
* @see \App\Http\Controllers\Api\ScheduleController::update
 * @see app/Http/Controllers/Api/ScheduleController.php:258
 * @route '/api/schedules/{schedule}'
 */
        updateForm.patch = (args: { schedule: string | number } | [schedule: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: update.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'PATCH',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    update.form = updateForm
const schedules = {
    destroy: Object.assign(destroy, destroy),
index: Object.assign(index, index),
store: Object.assign(store, store),
show: Object.assign(show, show),
update: Object.assign(update, update),
}

export default schedules