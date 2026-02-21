import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\Student\AssessmentController::show
 * @see app/Http/Controllers/Api/Student/AssessmentController.php:27
 * @route '/api/student/assessment'
 */
export const show = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/api/student/assessment',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\Student\AssessmentController::show
 * @see app/Http/Controllers/Api/Student/AssessmentController.php:27
 * @route '/api/student/assessment'
 */
show.url = (options?: RouteQueryOptions) => {
    return show.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Student\AssessmentController::show
 * @see app/Http/Controllers/Api/Student/AssessmentController.php:27
 * @route '/api/student/assessment'
 */
show.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Api\Student\AssessmentController::show
 * @see app/Http/Controllers/Api/Student/AssessmentController.php:27
 * @route '/api/student/assessment'
 */
show.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\Api\Student\AssessmentController::show
 * @see app/Http/Controllers/Api/Student/AssessmentController.php:27
 * @route '/api/student/assessment'
 */
    const showForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: show.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\Api\Student\AssessmentController::show
 * @see app/Http/Controllers/Api/Student/AssessmentController.php:27
 * @route '/api/student/assessment'
 */
        showForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: show.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\Api\Student\AssessmentController::show
 * @see app/Http/Controllers/Api/Student/AssessmentController.php:27
 * @route '/api/student/assessment'
 */
        showForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: show.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    show.form = showForm
/**
* @see \App\Http\Controllers\Api\Student\AssessmentController::getSkillAssessments
 * @see app/Http/Controllers/Api/Student/AssessmentController.php:76
 * @route '/api/student/skills/assessments'
 */
export const getSkillAssessments = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getSkillAssessments.url(options),
    method: 'get',
})

getSkillAssessments.definition = {
    methods: ["get","head"],
    url: '/api/student/skills/assessments',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\Student\AssessmentController::getSkillAssessments
 * @see app/Http/Controllers/Api/Student/AssessmentController.php:76
 * @route '/api/student/skills/assessments'
 */
getSkillAssessments.url = (options?: RouteQueryOptions) => {
    return getSkillAssessments.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Student\AssessmentController::getSkillAssessments
 * @see app/Http/Controllers/Api/Student/AssessmentController.php:76
 * @route '/api/student/skills/assessments'
 */
getSkillAssessments.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getSkillAssessments.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Api\Student\AssessmentController::getSkillAssessments
 * @see app/Http/Controllers/Api/Student/AssessmentController.php:76
 * @route '/api/student/skills/assessments'
 */
getSkillAssessments.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getSkillAssessments.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\Api\Student\AssessmentController::getSkillAssessments
 * @see app/Http/Controllers/Api/Student/AssessmentController.php:76
 * @route '/api/student/skills/assessments'
 */
    const getSkillAssessmentsForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: getSkillAssessments.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\Api\Student\AssessmentController::getSkillAssessments
 * @see app/Http/Controllers/Api/Student/AssessmentController.php:76
 * @route '/api/student/skills/assessments'
 */
        getSkillAssessmentsForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: getSkillAssessments.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\Api\Student\AssessmentController::getSkillAssessments
 * @see app/Http/Controllers/Api/Student/AssessmentController.php:76
 * @route '/api/student/skills/assessments'
 */
        getSkillAssessmentsForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: getSkillAssessments.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    getSkillAssessments.form = getSkillAssessmentsForm
/**
* @see \App\Http\Controllers\Api\Student\AssessmentController::getStrengths
 * @see app/Http/Controllers/Api/Student/AssessmentController.php:103
 * @route '/api/student/strengths'
 */
export const getStrengths = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getStrengths.url(options),
    method: 'get',
})

getStrengths.definition = {
    methods: ["get","head"],
    url: '/api/student/strengths',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\Student\AssessmentController::getStrengths
 * @see app/Http/Controllers/Api/Student/AssessmentController.php:103
 * @route '/api/student/strengths'
 */
getStrengths.url = (options?: RouteQueryOptions) => {
    return getStrengths.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Student\AssessmentController::getStrengths
 * @see app/Http/Controllers/Api/Student/AssessmentController.php:103
 * @route '/api/student/strengths'
 */
getStrengths.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getStrengths.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Api\Student\AssessmentController::getStrengths
 * @see app/Http/Controllers/Api/Student/AssessmentController.php:103
 * @route '/api/student/strengths'
 */
getStrengths.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getStrengths.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\Api\Student\AssessmentController::getStrengths
 * @see app/Http/Controllers/Api/Student/AssessmentController.php:103
 * @route '/api/student/strengths'
 */
    const getStrengthsForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: getStrengths.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\Api\Student\AssessmentController::getStrengths
 * @see app/Http/Controllers/Api/Student/AssessmentController.php:103
 * @route '/api/student/strengths'
 */
        getStrengthsForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: getStrengths.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\Api\Student\AssessmentController::getStrengths
 * @see app/Http/Controllers/Api/Student/AssessmentController.php:103
 * @route '/api/student/strengths'
 */
        getStrengthsForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: getStrengths.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    getStrengths.form = getStrengthsForm
/**
* @see \App\Http\Controllers\Api\Student\AssessmentController::getWeaknesses
 * @see app/Http/Controllers/Api/Student/AssessmentController.php:127
 * @route '/api/student/weaknesses'
 */
export const getWeaknesses = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getWeaknesses.url(options),
    method: 'get',
})

getWeaknesses.definition = {
    methods: ["get","head"],
    url: '/api/student/weaknesses',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\Student\AssessmentController::getWeaknesses
 * @see app/Http/Controllers/Api/Student/AssessmentController.php:127
 * @route '/api/student/weaknesses'
 */
getWeaknesses.url = (options?: RouteQueryOptions) => {
    return getWeaknesses.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Student\AssessmentController::getWeaknesses
 * @see app/Http/Controllers/Api/Student/AssessmentController.php:127
 * @route '/api/student/weaknesses'
 */
getWeaknesses.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getWeaknesses.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Api\Student\AssessmentController::getWeaknesses
 * @see app/Http/Controllers/Api/Student/AssessmentController.php:127
 * @route '/api/student/weaknesses'
 */
getWeaknesses.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getWeaknesses.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\Api\Student\AssessmentController::getWeaknesses
 * @see app/Http/Controllers/Api/Student/AssessmentController.php:127
 * @route '/api/student/weaknesses'
 */
    const getWeaknessesForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: getWeaknesses.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\Api\Student\AssessmentController::getWeaknesses
 * @see app/Http/Controllers/Api/Student/AssessmentController.php:127
 * @route '/api/student/weaknesses'
 */
        getWeaknessesForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: getWeaknesses.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\Api\Student\AssessmentController::getWeaknesses
 * @see app/Http/Controllers/Api/Student/AssessmentController.php:127
 * @route '/api/student/weaknesses'
 */
        getWeaknessesForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: getWeaknesses.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    getWeaknesses.form = getWeaknessesForm
/**
* @see \App\Http\Controllers\Api\Student\AssessmentController::getRadarData
 * @see app/Http/Controllers/Api/Student/AssessmentController.php:151
 * @route '/api/student/assessment/radar'
 */
export const getRadarData = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getRadarData.url(options),
    method: 'get',
})

getRadarData.definition = {
    methods: ["get","head"],
    url: '/api/student/assessment/radar',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\Student\AssessmentController::getRadarData
 * @see app/Http/Controllers/Api/Student/AssessmentController.php:151
 * @route '/api/student/assessment/radar'
 */
getRadarData.url = (options?: RouteQueryOptions) => {
    return getRadarData.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Student\AssessmentController::getRadarData
 * @see app/Http/Controllers/Api/Student/AssessmentController.php:151
 * @route '/api/student/assessment/radar'
 */
getRadarData.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getRadarData.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Api\Student\AssessmentController::getRadarData
 * @see app/Http/Controllers/Api/Student/AssessmentController.php:151
 * @route '/api/student/assessment/radar'
 */
getRadarData.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getRadarData.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\Api\Student\AssessmentController::getRadarData
 * @see app/Http/Controllers/Api/Student/AssessmentController.php:151
 * @route '/api/student/assessment/radar'
 */
    const getRadarDataForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: getRadarData.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\Api\Student\AssessmentController::getRadarData
 * @see app/Http/Controllers/Api/Student/AssessmentController.php:151
 * @route '/api/student/assessment/radar'
 */
        getRadarDataForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: getRadarData.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\Api\Student\AssessmentController::getRadarData
 * @see app/Http/Controllers/Api/Student/AssessmentController.php:151
 * @route '/api/student/assessment/radar'
 */
        getRadarDataForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: getRadarData.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    getRadarData.form = getRadarDataForm
/**
* @see \App\Http\Controllers\Api\Student\AssessmentController::getStudentAssessment
 * @see app/Http/Controllers/Api/Student/AssessmentController.php:52
 * @route '/api/admin/student/{studentId}/assessment'
 */
export const getStudentAssessment = (args: { studentId: string | number } | [studentId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getStudentAssessment.url(args, options),
    method: 'get',
})

getStudentAssessment.definition = {
    methods: ["get","head"],
    url: '/api/admin/student/{studentId}/assessment',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\Student\AssessmentController::getStudentAssessment
 * @see app/Http/Controllers/Api/Student/AssessmentController.php:52
 * @route '/api/admin/student/{studentId}/assessment'
 */
getStudentAssessment.url = (args: { studentId: string | number } | [studentId: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { studentId: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    studentId: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        studentId: args.studentId,
                }

    return getStudentAssessment.definition.url
            .replace('{studentId}', parsedArgs.studentId.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Student\AssessmentController::getStudentAssessment
 * @see app/Http/Controllers/Api/Student/AssessmentController.php:52
 * @route '/api/admin/student/{studentId}/assessment'
 */
getStudentAssessment.get = (args: { studentId: string | number } | [studentId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getStudentAssessment.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Api\Student\AssessmentController::getStudentAssessment
 * @see app/Http/Controllers/Api/Student/AssessmentController.php:52
 * @route '/api/admin/student/{studentId}/assessment'
 */
getStudentAssessment.head = (args: { studentId: string | number } | [studentId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getStudentAssessment.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\Api\Student\AssessmentController::getStudentAssessment
 * @see app/Http/Controllers/Api/Student/AssessmentController.php:52
 * @route '/api/admin/student/{studentId}/assessment'
 */
    const getStudentAssessmentForm = (args: { studentId: string | number } | [studentId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: getStudentAssessment.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\Api\Student\AssessmentController::getStudentAssessment
 * @see app/Http/Controllers/Api/Student/AssessmentController.php:52
 * @route '/api/admin/student/{studentId}/assessment'
 */
        getStudentAssessmentForm.get = (args: { studentId: string | number } | [studentId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: getStudentAssessment.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\Api\Student\AssessmentController::getStudentAssessment
 * @see app/Http/Controllers/Api/Student/AssessmentController.php:52
 * @route '/api/admin/student/{studentId}/assessment'
 */
        getStudentAssessmentForm.head = (args: { studentId: string | number } | [studentId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: getStudentAssessment.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    getStudentAssessment.form = getStudentAssessmentForm
/**
* @see \App\Http\Controllers\Api\Student\AssessmentController::recalculateCourseAssessments
 * @see app/Http/Controllers/Api/Student/AssessmentController.php:174
 * @route '/api/admin/course/{courseId}/recalculate-assessments'
 */
export const recalculateCourseAssessments = (args: { courseId: string | number } | [courseId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: recalculateCourseAssessments.url(args, options),
    method: 'post',
})

recalculateCourseAssessments.definition = {
    methods: ["post"],
    url: '/api/admin/course/{courseId}/recalculate-assessments',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\Student\AssessmentController::recalculateCourseAssessments
 * @see app/Http/Controllers/Api/Student/AssessmentController.php:174
 * @route '/api/admin/course/{courseId}/recalculate-assessments'
 */
recalculateCourseAssessments.url = (args: { courseId: string | number } | [courseId: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { courseId: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    courseId: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        courseId: args.courseId,
                }

    return recalculateCourseAssessments.definition.url
            .replace('{courseId}', parsedArgs.courseId.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Student\AssessmentController::recalculateCourseAssessments
 * @see app/Http/Controllers/Api/Student/AssessmentController.php:174
 * @route '/api/admin/course/{courseId}/recalculate-assessments'
 */
recalculateCourseAssessments.post = (args: { courseId: string | number } | [courseId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: recalculateCourseAssessments.url(args, options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\Api\Student\AssessmentController::recalculateCourseAssessments
 * @see app/Http/Controllers/Api/Student/AssessmentController.php:174
 * @route '/api/admin/course/{courseId}/recalculate-assessments'
 */
    const recalculateCourseAssessmentsForm = (args: { courseId: string | number } | [courseId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: recalculateCourseAssessments.url(args, options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\Api\Student\AssessmentController::recalculateCourseAssessments
 * @see app/Http/Controllers/Api/Student/AssessmentController.php:174
 * @route '/api/admin/course/{courseId}/recalculate-assessments'
 */
        recalculateCourseAssessmentsForm.post = (args: { courseId: string | number } | [courseId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: recalculateCourseAssessments.url(args, options),
            method: 'post',
        })
    
    recalculateCourseAssessments.form = recalculateCourseAssessmentsForm
/**
* @see \App\Http\Controllers\Api\Student\AssessmentController::compareAssessments
 * @see app/Http/Controllers/Api/Student/AssessmentController.php:202
 * @route '/api/admin/assessment/compare'
 */
export const compareAssessments = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: compareAssessments.url(options),
    method: 'post',
})

compareAssessments.definition = {
    methods: ["post"],
    url: '/api/admin/assessment/compare',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\Student\AssessmentController::compareAssessments
 * @see app/Http/Controllers/Api/Student/AssessmentController.php:202
 * @route '/api/admin/assessment/compare'
 */
compareAssessments.url = (options?: RouteQueryOptions) => {
    return compareAssessments.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Student\AssessmentController::compareAssessments
 * @see app/Http/Controllers/Api/Student/AssessmentController.php:202
 * @route '/api/admin/assessment/compare'
 */
compareAssessments.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: compareAssessments.url(options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\Api\Student\AssessmentController::compareAssessments
 * @see app/Http/Controllers/Api/Student/AssessmentController.php:202
 * @route '/api/admin/assessment/compare'
 */
    const compareAssessmentsForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: compareAssessments.url(options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\Api\Student\AssessmentController::compareAssessments
 * @see app/Http/Controllers/Api/Student/AssessmentController.php:202
 * @route '/api/admin/assessment/compare'
 */
        compareAssessmentsForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: compareAssessments.url(options),
            method: 'post',
        })
    
    compareAssessments.form = compareAssessmentsForm
const AssessmentController = { show, getSkillAssessments, getStrengths, getWeaknesses, getRadarData, getStudentAssessment, recalculateCourseAssessments, compareAssessments }

export default AssessmentController