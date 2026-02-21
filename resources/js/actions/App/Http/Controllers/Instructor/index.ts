import SkillManagementController from './SkillManagementController'
import ActivitySkillController from './ActivitySkillController'
import StudentManagementController from './StudentManagementController'
import NotificationController from './NotificationController'
import AssignmentGradingController from './AssignmentGradingController'
import StudentSubmissionController from './StudentSubmissionController'
const Instructor = {
    SkillManagementController: Object.assign(SkillManagementController, SkillManagementController),
ActivitySkillController: Object.assign(ActivitySkillController, ActivitySkillController),
StudentManagementController: Object.assign(StudentManagementController, StudentManagementController),
NotificationController: Object.assign(NotificationController, NotificationController),
AssignmentGradingController: Object.assign(AssignmentGradingController, AssignmentGradingController),
StudentSubmissionController: Object.assign(StudentSubmissionController, StudentSubmissionController),
}

export default Instructor