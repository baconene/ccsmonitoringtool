import StudentCourseController from './StudentCourseController'
import StudentActivitiesController from './StudentActivitiesController'
import StudentQuizController from './StudentQuizController'
const Student = {
    StudentCourseController: Object.assign(StudentCourseController, StudentCourseController),
StudentActivitiesController: Object.assign(StudentActivitiesController, StudentActivitiesController),
StudentQuizController: Object.assign(StudentQuizController, StudentQuizController),
}

export default Student