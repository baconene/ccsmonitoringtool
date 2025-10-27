import StudentManagementController from './StudentManagementController'
import NotificationController from './NotificationController'
import AssignmentGradingController from './AssignmentGradingController'
import StudentSubmissionController from './StudentSubmissionController'
const Instructor = {
    StudentManagementController: Object.assign(StudentManagementController, StudentManagementController),
NotificationController: Object.assign(NotificationController, NotificationController),
AssignmentGradingController: Object.assign(AssignmentGradingController, AssignmentGradingController),
StudentSubmissionController: Object.assign(StudentSubmissionController, StudentSubmissionController),
}

export default Instructor