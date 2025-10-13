import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../wayfinder'
import enrollments889e66 from './enrollments'
/**
* @see \App\Http\Controllers\CourseController::index
 * @see app/Http/Controllers/CourseController.php:33
 * @route '/courses'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/courses',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\CourseController::index
 * @see app/Http/Controllers/CourseController.php:33
 * @route '/courses'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\CourseController::index
 * @see app/Http/Controllers/CourseController.php:33
 * @route '/courses'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\CourseController::index
 * @see app/Http/Controllers/CourseController.php:33
 * @route '/courses'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\CourseController::index
 * @see app/Http/Controllers/CourseController.php:33
 * @route '/courses'
 */
    const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: index.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\CourseController::index
 * @see app/Http/Controllers/CourseController.php:33
 * @route '/courses'
 */
        indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: index.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\CourseController::index
 * @see app/Http/Controllers/CourseController.php:33
 * @route '/courses'
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
* @see \App\Http\Controllers\CourseController::store
 * @see app/Http/Controllers/CourseController.php:129
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
 * @see app/Http/Controllers/CourseController.php:129
 * @route '/courses'
 */
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\CourseController::store
 * @see app/Http/Controllers/CourseController.php:129
 * @route '/courses'
 */
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\CourseController::store
 * @see app/Http/Controllers/CourseController.php:129
 * @route '/courses'
 */
    const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: store.url(options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\CourseController::store
 * @see app/Http/Controllers/CourseController.php:129
 * @route '/courses'
 */
        storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: store.url(options),
            method: 'post',
        })
    
    store.form = storeForm
/**
* @see \App\Http\Controllers\CourseController::update
 * @see app/Http/Controllers/CourseController.php:216
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
 * @see app/Http/Controllers/CourseController.php:216
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
 * @see app/Http/Controllers/CourseController.php:216
 * @route '/courses/{course}'
 */
update.put = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})
/**
* @see \App\Http\Controllers\CourseController::update
 * @see app/Http/Controllers/CourseController.php:216
 * @route '/courses/{course}'
 */
update.patch = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

    /**
* @see \App\Http\Controllers\CourseController::update
 * @see app/Http/Controllers/CourseController.php:216
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
 * @see app/Http/Controllers/CourseController.php:216
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
 * @see app/Http/Controllers/CourseController.php:216
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
 * @see app/Http/Controllers/CourseController.php:346
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
 * @see app/Http/Controllers/CourseController.php:346
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
 * @see app/Http/Controllers/CourseController.php:346
 * @route '/courses/{course}'
 */
destroy.delete = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

    /**
* @see \App\Http\Controllers\CourseController::destroy
 * @see app/Http/Controllers/CourseController.php:346
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
 * @see app/Http/Controllers/CourseController.php:346
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
 * @see app/Http/Controllers/CourseController.php:394
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
 * @see app/Http/Controllers/CourseController.php:394
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
 * @see app/Http/Controllers/CourseController.php:394
 * @route '/courses/{course}/assign-grade-levels'
 */
assignGradeLevels.post = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: assignGradeLevels.url(args, options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\CourseController::assignGradeLevels
 * @see app/Http/Controllers/CourseController.php:394
 * @route '/courses/{course}/assign-grade-levels'
 */
    const assignGradeLevelsForm = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: assignGradeLevels.url(args, options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\CourseController::assignGradeLevels
 * @see app/Http/Controllers/CourseController.php:394
 * @route '/courses/{course}/assign-grade-levels'
 */
        assignGradeLevelsForm.post = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: assignGradeLevels.url(args, options),
            method: 'post',
        })
    
    assignGradeLevels.form = assignGradeLevelsForm
/**
* @see \App\Http\Controllers\CourseController::byGradeLevel
 * @see app/Http/Controllers/CourseController.php:467
 * @route '/courses/by-grade-level'
 */
export const byGradeLevel = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: byGradeLevel.url(options),
    method: 'get',
})

byGradeLevel.definition = {
    methods: ["get","head"],
    url: '/courses/by-grade-level',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\CourseController::byGradeLevel
 * @see app/Http/Controllers/CourseController.php:467
 * @route '/courses/by-grade-level'
 */
byGradeLevel.url = (options?: RouteQueryOptions) => {
    return byGradeLevel.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\CourseController::byGradeLevel
 * @see app/Http/Controllers/CourseController.php:467
 * @route '/courses/by-grade-level'
 */
byGradeLevel.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: byGradeLevel.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\CourseController::byGradeLevel
 * @see app/Http/Controllers/CourseController.php:467
 * @route '/courses/by-grade-level'
 */
byGradeLevel.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: byGradeLevel.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\CourseController::byGradeLevel
 * @see app/Http/Controllers/CourseController.php:467
 * @route '/courses/by-grade-level'
 */
    const byGradeLevelForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: byGradeLevel.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\CourseController::byGradeLevel
 * @see app/Http/Controllers/CourseController.php:467
 * @route '/courses/by-grade-level'
 */
        byGradeLevelForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: byGradeLevel.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\CourseController::byGradeLevel
 * @see app/Http/Controllers/CourseController.php:467
 * @route '/courses/by-grade-level'
 */
        byGradeLevelForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: byGradeLevel.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    byGradeLevel.form = byGradeLevelForm
/**
* @see \App\Http\Controllers\CourseStudentController::manageStudents
 * @see app/Http/Controllers/CourseStudentController.php:27
 * @route '/courses/{course}/manage-students'
 */
export const manageStudents = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: manageStudents.url(args, options),
    method: 'get',
})

manageStudents.definition = {
    methods: ["get","head"],
    url: '/courses/{course}/manage-students',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\CourseStudentController::manageStudents
 * @see app/Http/Controllers/CourseStudentController.php:27
 * @route '/courses/{course}/manage-students'
 */
manageStudents.url = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return manageStudents.definition.url
            .replace('{course}', parsedArgs.course.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\CourseStudentController::manageStudents
 * @see app/Http/Controllers/CourseStudentController.php:27
 * @route '/courses/{course}/manage-students'
 */
manageStudents.get = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: manageStudents.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\CourseStudentController::manageStudents
 * @see app/Http/Controllers/CourseStudentController.php:27
 * @route '/courses/{course}/manage-students'
 */
manageStudents.head = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: manageStudents.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\CourseStudentController::manageStudents
 * @see app/Http/Controllers/CourseStudentController.php:27
 * @route '/courses/{course}/manage-students'
 */
    const manageStudentsForm = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: manageStudents.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\CourseStudentController::manageStudents
 * @see app/Http/Controllers/CourseStudentController.php:27
 * @route '/courses/{course}/manage-students'
 */
        manageStudentsForm.get = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: manageStudents.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\CourseStudentController::manageStudents
 * @see app/Http/Controllers/CourseStudentController.php:27
 * @route '/courses/{course}/manage-students'
 */
        manageStudentsForm.head = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: manageStudents.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    manageStudents.form = manageStudentsForm
/**
* @see \App\Http\Controllers\CourseStudentController::enrollStudents
 * @see app/Http/Controllers/CourseStudentController.php:98
 * @route '/courses/{course}/enroll-students'
 */
export const enrollStudents = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: enrollStudents.url(args, options),
    method: 'post',
})

enrollStudents.definition = {
    methods: ["post"],
    url: '/courses/{course}/enroll-students',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\CourseStudentController::enrollStudents
 * @see app/Http/Controllers/CourseStudentController.php:98
 * @route '/courses/{course}/enroll-students'
 */
enrollStudents.url = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return enrollStudents.definition.url
            .replace('{course}', parsedArgs.course.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\CourseStudentController::enrollStudents
 * @see app/Http/Controllers/CourseStudentController.php:98
 * @route '/courses/{course}/enroll-students'
 */
enrollStudents.post = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: enrollStudents.url(args, options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\CourseStudentController::enrollStudents
 * @see app/Http/Controllers/CourseStudentController.php:98
 * @route '/courses/{course}/enroll-students'
 */
    const enrollStudentsForm = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: enrollStudents.url(args, options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\CourseStudentController::enrollStudents
 * @see app/Http/Controllers/CourseStudentController.php:98
 * @route '/courses/{course}/enroll-students'
 */
        enrollStudentsForm.post = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: enrollStudents.url(args, options),
            method: 'post',
        })
    
    enrollStudents.form = enrollStudentsForm
/**
* @see \App\Http\Controllers\CourseStudentController::removeStudents
 * @see app/Http/Controllers/CourseStudentController.php:186
 * @route '/courses/{course}/remove-students'
 */
export const removeStudents = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: removeStudents.url(args, options),
    method: 'post',
})

removeStudents.definition = {
    methods: ["post"],
    url: '/courses/{course}/remove-students',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\CourseStudentController::removeStudents
 * @see app/Http/Controllers/CourseStudentController.php:186
 * @route '/courses/{course}/remove-students'
 */
removeStudents.url = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return removeStudents.definition.url
            .replace('{course}', parsedArgs.course.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\CourseStudentController::removeStudents
 * @see app/Http/Controllers/CourseStudentController.php:186
 * @route '/courses/{course}/remove-students'
 */
removeStudents.post = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: removeStudents.url(args, options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\CourseStudentController::removeStudents
 * @see app/Http/Controllers/CourseStudentController.php:186
 * @route '/courses/{course}/remove-students'
 */
    const removeStudentsForm = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: removeStudents.url(args, options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\CourseStudentController::removeStudents
 * @see app/Http/Controllers/CourseStudentController.php:186
 * @route '/courses/{course}/remove-students'
 */
        removeStudentsForm.post = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: removeStudents.url(args, options),
            method: 'post',
        })
    
    removeStudents.form = removeStudentsForm
/**
* @see \App\Http\Controllers\CourseStudentController::eligibleStudents
 * @see app/Http/Controllers/CourseStudentController.php:254
 * @route '/courses/{course}/eligible-students'
 */
export const eligibleStudents = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: eligibleStudents.url(args, options),
    method: 'get',
})

eligibleStudents.definition = {
    methods: ["get","head"],
    url: '/courses/{course}/eligible-students',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\CourseStudentController::eligibleStudents
 * @see app/Http/Controllers/CourseStudentController.php:254
 * @route '/courses/{course}/eligible-students'
 */
eligibleStudents.url = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return eligibleStudents.definition.url
            .replace('{course}', parsedArgs.course.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\CourseStudentController::eligibleStudents
 * @see app/Http/Controllers/CourseStudentController.php:254
 * @route '/courses/{course}/eligible-students'
 */
eligibleStudents.get = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: eligibleStudents.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\CourseStudentController::eligibleStudents
 * @see app/Http/Controllers/CourseStudentController.php:254
 * @route '/courses/{course}/eligible-students'
 */
eligibleStudents.head = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: eligibleStudents.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\CourseStudentController::eligibleStudents
 * @see app/Http/Controllers/CourseStudentController.php:254
 * @route '/courses/{course}/eligible-students'
 */
    const eligibleStudentsForm = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: eligibleStudents.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\CourseStudentController::eligibleStudents
 * @see app/Http/Controllers/CourseStudentController.php:254
 * @route '/courses/{course}/eligible-students'
 */
        eligibleStudentsForm.get = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: eligibleStudents.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\CourseStudentController::eligibleStudents
 * @see app/Http/Controllers/CourseStudentController.php:254
 * @route '/courses/{course}/eligible-students'
 */
        eligibleStudentsForm.head = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: eligibleStudents.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    eligibleStudents.form = eligibleStudentsForm
/**
* @see \App\Http\Controllers\CourseStudentController::enrollmentStatistics
 * @see app/Http/Controllers/CourseStudentController.php:293
 * @route '/courses/{course}/enrollment-statistics'
 */
export const enrollmentStatistics = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: enrollmentStatistics.url(args, options),
    method: 'get',
})

enrollmentStatistics.definition = {
    methods: ["get","head"],
    url: '/courses/{course}/enrollment-statistics',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\CourseStudentController::enrollmentStatistics
 * @see app/Http/Controllers/CourseStudentController.php:293
 * @route '/courses/{course}/enrollment-statistics'
 */
enrollmentStatistics.url = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return enrollmentStatistics.definition.url
            .replace('{course}', parsedArgs.course.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\CourseStudentController::enrollmentStatistics
 * @see app/Http/Controllers/CourseStudentController.php:293
 * @route '/courses/{course}/enrollment-statistics'
 */
enrollmentStatistics.get = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: enrollmentStatistics.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\CourseStudentController::enrollmentStatistics
 * @see app/Http/Controllers/CourseStudentController.php:293
 * @route '/courses/{course}/enrollment-statistics'
 */
enrollmentStatistics.head = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: enrollmentStatistics.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\CourseStudentController::enrollmentStatistics
 * @see app/Http/Controllers/CourseStudentController.php:293
 * @route '/courses/{course}/enrollment-statistics'
 */
    const enrollmentStatisticsForm = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: enrollmentStatistics.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\CourseStudentController::enrollmentStatistics
 * @see app/Http/Controllers/CourseStudentController.php:293
 * @route '/courses/{course}/enrollment-statistics'
 */
        enrollmentStatisticsForm.get = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: enrollmentStatistics.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\CourseStudentController::enrollmentStatistics
 * @see app/Http/Controllers/CourseStudentController.php:293
 * @route '/courses/{course}/enrollment-statistics'
 */
        enrollmentStatisticsForm.head = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: enrollmentStatistics.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    enrollmentStatistics.form = enrollmentStatisticsForm
/**
* @see \App\Http\Controllers\CourseStudentController::enrollments
 * @see app/Http/Controllers/CourseStudentController.php:319
 * @route '/courses/{course}/enrollments'
 */
export const enrollments = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: enrollments.url(args, options),
    method: 'get',
})

enrollments.definition = {
    methods: ["get","head"],
    url: '/courses/{course}/enrollments',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\CourseStudentController::enrollments
 * @see app/Http/Controllers/CourseStudentController.php:319
 * @route '/courses/{course}/enrollments'
 */
enrollments.url = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return enrollments.definition.url
            .replace('{course}', parsedArgs.course.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\CourseStudentController::enrollments
 * @see app/Http/Controllers/CourseStudentController.php:319
 * @route '/courses/{course}/enrollments'
 */
enrollments.get = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: enrollments.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\CourseStudentController::enrollments
 * @see app/Http/Controllers/CourseStudentController.php:319
 * @route '/courses/{course}/enrollments'
 */
enrollments.head = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: enrollments.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\CourseStudentController::enrollments
 * @see app/Http/Controllers/CourseStudentController.php:319
 * @route '/courses/{course}/enrollments'
 */
    const enrollmentsForm = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: enrollments.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\CourseStudentController::enrollments
 * @see app/Http/Controllers/CourseStudentController.php:319
 * @route '/courses/{course}/enrollments'
 */
        enrollmentsForm.get = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: enrollments.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\CourseStudentController::enrollments
 * @see app/Http/Controllers/CourseStudentController.php:319
 * @route '/courses/{course}/enrollments'
 */
        enrollmentsForm.head = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: enrollments.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    enrollments.form = enrollmentsForm
const courses = {
    index: Object.assign(index, index),
store: Object.assign(store, store),
update: Object.assign(update, update),
destroy: Object.assign(destroy, destroy),
assignGradeLevels: Object.assign(assignGradeLevels, assignGradeLevels),
byGradeLevel: Object.assign(byGradeLevel, byGradeLevel),
manageStudents: Object.assign(manageStudents, manageStudents),
enrollStudents: Object.assign(enrollStudents, enrollStudents),
removeStudents: Object.assign(removeStudents, removeStudents),
eligibleStudents: Object.assign(eligibleStudents, eligibleStudents),
enrollmentStatistics: Object.assign(enrollmentStatistics, enrollmentStatistics),
enrollments: Object.assign(enrollments, enrollments889e66),
}

export default courses