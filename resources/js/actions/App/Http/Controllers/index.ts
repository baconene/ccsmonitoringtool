import Auth from './Auth'
import Api from './Api'
import CourseController from './CourseController'
import ModuleController from './ModuleController'
import LessonController from './LessonController'
import Settings from './Settings'

const Controllers = {
    Auth: Object.assign(Auth, Auth),
    Api: Object.assign(Api, Api),
    CourseController: Object.assign(CourseController, CourseController),
    ModuleController: Object.assign(ModuleController, ModuleController),
    LessonController: Object.assign(LessonController, LessonController),
    Settings: Object.assign(Settings, Settings),
}

export default Controllers