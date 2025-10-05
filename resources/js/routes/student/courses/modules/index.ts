import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\Student\StudentCourseController::complete
 * @see app/Http/Controllers/Student/StudentCourseController.php:280
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
 * @see app/Http/Controllers/Student/StudentCourseController.php:280
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
 * @see app/Http/Controllers/Student/StudentCourseController.php:280
 * @route '/student/courses/{course}/modules/{moduleId}/complete'
 */
complete.post = (args: { course: number | { id: number }, moduleId: string | number } | [course: number | { id: number }, moduleId: string | number ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: complete.url(args, options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\Student\StudentCourseController::complete
 * @see app/Http/Controllers/Student/StudentCourseController.php:280
 * @route '/student/courses/{course}/modules/{moduleId}/complete'
 */
    const completeForm = (args: { course: number | { id: number }, moduleId: string | number } | [course: number | { id: number }, moduleId: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: complete.url(args, options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\Student\StudentCourseController::complete
 * @see app/Http/Controllers/Student/StudentCourseController.php:280
 * @route '/student/courses/{course}/modules/{moduleId}/complete'
 */
        completeForm.post = (args: { course: number | { id: number }, moduleId: string | number } | [course: number | { id: number }, moduleId: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: complete.url(args, options),
            method: 'post',
        })
    
    complete.form = completeForm
const modules = {
    complete: Object.assign(complete, complete),
}

export default modules