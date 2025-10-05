import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../wayfinder'
/**
 * @see routes/web.php:97
 * @route '/role-management'
 */
export const management = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: management.url(options),
    method: 'get',
})

management.definition = {
    methods: ["get","head"],
    url: '/role-management',
} satisfies RouteDefinition<["get","head"]>

/**
 * @see routes/web.php:97
 * @route '/role-management'
 */
management.url = (options?: RouteQueryOptions) => {
    return management.definition.url + queryParams(options)
}

/**
 * @see routes/web.php:97
 * @route '/role-management'
 */
management.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: management.url(options),
    method: 'get',
})
/**
 * @see routes/web.php:97
 * @route '/role-management'
 */
management.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: management.url(options),
    method: 'head',
})

    /**
 * @see routes/web.php:97
 * @route '/role-management'
 */
    const managementForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: management.url(options),
        method: 'get',
    })

            /**
 * @see routes/web.php:97
 * @route '/role-management'
 */
        managementForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: management.url(options),
            method: 'get',
        })
            /**
 * @see routes/web.php:97
 * @route '/role-management'
 */
        managementForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: management.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    management.form = managementForm
const role = {
    management: Object.assign(management, management),
}

export default role