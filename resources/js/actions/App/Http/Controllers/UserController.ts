import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
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
* @see \App\Http\Controllers\UserController::studentDetails
 * @see app/Http/Controllers/UserController.php:262
 * @route '/api/users/{id}/student-details'
 */
export const studentDetails = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: studentDetails.url(args, options),
    method: 'get',
})

studentDetails.definition = {
    methods: ["get","head"],
    url: '/api/users/{id}/student-details',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\UserController::studentDetails
 * @see app/Http/Controllers/UserController.php:262
 * @route '/api/users/{id}/student-details'
 */
studentDetails.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return studentDetails.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\UserController::studentDetails
 * @see app/Http/Controllers/UserController.php:262
 * @route '/api/users/{id}/student-details'
 */
studentDetails.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: studentDetails.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\UserController::studentDetails
 * @see app/Http/Controllers/UserController.php:262
 * @route '/api/users/{id}/student-details'
 */
studentDetails.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: studentDetails.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\UserController::studentDetails
 * @see app/Http/Controllers/UserController.php:262
 * @route '/api/users/{id}/student-details'
 */
    const studentDetailsForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: studentDetails.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\UserController::studentDetails
 * @see app/Http/Controllers/UserController.php:262
 * @route '/api/users/{id}/student-details'
 */
        studentDetailsForm.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: studentDetails.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\UserController::studentDetails
 * @see app/Http/Controllers/UserController.php:262
 * @route '/api/users/{id}/student-details'
 */
        studentDetailsForm.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: studentDetails.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    studentDetails.form = studentDetailsForm
/**
* @see \App\Http\Controllers\UserController::uploadCSV
 * @see app/Http/Controllers/UserController.php:365
 * @route '/api/users/bulk-upload'
 */
export const uploadCSV = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: uploadCSV.url(options),
    method: 'post',
})

uploadCSV.definition = {
    methods: ["post"],
    url: '/api/users/bulk-upload',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\UserController::uploadCSV
 * @see app/Http/Controllers/UserController.php:365
 * @route '/api/users/bulk-upload'
 */
uploadCSV.url = (options?: RouteQueryOptions) => {
    return uploadCSV.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\UserController::uploadCSV
 * @see app/Http/Controllers/UserController.php:365
 * @route '/api/users/bulk-upload'
 */
uploadCSV.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: uploadCSV.url(options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\UserController::uploadCSV
 * @see app/Http/Controllers/UserController.php:365
 * @route '/api/users/bulk-upload'
 */
    const uploadCSVForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: uploadCSV.url(options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\UserController::uploadCSV
 * @see app/Http/Controllers/UserController.php:365
 * @route '/api/users/bulk-upload'
 */
        uploadCSVForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: uploadCSV.url(options),
            method: 'post',
        })
    
    uploadCSV.form = uploadCSVForm
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
* @see \App\Http\Controllers\UserController::getCsvFormatInfo
 * @see app/Http/Controllers/UserController.php:588
 * @route '/api/users/csv-format-info'
 */
export const getCsvFormatInfo = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getCsvFormatInfo.url(options),
    method: 'get',
})

getCsvFormatInfo.definition = {
    methods: ["get","head"],
    url: '/api/users/csv-format-info',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\UserController::getCsvFormatInfo
 * @see app/Http/Controllers/UserController.php:588
 * @route '/api/users/csv-format-info'
 */
getCsvFormatInfo.url = (options?: RouteQueryOptions) => {
    return getCsvFormatInfo.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\UserController::getCsvFormatInfo
 * @see app/Http/Controllers/UserController.php:588
 * @route '/api/users/csv-format-info'
 */
getCsvFormatInfo.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getCsvFormatInfo.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\UserController::getCsvFormatInfo
 * @see app/Http/Controllers/UserController.php:588
 * @route '/api/users/csv-format-info'
 */
getCsvFormatInfo.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getCsvFormatInfo.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\UserController::getCsvFormatInfo
 * @see app/Http/Controllers/UserController.php:588
 * @route '/api/users/csv-format-info'
 */
    const getCsvFormatInfoForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: getCsvFormatInfo.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\UserController::getCsvFormatInfo
 * @see app/Http/Controllers/UserController.php:588
 * @route '/api/users/csv-format-info'
 */
        getCsvFormatInfoForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: getCsvFormatInfo.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\UserController::getCsvFormatInfo
 * @see app/Http/Controllers/UserController.php:588
 * @route '/api/users/csv-format-info'
 */
        getCsvFormatInfoForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: getCsvFormatInfo.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    getCsvFormatInfo.form = getCsvFormatInfoForm
/**
* @see \App\Http\Controllers\UserController::updateInstructor
 * @see app/Http/Controllers/UserController.php:394
 * @route '/api/instructor/{id}'
 */
export const updateInstructor = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: updateInstructor.url(args, options),
    method: 'put',
})

updateInstructor.definition = {
    methods: ["put"],
    url: '/api/instructor/{id}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\UserController::updateInstructor
 * @see app/Http/Controllers/UserController.php:394
 * @route '/api/instructor/{id}'
 */
updateInstructor.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return updateInstructor.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\UserController::updateInstructor
 * @see app/Http/Controllers/UserController.php:394
 * @route '/api/instructor/{id}'
 */
updateInstructor.put = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: updateInstructor.url(args, options),
    method: 'put',
})

    /**
* @see \App\Http\Controllers\UserController::updateInstructor
 * @see app/Http/Controllers/UserController.php:394
 * @route '/api/instructor/{id}'
 */
    const updateInstructorForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: updateInstructor.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'PUT',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\UserController::updateInstructor
 * @see app/Http/Controllers/UserController.php:394
 * @route '/api/instructor/{id}'
 */
        updateInstructorForm.put = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: updateInstructor.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'PUT',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    updateInstructor.form = updateInstructorForm
const UserController = { index, store, show, update, destroy, studentDetails, uploadCSV, downloadCsvTemplate, downloadAdminExample, downloadInstructorExample, downloadStudentExample, getCsvFormatInfo, updateInstructor }

export default UserController