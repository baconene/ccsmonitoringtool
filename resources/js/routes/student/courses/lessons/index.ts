import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\Student\StudentCourseController::complete
 * @see app/Http/Controllers/Student/StudentCourseController.php:291
 * @route '/student/courses/{course}/lessons/{lessonId}/complete'
 */
export const complete = (args: { course: number | { id: number }, lessonId: string | number } | [course: number | { id: number }, lessonId: string | number ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: complete.url(args, options),
    method: 'post',
})

complete.definition = {
    methods: ["post"],
    url: '/student/courses/{course}/lessons/{lessonId}/complete',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Student\StudentCourseController::complete
 * @see app/Http/Controllers/Student/StudentCourseController.php:291
 * @route '/student/courses/{course}/lessons/{lessonId}/complete'
 */
complete.url = (args: { course: number | { id: number }, lessonId: string | number } | [course: number | { id: number }, lessonId: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
                    course: args[0],
                    lessonId: args[1],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        course: typeof args.course === 'object'
                ? args.course.id
                : args.course,
                                lessonId: args.lessonId,
                }

    return complete.definition.url
            .replace('{course}', parsedArgs.course.toString())
            .replace('{lessonId}', parsedArgs.lessonId.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Student\StudentCourseController::complete
 * @see app/Http/Controllers/Student/StudentCourseController.php:291
 * @route '/student/courses/{course}/lessons/{lessonId}/complete'
 */
complete.post = (args: { course: number | { id: number }, lessonId: string | number } | [course: number | { id: number }, lessonId: string | number ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: complete.url(args, options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\Student\StudentCourseController::complete
 * @see app/Http/Controllers/Student/StudentCourseController.php:291
 * @route '/student/courses/{course}/lessons/{lessonId}/complete'
 */
    const completeForm = (args: { course: number | { id: number }, lessonId: string | number } | [course: number | { id: number }, lessonId: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: complete.url(args, options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\Student\StudentCourseController::complete
 * @see app/Http/Controllers/Student/StudentCourseController.php:291
 * @route '/student/courses/{course}/lessons/{lessonId}/complete'
 */
        completeForm.post = (args: { course: number | { id: number }, lessonId: string | number } | [course: number | { id: number }, lessonId: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: complete.url(args, options),
            method: 'post',
        })
    
    complete.form = completeForm