import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\CourseStudentController::index
 * @see app/Http/Controllers/CourseStudentController.php:17
 * @route '/courses/{course}/manage-students'
 */
export const index = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(args, options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/courses/{course}/manage-students',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\CourseStudentController::index
 * @see app/Http/Controllers/CourseStudentController.php:17
 * @route '/courses/{course}/manage-students'
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
* @see \App\Http\Controllers\CourseStudentController::index
 * @see app/Http/Controllers/CourseStudentController.php:17
 * @route '/courses/{course}/manage-students'
 */
index.get = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\CourseStudentController::index
 * @see app/Http/Controllers/CourseStudentController.php:17
 * @route '/courses/{course}/manage-students'
 */
index.head = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\CourseStudentController::index
 * @see app/Http/Controllers/CourseStudentController.php:17
 * @route '/courses/{course}/manage-students'
 */
    const indexForm = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: index.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\CourseStudentController::index
 * @see app/Http/Controllers/CourseStudentController.php:17
 * @route '/courses/{course}/manage-students'
 */
        indexForm.get = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: index.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\CourseStudentController::index
 * @see app/Http/Controllers/CourseStudentController.php:17
 * @route '/courses/{course}/manage-students'
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
* @see \App\Http\Controllers\CourseStudentController::enrollStudents
 * @see app/Http/Controllers/CourseStudentController.php:78
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
 * @see app/Http/Controllers/CourseStudentController.php:78
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
 * @see app/Http/Controllers/CourseStudentController.php:78
 * @route '/courses/{course}/enroll-students'
 */
enrollStudents.post = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: enrollStudents.url(args, options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\CourseStudentController::enrollStudents
 * @see app/Http/Controllers/CourseStudentController.php:78
 * @route '/courses/{course}/enroll-students'
 */
    const enrollStudentsForm = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: enrollStudents.url(args, options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\CourseStudentController::enrollStudents
 * @see app/Http/Controllers/CourseStudentController.php:78
 * @route '/courses/{course}/enroll-students'
 */
        enrollStudentsForm.post = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: enrollStudents.url(args, options),
            method: 'post',
        })
    
    enrollStudents.form = enrollStudentsForm
/**
* @see \App\Http\Controllers\CourseStudentController::removeStudents
 * @see app/Http/Controllers/CourseStudentController.php:131
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
 * @see app/Http/Controllers/CourseStudentController.php:131
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
 * @see app/Http/Controllers/CourseStudentController.php:131
 * @route '/courses/{course}/remove-students'
 */
removeStudents.post = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: removeStudents.url(args, options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\CourseStudentController::removeStudents
 * @see app/Http/Controllers/CourseStudentController.php:131
 * @route '/courses/{course}/remove-students'
 */
    const removeStudentsForm = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: removeStudents.url(args, options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\CourseStudentController::removeStudents
 * @see app/Http/Controllers/CourseStudentController.php:131
 * @route '/courses/{course}/remove-students'
 */
        removeStudentsForm.post = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: removeStudents.url(args, options),
            method: 'post',
        })
    
    removeStudents.form = removeStudentsForm
/**
* @see \App\Http\Controllers\CourseStudentController::getEligibleStudents
 * @see app/Http/Controllers/CourseStudentController.php:151
 * @route '/courses/{course}/eligible-students'
 */
export const getEligibleStudents = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getEligibleStudents.url(args, options),
    method: 'get',
})

getEligibleStudents.definition = {
    methods: ["get","head"],
    url: '/courses/{course}/eligible-students',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\CourseStudentController::getEligibleStudents
 * @see app/Http/Controllers/CourseStudentController.php:151
 * @route '/courses/{course}/eligible-students'
 */
getEligibleStudents.url = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return getEligibleStudents.definition.url
            .replace('{course}', parsedArgs.course.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\CourseStudentController::getEligibleStudents
 * @see app/Http/Controllers/CourseStudentController.php:151
 * @route '/courses/{course}/eligible-students'
 */
getEligibleStudents.get = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getEligibleStudents.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\CourseStudentController::getEligibleStudents
 * @see app/Http/Controllers/CourseStudentController.php:151
 * @route '/courses/{course}/eligible-students'
 */
getEligibleStudents.head = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getEligibleStudents.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\CourseStudentController::getEligibleStudents
 * @see app/Http/Controllers/CourseStudentController.php:151
 * @route '/courses/{course}/eligible-students'
 */
    const getEligibleStudentsForm = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: getEligibleStudents.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\CourseStudentController::getEligibleStudents
 * @see app/Http/Controllers/CourseStudentController.php:151
 * @route '/courses/{course}/eligible-students'
 */
        getEligibleStudentsForm.get = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: getEligibleStudents.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\CourseStudentController::getEligibleStudents
 * @see app/Http/Controllers/CourseStudentController.php:151
 * @route '/courses/{course}/eligible-students'
 */
        getEligibleStudentsForm.head = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: getEligibleStudents.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    getEligibleStudents.form = getEligibleStudentsForm
const CourseStudentController = { index, enrollStudents, removeStudents, getEligibleStudents }

export default CourseStudentController