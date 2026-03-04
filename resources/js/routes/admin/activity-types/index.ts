import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\AdminConfigurationController::store
 * @see app/Http/Controllers/AdminConfigurationController.php:103
 * @route '/admin/activity-types'
 */
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/admin/activity-types',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\AdminConfigurationController::store
 * @see app/Http/Controllers/AdminConfigurationController.php:103
 * @route '/admin/activity-types'
 */
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\AdminConfigurationController::store
 * @see app/Http/Controllers/AdminConfigurationController.php:103
 * @route '/admin/activity-types'
 */
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\AdminConfigurationController::store
 * @see app/Http/Controllers/AdminConfigurationController.php:103
 * @route '/admin/activity-types'
 */
    const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: store.url(options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\AdminConfigurationController::store
 * @see app/Http/Controllers/AdminConfigurationController.php:103
 * @route '/admin/activity-types'
 */
        storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: store.url(options),
            method: 'post',
        })
    
    store.form = storeForm
/**
* @see \App\Http\Controllers\AdminConfigurationController::update
 * @see app/Http/Controllers/AdminConfigurationController.php:126
 * @route '/admin/activity-types/{activityType}'
 */
export const update = (args: { activityType: number | { id: number } } | [activityType: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/admin/activity-types/{activityType}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\AdminConfigurationController::update
 * @see app/Http/Controllers/AdminConfigurationController.php:126
 * @route '/admin/activity-types/{activityType}'
 */
update.url = (args: { activityType: number | { id: number } } | [activityType: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return update.definition.url
            .replace('{activityType}', parsedArgs.activityType.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\AdminConfigurationController::update
 * @see app/Http/Controllers/AdminConfigurationController.php:126
 * @route '/admin/activity-types/{activityType}'
 */
update.put = (args: { activityType: number | { id: number } } | [activityType: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

    /**
* @see \App\Http\Controllers\AdminConfigurationController::update
 * @see app/Http/Controllers/AdminConfigurationController.php:126
 * @route '/admin/activity-types/{activityType}'
 */
    const updateForm = (args: { activityType: number | { id: number } } | [activityType: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: update.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'PUT',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\AdminConfigurationController::update
 * @see app/Http/Controllers/AdminConfigurationController.php:126
 * @route '/admin/activity-types/{activityType}'
 */
        updateForm.put = (args: { activityType: number | { id: number } } | [activityType: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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
* @see \App\Http\Controllers\AdminConfigurationController::destroy
 * @see app/Http/Controllers/AdminConfigurationController.php:149
 * @route '/admin/activity-types/{activityType}'
 */
export const destroy = (args: { activityType: number | { id: number } } | [activityType: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/admin/activity-types/{activityType}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\AdminConfigurationController::destroy
 * @see app/Http/Controllers/AdminConfigurationController.php:149
 * @route '/admin/activity-types/{activityType}'
 */
destroy.url = (args: { activityType: number | { id: number } } | [activityType: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return destroy.definition.url
            .replace('{activityType}', parsedArgs.activityType.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\AdminConfigurationController::destroy
 * @see app/Http/Controllers/AdminConfigurationController.php:149
 * @route '/admin/activity-types/{activityType}'
 */
destroy.delete = (args: { activityType: number | { id: number } } | [activityType: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

    /**
* @see \App\Http\Controllers\AdminConfigurationController::destroy
 * @see app/Http/Controllers/AdminConfigurationController.php:149
 * @route '/admin/activity-types/{activityType}'
 */
    const destroyForm = (args: { activityType: number | { id: number } } | [activityType: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: destroy.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'DELETE',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\AdminConfigurationController::destroy
 * @see app/Http/Controllers/AdminConfigurationController.php:149
 * @route '/admin/activity-types/{activityType}'
 */
        destroyForm.delete = (args: { activityType: number | { id: number } } | [activityType: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: destroy.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'DELETE',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    destroy.form = destroyForm
const activityTypes = {
    store: Object.assign(store, store),
update: Object.assign(update, update),
destroy: Object.assign(destroy, destroy),
}

export default activityTypes