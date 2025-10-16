import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\Instructor\StudentManagementController::students
 * @see app/Http/Controllers/Instructor/StudentManagementController.php:62
 * @route '/student-management/course/{course}/students'
 */
export const students = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: students.url(args, options),
    method: 'get',
})

students.definition = {
    methods: ["get","head"],
    url: '/student-management/course/{course}/students',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Instructor\StudentManagementController::students
 * @see app/Http/Controllers/Instructor/StudentManagementController.php:62
 * @route '/student-management/course/{course}/students'
 */
students.url = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return students.definition.url
            .replace('{course}', parsedArgs.course.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Instructor\StudentManagementController::students
 * @see app/Http/Controllers/Instructor/StudentManagementController.php:62
 * @route '/student-management/course/{course}/students'
 */
students.get = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: students.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Instructor\StudentManagementController::students
 * @see app/Http/Controllers/Instructor/StudentManagementController.php:62
 * @route '/student-management/course/{course}/students'
 */
students.head = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: students.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\Instructor\StudentManagementController::students
 * @see app/Http/Controllers/Instructor/StudentManagementController.php:62
 * @route '/student-management/course/{course}/students'
 */
    const studentsForm = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: students.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\Instructor\StudentManagementController::students
 * @see app/Http/Controllers/Instructor/StudentManagementController.php:62
 * @route '/student-management/course/{course}/students'
 */
        studentsForm.get = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: students.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\Instructor\StudentManagementController::students
 * @see app/Http/Controllers/Instructor/StudentManagementController.php:62
 * @route '/student-management/course/{course}/students'
 */
        studentsForm.head = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: students.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    students.form = studentsForm
const course = {
    students: Object.assign(students, students),
}

export default course