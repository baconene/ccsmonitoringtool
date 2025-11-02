import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\Student\StudentCourseController::show
 * @see app/Http/Controllers/Student/StudentCourseController.php:669
 * @route '/student/courses/{course}/modules/{moduleId}'
 */
export const show = (args: { course: number | { id: number }, moduleId: string | number } | [course: number | { id: number }, moduleId: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/student/courses/{course}/modules/{moduleId}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Student\StudentCourseController::show
 * @see app/Http/Controllers/Student/StudentCourseController.php:669
 * @route '/student/courses/{course}/modules/{moduleId}'
 */
show.url = (args: { course: number | { id: number }, moduleId: string | number } | [course: number | { id: number }, moduleId: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
                    course: args[0],
                    moduleId: args[1],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        course: typeof args.course === 'object'
                ? args.course.id
                : args.course,
                                moduleId: args.moduleId,
                }

    return show.definition.url
            .replace('{course}', parsedArgs.course.toString())
            .replace('{moduleId}', parsedArgs.moduleId.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Student\StudentCourseController::show
 * @see app/Http/Controllers/Student/StudentCourseController.php:669
 * @route '/student/courses/{course}/modules/{moduleId}'
 */
show.get = (args: { course: number | { id: number }, moduleId: string | number } | [course: number | { id: number }, moduleId: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Student\StudentCourseController::show
 * @see app/Http/Controllers/Student/StudentCourseController.php:669
 * @route '/student/courses/{course}/modules/{moduleId}'
 */
show.head = (args: { course: number | { id: number }, moduleId: string | number } | [course: number | { id: number }, moduleId: string | number ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\Student\StudentCourseController::show
 * @see app/Http/Controllers/Student/StudentCourseController.php:669
 * @route '/student/courses/{course}/modules/{moduleId}'
 */
    const showForm = (args: { course: number | { id: number }, moduleId: string | number } | [course: number | { id: number }, moduleId: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: show.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\Student\StudentCourseController::show
 * @see app/Http/Controllers/Student/StudentCourseController.php:669
 * @route '/student/courses/{course}/modules/{moduleId}'
 */
        showForm.get = (args: { course: number | { id: number }, moduleId: string | number } | [course: number | { id: number }, moduleId: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: show.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\Student\StudentCourseController::show
 * @see app/Http/Controllers/Student/StudentCourseController.php:669
 * @route '/student/courses/{course}/modules/{moduleId}'
 */
        showForm.head = (args: { course: number | { id: number }, moduleId: string | number } | [course: number | { id: number }, moduleId: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
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
* @see \App\Http\Controllers\Student\StudentCourseController::complete
 * @see app/Http/Controllers/Student/StudentCourseController.php:429
 * @route '/student/courses/{course}/modules/{moduleId}/complete'
 */
export const complete = (args: { course: number | { id: number }, moduleId: string | number } | [course: number | { id: number }, moduleId: string | number ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: complete.url(args, options),
    method: 'post',
})

complete.definition = {
    methods: ["post"],
    url: '/student/courses/{course}/modules/{moduleId}/complete',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Student\StudentCourseController::complete
 * @see app/Http/Controllers/Student/StudentCourseController.php:429
 * @route '/student/courses/{course}/modules/{moduleId}/complete'
 */
complete.url = (args: { course: number | { id: number }, moduleId: string | number } | [course: number | { id: number }, moduleId: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
                    course: args[0],
                    moduleId: args[1],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        course: typeof args.course === 'object'
                ? args.course.id
                : args.course,
                                moduleId: args.moduleId,
                }

    return complete.definition.url
            .replace('{course}', parsedArgs.course.toString())
            .replace('{moduleId}', parsedArgs.moduleId.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Student\StudentCourseController::complete
 * @see app/Http/Controllers/Student/StudentCourseController.php:429
 * @route '/student/courses/{course}/modules/{moduleId}/complete'
 */
complete.post = (args: { course: number | { id: number }, moduleId: string | number } | [course: number | { id: number }, moduleId: string | number ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: complete.url(args, options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\Student\StudentCourseController::complete
 * @see app/Http/Controllers/Student/StudentCourseController.php:429
 * @route '/student/courses/{course}/modules/{moduleId}/complete'
 */
    const completeForm = (args: { course: number | { id: number }, moduleId: string | number } | [course: number | { id: number }, moduleId: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: complete.url(args, options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\Student\StudentCourseController::complete
 * @see app/Http/Controllers/Student/StudentCourseController.php:429
 * @route '/student/courses/{course}/modules/{moduleId}/complete'
 */
        completeForm.post = (args: { course: number | { id: number }, moduleId: string | number } | [course: number | { id: number }, moduleId: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: complete.url(args, options),
            method: 'post',
        })
    
    complete.form = completeForm
const modules = {
    show: Object.assign(show, show),
complete: Object.assign(complete, complete),
}

export default modules