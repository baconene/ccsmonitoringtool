import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\LessonController::store
 * @see app/Http/Controllers/LessonController.php:31
 * @route '/lessons'
 */
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/lessons',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\LessonController::store
 * @see app/Http/Controllers/LessonController.php:31
 * @route '/lessons'
 */
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\LessonController::store
 * @see app/Http/Controllers/LessonController.php:31
 * @route '/lessons'
 */
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\LessonController::store
 * @see app/Http/Controllers/LessonController.php:31
 * @route '/lessons'
 */
    const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: store.url(options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\LessonController::store
 * @see app/Http/Controllers/LessonController.php:31
 * @route '/lessons'
 */
        storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: store.url(options),
            method: 'post',
        })
    
    store.form = storeForm
/**
* @see \App\Http\Controllers\LessonController::update
 * @see app/Http/Controllers/LessonController.php:86
 * @route '/lessons/{lessonId}'
 */
export const update = (args: { lessonId: string | number } | [lessonId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/lessons/{lessonId}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\LessonController::update
 * @see app/Http/Controllers/LessonController.php:86
 * @route '/lessons/{lessonId}'
 */
update.url = (args: { lessonId: string | number } | [lessonId: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { lessonId: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    lessonId: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        lessonId: args.lessonId,
                }

    return update.definition.url
            .replace('{lessonId}', parsedArgs.lessonId.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\LessonController::update
 * @see app/Http/Controllers/LessonController.php:86
 * @route '/lessons/{lessonId}'
 */
update.put = (args: { lessonId: string | number } | [lessonId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

    /**
* @see \App\Http\Controllers\LessonController::update
 * @see app/Http/Controllers/LessonController.php:86
 * @route '/lessons/{lessonId}'
 */
    const updateForm = (args: { lessonId: string | number } | [lessonId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: update.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'PUT',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\LessonController::update
 * @see app/Http/Controllers/LessonController.php:86
 * @route '/lessons/{lessonId}'
 */
        updateForm.put = (args: { lessonId: string | number } | [lessonId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: update.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'PUT',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    update.form = updateForm
const LessonController = { store, update }

export default LessonController