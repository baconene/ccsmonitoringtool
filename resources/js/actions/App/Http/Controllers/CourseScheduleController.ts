import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\CourseScheduleController::index
 * @see app/Http/Controllers/CourseScheduleController.php:19
 * @route '/courses/{course}/schedules'
 */
export const index = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(args, options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/courses/{course}/schedules',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\CourseScheduleController::index
 * @see app/Http/Controllers/CourseScheduleController.php:19
 * @route '/courses/{course}/schedules'
 */
index.url = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return index.definition.url
            .replace('{course}', parsedArgs.course.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\CourseScheduleController::index
 * @see app/Http/Controllers/CourseScheduleController.php:19
 * @route '/courses/{course}/schedules'
 */
index.get = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\CourseScheduleController::index
 * @see app/Http/Controllers/CourseScheduleController.php:19
 * @route '/courses/{course}/schedules'
 */
index.head = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\CourseScheduleController::index
 * @see app/Http/Controllers/CourseScheduleController.php:19
 * @route '/courses/{course}/schedules'
 */
    const indexForm = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: index.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\CourseScheduleController::index
 * @see app/Http/Controllers/CourseScheduleController.php:19
 * @route '/courses/{course}/schedules'
 */
        indexForm.get = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: index.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\CourseScheduleController::index
 * @see app/Http/Controllers/CourseScheduleController.php:19
 * @route '/courses/{course}/schedules'
 */
        indexForm.head = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: index.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    index.form = indexForm
/**
* @see \App\Http\Controllers\CourseScheduleController::store
 * @see app/Http/Controllers/CourseScheduleController.php:50
 * @route '/courses/{course}/schedules'
 */
export const store = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/courses/{course}/schedules',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\CourseScheduleController::store
 * @see app/Http/Controllers/CourseScheduleController.php:50
 * @route '/courses/{course}/schedules'
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
* @see \App\Http\Controllers\CourseScheduleController::store
 * @see app/Http/Controllers/CourseScheduleController.php:50
 * @route '/courses/{course}/schedules'
 */
store.post = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\CourseScheduleController::store
 * @see app/Http/Controllers/CourseScheduleController.php:50
 * @route '/courses/{course}/schedules'
 */
    const storeForm = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: store.url(args, options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\CourseScheduleController::store
 * @see app/Http/Controllers/CourseScheduleController.php:50
 * @route '/courses/{course}/schedules'
 */
        storeForm.post = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: store.url(args, options),
            method: 'post',
        })
    
    store.form = storeForm
/**
* @see \App\Http\Controllers\CourseScheduleController::update
 * @see app/Http/Controllers/CourseScheduleController.php:132
 * @route '/courses/{course}/schedules/{schedule}'
 */
export const update = (args: { course: number | { id: number }, schedule: number | { id: number } } | [course: number | { id: number }, schedule: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/courses/{course}/schedules/{schedule}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\CourseScheduleController::update
 * @see app/Http/Controllers/CourseScheduleController.php:132
 * @route '/courses/{course}/schedules/{schedule}'
 */
update.url = (args: { course: number | { id: number }, schedule: number | { id: number } } | [course: number | { id: number }, schedule: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
                    course: args[0],
                    schedule: args[1],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        course: typeof args.course === 'object'
                ? args.course.id
                : args.course,
                                schedule: typeof args.schedule === 'object'
                ? args.schedule.id
                : args.schedule,
                }

    return update.definition.url
            .replace('{course}', parsedArgs.course.toString())
            .replace('{schedule}', parsedArgs.schedule.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\CourseScheduleController::update
 * @see app/Http/Controllers/CourseScheduleController.php:132
 * @route '/courses/{course}/schedules/{schedule}'
 */
update.put = (args: { course: number | { id: number }, schedule: number | { id: number } } | [course: number | { id: number }, schedule: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

    /**
* @see \App\Http\Controllers\CourseScheduleController::update
 * @see app/Http/Controllers/CourseScheduleController.php:132
 * @route '/courses/{course}/schedules/{schedule}'
 */
    const updateForm = (args: { course: number | { id: number }, schedule: number | { id: number } } | [course: number | { id: number }, schedule: number | { id: number } ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: update.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'PUT',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\CourseScheduleController::update
 * @see app/Http/Controllers/CourseScheduleController.php:132
 * @route '/courses/{course}/schedules/{schedule}'
 */
        updateForm.put = (args: { course: number | { id: number }, schedule: number | { id: number } } | [course: number | { id: number }, schedule: number | { id: number } ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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
* @see \App\Http\Controllers\CourseScheduleController::destroy
 * @see app/Http/Controllers/CourseScheduleController.php:203
 * @route '/courses/{course}/schedules/{schedule}'
 */
export const destroy = (args: { course: number | { id: number }, schedule: number | { id: number } } | [course: number | { id: number }, schedule: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/courses/{course}/schedules/{schedule}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\CourseScheduleController::destroy
 * @see app/Http/Controllers/CourseScheduleController.php:203
 * @route '/courses/{course}/schedules/{schedule}'
 */
destroy.url = (args: { course: number | { id: number }, schedule: number | { id: number } } | [course: number | { id: number }, schedule: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
                    course: args[0],
                    schedule: args[1],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        course: typeof args.course === 'object'
                ? args.course.id
                : args.course,
                                schedule: typeof args.schedule === 'object'
                ? args.schedule.id
                : args.schedule,
                }

    return destroy.definition.url
            .replace('{course}', parsedArgs.course.toString())
            .replace('{schedule}', parsedArgs.schedule.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\CourseScheduleController::destroy
 * @see app/Http/Controllers/CourseScheduleController.php:203
 * @route '/courses/{course}/schedules/{schedule}'
 */
destroy.delete = (args: { course: number | { id: number }, schedule: number | { id: number } } | [course: number | { id: number }, schedule: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

    /**
* @see \App\Http\Controllers\CourseScheduleController::destroy
 * @see app/Http/Controllers/CourseScheduleController.php:203
 * @route '/courses/{course}/schedules/{schedule}'
 */
    const destroyForm = (args: { course: number | { id: number }, schedule: number | { id: number } } | [course: number | { id: number }, schedule: number | { id: number } ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: destroy.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'DELETE',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\CourseScheduleController::destroy
 * @see app/Http/Controllers/CourseScheduleController.php:203
 * @route '/courses/{course}/schedules/{schedule}'
 */
        destroyForm.delete = (args: { course: number | { id: number }, schedule: number | { id: number } } | [course: number | { id: number }, schedule: number | { id: number } ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: destroy.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'DELETE',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    destroy.form = destroyForm
const CourseScheduleController = { index, store, update, destroy }

export default CourseScheduleController