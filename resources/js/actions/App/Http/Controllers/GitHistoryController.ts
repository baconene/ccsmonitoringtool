import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\GitHistoryController::getHistory
 * @see app/Http/Controllers/GitHistoryController.php:51
 * @route '/api/admin/git/history'
 */
export const getHistory = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getHistory.url(options),
    method: 'get',
})

getHistory.definition = {
    methods: ["get","head"],
    url: '/api/admin/git/history',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\GitHistoryController::getHistory
 * @see app/Http/Controllers/GitHistoryController.php:51
 * @route '/api/admin/git/history'
 */
getHistory.url = (options?: RouteQueryOptions) => {
    return getHistory.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\GitHistoryController::getHistory
 * @see app/Http/Controllers/GitHistoryController.php:51
 * @route '/api/admin/git/history'
 */
getHistory.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getHistory.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\GitHistoryController::getHistory
 * @see app/Http/Controllers/GitHistoryController.php:51
 * @route '/api/admin/git/history'
 */
getHistory.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getHistory.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\GitHistoryController::getHistory
 * @see app/Http/Controllers/GitHistoryController.php:51
 * @route '/api/admin/git/history'
 */
    const getHistoryForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: getHistory.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\GitHistoryController::getHistory
 * @see app/Http/Controllers/GitHistoryController.php:51
 * @route '/api/admin/git/history'
 */
        getHistoryForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: getHistory.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\GitHistoryController::getHistory
 * @see app/Http/Controllers/GitHistoryController.php:51
 * @route '/api/admin/git/history'
 */
        getHistoryForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: getHistory.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    getHistory.form = getHistoryForm
/**
* @see \App\Http\Controllers\GitHistoryController::getBranches
 * @see app/Http/Controllers/GitHistoryController.php:199
 * @route '/api/admin/git/branches'
 */
export const getBranches = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getBranches.url(options),
    method: 'get',
})

getBranches.definition = {
    methods: ["get","head"],
    url: '/api/admin/git/branches',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\GitHistoryController::getBranches
 * @see app/Http/Controllers/GitHistoryController.php:199
 * @route '/api/admin/git/branches'
 */
getBranches.url = (options?: RouteQueryOptions) => {
    return getBranches.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\GitHistoryController::getBranches
 * @see app/Http/Controllers/GitHistoryController.php:199
 * @route '/api/admin/git/branches'
 */
getBranches.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getBranches.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\GitHistoryController::getBranches
 * @see app/Http/Controllers/GitHistoryController.php:199
 * @route '/api/admin/git/branches'
 */
getBranches.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getBranches.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\GitHistoryController::getBranches
 * @see app/Http/Controllers/GitHistoryController.php:199
 * @route '/api/admin/git/branches'
 */
    const getBranchesForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: getBranches.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\GitHistoryController::getBranches
 * @see app/Http/Controllers/GitHistoryController.php:199
 * @route '/api/admin/git/branches'
 */
        getBranchesForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: getBranches.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\GitHistoryController::getBranches
 * @see app/Http/Controllers/GitHistoryController.php:199
 * @route '/api/admin/git/branches'
 */
        getBranchesForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: getBranches.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    getBranches.form = getBranchesForm
/**
* @see \App\Http\Controllers\GitHistoryController::getStatus
 * @see app/Http/Controllers/GitHistoryController.php:259
 * @route '/api/admin/git/status'
 */
export const getStatus = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getStatus.url(options),
    method: 'get',
})

getStatus.definition = {
    methods: ["get","head"],
    url: '/api/admin/git/status',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\GitHistoryController::getStatus
 * @see app/Http/Controllers/GitHistoryController.php:259
 * @route '/api/admin/git/status'
 */
getStatus.url = (options?: RouteQueryOptions) => {
    return getStatus.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\GitHistoryController::getStatus
 * @see app/Http/Controllers/GitHistoryController.php:259
 * @route '/api/admin/git/status'
 */
getStatus.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getStatus.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\GitHistoryController::getStatus
 * @see app/Http/Controllers/GitHistoryController.php:259
 * @route '/api/admin/git/status'
 */
getStatus.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getStatus.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\GitHistoryController::getStatus
 * @see app/Http/Controllers/GitHistoryController.php:259
 * @route '/api/admin/git/status'
 */
    const getStatusForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: getStatus.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\GitHistoryController::getStatus
 * @see app/Http/Controllers/GitHistoryController.php:259
 * @route '/api/admin/git/status'
 */
        getStatusForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: getStatus.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\GitHistoryController::getStatus
 * @see app/Http/Controllers/GitHistoryController.php:259
 * @route '/api/admin/git/status'
 */
        getStatusForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: getStatus.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    getStatus.form = getStatusForm
/**
* @see \App\Http\Controllers\GitHistoryController::getCommitDetails
 * @see app/Http/Controllers/GitHistoryController.php:145
 * @route '/api/admin/git/commit/{hash}'
 */
export const getCommitDetails = (args: { hash: string | number } | [hash: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getCommitDetails.url(args, options),
    method: 'get',
})

getCommitDetails.definition = {
    methods: ["get","head"],
    url: '/api/admin/git/commit/{hash}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\GitHistoryController::getCommitDetails
 * @see app/Http/Controllers/GitHistoryController.php:145
 * @route '/api/admin/git/commit/{hash}'
 */
getCommitDetails.url = (args: { hash: string | number } | [hash: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { hash: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    hash: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        hash: args.hash,
                }

    return getCommitDetails.definition.url
            .replace('{hash}', parsedArgs.hash.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\GitHistoryController::getCommitDetails
 * @see app/Http/Controllers/GitHistoryController.php:145
 * @route '/api/admin/git/commit/{hash}'
 */
getCommitDetails.get = (args: { hash: string | number } | [hash: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getCommitDetails.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\GitHistoryController::getCommitDetails
 * @see app/Http/Controllers/GitHistoryController.php:145
 * @route '/api/admin/git/commit/{hash}'
 */
getCommitDetails.head = (args: { hash: string | number } | [hash: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getCommitDetails.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\GitHistoryController::getCommitDetails
 * @see app/Http/Controllers/GitHistoryController.php:145
 * @route '/api/admin/git/commit/{hash}'
 */
    const getCommitDetailsForm = (args: { hash: string | number } | [hash: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: getCommitDetails.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\GitHistoryController::getCommitDetails
 * @see app/Http/Controllers/GitHistoryController.php:145
 * @route '/api/admin/git/commit/{hash}'
 */
        getCommitDetailsForm.get = (args: { hash: string | number } | [hash: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: getCommitDetails.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\GitHistoryController::getCommitDetails
 * @see app/Http/Controllers/GitHistoryController.php:145
 * @route '/api/admin/git/commit/{hash}'
 */
        getCommitDetailsForm.head = (args: { hash: string | number } | [hash: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: getCommitDetails.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    getCommitDetails.form = getCommitDetailsForm
const GitHistoryController = { getHistory, getBranches, getStatus, getCommitDetails }

export default GitHistoryController