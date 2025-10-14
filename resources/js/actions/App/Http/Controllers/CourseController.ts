import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\CourseController::getCourses
 * @see app/Http/Controllers/CourseController.php:90
 * @route '/api/courses'
 */
export const getCourses = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getCourses.url(options),
    method: 'get',
})

getCourses.definition = {
    methods: ["get","head"],
    url: '/api/courses',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\CourseController::getCourses
 * @see app/Http/Controllers/CourseController.php:90
 * @route '/api/courses'
 */
getCourses.url = (options?: RouteQueryOptions) => {
    return getCourses.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\CourseController::getCourses
 * @see app/Http/Controllers/CourseController.php:90
 * @route '/api/courses'
 */
getCourses.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getCourses.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\CourseController::getCourses
 * @see app/Http/Controllers/CourseController.php:90
 * @route '/api/courses'
 */
getCourses.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getCourses.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\CourseController::getCourses
 * @see app/Http/Controllers/CourseController.php:90
 * @route '/api/courses'
 */
    const getCoursesForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: getCourses.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\CourseController::getCourses
 * @see app/Http/Controllers/CourseController.php:90
 * @route '/api/courses'
 */
        getCoursesForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: getCourses.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\CourseController::getCourses
 * @see app/Http/Controllers/CourseController.php:90
 * @route '/api/courses'
 */
        getCoursesForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: getCourses.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    getCourses.form = getCoursesForm
/**
* @see \App\Http\Controllers\CourseController::index
 * @see app/Http/Controllers/CourseController.php:34
 * @route '/course-management'
 */
const index0a0081d6d653fafb98e0ba542b6152c2 = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index0a0081d6d653fafb98e0ba542b6152c2.url(options),
    method: 'get',
})

index0a0081d6d653fafb98e0ba542b6152c2.definition = {
    methods: ["get","head"],
    url: '/course-management',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\CourseController::index
 * @see app/Http/Controllers/CourseController.php:34
 * @route '/course-management'
 */
index0a0081d6d653fafb98e0ba542b6152c2.url = (options?: RouteQueryOptions) => {
    return index0a0081d6d653fafb98e0ba542b6152c2.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\CourseController::index
 * @see app/Http/Controllers/CourseController.php:34
 * @route '/course-management'
 */
index0a0081d6d653fafb98e0ba542b6152c2.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index0a0081d6d653fafb98e0ba542b6152c2.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\CourseController::index
 * @see app/Http/Controllers/CourseController.php:34
 * @route '/course-management'
 */
index0a0081d6d653fafb98e0ba542b6152c2.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index0a0081d6d653fafb98e0ba542b6152c2.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\CourseController::index
 * @see app/Http/Controllers/CourseController.php:34
 * @route '/course-management'
 */
    const index0a0081d6d653fafb98e0ba542b6152c2Form = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: index0a0081d6d653fafb98e0ba542b6152c2.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\CourseController::index
 * @see app/Http/Controllers/CourseController.php:34
 * @route '/course-management'
 */
        index0a0081d6d653fafb98e0ba542b6152c2Form.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: index0a0081d6d653fafb98e0ba542b6152c2.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\CourseController::index
 * @see app/Http/Controllers/CourseController.php:34
 * @route '/course-management'
 */
        index0a0081d6d653fafb98e0ba542b6152c2Form.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: index0a0081d6d653fafb98e0ba542b6152c2.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    index0a0081d6d653fafb98e0ba542b6152c2.form = index0a0081d6d653fafb98e0ba542b6152c2Form
    /**
* @see \App\Http\Controllers\CourseController::index
 * @see app/Http/Controllers/CourseController.php:34
 * @route '/courses'
 */
const indexae0d8013bc7dd1aeb7c9b49bac5f9e3b = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: indexae0d8013bc7dd1aeb7c9b49bac5f9e3b.url(options),
    method: 'get',
})

indexae0d8013bc7dd1aeb7c9b49bac5f9e3b.definition = {
    methods: ["get","head"],
    url: '/courses',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\CourseController::index
 * @see app/Http/Controllers/CourseController.php:34
 * @route '/courses'
 */
indexae0d8013bc7dd1aeb7c9b49bac5f9e3b.url = (options?: RouteQueryOptions) => {
    return indexae0d8013bc7dd1aeb7c9b49bac5f9e3b.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\CourseController::index
 * @see app/Http/Controllers/CourseController.php:34
 * @route '/courses'
 */
indexae0d8013bc7dd1aeb7c9b49bac5f9e3b.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: indexae0d8013bc7dd1aeb7c9b49bac5f9e3b.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\CourseController::index
 * @see app/Http/Controllers/CourseController.php:34
 * @route '/courses'
 */
indexae0d8013bc7dd1aeb7c9b49bac5f9e3b.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: indexae0d8013bc7dd1aeb7c9b49bac5f9e3b.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\CourseController::index
 * @see app/Http/Controllers/CourseController.php:34
 * @route '/courses'
 */
    const indexae0d8013bc7dd1aeb7c9b49bac5f9e3bForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: indexae0d8013bc7dd1aeb7c9b49bac5f9e3b.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\CourseController::index
 * @see app/Http/Controllers/CourseController.php:34
 * @route '/courses'
 */
        indexae0d8013bc7dd1aeb7c9b49bac5f9e3bForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: indexae0d8013bc7dd1aeb7c9b49bac5f9e3b.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\CourseController::index
 * @see app/Http/Controllers/CourseController.php:34
 * @route '/courses'
 */
        indexae0d8013bc7dd1aeb7c9b49bac5f9e3bForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: indexae0d8013bc7dd1aeb7c9b49bac5f9e3b.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    indexae0d8013bc7dd1aeb7c9b49bac5f9e3b.form = indexae0d8013bc7dd1aeb7c9b49bac5f9e3bForm

export const index = {
    '/course-management': index0a0081d6d653fafb98e0ba542b6152c2,
    '/courses': indexae0d8013bc7dd1aeb7c9b49bac5f9e3b,
}

/**
* @see \App\Http\Controllers\CourseController::store
 * @see app/Http/Controllers/CourseController.php:196
 * @route '/courses'
 */
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/courses',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\CourseController::store
 * @see app/Http/Controllers/CourseController.php:196
 * @route '/courses'
 */
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\CourseController::store
 * @see app/Http/Controllers/CourseController.php:196
 * @route '/courses'
 */
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\CourseController::store
 * @see app/Http/Controllers/CourseController.php:196
 * @route '/courses'
 */
    const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: store.url(options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\CourseController::store
 * @see app/Http/Controllers/CourseController.php:196
 * @route '/courses'
 */
        storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: store.url(options),
            method: 'post',
        })
    
    store.form = storeForm
/**
* @see \App\Http\Controllers\CourseController::update
 * @see app/Http/Controllers/CourseController.php:288
 * @route '/courses/{course}'
 */
export const update = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put","patch"],
    url: '/courses/{course}',
} satisfies RouteDefinition<["put","patch"]>

/**
* @see \App\Http\Controllers\CourseController::update
 * @see app/Http/Controllers/CourseController.php:288
 * @route '/courses/{course}'
 */
update.url = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return update.definition.url
            .replace('{course}', parsedArgs.course.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\CourseController::update
 * @see app/Http/Controllers/CourseController.php:288
 * @route '/courses/{course}'
 */
update.put = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})
/**
* @see \App\Http\Controllers\CourseController::update
 * @see app/Http/Controllers/CourseController.php:288
 * @route '/courses/{course}'
 */
update.patch = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

    /**
* @see \App\Http\Controllers\CourseController::update
 * @see app/Http/Controllers/CourseController.php:288
 * @route '/courses/{course}'
 */
    const updateForm = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: update.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'PUT',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\CourseController::update
 * @see app/Http/Controllers/CourseController.php:288
 * @route '/courses/{course}'
 */
        updateForm.put = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: update.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'PUT',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
            /**
* @see \App\Http\Controllers\CourseController::update
 * @see app/Http/Controllers/CourseController.php:288
 * @route '/courses/{course}'
 */
        updateForm.patch = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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
* @see \App\Http\Controllers\CourseController::destroy
 * @see app/Http/Controllers/CourseController.php:431
 * @route '/courses/{course}'
 */
export const destroy = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/courses/{course}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\CourseController::destroy
 * @see app/Http/Controllers/CourseController.php:431
 * @route '/courses/{course}'
 */
destroy.url = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return destroy.definition.url
            .replace('{course}', parsedArgs.course.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\CourseController::destroy
 * @see app/Http/Controllers/CourseController.php:431
 * @route '/courses/{course}'
 */
destroy.delete = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

    /**
* @see \App\Http\Controllers\CourseController::destroy
 * @see app/Http/Controllers/CourseController.php:431
 * @route '/courses/{course}'
 */
    const destroyForm = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: destroy.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'DELETE',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\CourseController::destroy
 * @see app/Http/Controllers/CourseController.php:431
 * @route '/courses/{course}'
 */
        destroyForm.delete = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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
* @see \App\Http\Controllers\CourseController::assignGradeLevels
 * @see app/Http/Controllers/CourseController.php:479
 * @route '/courses/{course}/assign-grade-levels'
 */
export const assignGradeLevels = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: assignGradeLevels.url(args, options),
    method: 'post',
})

assignGradeLevels.definition = {
    methods: ["post"],
    url: '/courses/{course}/assign-grade-levels',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\CourseController::assignGradeLevels
 * @see app/Http/Controllers/CourseController.php:479
 * @route '/courses/{course}/assign-grade-levels'
 */
assignGradeLevels.url = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return assignGradeLevels.definition.url
            .replace('{course}', parsedArgs.course.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\CourseController::assignGradeLevels
 * @see app/Http/Controllers/CourseController.php:479
 * @route '/courses/{course}/assign-grade-levels'
 */
assignGradeLevels.post = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: assignGradeLevels.url(args, options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\CourseController::assignGradeLevels
 * @see app/Http/Controllers/CourseController.php:479
 * @route '/courses/{course}/assign-grade-levels'
 */
    const assignGradeLevelsForm = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: assignGradeLevels.url(args, options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\CourseController::assignGradeLevels
 * @see app/Http/Controllers/CourseController.php:479
 * @route '/courses/{course}/assign-grade-levels'
 */
        assignGradeLevelsForm.post = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: assignGradeLevels.url(args, options),
            method: 'post',
        })
    
    assignGradeLevels.form = assignGradeLevelsForm
/**
* @see \App\Http\Controllers\CourseController::getAvailableGradeLevels
 * @see app/Http/Controllers/CourseController.php:520
 * @route '/grade-levels'
 */
export const getAvailableGradeLevels = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getAvailableGradeLevels.url(options),
    method: 'get',
})

getAvailableGradeLevels.definition = {
    methods: ["get","head"],
    url: '/grade-levels',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\CourseController::getAvailableGradeLevels
 * @see app/Http/Controllers/CourseController.php:520
 * @route '/grade-levels'
 */
getAvailableGradeLevels.url = (options?: RouteQueryOptions) => {
    return getAvailableGradeLevels.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\CourseController::getAvailableGradeLevels
 * @see app/Http/Controllers/CourseController.php:520
 * @route '/grade-levels'
 */
getAvailableGradeLevels.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getAvailableGradeLevels.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\CourseController::getAvailableGradeLevels
 * @see app/Http/Controllers/CourseController.php:520
 * @route '/grade-levels'
 */
getAvailableGradeLevels.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getAvailableGradeLevels.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\CourseController::getAvailableGradeLevels
 * @see app/Http/Controllers/CourseController.php:520
 * @route '/grade-levels'
 */
    const getAvailableGradeLevelsForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: getAvailableGradeLevels.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\CourseController::getAvailableGradeLevels
 * @see app/Http/Controllers/CourseController.php:520
 * @route '/grade-levels'
 */
        getAvailableGradeLevelsForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: getAvailableGradeLevels.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\CourseController::getAvailableGradeLevels
 * @see app/Http/Controllers/CourseController.php:520
 * @route '/grade-levels'
 */
        getAvailableGradeLevelsForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: getAvailableGradeLevels.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    getAvailableGradeLevels.form = getAvailableGradeLevelsForm
/**
* @see \App\Http\Controllers\CourseController::getCoursesForGradeLevel
 * @see app/Http/Controllers/CourseController.php:552
 * @route '/courses/by-grade-level'
 */
export const getCoursesForGradeLevel = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getCoursesForGradeLevel.url(options),
    method: 'get',
})

getCoursesForGradeLevel.definition = {
    methods: ["get","head"],
    url: '/courses/by-grade-level',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\CourseController::getCoursesForGradeLevel
 * @see app/Http/Controllers/CourseController.php:552
 * @route '/courses/by-grade-level'
 */
getCoursesForGradeLevel.url = (options?: RouteQueryOptions) => {
    return getCoursesForGradeLevel.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\CourseController::getCoursesForGradeLevel
 * @see app/Http/Controllers/CourseController.php:552
 * @route '/courses/by-grade-level'
 */
getCoursesForGradeLevel.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getCoursesForGradeLevel.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\CourseController::getCoursesForGradeLevel
 * @see app/Http/Controllers/CourseController.php:552
 * @route '/courses/by-grade-level'
 */
getCoursesForGradeLevel.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getCoursesForGradeLevel.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\CourseController::getCoursesForGradeLevel
 * @see app/Http/Controllers/CourseController.php:552
 * @route '/courses/by-grade-level'
 */
    const getCoursesForGradeLevelForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: getCoursesForGradeLevel.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\CourseController::getCoursesForGradeLevel
 * @see app/Http/Controllers/CourseController.php:552
 * @route '/courses/by-grade-level'
 */
        getCoursesForGradeLevelForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: getCoursesForGradeLevel.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\CourseController::getCoursesForGradeLevel
 * @see app/Http/Controllers/CourseController.php:552
 * @route '/courses/by-grade-level'
 */
        getCoursesForGradeLevelForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: getCoursesForGradeLevel.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    getCoursesForGradeLevel.form = getCoursesForGradeLevelForm
const CourseController = { getCourses, index, store, update, destroy, assignGradeLevels, getAvailableGradeLevels, getCoursesForGradeLevel }

export default CourseController