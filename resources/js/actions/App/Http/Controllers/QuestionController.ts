import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\QuestionController::store
 * @see app/Http/Controllers/QuestionController.php:16
 * @route '/questions'
 */
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/questions',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\QuestionController::store
 * @see app/Http/Controllers/QuestionController.php:16
 * @route '/questions'
 */
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\QuestionController::store
 * @see app/Http/Controllers/QuestionController.php:16
 * @route '/questions'
 */
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\QuestionController::store
 * @see app/Http/Controllers/QuestionController.php:16
 * @route '/questions'
 */
    const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: store.url(options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\QuestionController::store
 * @see app/Http/Controllers/QuestionController.php:16
 * @route '/questions'
 */
        storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: store.url(options),
            method: 'post',
        })
    
    store.form = storeForm
/**
* @see \App\Http\Controllers\QuestionController::update
 * @see app/Http/Controllers/QuestionController.php:50
 * @route '/questions/{question}'
 */
export const update = (args: { question: number | { id: number } } | [question: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/questions/{question}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\QuestionController::update
 * @see app/Http/Controllers/QuestionController.php:50
 * @route '/questions/{question}'
 */
update.url = (args: { question: number | { id: number } } | [question: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { question: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { question: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    question: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        question: typeof args.question === 'object'
                ? args.question.id
                : args.question,
                }

    return update.definition.url
            .replace('{question}', parsedArgs.question.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\QuestionController::update
 * @see app/Http/Controllers/QuestionController.php:50
 * @route '/questions/{question}'
 */
update.put = (args: { question: number | { id: number } } | [question: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

    /**
* @see \App\Http\Controllers\QuestionController::update
 * @see app/Http/Controllers/QuestionController.php:50
 * @route '/questions/{question}'
 */
    const updateForm = (args: { question: number | { id: number } } | [question: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: update.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'PUT',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\QuestionController::update
 * @see app/Http/Controllers/QuestionController.php:50
 * @route '/questions/{question}'
 */
        updateForm.put = (args: { question: number | { id: number } } | [question: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: update.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'PUT',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    update.form = updateForm
/**
* @see \App\Http\Controllers\QuestionController::destroy
 * @see app/Http/Controllers/QuestionController.php:98
 * @route '/questions/{question}'
 */
export const destroy = (args: { question: number | { id: number } } | [question: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/questions/{question}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\QuestionController::destroy
 * @see app/Http/Controllers/QuestionController.php:98
 * @route '/questions/{question}'
 */
destroy.url = (args: { question: number | { id: number } } | [question: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { question: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { question: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    question: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        question: typeof args.question === 'object'
                ? args.question.id
                : args.question,
                }

    return destroy.definition.url
            .replace('{question}', parsedArgs.question.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\QuestionController::destroy
 * @see app/Http/Controllers/QuestionController.php:98
 * @route '/questions/{question}'
 */
destroy.delete = (args: { question: number | { id: number } } | [question: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

    /**
* @see \App\Http\Controllers\QuestionController::destroy
 * @see app/Http/Controllers/QuestionController.php:98
 * @route '/questions/{question}'
 */
    const destroyForm = (args: { question: number | { id: number } } | [question: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: destroy.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'DELETE',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\QuestionController::destroy
 * @see app/Http/Controllers/QuestionController.php:98
 * @route '/questions/{question}'
 */
        destroyForm.delete = (args: { question: number | { id: number } } | [question: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: destroy.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'DELETE',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    destroy.form = destroyForm
const QuestionController = { store, update, destroy }

export default QuestionController