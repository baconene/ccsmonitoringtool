import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../wayfinder'
/**
* @see \App\Http\Controllers\UserController::index
 * @see app/Http/Controllers/UserController.php:20
 * @route '/api/users'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/api/users',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\UserController::index
 * @see app/Http/Controllers/UserController.php:20
 * @route '/api/users'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\UserController::index
 * @see app/Http/Controllers/UserController.php:20
 * @route '/api/users'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\UserController::index
 * @see app/Http/Controllers/UserController.php:20
 * @route '/api/users'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\UserController::index
 * @see app/Http/Controllers/UserController.php:20
 * @route '/api/users'
 */
    const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: index.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\UserController::index
 * @see app/Http/Controllers/UserController.php:20
 * @route '/api/users'
 */
        indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: index.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\UserController::index
 * @see app/Http/Controllers/UserController.php:20
 * @route '/api/users'
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
* @see \App\Http\Controllers\UserController::store
 * @see app/Http/Controllers/UserController.php:53
 * @route '/api/users'
 */
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/api/users',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\UserController::store
 * @see app/Http/Controllers/UserController.php:53
 * @route '/api/users'
 */
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\UserController::store
 * @see app/Http/Controllers/UserController.php:53
 * @route '/api/users'
 */
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\UserController::store
 * @see app/Http/Controllers/UserController.php:53
 * @route '/api/users'
 */
    const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: store.url(options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\UserController::store
 * @see app/Http/Controllers/UserController.php:53
 * @route '/api/users'
 */
        storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: store.url(options),
            method: 'post',
        })
    
    store.form = storeForm
/**
* @see \App\Http\Controllers\UserController::show
 * @see app/Http/Controllers/UserController.php:0
 * @route '/api/users/{user}'
 */
export const show = (args: { user: string | number } | [user: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/api/users/{user}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\UserController::show
 * @see app/Http/Controllers/UserController.php:0
 * @route '/api/users/{user}'
 */
show.url = (args: { user: string | number } | [user: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { user: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    user: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        user: args.user,
                }

    return show.definition.url
            .replace('{user}', parsedArgs.user.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\UserController::show
 * @see app/Http/Controllers/UserController.php:0
 * @route '/api/users/{user}'
 */
show.get = (args: { user: string | number } | [user: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\UserController::show
 * @see app/Http/Controllers/UserController.php:0
 * @route '/api/users/{user}'
 */
show.head = (args: { user: string | number } | [user: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\UserController::show
 * @see app/Http/Controllers/UserController.php:0
 * @route '/api/users/{user}'
 */
    const showForm = (args: { user: string | number } | [user: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: show.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\UserController::show
 * @see app/Http/Controllers/UserController.php:0
 * @route '/api/users/{user}'
 */
        showForm.get = (args: { user: string | number } | [user: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: show.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\UserController::show
 * @see app/Http/Controllers/UserController.php:0
 * @route '/api/users/{user}'
 */
        showForm.head = (args: { user: string | number } | [user: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
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
* @see \App\Http\Controllers\UserController::update
 * @see app/Http/Controllers/UserController.php:125
 * @route '/api/users/{user}'
 */
export const update = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put","patch"],
    url: '/api/users/{user}',
} satisfies RouteDefinition<["put","patch"]>

/**
* @see \App\Http\Controllers\UserController::update
 * @see app/Http/Controllers/UserController.php:125
 * @route '/api/users/{user}'
 */
update.url = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { user: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { user: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    user: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        user: typeof args.user === 'object'
                ? args.user.id
                : args.user,
                }

    return update.definition.url
            .replace('{user}', parsedArgs.user.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\UserController::update
 * @see app/Http/Controllers/UserController.php:125
 * @route '/api/users/{user}'
 */
update.put = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})
/**
* @see \App\Http\Controllers\UserController::update
 * @see app/Http/Controllers/UserController.php:125
 * @route '/api/users/{user}'
 */
update.patch = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

    /**
* @see \App\Http\Controllers\UserController::update
 * @see app/Http/Controllers/UserController.php:125
 * @route '/api/users/{user}'
 */
    const updateForm = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: update.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'PUT',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\UserController::update
 * @see app/Http/Controllers/UserController.php:125
 * @route '/api/users/{user}'
 */
        updateForm.put = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: update.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'PUT',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
            /**
* @see \App\Http\Controllers\UserController::update
 * @see app/Http/Controllers/UserController.php:125
 * @route '/api/users/{user}'
 */
        updateForm.patch = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: update.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'PATCH',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    update.form = updateForm
/**
* @see \App\Http\Controllers\UserController::destroy
 * @see app/Http/Controllers/UserController.php:252
 * @route '/api/users/{user}'
 */
export const destroy = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/api/users/{user}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\UserController::destroy
 * @see app/Http/Controllers/UserController.php:252
 * @route '/api/users/{user}'
 */
destroy.url = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { user: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { user: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    user: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        user: typeof args.user === 'object'
                ? args.user.id
                : args.user,
                }

    return destroy.definition.url
            .replace('{user}', parsedArgs.user.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\UserController::destroy
 * @see app/Http/Controllers/UserController.php:252
 * @route '/api/users/{user}'
 */
destroy.delete = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

    /**
* @see \App\Http\Controllers\UserController::destroy
 * @see app/Http/Controllers/UserController.php:252
 * @route '/api/users/{user}'
 */
    const destroyForm = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: destroy.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'DELETE',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\UserController::destroy
 * @see app/Http/Controllers/UserController.php:252
 * @route '/api/users/{user}'
 */
        destroyForm.delete = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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
* @see \App\Http\Controllers\UserController::bulkUpload
 * @see app/Http/Controllers/UserController.php:365
 * @route '/api/users/bulk-upload'
 */
export const bulkUpload = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: bulkUpload.url(options),
    method: 'post',
})

bulkUpload.definition = {
    methods: ["post"],
    url: '/api/users/bulk-upload',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\UserController::bulkUpload
 * @see app/Http/Controllers/UserController.php:365
 * @route '/api/users/bulk-upload'
 */
bulkUpload.url = (options?: RouteQueryOptions) => {
    return bulkUpload.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\UserController::bulkUpload
 * @see app/Http/Controllers/UserController.php:365
 * @route '/api/users/bulk-upload'
 */
bulkUpload.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: bulkUpload.url(options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\UserController::bulkUpload
 * @see app/Http/Controllers/UserController.php:365
 * @route '/api/users/bulk-upload'
 */
    const bulkUploadForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: bulkUpload.url(options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\UserController::bulkUpload
 * @see app/Http/Controllers/UserController.php:365
 * @route '/api/users/bulk-upload'
 */
        bulkUploadForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: bulkUpload.url(options),
            method: 'post',
        })
    
    bulkUpload.form = bulkUploadForm
/**
* @see \App\Http\Controllers\UserController::downloadCsvTemplate
 * @see app/Http/Controllers/UserController.php:524
 * @route '/api/users/download-csv-template'
 */
export const downloadCsvTemplate = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: downloadCsvTemplate.url(options),
    method: 'get',
})

downloadCsvTemplate.definition = {
    methods: ["get","head"],
    url: '/api/users/download-csv-template',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\UserController::downloadCsvTemplate
 * @see app/Http/Controllers/UserController.php:524
 * @route '/api/users/download-csv-template'
 */
downloadCsvTemplate.url = (options?: RouteQueryOptions) => {
    return downloadCsvTemplate.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\UserController::downloadCsvTemplate
 * @see app/Http/Controllers/UserController.php:524
 * @route '/api/users/download-csv-template'
 */
downloadCsvTemplate.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: downloadCsvTemplate.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\UserController::downloadCsvTemplate
 * @see app/Http/Controllers/UserController.php:524
 * @route '/api/users/download-csv-template'
 */
downloadCsvTemplate.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: downloadCsvTemplate.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\UserController::downloadCsvTemplate
 * @see app/Http/Controllers/UserController.php:524
 * @route '/api/users/download-csv-template'
 */
    const downloadCsvTemplateForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: downloadCsvTemplate.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\UserController::downloadCsvTemplate
 * @see app/Http/Controllers/UserController.php:524
 * @route '/api/users/download-csv-template'
 */
        downloadCsvTemplateForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: downloadCsvTemplate.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\UserController::downloadCsvTemplate
 * @see app/Http/Controllers/UserController.php:524
 * @route '/api/users/download-csv-template'
 */
        downloadCsvTemplateForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: downloadCsvTemplate.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    downloadCsvTemplate.form = downloadCsvTemplateForm
/**
* @see \App\Http\Controllers\UserController::downloadAdminExample
 * @see app/Http/Controllers/UserController.php:540
 * @route '/api/users/download-admin-example'
 */
export const downloadAdminExample = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: downloadAdminExample.url(options),
    method: 'get',
})

downloadAdminExample.definition = {
    methods: ["get","head"],
    url: '/api/users/download-admin-example',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\UserController::downloadAdminExample
 * @see app/Http/Controllers/UserController.php:540
 * @route '/api/users/download-admin-example'
 */
downloadAdminExample.url = (options?: RouteQueryOptions) => {
    return downloadAdminExample.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\UserController::downloadAdminExample
 * @see app/Http/Controllers/UserController.php:540
 * @route '/api/users/download-admin-example'
 */
downloadAdminExample.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: downloadAdminExample.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\UserController::downloadAdminExample
 * @see app/Http/Controllers/UserController.php:540
 * @route '/api/users/download-admin-example'
 */
downloadAdminExample.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: downloadAdminExample.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\UserController::downloadAdminExample
 * @see app/Http/Controllers/UserController.php:540
 * @route '/api/users/download-admin-example'
 */
    const downloadAdminExampleForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: downloadAdminExample.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\UserController::downloadAdminExample
 * @see app/Http/Controllers/UserController.php:540
 * @route '/api/users/download-admin-example'
 */
        downloadAdminExampleForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: downloadAdminExample.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\UserController::downloadAdminExample
 * @see app/Http/Controllers/UserController.php:540
 * @route '/api/users/download-admin-example'
 */
        downloadAdminExampleForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: downloadAdminExample.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    downloadAdminExample.form = downloadAdminExampleForm
/**
* @see \App\Http\Controllers\UserController::downloadInstructorExample
 * @see app/Http/Controllers/UserController.php:556
 * @route '/api/users/download-instructor-example'
 */
export const downloadInstructorExample = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: downloadInstructorExample.url(options),
    method: 'get',
})

downloadInstructorExample.definition = {
    methods: ["get","head"],
    url: '/api/users/download-instructor-example',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\UserController::downloadInstructorExample
 * @see app/Http/Controllers/UserController.php:556
 * @route '/api/users/download-instructor-example'
 */
downloadInstructorExample.url = (options?: RouteQueryOptions) => {
    return downloadInstructorExample.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\UserController::downloadInstructorExample
 * @see app/Http/Controllers/UserController.php:556
 * @route '/api/users/download-instructor-example'
 */
downloadInstructorExample.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: downloadInstructorExample.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\UserController::downloadInstructorExample
 * @see app/Http/Controllers/UserController.php:556
 * @route '/api/users/download-instructor-example'
 */
downloadInstructorExample.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: downloadInstructorExample.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\UserController::downloadInstructorExample
 * @see app/Http/Controllers/UserController.php:556
 * @route '/api/users/download-instructor-example'
 */
    const downloadInstructorExampleForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: downloadInstructorExample.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\UserController::downloadInstructorExample
 * @see app/Http/Controllers/UserController.php:556
 * @route '/api/users/download-instructor-example'
 */
        downloadInstructorExampleForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: downloadInstructorExample.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\UserController::downloadInstructorExample
 * @see app/Http/Controllers/UserController.php:556
 * @route '/api/users/download-instructor-example'
 */
        downloadInstructorExampleForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: downloadInstructorExample.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    downloadInstructorExample.form = downloadInstructorExampleForm
/**
* @see \App\Http\Controllers\UserController::downloadStudentExample
 * @see app/Http/Controllers/UserController.php:572
 * @route '/api/users/download-student-example'
 */
export const downloadStudentExample = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: downloadStudentExample.url(options),
    method: 'get',
})

downloadStudentExample.definition = {
    methods: ["get","head"],
    url: '/api/users/download-student-example',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\UserController::downloadStudentExample
 * @see app/Http/Controllers/UserController.php:572
 * @route '/api/users/download-student-example'
 */
downloadStudentExample.url = (options?: RouteQueryOptions) => {
    return downloadStudentExample.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\UserController::downloadStudentExample
 * @see app/Http/Controllers/UserController.php:572
 * @route '/api/users/download-student-example'
 */
downloadStudentExample.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: downloadStudentExample.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\UserController::downloadStudentExample
 * @see app/Http/Controllers/UserController.php:572
 * @route '/api/users/download-student-example'
 */
downloadStudentExample.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: downloadStudentExample.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\UserController::downloadStudentExample
 * @see app/Http/Controllers/UserController.php:572
 * @route '/api/users/download-student-example'
 */
    const downloadStudentExampleForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: downloadStudentExample.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\UserController::downloadStudentExample
 * @see app/Http/Controllers/UserController.php:572
 * @route '/api/users/download-student-example'
 */
        downloadStudentExampleForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: downloadStudentExample.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\UserController::downloadStudentExample
 * @see app/Http/Controllers/UserController.php:572
 * @route '/api/users/download-student-example'
 */
        downloadStudentExampleForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: downloadStudentExample.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    downloadStudentExample.form = downloadStudentExampleForm
/**
* @see \App\Http\Controllers\UserController::csvFormatInfo
 * @see app/Http/Controllers/UserController.php:588
 * @route '/api/users/csv-format-info'
 */
export const csvFormatInfo = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: csvFormatInfo.url(options),
    method: 'get',
})

csvFormatInfo.definition = {
    methods: ["get","head"],
    url: '/api/users/csv-format-info',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\UserController::csvFormatInfo
 * @see app/Http/Controllers/UserController.php:588
 * @route '/api/users/csv-format-info'
 */
csvFormatInfo.url = (options?: RouteQueryOptions) => {
    return csvFormatInfo.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\UserController::csvFormatInfo
 * @see app/Http/Controllers/UserController.php:588
 * @route '/api/users/csv-format-info'
 */
csvFormatInfo.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: csvFormatInfo.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\UserController::csvFormatInfo
 * @see app/Http/Controllers/UserController.php:588
 * @route '/api/users/csv-format-info'
 */
csvFormatInfo.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: csvFormatInfo.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\UserController::csvFormatInfo
 * @see app/Http/Controllers/UserController.php:588
 * @route '/api/users/csv-format-info'
 */
    const csvFormatInfoForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: csvFormatInfo.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\UserController::csvFormatInfo
 * @see app/Http/Controllers/UserController.php:588
 * @route '/api/users/csv-format-info'
 */
        csvFormatInfoForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: csvFormatInfo.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\UserController::csvFormatInfo
 * @see app/Http/Controllers/UserController.php:588
 * @route '/api/users/csv-format-info'
 */
        csvFormatInfoForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: csvFormatInfo.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    csvFormatInfo.form = csvFormatInfoForm
const users = {
    index: Object.assign(index, index),
store: Object.assign(store, store),
show: Object.assign(show, show),
update: Object.assign(update, update),
destroy: Object.assign(destroy, destroy),
bulkUpload: Object.assign(bulkUpload, bulkUpload),
downloadCsvTemplate: Object.assign(downloadCsvTemplate, downloadCsvTemplate),
downloadAdminExample: Object.assign(downloadAdminExample, downloadAdminExample),
downloadInstructorExample: Object.assign(downloadInstructorExample, downloadInstructorExample),
downloadStudentExample: Object.assign(downloadStudentExample, downloadStudentExample),
csvFormatInfo: Object.assign(csvFormatInfo, csvFormatInfo),
}

export default users