import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../wayfinder'
import modules from './modules'
/**
* @see \App\Http\Controllers\Student\StudentCourseController::index
 * @see app/Http/Controllers/Student/StudentCourseController.php:18
 * @route '/student/courses'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/student/courses',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Student\StudentCourseController::index
 * @see app/Http/Controllers/Student/StudentCourseController.php:18
 * @route '/student/courses'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Student\StudentCourseController::index
 * @see app/Http/Controllers/Student/StudentCourseController.php:18
 * @route '/student/courses'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Student\StudentCourseController::index
 * @see app/Http/Controllers/Student/StudentCourseController.php:18
 * @route '/student/courses'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\Student\StudentCourseController::index
 * @see app/Http/Controllers/Student/StudentCourseController.php:18
 * @route '/student/courses'
 */
    const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: index.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\Student\StudentCourseController::index
 * @see app/Http/Controllers/Student/StudentCourseController.php:18
 * @route '/student/courses'
 */
        indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: index.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\Student\StudentCourseController::index
 * @see app/Http/Controllers/Student/StudentCourseController.php:18
 * @route '/student/courses'
 */
        indexForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: index.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    index.form = indexForm
/**
* @see \App\Http\Controllers\Student\StudentCourseController::show
 * @see app/Http/Controllers/Student/StudentCourseController.php:66
 * @route '/student/courses/{course}'
 */
export const show = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/student/courses/{course}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Student\StudentCourseController::show
 * @see app/Http/Controllers/Student/StudentCourseController.php:66
 * @route '/student/courses/{course}'
 */
show.url = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return show.definition.url
            .replace('{course}', parsedArgs.course.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Student\StudentCourseController::show
 * @see app/Http/Controllers/Student/StudentCourseController.php:66
 * @route '/student/courses/{course}'
 */
show.get = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Student\StudentCourseController::show
 * @see app/Http/Controllers/Student/StudentCourseController.php:66
 * @route '/student/courses/{course}'
 */
show.head = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\Student\StudentCourseController::show
 * @see app/Http/Controllers/Student/StudentCourseController.php:66
 * @route '/student/courses/{course}'
 */
    const showForm = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: show.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\Student\StudentCourseController::show
 * @see app/Http/Controllers/Student/StudentCourseController.php:66
 * @route '/student/courses/{course}'
 */
        showForm.get = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: show.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\Student\StudentCourseController::show
 * @see app/Http/Controllers/Student/StudentCourseController.php:66
 * @route '/student/courses/{course}'
 */
        showForm.head = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
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
* @see \App\Http\Controllers\Student\StudentCourseController::lessons
 * @see app/Http/Controllers/Student/StudentCourseController.php:244
 * @route '/student/courses/{course}/lessons'
 */
export const lessons = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: lessons.url(args, options),
    method: 'get',
})

lessons.definition = {
    methods: ["get","head"],
    url: '/student/courses/{course}/lessons',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Student\StudentCourseController::lessons
 * @see app/Http/Controllers/Student/StudentCourseController.php:244
 * @route '/student/courses/{course}/lessons'
 */
lessons.url = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return lessons.definition.url
            .replace('{course}', parsedArgs.course.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Student\StudentCourseController::lessons
 * @see app/Http/Controllers/Student/StudentCourseController.php:244
 * @route '/student/courses/{course}/lessons'
 */
lessons.get = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: lessons.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Student\StudentCourseController::lessons
 * @see app/Http/Controllers/Student/StudentCourseController.php:244
 * @route '/student/courses/{course}/lessons'
 */
lessons.head = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: lessons.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\Student\StudentCourseController::lessons
 * @see app/Http/Controllers/Student/StudentCourseController.php:244
 * @route '/student/courses/{course}/lessons'
 */
    const lessonsForm = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: lessons.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\Student\StudentCourseController::lessons
 * @see app/Http/Controllers/Student/StudentCourseController.php:244
 * @route '/student/courses/{course}/lessons'
 */
        lessonsForm.get = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: lessons.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\Student\StudentCourseController::lessons
 * @see app/Http/Controllers/Student/StudentCourseController.php:244
 * @route '/student/courses/{course}/lessons'
 */
        lessonsForm.head = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: lessons.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    lessons.form = lessonsForm
const courses = {
    index: Object.assign(index, index),
show: Object.assign(show, show),
modules: Object.assign(modules, modules),
lessons: Object.assign(lessons, lessons),
}

export default courses