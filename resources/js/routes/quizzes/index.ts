import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../wayfinder'
/**
* @see \App\Http\Controllers\QuizController::index
 * @see app/Http/Controllers/QuizController.php:18
 * @route '/quizzes'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/quizzes',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\QuizController::index
 * @see app/Http/Controllers/QuizController.php:18
 * @route '/quizzes'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\QuizController::index
 * @see app/Http/Controllers/QuizController.php:18
 * @route '/quizzes'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\QuizController::index
 * @see app/Http/Controllers/QuizController.php:18
 * @route '/quizzes'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\QuizController::index
 * @see app/Http/Controllers/QuizController.php:18
 * @route '/quizzes'
 */
    const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: index.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\QuizController::index
 * @see app/Http/Controllers/QuizController.php:18
 * @route '/quizzes'
 */
        indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: index.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\QuizController::index
 * @see app/Http/Controllers/QuizController.php:18
 * @route '/quizzes'
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
* @see \App\Http\Controllers\QuizController::create
 * @see app/Http/Controllers/QuizController.php:27
 * @route '/quizzes/create'
 */
export const create = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/quizzes/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\QuizController::create
 * @see app/Http/Controllers/QuizController.php:27
 * @route '/quizzes/create'
 */
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\QuizController::create
 * @see app/Http/Controllers/QuizController.php:27
 * @route '/quizzes/create'
 */
create.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\QuizController::create
 * @see app/Http/Controllers/QuizController.php:27
 * @route '/quizzes/create'
 */
create.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\QuizController::create
 * @see app/Http/Controllers/QuizController.php:27
 * @route '/quizzes/create'
 */
    const createForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: create.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\QuizController::create
 * @see app/Http/Controllers/QuizController.php:27
 * @route '/quizzes/create'
 */
        createForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: create.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\QuizController::create
 * @see app/Http/Controllers/QuizController.php:27
 * @route '/quizzes/create'
 */
        createForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: create.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    create.form = createForm
/**
* @see \App\Http\Controllers\QuizController::store
 * @see app/Http/Controllers/QuizController.php:37
 * @route '/quizzes'
 */
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/quizzes',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\QuizController::store
 * @see app/Http/Controllers/QuizController.php:37
 * @route '/quizzes'
 */
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\QuizController::store
 * @see app/Http/Controllers/QuizController.php:37
 * @route '/quizzes'
 */
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\QuizController::store
 * @see app/Http/Controllers/QuizController.php:37
 * @route '/quizzes'
 */
    const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: store.url(options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\QuizController::store
 * @see app/Http/Controllers/QuizController.php:37
 * @route '/quizzes'
 */
        storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: store.url(options),
            method: 'post',
        })
    
    store.form = storeForm
/**
* @see \App\Http\Controllers\QuizController::show
 * @see app/Http/Controllers/QuizController.php:58
 * @route '/quizzes/{quiz}'
 */
export const show = (args: { quiz: number | { id: number } } | [quiz: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/quizzes/{quiz}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\QuizController::show
 * @see app/Http/Controllers/QuizController.php:58
 * @route '/quizzes/{quiz}'
 */
show.url = (args: { quiz: number | { id: number } } | [quiz: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { quiz: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { quiz: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    quiz: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        quiz: typeof args.quiz === 'object'
                ? args.quiz.id
                : args.quiz,
                }

    return show.definition.url
            .replace('{quiz}', parsedArgs.quiz.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\QuizController::show
 * @see app/Http/Controllers/QuizController.php:58
 * @route '/quizzes/{quiz}'
 */
show.get = (args: { quiz: number | { id: number } } | [quiz: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\QuizController::show
 * @see app/Http/Controllers/QuizController.php:58
 * @route '/quizzes/{quiz}'
 */
show.head = (args: { quiz: number | { id: number } } | [quiz: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\QuizController::show
 * @see app/Http/Controllers/QuizController.php:58
 * @route '/quizzes/{quiz}'
 */
    const showForm = (args: { quiz: number | { id: number } } | [quiz: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: show.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\QuizController::show
 * @see app/Http/Controllers/QuizController.php:58
 * @route '/quizzes/{quiz}'
 */
        showForm.get = (args: { quiz: number | { id: number } } | [quiz: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: show.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\QuizController::show
 * @see app/Http/Controllers/QuizController.php:58
 * @route '/quizzes/{quiz}'
 */
        showForm.head = (args: { quiz: number | { id: number } } | [quiz: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
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
* @see \App\Http\Controllers\QuizController::edit
 * @see app/Http/Controllers/QuizController.php:67
 * @route '/quizzes/{quiz}/edit'
 */
export const edit = (args: { quiz: number | { id: number } } | [quiz: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/quizzes/{quiz}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\QuizController::edit
 * @see app/Http/Controllers/QuizController.php:67
 * @route '/quizzes/{quiz}/edit'
 */
edit.url = (args: { quiz: number | { id: number } } | [quiz: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { quiz: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { quiz: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    quiz: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        quiz: typeof args.quiz === 'object'
                ? args.quiz.id
                : args.quiz,
                }

    return edit.definition.url
            .replace('{quiz}', parsedArgs.quiz.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\QuizController::edit
 * @see app/Http/Controllers/QuizController.php:67
 * @route '/quizzes/{quiz}/edit'
 */
edit.get = (args: { quiz: number | { id: number } } | [quiz: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\QuizController::edit
 * @see app/Http/Controllers/QuizController.php:67
 * @route '/quizzes/{quiz}/edit'
 */
edit.head = (args: { quiz: number | { id: number } } | [quiz: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\QuizController::edit
 * @see app/Http/Controllers/QuizController.php:67
 * @route '/quizzes/{quiz}/edit'
 */
    const editForm = (args: { quiz: number | { id: number } } | [quiz: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: edit.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\QuizController::edit
 * @see app/Http/Controllers/QuizController.php:67
 * @route '/quizzes/{quiz}/edit'
 */
        editForm.get = (args: { quiz: number | { id: number } } | [quiz: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: edit.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\QuizController::edit
 * @see app/Http/Controllers/QuizController.php:67
 * @route '/quizzes/{quiz}/edit'
 */
        editForm.head = (args: { quiz: number | { id: number } } | [quiz: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: edit.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    edit.form = editForm
/**
* @see \App\Http\Controllers\QuizController::update
 * @see app/Http/Controllers/QuizController.php:76
 * @route '/quizzes/{quiz}'
 */
export const update = (args: { quiz: number | { id: number } } | [quiz: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put","patch"],
    url: '/quizzes/{quiz}',
} satisfies RouteDefinition<["put","patch"]>

/**
* @see \App\Http\Controllers\QuizController::update
 * @see app/Http/Controllers/QuizController.php:76
 * @route '/quizzes/{quiz}'
 */
update.url = (args: { quiz: number | { id: number } } | [quiz: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { quiz: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { quiz: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    quiz: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        quiz: typeof args.quiz === 'object'
                ? args.quiz.id
                : args.quiz,
                }

    return update.definition.url
            .replace('{quiz}', parsedArgs.quiz.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\QuizController::update
 * @see app/Http/Controllers/QuizController.php:76
 * @route '/quizzes/{quiz}'
 */
update.put = (args: { quiz: number | { id: number } } | [quiz: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})
/**
* @see \App\Http\Controllers\QuizController::update
 * @see app/Http/Controllers/QuizController.php:76
 * @route '/quizzes/{quiz}'
 */
update.patch = (args: { quiz: number | { id: number } } | [quiz: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

    /**
* @see \App\Http\Controllers\QuizController::update
 * @see app/Http/Controllers/QuizController.php:76
 * @route '/quizzes/{quiz}'
 */
    const updateForm = (args: { quiz: number | { id: number } } | [quiz: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: update.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'PUT',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\QuizController::update
 * @see app/Http/Controllers/QuizController.php:76
 * @route '/quizzes/{quiz}'
 */
        updateForm.put = (args: { quiz: number | { id: number } } | [quiz: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: update.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'PUT',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
            /**
* @see \App\Http\Controllers\QuizController::update
 * @see app/Http/Controllers/QuizController.php:76
 * @route '/quizzes/{quiz}'
 */
        updateForm.patch = (args: { quiz: number | { id: number } } | [quiz: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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
* @see \App\Http\Controllers\QuizController::destroy
 * @see app/Http/Controllers/QuizController.php:92
 * @route '/quizzes/{quiz}'
 */
export const destroy = (args: { quiz: number | { id: number } } | [quiz: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/quizzes/{quiz}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\QuizController::destroy
 * @see app/Http/Controllers/QuizController.php:92
 * @route '/quizzes/{quiz}'
 */
destroy.url = (args: { quiz: number | { id: number } } | [quiz: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { quiz: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { quiz: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    quiz: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        quiz: typeof args.quiz === 'object'
                ? args.quiz.id
                : args.quiz,
                }

    return destroy.definition.url
            .replace('{quiz}', parsedArgs.quiz.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\QuizController::destroy
 * @see app/Http/Controllers/QuizController.php:92
 * @route '/quizzes/{quiz}'
 */
destroy.delete = (args: { quiz: number | { id: number } } | [quiz: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

    /**
* @see \App\Http\Controllers\QuizController::destroy
 * @see app/Http/Controllers/QuizController.php:92
 * @route '/quizzes/{quiz}'
 */
    const destroyForm = (args: { quiz: number | { id: number } } | [quiz: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: destroy.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'DELETE',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\QuizController::destroy
 * @see app/Http/Controllers/QuizController.php:92
 * @route '/quizzes/{quiz}'
 */
        destroyForm.delete = (args: { quiz: number | { id: number } } | [quiz: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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
* @see \App\Http\Controllers\QuizController::bulkUpload
 * @see app/Http/Controllers/QuizController.php:104
 * @route '/quizzes/bulk-upload'
 */
export const bulkUpload = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: bulkUpload.url(options),
    method: 'post',
})

bulkUpload.definition = {
    methods: ["post"],
    url: '/quizzes/bulk-upload',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\QuizController::bulkUpload
 * @see app/Http/Controllers/QuizController.php:104
 * @route '/quizzes/bulk-upload'
 */
bulkUpload.url = (options?: RouteQueryOptions) => {
    return bulkUpload.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\QuizController::bulkUpload
 * @see app/Http/Controllers/QuizController.php:104
 * @route '/quizzes/bulk-upload'
 */
bulkUpload.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: bulkUpload.url(options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\QuizController::bulkUpload
 * @see app/Http/Controllers/QuizController.php:104
 * @route '/quizzes/bulk-upload'
 */
    const bulkUploadForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: bulkUpload.url(options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\QuizController::bulkUpload
 * @see app/Http/Controllers/QuizController.php:104
 * @route '/quizzes/bulk-upload'
 */
        bulkUploadForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: bulkUpload.url(options),
            method: 'post',
        })
    
    bulkUpload.form = bulkUploadForm
/**
* @see \App\Http\Controllers\QuizController::csvExample
 * @see app/Http/Controllers/QuizController.php:131
 * @route '/quizzes/csv-example'
 */
export const csvExample = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: csvExample.url(options),
    method: 'get',
})

csvExample.definition = {
    methods: ["get","head"],
    url: '/quizzes/csv-example',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\QuizController::csvExample
 * @see app/Http/Controllers/QuizController.php:131
 * @route '/quizzes/csv-example'
 */
csvExample.url = (options?: RouteQueryOptions) => {
    return csvExample.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\QuizController::csvExample
 * @see app/Http/Controllers/QuizController.php:131
 * @route '/quizzes/csv-example'
 */
csvExample.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: csvExample.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\QuizController::csvExample
 * @see app/Http/Controllers/QuizController.php:131
 * @route '/quizzes/csv-example'
 */
csvExample.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: csvExample.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\QuizController::csvExample
 * @see app/Http/Controllers/QuizController.php:131
 * @route '/quizzes/csv-example'
 */
    const csvExampleForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: csvExample.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\QuizController::csvExample
 * @see app/Http/Controllers/QuizController.php:131
 * @route '/quizzes/csv-example'
 */
        csvExampleForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: csvExample.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\QuizController::csvExample
 * @see app/Http/Controllers/QuizController.php:131
 * @route '/quizzes/csv-example'
 */
        csvExampleForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: csvExample.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    csvExample.form = csvExampleForm
/**
* @see \App\Http\Controllers\QuizController::csvTemplate
 * @see app/Http/Controllers/QuizController.php:145
 * @route '/quizzes/csv-template'
 */
export const csvTemplate = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: csvTemplate.url(options),
    method: 'get',
})

csvTemplate.definition = {
    methods: ["get","head"],
    url: '/quizzes/csv-template',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\QuizController::csvTemplate
 * @see app/Http/Controllers/QuizController.php:145
 * @route '/quizzes/csv-template'
 */
csvTemplate.url = (options?: RouteQueryOptions) => {
    return csvTemplate.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\QuizController::csvTemplate
 * @see app/Http/Controllers/QuizController.php:145
 * @route '/quizzes/csv-template'
 */
csvTemplate.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: csvTemplate.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\QuizController::csvTemplate
 * @see app/Http/Controllers/QuizController.php:145
 * @route '/quizzes/csv-template'
 */
csvTemplate.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: csvTemplate.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\QuizController::csvTemplate
 * @see app/Http/Controllers/QuizController.php:145
 * @route '/quizzes/csv-template'
 */
    const csvTemplateForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: csvTemplate.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\QuizController::csvTemplate
 * @see app/Http/Controllers/QuizController.php:145
 * @route '/quizzes/csv-template'
 */
        csvTemplateForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: csvTemplate.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\QuizController::csvTemplate
 * @see app/Http/Controllers/QuizController.php:145
 * @route '/quizzes/csv-template'
 */
        csvTemplateForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: csvTemplate.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    csvTemplate.form = csvTemplateForm
const quizzes = {
    index: Object.assign(index, index),
create: Object.assign(create, create),
store: Object.assign(store, store),
show: Object.assign(show, show),
edit: Object.assign(edit, edit),
update: Object.assign(update, update),
destroy: Object.assign(destroy, destroy),
bulkUpload: Object.assign(bulkUpload, bulkUpload),
csvExample: Object.assign(csvExample, csvExample),
csvTemplate: Object.assign(csvTemplate, csvTemplate),
}

export default quizzes