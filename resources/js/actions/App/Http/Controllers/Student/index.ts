import StudentCourseController from './StudentCourseController'
import StudentActivityController from './StudentActivityController'
import StudentQuizController from './StudentQuizController'
const Student = {
    StudentCourseController: Object.assign(StudentCourseController, StudentCourseController),
StudentActivityController: Object.assign(StudentActivityController, StudentActivityController),
StudentQuizController: Object.assign(StudentQuizController, StudentQuizController),
}

export default Student