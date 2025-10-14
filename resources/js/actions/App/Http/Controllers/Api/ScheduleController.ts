import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\ScheduleController::destroy
 * @see app/Http/Controllers/Api/ScheduleController.php:309
 * @route '/api/schedules/{schedule}'
 */
export const destroy = (args: { schedule: string | number } | [schedule: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/api/schedules/{schedule}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Api\ScheduleController::destroy
 * @see app/Http/Controllers/Api/ScheduleController.php:309
 * @route '/api/schedules/{schedule}'
 */
destroy.url = (args: { schedule: string | number } | [schedule: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { schedule: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    schedule: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        schedule: args.schedule,
                }

    return destroy.definition.url
            .replace('{schedule}', parsedArgs.schedule.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScheduleController::destroy
 * @see app/Http/Controllers/Api/ScheduleController.php:309
 * @route '/api/schedules/{schedule}'
 */
destroy.delete = (args: { schedule: string | number } | [schedule: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

    /**
* @see \App\Http\Controllers\Api\ScheduleController::destroy
 * @see app/Http/Controllers/Api/ScheduleController.php:309
 * @route '/api/schedules/{schedule}'
 */
    const destroyForm = (args: { schedule: string | number } | [schedule: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: destroy.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'DELETE',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\Api\ScheduleController::destroy
 * @see app/Http/Controllers/Api/ScheduleController.php:309
 * @route '/api/schedules/{schedule}'
 */
        destroyForm.delete = (args: { schedule: string | number } | [schedule: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: destroy.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'DELETE',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    destroy.form = destroyForm
/**
* @see \App\Http\Controllers\Api\ScheduleController::getUserUpcomingSchedules
 * @see app/Http/Controllers/Api/ScheduleController.php:23
 * @route '/api/users/{userId}/schedules/upcoming'
 */
export const getUserUpcomingSchedules = (args: { userId: string | number } | [userId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getUserUpcomingSchedules.url(args, options),
    method: 'get',
})

getUserUpcomingSchedules.definition = {
    methods: ["get","head"],
    url: '/api/users/{userId}/schedules/upcoming',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\ScheduleController::getUserUpcomingSchedules
 * @see app/Http/Controllers/Api/ScheduleController.php:23
 * @route '/api/users/{userId}/schedules/upcoming'
 */
getUserUpcomingSchedules.url = (args: { userId: string | number } | [userId: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { userId: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    userId: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        userId: args.userId,
                }

    return getUserUpcomingSchedules.definition.url
            .replace('{userId}', parsedArgs.userId.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScheduleController::getUserUpcomingSchedules
 * @see app/Http/Controllers/Api/ScheduleController.php:23
 * @route '/api/users/{userId}/schedules/upcoming'
 */
getUserUpcomingSchedules.get = (args: { userId: string | number } | [userId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getUserUpcomingSchedules.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Api\ScheduleController::getUserUpcomingSchedules
 * @see app/Http/Controllers/Api/ScheduleController.php:23
 * @route '/api/users/{userId}/schedules/upcoming'
 */
getUserUpcomingSchedules.head = (args: { userId: string | number } | [userId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getUserUpcomingSchedules.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\Api\ScheduleController::getUserUpcomingSchedules
 * @see app/Http/Controllers/Api/ScheduleController.php:23
 * @route '/api/users/{userId}/schedules/upcoming'
 */
    const getUserUpcomingSchedulesForm = (args: { userId: string | number } | [userId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: getUserUpcomingSchedules.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\Api\ScheduleController::getUserUpcomingSchedules
 * @see app/Http/Controllers/Api/ScheduleController.php:23
 * @route '/api/users/{userId}/schedules/upcoming'
 */
        getUserUpcomingSchedulesForm.get = (args: { userId: string | number } | [userId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: getUserUpcomingSchedules.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\Api\ScheduleController::getUserUpcomingSchedules
 * @see app/Http/Controllers/Api/ScheduleController.php:23
 * @route '/api/users/{userId}/schedules/upcoming'
 */
        getUserUpcomingSchedulesForm.head = (args: { userId: string | number } | [userId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: getUserUpcomingSchedules.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    getUserUpcomingSchedules.form = getUserUpcomingSchedulesForm
/**
* @see \App\Http\Controllers\Api\ScheduleController::getSchedulesInRange
 * @see app/Http/Controllers/Api/ScheduleController.php:104
 * @route '/api/schedules/range'
 */
export const getSchedulesInRange = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getSchedulesInRange.url(options),
    method: 'get',
})

getSchedulesInRange.definition = {
    methods: ["get","head"],
    url: '/api/schedules/range',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\ScheduleController::getSchedulesInRange
 * @see app/Http/Controllers/Api/ScheduleController.php:104
 * @route '/api/schedules/range'
 */
getSchedulesInRange.url = (options?: RouteQueryOptions) => {
    return getSchedulesInRange.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScheduleController::getSchedulesInRange
 * @see app/Http/Controllers/Api/ScheduleController.php:104
 * @route '/api/schedules/range'
 */
getSchedulesInRange.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getSchedulesInRange.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Api\ScheduleController::getSchedulesInRange
 * @see app/Http/Controllers/Api/ScheduleController.php:104
 * @route '/api/schedules/range'
 */
getSchedulesInRange.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getSchedulesInRange.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\Api\ScheduleController::getSchedulesInRange
 * @see app/Http/Controllers/Api/ScheduleController.php:104
 * @route '/api/schedules/range'
 */
    const getSchedulesInRangeForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: getSchedulesInRange.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\Api\ScheduleController::getSchedulesInRange
 * @see app/Http/Controllers/Api/ScheduleController.php:104
 * @route '/api/schedules/range'
 */
        getSchedulesInRangeForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: getSchedulesInRange.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\Api\ScheduleController::getSchedulesInRange
 * @see app/Http/Controllers/Api/ScheduleController.php:104
 * @route '/api/schedules/range'
 */
        getSchedulesInRangeForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: getSchedulesInRange.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    getSchedulesInRange.form = getSchedulesInRangeForm
/**
* @see \App\Http\Controllers\Api\ScheduleController::checkConflicts
 * @see app/Http/Controllers/Api/ScheduleController.php:365
 * @route '/api/schedules/check-conflicts'
 */
export const checkConflicts = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: checkConflicts.url(options),
    method: 'post',
})

checkConflicts.definition = {
    methods: ["post"],
    url: '/api/schedules/check-conflicts',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\ScheduleController::checkConflicts
 * @see app/Http/Controllers/Api/ScheduleController.php:365
 * @route '/api/schedules/check-conflicts'
 */
checkConflicts.url = (options?: RouteQueryOptions) => {
    return checkConflicts.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScheduleController::checkConflicts
 * @see app/Http/Controllers/Api/ScheduleController.php:365
 * @route '/api/schedules/check-conflicts'
 */
checkConflicts.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: checkConflicts.url(options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\Api\ScheduleController::checkConflicts
 * @see app/Http/Controllers/Api/ScheduleController.php:365
 * @route '/api/schedules/check-conflicts'
 */
    const checkConflictsForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: checkConflicts.url(options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\Api\ScheduleController::checkConflicts
 * @see app/Http/Controllers/Api/ScheduleController.php:365
 * @route '/api/schedules/check-conflicts'
 */
        checkConflictsForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: checkConflicts.url(options),
            method: 'post',
        })
    
    checkConflicts.form = checkConflictsForm
/**
* @see \App\Http\Controllers\Api\ScheduleController::index
 * @see app/Http/Controllers/Api/ScheduleController.php:0
 * @route '/api/schedules'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/api/schedules',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\ScheduleController::index
 * @see app/Http/Controllers/Api/ScheduleController.php:0
 * @route '/api/schedules'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScheduleController::index
 * @see app/Http/Controllers/Api/ScheduleController.php:0
 * @route '/api/schedules'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Api\ScheduleController::index
 * @see app/Http/Controllers/Api/ScheduleController.php:0
 * @route '/api/schedules'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\Api\ScheduleController::index
 * @see app/Http/Controllers/Api/ScheduleController.php:0
 * @route '/api/schedules'
 */
    const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: index.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\Api\ScheduleController::index
 * @see app/Http/Controllers/Api/ScheduleController.php:0
 * @route '/api/schedules'
 */
        indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: index.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\Api\ScheduleController::index
 * @see app/Http/Controllers/Api/ScheduleController.php:0
 * @route '/api/schedules'
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
* @see \App\Http\Controllers\Api\ScheduleController::store
 * @see app/Http/Controllers/Api/ScheduleController.php:177
 * @route '/api/schedules'
 */
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/api/schedules',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\ScheduleController::store
 * @see app/Http/Controllers/Api/ScheduleController.php:177
 * @route '/api/schedules'
 */
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScheduleController::store
 * @see app/Http/Controllers/Api/ScheduleController.php:177
 * @route '/api/schedules'
 */
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\Api\ScheduleController::store
 * @see app/Http/Controllers/Api/ScheduleController.php:177
 * @route '/api/schedules'
 */
    const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: store.url(options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\Api\ScheduleController::store
 * @see app/Http/Controllers/Api/ScheduleController.php:177
 * @route '/api/schedules'
 */
        storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: store.url(options),
            method: 'post',
        })
    
    store.form = storeForm
/**
* @see \App\Http\Controllers\Api\ScheduleController::show
 * @see app/Http/Controllers/Api/ScheduleController.php:334
 * @route '/api/schedules/{schedule}'
 */
export const show = (args: { schedule: string | number } | [schedule: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/api/schedules/{schedule}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\ScheduleController::show
 * @see app/Http/Controllers/Api/ScheduleController.php:334
 * @route '/api/schedules/{schedule}'
 */
show.url = (args: { schedule: string | number } | [schedule: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { schedule: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    schedule: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        schedule: args.schedule,
                }

    return show.definition.url
            .replace('{schedule}', parsedArgs.schedule.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScheduleController::show
 * @see app/Http/Controllers/Api/ScheduleController.php:334
 * @route '/api/schedules/{schedule}'
 */
show.get = (args: { schedule: string | number } | [schedule: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Api\ScheduleController::show
 * @see app/Http/Controllers/Api/ScheduleController.php:334
 * @route '/api/schedules/{schedule}'
 */
show.head = (args: { schedule: string | number } | [schedule: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\Api\ScheduleController::show
 * @see app/Http/Controllers/Api/ScheduleController.php:334
 * @route '/api/schedules/{schedule}'
 */
    const showForm = (args: { schedule: string | number } | [schedule: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: show.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\Api\ScheduleController::show
 * @see app/Http/Controllers/Api/ScheduleController.php:334
 * @route '/api/schedules/{schedule}'
 */
        showForm.get = (args: { schedule: string | number } | [schedule: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: show.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\Api\ScheduleController::show
 * @see app/Http/Controllers/Api/ScheduleController.php:334
 * @route '/api/schedules/{schedule}'
 */
        showForm.head = (args: { schedule: string | number } | [schedule: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
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
* @see \App\Http\Controllers\Api\ScheduleController::update
 * @see app/Http/Controllers/Api/ScheduleController.php:258
 * @route '/api/schedules/{schedule}'
 */
export const update = (args: { schedule: string | number } | [schedule: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put","patch"],
    url: '/api/schedules/{schedule}',
} satisfies RouteDefinition<["put","patch"]>

/**
* @see \App\Http\Controllers\Api\ScheduleController::update
 * @see app/Http/Controllers/Api/ScheduleController.php:258
 * @route '/api/schedules/{schedule}'
 */
update.url = (args: { schedule: string | number } | [schedule: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { schedule: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    schedule: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        schedule: args.schedule,
                }

    return update.definition.url
            .replace('{schedule}', parsedArgs.schedule.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScheduleController::update
 * @see app/Http/Controllers/Api/ScheduleController.php:258
 * @route '/api/schedules/{schedule}'
 */
update.put = (args: { schedule: string | number } | [schedule: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})
/**
* @see \App\Http\Controllers\Api\ScheduleController::update
 * @see app/Http/Controllers/Api/ScheduleController.php:258
 * @route '/api/schedules/{schedule}'
 */
update.patch = (args: { schedule: string | number } | [schedule: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

    /**
* @see \App\Http\Controllers\Api\ScheduleController::update
 * @see app/Http/Controllers/Api/ScheduleController.php:258
 * @route '/api/schedules/{schedule}'
 */
    const updateForm = (args: { schedule: string | number } | [schedule: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: update.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'PUT',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\Api\ScheduleController::update
 * @see app/Http/Controllers/Api/ScheduleController.php:258
 * @route '/api/schedules/{schedule}'
 */
        updateForm.put = (args: { schedule: string | number } | [schedule: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: update.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'PUT',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
            /**
* @see \App\Http\Controllers\Api\ScheduleController::update
 * @see app/Http/Controllers/Api/ScheduleController.php:258
 * @route '/api/schedules/{schedule}'
 */
        updateForm.patch = (args: { schedule: string | number } | [schedule: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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
* @see \App\Http\Controllers\Api\ScheduleController::updateParticipantStatus
 * @see app/Http/Controllers/Api/ScheduleController.php:435
 * @route '/api/schedules/{scheduleId}/participants/{userId}/status'
 */
export const updateParticipantStatus = (args: { scheduleId: string | number, userId: string | number } | [scheduleId: string | number, userId: string | number ], options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: updateParticipantStatus.url(args, options),
    method: 'patch',
})

updateParticipantStatus.definition = {
    methods: ["patch"],
    url: '/api/schedules/{scheduleId}/participants/{userId}/status',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Http\Controllers\Api\ScheduleController::updateParticipantStatus
 * @see app/Http/Controllers/Api/ScheduleController.php:435
 * @route '/api/schedules/{scheduleId}/participants/{userId}/status'
 */
updateParticipantStatus.url = (args: { scheduleId: string | number, userId: string | number } | [scheduleId: string | number, userId: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
                    scheduleId: args[0],
                    userId: args[1],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        scheduleId: args.scheduleId,
                                userId: args.userId,
                }

    return updateParticipantStatus.definition.url
            .replace('{scheduleId}', parsedArgs.scheduleId.toString())
            .replace('{userId}', parsedArgs.userId.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScheduleController::updateParticipantStatus
 * @see app/Http/Controllers/Api/ScheduleController.php:435
 * @route '/api/schedules/{scheduleId}/participants/{userId}/status'
 */
updateParticipantStatus.patch = (args: { scheduleId: string | number, userId: string | number } | [scheduleId: string | number, userId: string | number ], options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: updateParticipantStatus.url(args, options),
    method: 'patch',
})

    /**
* @see \App\Http\Controllers\Api\ScheduleController::updateParticipantStatus
 * @see app/Http/Controllers/Api/ScheduleController.php:435
 * @route '/api/schedules/{scheduleId}/participants/{userId}/status'
 */
    const updateParticipantStatusForm = (args: { scheduleId: string | number, userId: string | number } | [scheduleId: string | number, userId: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: updateParticipantStatus.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'PATCH',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\Api\ScheduleController::updateParticipantStatus
 * @see app/Http/Controllers/Api/ScheduleController.php:435
 * @route '/api/schedules/{scheduleId}/participants/{userId}/status'
 */
        updateParticipantStatusForm.patch = (args: { scheduleId: string | number, userId: string | number } | [scheduleId: string | number, userId: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: updateParticipantStatus.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'PATCH',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    updateParticipantStatus.form = updateParticipantStatusForm
const ScheduleController = { destroy, getUserUpcomingSchedules, getSchedulesInRange, checkConflicts, index, store, show, update, updateParticipantStatus }

export default ScheduleController