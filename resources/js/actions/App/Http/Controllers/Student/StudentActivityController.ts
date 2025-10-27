import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Student\StudentActivityController::markComplete
 * @see app/Http/Controllers/Student/StudentActivityController.php:18
 * @route '/student/activities/{activity}/mark-complete'
 */
export const markComplete = (args: { activity: string | number } | [activity: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: markComplete.url(args, options),
    method: 'post',
})

markComplete.definition = {
    methods: ["post"],
    url: '/student/activities/{activity}/mark-complete',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Student\StudentActivityController::markComplete
 * @see app/Http/Controllers/Student/StudentActivityController.php:18
 * @route '/student/activities/{activity}/mark-complete'
 */
markComplete.url = (args: { activity: string | number } | [activity: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { activity: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    activity: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        activity: args.activity,
                }

    return markComplete.definition.url
            .replace('{activity}', parsedArgs.activity.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Student\StudentActivityController::markComplete
 * @see app/Http/Controllers/Student/StudentActivityController.php:18
 * @route '/student/activities/{activity}/mark-complete'
 */
markComplete.post = (args: { activity: string | number } | [activity: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: markComplete.url(args, options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\Student\StudentActivityController::markComplete
 * @see app/Http/Controllers/Student/StudentActivityController.php:18
 * @route '/student/activities/{activity}/mark-complete'
 */
    const markCompleteForm = (args: { activity: string | number } | [activity: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: markComplete.url(args, options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\Student\StudentActivityController::markComplete
 * @see app/Http/Controllers/Student/StudentActivityController.php:18
 * @route '/student/activities/{activity}/mark-complete'
 */
        markCompleteForm.post = (args: { activity: string | number } | [activity: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: markComplete.url(args, options),
            method: 'post',
        })
    
    markComplete.form = markCompleteForm
const StudentActivityController = { markComplete }

export default StudentActivityController