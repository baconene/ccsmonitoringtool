import StudentCourseController from './StudentCourseController'
import StudentActivityController from './StudentActivityController'
import StudentActivityResultsController from './StudentActivityResultsController'
import StudentQuizController from './StudentQuizController'
const Student = {
    StudentCourseController: Object.assign(StudentCourseController, StudentCourseController),
StudentActivityController: Object.assign(StudentActivityController, StudentActivityController),
StudentActivityResultsController: Object.assign(StudentActivityResultsController, StudentActivityResultsController),
StudentQuizController: Object.assign(StudentQuizController, StudentQuizController),
}

export default Student