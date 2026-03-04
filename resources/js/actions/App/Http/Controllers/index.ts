import Auth from './Auth'
import Api from './Api'
import GradeLevelController from './GradeLevelController'
import CourseController from './CourseController'
import GradeController from './GradeController'
import GitHistoryController from './GitHistoryController'
import UserController from './UserController'
import AdminConfigurationController from './AdminConfigurationController'
import ActivityController from './ActivityController'
import QuizController from './QuizController'
import QuestionController from './QuestionController'
import AssignmentController from './AssignmentController'
import Instructor from './Instructor'
import ModuleController from './ModuleController'
import CourseStudentController from './CourseStudentController'
import CourseScheduleController from './CourseScheduleController'
import LessonController from './LessonController'
import DocumentController from './DocumentController'
import Student from './Student'
import StudentAssignmentController from './StudentAssignmentController'
import GradeSettingsController from './GradeSettingsController'
import Settings from './Settings'
const Controllers = {
    Auth: Object.assign(Auth, Auth),
Api: Object.assign(Api, Api),
GradeLevelController: Object.assign(GradeLevelController, GradeLevelController),
CourseController: Object.assign(CourseController, CourseController),
GradeController: Object.assign(GradeController, GradeController),
GitHistoryController: Object.assign(GitHistoryController, GitHistoryController),
UserController: Object.assign(UserController, UserController),
AdminConfigurationController: Object.assign(AdminConfigurationController, AdminConfigurationController),
ActivityController: Object.assign(ActivityController, ActivityController),
QuizController: Object.assign(QuizController, QuizController),
QuestionController: Object.assign(QuestionController, QuestionController),
AssignmentController: Object.assign(AssignmentController, AssignmentController),
Instructor: Object.assign(Instructor, Instructor),
ModuleController: Object.assign(ModuleController, ModuleController),
CourseStudentController: Object.assign(CourseStudentController, CourseStudentController),
CourseScheduleController: Object.assign(CourseScheduleController, CourseScheduleController),
LessonController: Object.assign(LessonController, LessonController),
DocumentController: Object.assign(DocumentController, DocumentController),
Student: Object.assign(Student, Student),
StudentAssignmentController: Object.assign(StudentAssignmentController, StudentAssignmentController),
GradeSettingsController: Object.assign(GradeSettingsController, GradeSettingsController),
Settings: Object.assign(Settings, Settings),
}

export default Controllers