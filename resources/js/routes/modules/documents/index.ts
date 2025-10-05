import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\ModuleController::upload
 * @see app/Http/Controllers/ModuleController.php:153
 * @route '/modules/{module}/documents'
 */
export const upload = (args: { module: number | { id: number } } | [module: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: upload.url(args, options),
    method: 'post',
})

upload.definition = {
    methods: ["post"],
    url: '/modules/{module}/documents',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\ModuleController::upload
 * @see app/Http/Controllers/ModuleController.php:153
 * @route '/modules/{module}/documents'
 */
upload.url = (args: { module: number | { id: number } } | [module: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { module: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { module: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    module: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        module: typeof args.module === 'object'
                ? args.module.id
                : args.module,
                }

    return upload.definition.url
            .replace('{module}', parsedArgs.module.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ModuleController::upload
 * @see app/Http/Controllers/ModuleController.php:153
 * @route '/modules/{module}/documents'
 */
upload.post = (args: { module: number | { id: number } } | [module: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: upload.url(args, options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\ModuleController::upload
 * @see app/Http/Controllers/ModuleController.php:153
 * @route '/modules/{module}/documents'
 */
    const uploadForm = (args: { module: number | { id: number } } | [module: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: upload.url(args, options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\ModuleController::upload
 * @see app/Http/Controllers/ModuleController.php:153
 * @route '/modules/{module}/documents'
 */
        uploadForm.post = (args: { module: number | { id: number } } | [module: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: upload.url(args, options),
            method: 'post',
        })
    
    upload.form = uploadForm
const documents = {
    upload: Object.assign(upload, upload),
}

export default documents