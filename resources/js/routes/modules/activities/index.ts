import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\ModuleController::add
 * @see app/Http/Controllers/ModuleController.php:110
 * @route '/modules/{module}/activities'
 */
export const add = (args: { module: number | { id: number } } | [module: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: add.url(args, options),
    method: 'post',
})

add.definition = {
    methods: ["post"],
    url: '/modules/{module}/activities',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\ModuleController::add
 * @see app/Http/Controllers/ModuleController.php:110
 * @route '/modules/{module}/activities'
 */
add.url = (args: { module: number | { id: number } } | [module: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return add.definition.url
            .replace('{module}', parsedArgs.module.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ModuleController::add
 * @see app/Http/Controllers/ModuleController.php:110
 * @route '/modules/{module}/activities'
 */
add.post = (args: { module: number | { id: number } } | [module: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: add.url(args, options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\ModuleController::add
 * @see app/Http/Controllers/ModuleController.php:110
 * @route '/modules/{module}/activities'
 */
    const addForm = (args: { module: number | { id: number } } | [module: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: add.url(args, options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\ModuleController::add
 * @see app/Http/Controllers/ModuleController.php:110
 * @route '/modules/{module}/activities'
 */
        addForm.post = (args: { module: number | { id: number } } | [module: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: add.url(args, options),
            method: 'post',
        })
    
    add.form = addForm
/**
* @see \App\Http\Controllers\ModuleController::remove
 * @see app/Http/Controllers/ModuleController.php:141
 * @route '/modules/{module}/activities/{activity}'
 */
export const remove = (args: { module: number | { id: number }, activity: string | number } | [module: number | { id: number }, activity: string | number ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: remove.url(args, options),
    method: 'delete',
})

remove.definition = {
    methods: ["delete"],
    url: '/modules/{module}/activities/{activity}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\ModuleController::remove
 * @see app/Http/Controllers/ModuleController.php:141
 * @route '/modules/{module}/activities/{activity}'
 */
remove.url = (args: { module: number | { id: number }, activity: string | number } | [module: number | { id: number }, activity: string | number ], options?: RouteQueryOptions) => {
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

    return remove.definition.url
            .replace('{module}', parsedArgs.module.toString())
            .replace('{activity}', parsedArgs.activity.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ModuleController::remove
 * @see app/Http/Controllers/ModuleController.php:141
 * @route '/modules/{module}/activities/{activity}'
 */
remove.delete = (args: { module: number | { id: number }, activity: string | number } | [module: number | { id: number }, activity: string | number ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: remove.url(args, options),
    method: 'delete',
})

    /**
* @see \App\Http\Controllers\ModuleController::remove
 * @see app/Http/Controllers/ModuleController.php:141
 * @route '/modules/{module}/activities/{activity}'
 */
    const removeForm = (args: { module: number | { id: number }, activity: string | number } | [module: number | { id: number }, activity: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: remove.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'DELETE',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\ModuleController::remove
 * @see app/Http/Controllers/ModuleController.php:141
 * @route '/modules/{module}/activities/{activity}'
 */
        removeForm.delete = (args: { module: number | { id: number }, activity: string | number } | [module: number | { id: number }, activity: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: remove.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'DELETE',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    remove.form = removeForm
const activities = {
    add: Object.assign(add, add),
remove: Object.assign(remove, remove),
}

export default activities