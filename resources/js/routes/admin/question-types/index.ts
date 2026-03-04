import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\AdminConfigurationController::store
 * @see app/Http/Controllers/AdminConfigurationController.php:169
 * @route '/admin/question-types'
 */
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/admin/question-types',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\AdminConfigurationController::store
 * @see app/Http/Controllers/AdminConfigurationController.php:169
 * @route '/admin/question-types'
 */
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\AdminConfigurationController::store
 * @see app/Http/Controllers/AdminConfigurationController.php:169
 * @route '/admin/question-types'
 */
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\AdminConfigurationController::store
 * @see app/Http/Controllers/AdminConfigurationController.php:169
 * @route '/admin/question-types'
 */
    const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: store.url(options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\AdminConfigurationController::store
 * @see app/Http/Controllers/AdminConfigurationController.php:169
 * @route '/admin/question-types'
 */
        storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: store.url(options),
            method: 'post',
        })
    
    store.form = storeForm
/**
* @see \App\Http\Controllers\AdminConfigurationController::update
 * @see app/Http/Controllers/AdminConfigurationController.php:191
 * @route '/admin/question-types/{questionType}'
 */
export const update = (args: { questionType: number | { id: number } } | [questionType: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/admin/question-types/{questionType}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\AdminConfigurationController::update
 * @see app/Http/Controllers/AdminConfigurationController.php:191
 * @route '/admin/question-types/{questionType}'
 */
update.url = (args: { questionType: number | { id: number } } | [questionType: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return update.definition.url
            .replace('{questionType}', parsedArgs.questionType.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\AdminConfigurationController::update
 * @see app/Http/Controllers/AdminConfigurationController.php:191
 * @route '/admin/question-types/{questionType}'
 */
update.put = (args: { questionType: number | { id: number } } | [questionType: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

    /**
* @see \App\Http\Controllers\AdminConfigurationController::update
 * @see app/Http/Controllers/AdminConfigurationController.php:191
 * @route '/admin/question-types/{questionType}'
 */
    const updateForm = (args: { questionType: number | { id: number } } | [questionType: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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
 * @see app/Http/Controllers/AdminConfigurationController.php:191
 * @route '/admin/question-types/{questionType}'
 */
        updateForm.put = (args: { questionType: number | { id: number } } | [questionType: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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
 * @see app/Http/Controllers/AdminConfigurationController.php:213
 * @route '/admin/question-types/{questionType}'
 */
export const destroy = (args: { questionType: number | { id: number } } | [questionType: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/admin/question-types/{questionType}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\AdminConfigurationController::destroy
 * @see app/Http/Controllers/AdminConfigurationController.php:213
 * @route '/admin/question-types/{questionType}'
 */
destroy.url = (args: { questionType: number | { id: number } } | [questionType: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return destroy.definition.url
            .replace('{questionType}', parsedArgs.questionType.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\AdminConfigurationController::destroy
 * @see app/Http/Controllers/AdminConfigurationController.php:213
 * @route '/admin/question-types/{questionType}'
 */
destroy.delete = (args: { questionType: number | { id: number } } | [questionType: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

    /**
* @see \App\Http\Controllers\AdminConfigurationController::destroy
 * @see app/Http/Controllers/AdminConfigurationController.php:213
 * @route '/admin/question-types/{questionType}'
 */
    const destroyForm = (args: { questionType: number | { id: number } } | [questionType: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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
 * @see app/Http/Controllers/AdminConfigurationController.php:213
 * @route '/admin/question-types/{questionType}'
 */
        destroyForm.delete = (args: { questionType: number | { id: number } } | [questionType: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: destroy.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'DELETE',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    destroy.form = destroyForm
const questionTypes = {
    store: Object.assign(store, store),
update: Object.assign(update, update),
destroy: Object.assign(destroy, destroy),
}

export default questionTypes