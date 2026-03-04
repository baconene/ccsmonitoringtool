import configuration from './configuration'
import gradeLevels from './grade-levels'
import activityTypes from './activity-types'
import questionTypes from './question-types'
const admin = {
    configuration: Object.assign(configuration, configuration),
gradeLevels: Object.assign(gradeLevels, gradeLevels),
activityTypes: Object.assign(activityTypes, activityTypes),
questionTypes: Object.assign(questionTypes, questionTypes),
}

export default admin