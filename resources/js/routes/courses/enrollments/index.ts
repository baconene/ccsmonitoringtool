import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\CourseStudentController::updateStatus
 * @see app/Http/Controllers/CourseStudentController.php:351
 * @route '/courses/{course}/enrollments/{studentUser}/status'
 */
export const updateStatus = (args: { course: number | { id: number }, studentUser: number | { id: number } } | [course: number | { id: number }, studentUser: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: updateStatus.url(args, options),
    method: 'put',
})

updateStatus.definition = {
    methods: ["put"],
    url: '/courses/{course}/enrollments/{studentUser}/status',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\CourseStudentController::updateStatus
 * @see app/Http/Controllers/CourseStudentController.php:351
 * @route '/courses/{course}/enrollments/{studentUser}/status'
 */
updateStatus.url = (args: { course: number | { id: number }, studentUser: number | { id: number } } | [course: number | { id: number }, studentUser: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
                    course: args[0],
                    studentUser: args[1],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        course: typeof args.course === 'object'
                ? args.course.id
                : args.course,
                                studentUser: typeof args.studentUser === 'object'
                ? args.studentUser.id
                : args.studentUser,
                }

    return updateStatus.definition.url
            .replace('{course}', parsedArgs.course.toString())
            .replace('{studentUser}', parsedArgs.studentUser.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\CourseStudentController::updateStatus
 * @see app/Http/Controllers/CourseStudentController.php:351
 * @route '/courses/{course}/enrollments/{studentUser}/status'
 */
updateStatus.put = (args: { course: number | { id: number }, studentUser: number | { id: number } } | [course: number | { id: number }, studentUser: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: updateStatus.url(args, options),
    method: 'put',
})

    /**
* @see \App\Http\Controllers\CourseStudentController::updateStatus
 * @see app/Http/Controllers/CourseStudentController.php:351
 * @route '/courses/{course}/enrollments/{studentUser}/status'
 */
    const updateStatusForm = (args: { course: number | { id: number }, studentUser: number | { id: number } } | [course: number | { id: number }, studentUser: number | { id: number } ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: updateStatus.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'PUT',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\CourseStudentController::updateStatus
 * @see app/Http/Controllers/CourseStudentController.php:351
 * @route '/courses/{course}/enrollments/{studentUser}/status'
 */
        updateStatusForm.put = (args: { course: number | { id: number }, studentUser: number | { id: number } } | [course: number | { id: number }, studentUser: number | { id: number } ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: updateStatus.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'PUT',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    updateStatus.form = updateStatusForm
const enrollments = {
    updateStatus: Object.assign(updateStatus, updateStatus),
}

export default enrollments