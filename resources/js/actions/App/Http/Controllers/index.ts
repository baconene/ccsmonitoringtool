import Auth from './Auth'
import Api from './Api'
import CourseController from './CourseController'
import UserController from './UserController'
import ActivityController from './ActivityController'
import QuizController from './QuizController'
import QuestionController from './QuestionController'
import AssignmentController from './AssignmentController'
import ModuleController from './ModuleController'
import CourseStudentController from './CourseStudentController'
import LessonController from './LessonController'
import Student from './Student'
import GradeLevelController from './GradeLevelController'
import GradeController from './GradeController'
import Settings from './Settings'
const Controllers = {
    Auth: Object.assign(Auth, Auth),
Api: Object.assign(Api, Api),
CourseController: Object.assign(CourseController, CourseController),
UserController: Object.assign(UserController, UserController),
ActivityController: Object.assign(ActivityController, ActivityController),
QuizController: Object.assign(QuizController, QuizController),
QuestionController: Object.assign(QuestionController, QuestionController),
AssignmentController: Object.assign(AssignmentController, AssignmentController),
ModuleController: Object.assign(ModuleController, ModuleController),
CourseStudentController: Object.assign(CourseStudentController, CourseStudentController),
LessonController: Object.assign(LessonController, LessonController),
Student: Object.assign(Student, Student),
GradeLevelController: Object.assign(GradeLevelController, GradeLevelController),
GradeController: Object.assign(GradeController, GradeController),
Settings: Object.assign(Settings, Settings),
}

export default Controllers