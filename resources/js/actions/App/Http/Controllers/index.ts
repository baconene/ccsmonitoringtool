import Auth from './Auth'
import Api from './Api'
import CourseController from './CourseController'
import UserController from './UserController'
import ModuleController from './ModuleController'
import CourseStudentController from './CourseStudentController'
import LessonController from './LessonController'
import Student from './Student'
import GradeLevelController from './GradeLevelController'
import Settings from './Settings'
const Controllers = {
    Auth: Object.assign(Auth, Auth),
Api: Object.assign(Api, Api),
CourseController: Object.assign(CourseController, CourseController),
UserController: Object.assign(UserController, UserController),
ModuleController: Object.assign(ModuleController, ModuleController),
CourseStudentController: Object.assign(CourseStudentController, CourseStudentController),
LessonController: Object.assign(LessonController, LessonController),
Student: Object.assign(Student, Student),
GradeLevelController: Object.assign(GradeLevelController, GradeLevelController),
Settings: Object.assign(Settings, Settings),
}

export default Controllers