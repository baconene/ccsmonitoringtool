import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
 * @see routes/web.php:642
 * @route '/instructor/notifications/test'
 */
export const test = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: test.url(options),
    method: 'get',
})

test.definition = {
    methods: ["get","head"],
    url: '/instructor/notifications/test',
} satisfies RouteDefinition<["get","head"]>

/**
 * @see routes/web.php:642
 * @route '/instructor/notifications/test'
 */
test.url = (options?: RouteQueryOptions) => {
    return test.definition.url + queryParams(options)
}

/**
 * @see routes/web.php:642
 * @route '/instructor/notifications/test'
 */
test.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: test.url(options),
    method: 'get',
})
/**
 * @see routes/web.php:642
 * @route '/instructor/notifications/test'
 */
test.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: test.url(options),
    method: 'head',
})

    /**
 * @see routes/web.php:642
 * @route '/instructor/notifications/test'
 */
    const testForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: test.url(options),
        method: 'get',
    })

            /**
 * @see routes/web.php:642
 * @route '/instructor/notifications/test'
 */
        testForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: test.url(options),
            method: 'get',
        })
            /**
 * @see routes/web.php:642
 * @route '/instructor/notifications/test'
 */
        testForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: test.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    test.form = testForm
/**
* @see \App\Http\Controllers\Instructor\NotificationController::unreadCount
 * @see app/Http/Controllers/Instructor/NotificationController.php:15
 * @route '/instructor/notifications/unread-count'
 */
export const unreadCount = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: unreadCount.url(options),
    method: 'get',
})

unreadCount.definition = {
    methods: ["get","head"],
    url: '/instructor/notifications/unread-count',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Instructor\NotificationController::unreadCount
 * @see app/Http/Controllers/Instructor/NotificationController.php:15
 * @route '/instructor/notifications/unread-count'
 */
unreadCount.url = (options?: RouteQueryOptions) => {
    return unreadCount.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Instructor\NotificationController::unreadCount
 * @see app/Http/Controllers/Instructor/NotificationController.php:15
 * @route '/instructor/notifications/unread-count'
 */
unreadCount.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: unreadCount.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Instructor\NotificationController::unreadCount
 * @see app/Http/Controllers/Instructor/NotificationController.php:15
 * @route '/instructor/notifications/unread-count'
 */
unreadCount.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: unreadCount.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\Instructor\NotificationController::unreadCount
 * @see app/Http/Controllers/Instructor/NotificationController.php:15
 * @route '/instructor/notifications/unread-count'
 */
    const unreadCountForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: unreadCount.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\Instructor\NotificationController::unreadCount
 * @see app/Http/Controllers/Instructor/NotificationController.php:15
 * @route '/instructor/notifications/unread-count'
 */
        unreadCountForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: unreadCount.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\Instructor\NotificationController::unreadCount
 * @see app/Http/Controllers/Instructor/NotificationController.php:15
 * @route '/instructor/notifications/unread-count'
 */
        unreadCountForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: unreadCount.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    unreadCount.form = unreadCountForm
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
* @see \App\Http\Controllers\Instructor\NotificationController::read
 * @see app/Http/Controllers/Instructor/NotificationController.php:50
 * @route '/instructor/notifications/{id}/read'
 */
export const read = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: read.url(args, options),
    method: 'post',
})

read.definition = {
    methods: ["post"],
    url: '/instructor/notifications/{id}/read',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Instructor\NotificationController::read
 * @see app/Http/Controllers/Instructor/NotificationController.php:50
 * @route '/instructor/notifications/{id}/read'
 */
read.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return read.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Instructor\NotificationController::read
 * @see app/Http/Controllers/Instructor/NotificationController.php:50
 * @route '/instructor/notifications/{id}/read'
 */
read.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: read.url(args, options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\Instructor\NotificationController::read
 * @see app/Http/Controllers/Instructor/NotificationController.php:50
 * @route '/instructor/notifications/{id}/read'
 */
    const readForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: read.url(args, options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\Instructor\NotificationController::read
 * @see app/Http/Controllers/Instructor/NotificationController.php:50
 * @route '/instructor/notifications/{id}/read'
 */
        readForm.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: read.url(args, options),
            method: 'post',
        })
    
    read.form = readForm
/**
* @see \App\Http\Controllers\Instructor\NotificationController::readAll
 * @see app/Http/Controllers/Instructor/NotificationController.php:63
 * @route '/instructor/notifications/read-all'
 */
export const readAll = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: readAll.url(options),
    method: 'post',
})

readAll.definition = {
    methods: ["post"],
    url: '/instructor/notifications/read-all',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Instructor\NotificationController::readAll
 * @see app/Http/Controllers/Instructor/NotificationController.php:63
 * @route '/instructor/notifications/read-all'
 */
readAll.url = (options?: RouteQueryOptions) => {
    return readAll.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Instructor\NotificationController::readAll
 * @see app/Http/Controllers/Instructor/NotificationController.php:63
 * @route '/instructor/notifications/read-all'
 */
readAll.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: readAll.url(options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\Instructor\NotificationController::readAll
 * @see app/Http/Controllers/Instructor/NotificationController.php:63
 * @route '/instructor/notifications/read-all'
 */
    const readAllForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: readAll.url(options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\Instructor\NotificationController::readAll
 * @see app/Http/Controllers/Instructor/NotificationController.php:63
 * @route '/instructor/notifications/read-all'
 */
        readAllForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: readAll.url(options),
            method: 'post',
        })
    
    readAll.form = readAllForm
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
const notifications = {
    test: Object.assign(test, test),
unreadCount: Object.assign(unreadCount, unreadCount),
index: Object.assign(index, index),
read: Object.assign(read, read),
readAll: Object.assign(readAll, readAll),
destroy: Object.assign(destroy, destroy),
}

export default notifications