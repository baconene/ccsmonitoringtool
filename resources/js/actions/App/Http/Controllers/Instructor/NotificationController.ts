import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Instructor\NotificationController::getUnreadCount
 * @see app/Http/Controllers/Instructor/NotificationController.php:15
 * @route '/instructor/notifications/unread-count'
 */
export const getUnreadCount = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getUnreadCount.url(options),
    method: 'get',
})

getUnreadCount.definition = {
    methods: ["get","head"],
    url: '/instructor/notifications/unread-count',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Instructor\NotificationController::getUnreadCount
 * @see app/Http/Controllers/Instructor/NotificationController.php:15
 * @route '/instructor/notifications/unread-count'
 */
getUnreadCount.url = (options?: RouteQueryOptions) => {
    return getUnreadCount.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Instructor\NotificationController::getUnreadCount
 * @see app/Http/Controllers/Instructor/NotificationController.php:15
 * @route '/instructor/notifications/unread-count'
 */
getUnreadCount.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getUnreadCount.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Instructor\NotificationController::getUnreadCount
 * @see app/Http/Controllers/Instructor/NotificationController.php:15
 * @route '/instructor/notifications/unread-count'
 */
getUnreadCount.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getUnreadCount.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\Instructor\NotificationController::getUnreadCount
 * @see app/Http/Controllers/Instructor/NotificationController.php:15
 * @route '/instructor/notifications/unread-count'
 */
    const getUnreadCountForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: getUnreadCount.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\Instructor\NotificationController::getUnreadCount
 * @see app/Http/Controllers/Instructor/NotificationController.php:15
 * @route '/instructor/notifications/unread-count'
 */
        getUnreadCountForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: getUnreadCount.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\Instructor\NotificationController::getUnreadCount
 * @see app/Http/Controllers/Instructor/NotificationController.php:15
 * @route '/instructor/notifications/unread-count'
 */
        getUnreadCountForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: getUnreadCount.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    getUnreadCount.form = getUnreadCountForm
/**
* @see \App\Http\Controllers\Instructor\NotificationController::index
 * @see app/Http/Controllers/Instructor/NotificationController.php:27
 * @route '/instructor/notifications'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/instructor/notifications',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Instructor\NotificationController::index
 * @see app/Http/Controllers/Instructor/NotificationController.php:27
 * @route '/instructor/notifications'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Instructor\NotificationController::index
 * @see app/Http/Controllers/Instructor/NotificationController.php:27
 * @route '/instructor/notifications'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Instructor\NotificationController::index
 * @see app/Http/Controllers/Instructor/NotificationController.php:27
 * @route '/instructor/notifications'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\Instructor\NotificationController::index
 * @see app/Http/Controllers/Instructor/NotificationController.php:27
 * @route '/instructor/notifications'
 */
    const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: index.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\Instructor\NotificationController::index
 * @see app/Http/Controllers/Instructor/NotificationController.php:27
 * @route '/instructor/notifications'
 */
        indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: index.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\Instructor\NotificationController::index
 * @see app/Http/Controllers/Instructor/NotificationController.php:27
 * @route '/instructor/notifications'
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
* @see \App\Http\Controllers\Instructor\NotificationController::markAsRead
 * @see app/Http/Controllers/Instructor/NotificationController.php:50
 * @route '/instructor/notifications/{id}/read'
 */
export const markAsRead = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: markAsRead.url(args, options),
    method: 'post',
})

markAsRead.definition = {
    methods: ["post"],
    url: '/instructor/notifications/{id}/read',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Instructor\NotificationController::markAsRead
 * @see app/Http/Controllers/Instructor/NotificationController.php:50
 * @route '/instructor/notifications/{id}/read'
 */
markAsRead.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { id: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    id: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        id: args.id,
                }

    return markAsRead.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Instructor\NotificationController::markAsRead
 * @see app/Http/Controllers/Instructor/NotificationController.php:50
 * @route '/instructor/notifications/{id}/read'
 */
markAsRead.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: markAsRead.url(args, options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\Instructor\NotificationController::markAsRead
 * @see app/Http/Controllers/Instructor/NotificationController.php:50
 * @route '/instructor/notifications/{id}/read'
 */
    const markAsReadForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: markAsRead.url(args, options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\Instructor\NotificationController::markAsRead
 * @see app/Http/Controllers/Instructor/NotificationController.php:50
 * @route '/instructor/notifications/{id}/read'
 */
        markAsReadForm.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: markAsRead.url(args, options),
            method: 'post',
        })
    
    markAsRead.form = markAsReadForm
/**
* @see \App\Http\Controllers\Instructor\NotificationController::markAllAsRead
 * @see app/Http/Controllers/Instructor/NotificationController.php:63
 * @route '/instructor/notifications/read-all'
 */
export const markAllAsRead = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: markAllAsRead.url(options),
    method: 'post',
})

markAllAsRead.definition = {
    methods: ["post"],
    url: '/instructor/notifications/read-all',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Instructor\NotificationController::markAllAsRead
 * @see app/Http/Controllers/Instructor/NotificationController.php:63
 * @route '/instructor/notifications/read-all'
 */
markAllAsRead.url = (options?: RouteQueryOptions) => {
    return markAllAsRead.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Instructor\NotificationController::markAllAsRead
 * @see app/Http/Controllers/Instructor/NotificationController.php:63
 * @route '/instructor/notifications/read-all'
 */
markAllAsRead.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: markAllAsRead.url(options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\Instructor\NotificationController::markAllAsRead
 * @see app/Http/Controllers/Instructor/NotificationController.php:63
 * @route '/instructor/notifications/read-all'
 */
    const markAllAsReadForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: markAllAsRead.url(options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\Instructor\NotificationController::markAllAsRead
 * @see app/Http/Controllers/Instructor/NotificationController.php:63
 * @route '/instructor/notifications/read-all'
 */
        markAllAsReadForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: markAllAsRead.url(options),
            method: 'post',
        })
    
    markAllAsRead.form = markAllAsReadForm
/**
* @see \App\Http\Controllers\Instructor\NotificationController::destroy
 * @see app/Http/Controllers/Instructor/NotificationController.php:78
 * @route '/instructor/notifications/{id}'
 */
export const destroy = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/instructor/notifications/{id}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Instructor\NotificationController::destroy
 * @see app/Http/Controllers/Instructor/NotificationController.php:78
 * @route '/instructor/notifications/{id}'
 */
destroy.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { id: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    id: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        id: args.id,
                }

    return destroy.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Instructor\NotificationController::destroy
 * @see app/Http/Controllers/Instructor/NotificationController.php:78
 * @route '/instructor/notifications/{id}'
 */
destroy.delete = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

    /**
* @see \App\Http\Controllers\Instructor\NotificationController::destroy
 * @see app/Http/Controllers/Instructor/NotificationController.php:78
 * @route '/instructor/notifications/{id}'
 */
    const destroyForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: destroy.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'DELETE',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\Instructor\NotificationController::destroy
 * @see app/Http/Controllers/Instructor/NotificationController.php:78
 * @route '/instructor/notifications/{id}'
 */
        destroyForm.delete = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: destroy.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'DELETE',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    destroy.form = destroyForm
const NotificationController = { getUnreadCount, index, markAsRead, markAllAsRead, destroy }

export default NotificationController