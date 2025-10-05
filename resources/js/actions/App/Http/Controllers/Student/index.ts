import StudentCourseController from './StudentCourseController'
import StudentQuizController from './StudentQuizController'
const Student = {
    StudentCourseController: Object.assign(StudentCourseController, StudentCourseController),
StudentQuizController: Object.assign(StudentQuizController, StudentQuizController),
}

export default Student