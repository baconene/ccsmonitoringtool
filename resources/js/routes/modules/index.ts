import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../wayfinder'
/**
* @see \App\Http\Controllers\ModuleController::store
* @see app/Http/Controllers/ModuleController.php:37
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
* @see app/Http/Controllers/ModuleController.php:37
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
* @see app/Http/Controllers/ModuleController.php:37
* @route '/courses/{course}/modules'
*/
store.post = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\ModuleController::store
* @see app/Http/Controllers/ModuleController.php:37
* @route '/courses/{course}/modules'
*/
const storeForm = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\ModuleController::store
* @see app/Http/Controllers/ModuleController.php:37
* @route '/courses/{course}/modules'
*/
storeForm.post = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(args, options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\ModuleController::update
* @see app/Http/Controllers/ModuleController.php:72
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
* @see app/Http/Controllers/ModuleController.php:72
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
* @see app/Http/Controllers/ModuleController.php:72
* @route '/modules/{module}'
*/
update.put = (args: { module: number | { id: number } } | [module: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\ModuleController::update
* @see app/Http/Controllers/ModuleController.php:72
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
* @see app/Http/Controllers/ModuleController.php:72
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
* @see app/Http/Controllers/ModuleController.php:89
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
* @see app/Http/Controllers/ModuleController.php:89
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
* @see app/Http/Controllers/ModuleController.php:89
* @route '/modules/{module}'
*/
destroy.delete = (args: { module: number | { id: number } } | [module: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\ModuleController::destroy
* @see app/Http/Controllers/ModuleController.php:89
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
* @see app/Http/Controllers/ModuleController.php:89
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

const modules = {
    store: Object.assign(store, store),
    update: Object.assign(update, update),
    destroy: Object.assign(destroy, destroy),
}

export default modules