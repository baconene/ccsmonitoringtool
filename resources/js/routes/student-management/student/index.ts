import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\Instructor\StudentManagementController::activities
 * @see app/Http/Controllers/Instructor/StudentManagementController.php:183
 * @route '/student-management/course/{course}/student/{student}/activities'
 */
export const activities = (args: { course: number | { id: number }, student: number | { id: number } } | [course: number | { id: number }, student: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: activities.url(args, options),
    method: 'get',
})

activities.definition = {
    methods: ["get","head"],
    url: '/student-management/course/{course}/student/{student}/activities',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Instructor\StudentManagementController::activities
 * @see app/Http/Controllers/Instructor/StudentManagementController.php:183
 * @route '/student-management/course/{course}/student/{student}/activities'
 */
activities.url = (args: { course: number | { id: number }, student: number | { id: number } } | [course: number | { id: number }, student: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
                    course: args[0],
                    student: args[1],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        course: typeof args.course === 'object'
                ? args.course.id
                : args.course,
                                student: typeof args.student === 'object'
                ? args.student.id
                : args.student,
                }

    return activities.definition.url
            .replace('{course}', parsedArgs.course.toString())
            .replace('{student}', parsedArgs.student.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Instructor\StudentManagementController::activities
 * @see app/Http/Controllers/Instructor/StudentManagementController.php:183
 * @route '/student-management/course/{course}/student/{student}/activities'
 */
activities.get = (args: { course: number | { id: number }, student: number | { id: number } } | [course: number | { id: number }, student: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: activities.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Instructor\StudentManagementController::activities
 * @see app/Http/Controllers/Instructor/StudentManagementController.php:183
 * @route '/student-management/course/{course}/student/{student}/activities'
 */
activities.head = (args: { course: number | { id: number }, student: number | { id: number } } | [course: number | { id: number }, student: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: activities.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\Instructor\StudentManagementController::activities
 * @see app/Http/Controllers/Instructor/StudentManagementController.php:183
 * @route '/student-management/course/{course}/student/{student}/activities'
 */
    const activitiesForm = (args: { course: number | { id: number }, student: number | { id: number } } | [course: number | { id: number }, student: number | { id: number } ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: activities.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\Instructor\StudentManagementController::activities
 * @see app/Http/Controllers/Instructor/StudentManagementController.php:183
 * @route '/student-management/course/{course}/student/{student}/activities'
 */
        activitiesForm.get = (args: { course: number | { id: number }, student: number | { id: number } } | [course: number | { id: number }, student: number | { id: number } ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: activities.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\Instructor\StudentManagementController::activities
 * @see app/Http/Controllers/Instructor/StudentManagementController.php:183
 * @route '/student-management/course/{course}/student/{student}/activities'
 */
        activitiesForm.head = (args: { course: number | { id: number }, student: number | { id: number } } | [course: number | { id: number }, student: number | { id: number } ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: activities.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    activities.form = activitiesForm
const student = {
    activities: Object.assign(activities, activities),
}

export default student