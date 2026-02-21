import ModuleStatusController from './ModuleStatusController'
import AssessmentController from './AssessmentController'
const Student = {
    ModuleStatusController: Object.assign(ModuleStatusController, ModuleStatusController),
AssessmentController: Object.assign(AssessmentController, AssessmentController),
}

export default Student