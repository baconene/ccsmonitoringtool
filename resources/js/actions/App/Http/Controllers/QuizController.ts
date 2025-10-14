import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
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
* @see \App\Http\Controllers\QuizController::getCsvExample
 * @see app/Http/Controllers/QuizController.php:131
 * @route '/quizzes/csv-example'
 */
export const getCsvExample = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getCsvExample.url(options),
    method: 'get',
})

getCsvExample.definition = {
    methods: ["get","head"],
    url: '/quizzes/csv-example',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\QuizController::getCsvExample
 * @see app/Http/Controllers/QuizController.php:131
 * @route '/quizzes/csv-example'
 */
getCsvExample.url = (options?: RouteQueryOptions) => {
    return getCsvExample.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\QuizController::getCsvExample
 * @see app/Http/Controllers/QuizController.php:131
 * @route '/quizzes/csv-example'
 */
getCsvExample.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getCsvExample.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\QuizController::getCsvExample
 * @see app/Http/Controllers/QuizController.php:131
 * @route '/quizzes/csv-example'
 */
getCsvExample.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getCsvExample.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\QuizController::getCsvExample
 * @see app/Http/Controllers/QuizController.php:131
 * @route '/quizzes/csv-example'
 */
    const getCsvExampleForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: getCsvExample.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\QuizController::getCsvExample
 * @see app/Http/Controllers/QuizController.php:131
 * @route '/quizzes/csv-example'
 */
        getCsvExampleForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: getCsvExample.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\QuizController::getCsvExample
 * @see app/Http/Controllers/QuizController.php:131
 * @route '/quizzes/csv-example'
 */
        getCsvExampleForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: getCsvExample.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    getCsvExample.form = getCsvExampleForm
/**
* @see \App\Http\Controllers\QuizController::downloadCsvTemplate
 * @see app/Http/Controllers/QuizController.php:145
 * @route '/quizzes/csv-template'
 */
export const downloadCsvTemplate = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: downloadCsvTemplate.url(options),
    method: 'get',
})

downloadCsvTemplate.definition = {
    methods: ["get","head"],
    url: '/quizzes/csv-template',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\QuizController::downloadCsvTemplate
 * @see app/Http/Controllers/QuizController.php:145
 * @route '/quizzes/csv-template'
 */
downloadCsvTemplate.url = (options?: RouteQueryOptions) => {
    return downloadCsvTemplate.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\QuizController::downloadCsvTemplate
 * @see app/Http/Controllers/QuizController.php:145
 * @route '/quizzes/csv-template'
 */
downloadCsvTemplate.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: downloadCsvTemplate.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\QuizController::downloadCsvTemplate
 * @see app/Http/Controllers/QuizController.php:145
 * @route '/quizzes/csv-template'
 */
downloadCsvTemplate.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: downloadCsvTemplate.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\QuizController::downloadCsvTemplate
 * @see app/Http/Controllers/QuizController.php:145
 * @route '/quizzes/csv-template'
 */
    const downloadCsvTemplateForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: downloadCsvTemplate.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\QuizController::downloadCsvTemplate
 * @see app/Http/Controllers/QuizController.php:145
 * @route '/quizzes/csv-template'
 */
        downloadCsvTemplateForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: downloadCsvTemplate.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\QuizController::downloadCsvTemplate
 * @see app/Http/Controllers/QuizController.php:145
 * @route '/quizzes/csv-template'
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
const QuizController = { index, create, store, show, edit, update, destroy, bulkUpload, getCsvExample, downloadCsvTemplate }

export default QuizController