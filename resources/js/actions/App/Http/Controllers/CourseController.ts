import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\CourseController::getCourses
* @see app/Http/Controllers/CourseController.php:27
* @route '/api/courses'
*/
export const getCourses = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getCourses.url(options),
    method: 'get',
})

getCourses.definition = {
    methods: ["get","head"],
    url: '/api/courses',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\CourseController::getCourses
* @see app/Http/Controllers/CourseController.php:27
* @route '/api/courses'
*/
getCourses.url = (options?: RouteQueryOptions) => {
    return getCourses.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\CourseController::getCourses
* @see app/Http/Controllers/CourseController.php:27
* @route '/api/courses'
*/
getCourses.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getCourses.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\CourseController::getCourses
* @see app/Http/Controllers/CourseController.php:27
* @route '/api/courses'
*/
getCourses.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getCourses.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\CourseController::getCourses
* @see app/Http/Controllers/CourseController.php:27
* @route '/api/courses'
*/
const getCoursesForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getCourses.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\CourseController::getCourses
* @see app/Http/Controllers/CourseController.php:27
* @route '/api/courses'
*/
getCoursesForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getCourses.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\CourseController::getCourses
* @see app/Http/Controllers/CourseController.php:27
* @route '/api/courses'
*/
getCoursesForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getCourses.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

getCourses.form = getCoursesForm

/**
* @see \App\Http\Controllers\CourseController::index
* @see app/Http/Controllers/CourseController.php:16
* @route '/course-management'
*/
const index0a0081d6d653fafb98e0ba542b6152c2 = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index0a0081d6d653fafb98e0ba542b6152c2.url(options),
    method: 'get',
})

index0a0081d6d653fafb98e0ba542b6152c2.definition = {
    methods: ["get","head"],
    url: '/course-management',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\CourseController::index
* @see app/Http/Controllers/CourseController.php:16
* @route '/course-management'
*/
index0a0081d6d653fafb98e0ba542b6152c2.url = (options?: RouteQueryOptions) => {
    return index0a0081d6d653fafb98e0ba542b6152c2.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\CourseController::index
* @see app/Http/Controllers/CourseController.php:16
* @route '/course-management'
*/
index0a0081d6d653fafb98e0ba542b6152c2.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index0a0081d6d653fafb98e0ba542b6152c2.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\CourseController::index
* @see app/Http/Controllers/CourseController.php:16
* @route '/course-management'
*/
index0a0081d6d653fafb98e0ba542b6152c2.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index0a0081d6d653fafb98e0ba542b6152c2.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\CourseController::index
* @see app/Http/Controllers/CourseController.php:16
* @route '/course-management'
*/
const index0a0081d6d653fafb98e0ba542b6152c2Form = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index0a0081d6d653fafb98e0ba542b6152c2.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\CourseController::index
* @see app/Http/Controllers/CourseController.php:16
* @route '/course-management'
*/
index0a0081d6d653fafb98e0ba542b6152c2Form.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index0a0081d6d653fafb98e0ba542b6152c2.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\CourseController::index
* @see app/Http/Controllers/CourseController.php:16
* @route '/course-management'
*/
index0a0081d6d653fafb98e0ba542b6152c2Form.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index0a0081d6d653fafb98e0ba542b6152c2.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

index0a0081d6d653fafb98e0ba542b6152c2.form = index0a0081d6d653fafb98e0ba542b6152c2Form
/**
* @see \App\Http\Controllers\CourseController::index
* @see app/Http/Controllers/CourseController.php:16
* @route '/courses'
*/
const indexae0d8013bc7dd1aeb7c9b49bac5f9e3b = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: indexae0d8013bc7dd1aeb7c9b49bac5f9e3b.url(options),
    method: 'get',
})

indexae0d8013bc7dd1aeb7c9b49bac5f9e3b.definition = {
    methods: ["get","head"],
    url: '/courses',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\CourseController::index
* @see app/Http/Controllers/CourseController.php:16
* @route '/courses'
*/
indexae0d8013bc7dd1aeb7c9b49bac5f9e3b.url = (options?: RouteQueryOptions) => {
    return indexae0d8013bc7dd1aeb7c9b49bac5f9e3b.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\CourseController::index
* @see app/Http/Controllers/CourseController.php:16
* @route '/courses'
*/
indexae0d8013bc7dd1aeb7c9b49bac5f9e3b.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: indexae0d8013bc7dd1aeb7c9b49bac5f9e3b.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\CourseController::index
* @see app/Http/Controllers/CourseController.php:16
* @route '/courses'
*/
indexae0d8013bc7dd1aeb7c9b49bac5f9e3b.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: indexae0d8013bc7dd1aeb7c9b49bac5f9e3b.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\CourseController::index
* @see app/Http/Controllers/CourseController.php:16
* @route '/courses'
*/
const indexae0d8013bc7dd1aeb7c9b49bac5f9e3bForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: indexae0d8013bc7dd1aeb7c9b49bac5f9e3b.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\CourseController::index
* @see app/Http/Controllers/CourseController.php:16
* @route '/courses'
*/
indexae0d8013bc7dd1aeb7c9b49bac5f9e3bForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: indexae0d8013bc7dd1aeb7c9b49bac5f9e3b.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\CourseController::index
* @see app/Http/Controllers/CourseController.php:16
* @route '/courses'
*/
indexae0d8013bc7dd1aeb7c9b49bac5f9e3bForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: indexae0d8013bc7dd1aeb7c9b49bac5f9e3b.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

indexae0d8013bc7dd1aeb7c9b49bac5f9e3b.form = indexae0d8013bc7dd1aeb7c9b49bac5f9e3bForm

export const index = {
    '/course-management': index0a0081d6d653fafb98e0ba542b6152c2,
    '/courses': indexae0d8013bc7dd1aeb7c9b49bac5f9e3b,
}

/**
* @see \App\Http\Controllers\CourseController::store
* @see app/Http/Controllers/CourseController.php:70
* @route '/courses'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/courses',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\CourseController::store
* @see app/Http/Controllers/CourseController.php:70
* @route '/courses'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\CourseController::store
* @see app/Http/Controllers/CourseController.php:70
* @route '/courses'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\CourseController::store
* @see app/Http/Controllers/CourseController.php:70
* @route '/courses'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\CourseController::store
* @see app/Http/Controllers/CourseController.php:70
* @route '/courses'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\CourseController::update
* @see app/Http/Controllers/CourseController.php:98
* @route '/courses/{course}'
*/
export const update = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put","patch"],
    url: '/courses/{course}',
} satisfies RouteDefinition<["put","patch"]>

/**
* @see \App\Http\Controllers\CourseController::update
* @see app/Http/Controllers/CourseController.php:98
* @route '/courses/{course}'
*/
update.url = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return update.definition.url
            .replace('{course}', parsedArgs.course.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\CourseController::update
* @see app/Http/Controllers/CourseController.php:98
* @route '/courses/{course}'
*/
update.put = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\CourseController::update
* @see app/Http/Controllers/CourseController.php:98
* @route '/courses/{course}'
*/
update.patch = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\CourseController::update
* @see app/Http/Controllers/CourseController.php:98
* @route '/courses/{course}'
*/
const updateForm = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\CourseController::update
* @see app/Http/Controllers/CourseController.php:98
* @route '/courses/{course}'
*/
updateForm.put = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\CourseController::update
* @see app/Http/Controllers/CourseController.php:98
* @route '/courses/{course}'
*/
updateForm.patch = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

update.form = updateForm

/**
* @see \App\Http\Controllers\CourseController::destroy
* @see app/Http/Controllers/CourseController.php:116
* @route '/courses/{course}'
*/
export const destroy = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/courses/{course}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\CourseController::destroy
* @see app/Http/Controllers/CourseController.php:116
* @route '/courses/{course}'
*/
destroy.url = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return destroy.definition.url
            .replace('{course}', parsedArgs.course.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\CourseController::destroy
* @see app/Http/Controllers/CourseController.php:116
* @route '/courses/{course}'
*/
destroy.delete = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\CourseController::destroy
* @see app/Http/Controllers/CourseController.php:116
* @route '/courses/{course}'
*/
const destroyForm = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\CourseController::destroy
* @see app/Http/Controllers/CourseController.php:116
* @route '/courses/{course}'
*/
destroyForm.delete = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroy.form = destroyForm

const CourseController = { getCourses, index, store, update, destroy }

export default CourseController