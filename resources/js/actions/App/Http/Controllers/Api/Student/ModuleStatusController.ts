import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\Student\ModuleStatusController::getModuleStatus
 * @see app/Http/Controllers/Api/Student/ModuleStatusController.php:110
 * @route '/api/student/module/status/{module_id}'
 */
export const getModuleStatus = (args: { module_id: string | number } | [module_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getModuleStatus.url(args, options),
    method: 'get',
})

getModuleStatus.definition = {
    methods: ["get","head"],
    url: '/api/student/module/status/{module_id}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\Student\ModuleStatusController::getModuleStatus
 * @see app/Http/Controllers/Api/Student/ModuleStatusController.php:110
 * @route '/api/student/module/status/{module_id}'
 */
getModuleStatus.url = (args: { module_id: string | number } | [module_id: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { module_id: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    module_id: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        module_id: args.module_id,
                }

    return getModuleStatus.definition.url
            .replace('{module_id}', parsedArgs.module_id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Student\ModuleStatusController::getModuleStatus
 * @see app/Http/Controllers/Api/Student/ModuleStatusController.php:110
 * @route '/api/student/module/status/{module_id}'
 */
getModuleStatus.get = (args: { module_id: string | number } | [module_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getModuleStatus.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Api\Student\ModuleStatusController::getModuleStatus
 * @see app/Http/Controllers/Api/Student/ModuleStatusController.php:110
 * @route '/api/student/module/status/{module_id}'
 */
getModuleStatus.head = (args: { module_id: string | number } | [module_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getModuleStatus.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\Api\Student\ModuleStatusController::getModuleStatus
 * @see app/Http/Controllers/Api/Student/ModuleStatusController.php:110
 * @route '/api/student/module/status/{module_id}'
 */
    const getModuleStatusForm = (args: { module_id: string | number } | [module_id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: getModuleStatus.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\Api\Student\ModuleStatusController::getModuleStatus
 * @see app/Http/Controllers/Api/Student/ModuleStatusController.php:110
 * @route '/api/student/module/status/{module_id}'
 */
        getModuleStatusForm.get = (args: { module_id: string | number } | [module_id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: getModuleStatus.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\Api\Student\ModuleStatusController::getModuleStatus
 * @see app/Http/Controllers/Api/Student/ModuleStatusController.php:110
 * @route '/api/student/module/status/{module_id}'
 */
        getModuleStatusForm.head = (args: { module_id: string | number } | [module_id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: getModuleStatus.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    getModuleStatus.form = getModuleStatusForm
/**
* @see \App\Http\Controllers\Api\Student\ModuleStatusController::markModuleComplete
 * @see app/Http/Controllers/Api/Student/ModuleStatusController.php:188
 * @route '/api/student/module/complete/{module_id}'
 */
export const markModuleComplete = (args: { module_id: string | number } | [module_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: markModuleComplete.url(args, options),
    method: 'post',
})

markModuleComplete.definition = {
    methods: ["post"],
    url: '/api/student/module/complete/{module_id}',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\Student\ModuleStatusController::markModuleComplete
 * @see app/Http/Controllers/Api/Student/ModuleStatusController.php:188
 * @route '/api/student/module/complete/{module_id}'
 */
markModuleComplete.url = (args: { module_id: string | number } | [module_id: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { module_id: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    module_id: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        module_id: args.module_id,
                }

    return markModuleComplete.definition.url
            .replace('{module_id}', parsedArgs.module_id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Student\ModuleStatusController::markModuleComplete
 * @see app/Http/Controllers/Api/Student/ModuleStatusController.php:188
 * @route '/api/student/module/complete/{module_id}'
 */
markModuleComplete.post = (args: { module_id: string | number } | [module_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: markModuleComplete.url(args, options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\Api\Student\ModuleStatusController::markModuleComplete
 * @see app/Http/Controllers/Api/Student/ModuleStatusController.php:188
 * @route '/api/student/module/complete/{module_id}'
 */
    const markModuleCompleteForm = (args: { module_id: string | number } | [module_id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: markModuleComplete.url(args, options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\Api\Student\ModuleStatusController::markModuleComplete
 * @see app/Http/Controllers/Api/Student/ModuleStatusController.php:188
 * @route '/api/student/module/complete/{module_id}'
 */
        markModuleCompleteForm.post = (args: { module_id: string | number } | [module_id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: markModuleComplete.url(args, options),
            method: 'post',
        })
    
    markModuleComplete.form = markModuleCompleteForm
const ModuleStatusController = { getModuleStatus, markModuleComplete }

export default ModuleStatusController