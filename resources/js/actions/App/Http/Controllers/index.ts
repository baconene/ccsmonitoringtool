import Auth from './Auth'
import Api from './Api'
import GradeLevelController from './GradeLevelController'
import CourseController from './CourseController'
import UserController from './UserController'
import ActivityController from './ActivityController'
import QuizController from './QuizController'
import QuestionController from './QuestionController'
import AssignmentController from './AssignmentController'
import ModuleController from './ModuleController'
import Instructor from './Instructor'
import CourseStudentController from './CourseStudentController'
import CourseScheduleController from './CourseScheduleController'
import LessonController from './LessonController'
import DocumentController from './DocumentController'
import Student from './Student'
import StudentAssignmentController from './StudentAssignmentController'
import GradeController from './GradeController'
import GradeSettingsController from './GradeSettingsController'
import Settings from './Settings'
const Controllers = {
    Auth: Object.assign(Auth, Auth),
Api: Object.assign(Api, Api),
GradeLevelController: Object.assign(GradeLevelController, GradeLevelController),
CourseController: Object.assign(CourseController, CourseController),
UserController: Object.assign(UserController, UserController),
ActivityController: Object.assign(ActivityController, ActivityController),
QuizController: Object.assign(QuizController, QuizController),
QuestionController: Object.assign(QuestionController, QuestionController),
AssignmentController: Object.assign(AssignmentController, AssignmentController),
ModuleController: Object.assign(ModuleController, ModuleController),
Instructor: Object.assign(Instructor, Instructor),
CourseStudentController: Object.assign(CourseStudentController, CourseStudentController),
CourseScheduleController: Object.assign(CourseScheduleController, CourseScheduleController),
LessonController: Object.assign(LessonController, LessonController),
DocumentController: Object.assign(DocumentController, DocumentController),
Student: Object.assign(Student, Student),
StudentAssignmentController: Object.assign(StudentAssignmentController, StudentAssignmentController),
GradeController: Object.assign(GradeController, GradeController),
GradeSettingsController: Object.assign(GradeSettingsController, GradeSettingsController),
Settings: Object.assign(Settings, Settings),
}

export default Controllers