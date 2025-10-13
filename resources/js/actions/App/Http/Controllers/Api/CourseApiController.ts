import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\CourseApiController::getStudents
 * @see app/Http/Controllers/Api/CourseApiController.php:161
 * @route '/api/courses/{course}/students'
 */
export const getStudents = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getStudents.url(args, options),
    method: 'get',
})

getStudents.definition = {
    methods: ["get","head"],
    url: '/api/courses/{course}/students',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\CourseApiController::getStudents
 * @see app/Http/Controllers/Api/CourseApiController.php:161
 * @route '/api/courses/{course}/students'
 */
getStudents.url = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return getStudents.definition.url
            .replace('{course}', parsedArgs.course.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\CourseApiController::getStudents
 * @see app/Http/Controllers/Api/CourseApiController.php:161
 * @route '/api/courses/{course}/students'
 */
getStudents.get = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getStudents.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Api\CourseApiController::getStudents
 * @see app/Http/Controllers/Api/CourseApiController.php:161
 * @route '/api/courses/{course}/students'
 */
getStudents.head = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getStudents.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\Api\CourseApiController::getStudents
 * @see app/Http/Controllers/Api/CourseApiController.php:161
 * @route '/api/courses/{course}/students'
 */
    const getStudentsForm = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: getStudents.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\Api\CourseApiController::getStudents
 * @see app/Http/Controllers/Api/CourseApiController.php:161
 * @route '/api/courses/{course}/students'
 */
        getStudentsForm.get = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: getStudents.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\Api\CourseApiController::getStudents
 * @see app/Http/Controllers/Api/CourseApiController.php:161
 * @route '/api/courses/{course}/students'
 */
        getStudentsForm.head = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: getStudents.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    getStudents.form = getStudentsForm
const CourseApiController = { getStudents }

export default CourseApiController