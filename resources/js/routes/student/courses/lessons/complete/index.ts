import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
 * @see routes/web.php:362
 * @route '/student/courses/{course}/lessons/{lessonId}/complete-test'
 */
export const test = (args: { course: number | { id: number }, lessonId: string | number } | [course: number | { id: number }, lessonId: string | number ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: test.url(args, options),
    method: 'post',
})

test.definition = {
    methods: ["post"],
    url: '/student/courses/{course}/lessons/{lessonId}/complete-test',
} satisfies RouteDefinition<["post"]>

/**
 * @see routes/web.php:362
 * @route '/student/courses/{course}/lessons/{lessonId}/complete-test'
 */
test.url = (args: { course: number | { id: number }, lessonId: string | number } | [course: number | { id: number }, lessonId: string | number ], options?: RouteQueryOptions) => {
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

    return test.definition.url
            .replace('{course}', parsedArgs.course.toString())
            .replace('{lessonId}', parsedArgs.lessonId.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
 * @see routes/web.php:362
 * @route '/student/courses/{course}/lessons/{lessonId}/complete-test'
 */
test.post = (args: { course: number | { id: number }, lessonId: string | number } | [course: number | { id: number }, lessonId: string | number ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: test.url(args, options),
    method: 'post',
})

    /**
 * @see routes/web.php:362
 * @route '/student/courses/{course}/lessons/{lessonId}/complete-test'
 */
    const testForm = (args: { course: number | { id: number }, lessonId: string | number } | [course: number | { id: number }, lessonId: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: test.url(args, options),
        method: 'post',
    })

            /**
 * @see routes/web.php:362
 * @route '/student/courses/{course}/lessons/{lessonId}/complete-test'
 */
        testForm.post = (args: { course: number | { id: number }, lessonId: string | number } | [course: number | { id: number }, lessonId: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: test.url(args, options),
            method: 'post',
        })
    
    test.form = testForm