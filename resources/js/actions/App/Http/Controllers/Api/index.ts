import ActivityTypeController from './ActivityTypeController'
import DashboardApiController from './DashboardApiController'
import CourseApiController from './CourseApiController'
import StudentApiController from './StudentApiController'
import ScheduleApiController from './ScheduleApiController'
import Student from './Student'
import StudentDashboardController from './StudentDashboardController'
const Api = {
    ActivityTypeController: Object.assign(ActivityTypeController, ActivityTypeController),
DashboardApiController: Object.assign(DashboardApiController, DashboardApiController),
CourseApiController: Object.assign(CourseApiController, CourseApiController),
StudentApiController: Object.assign(StudentApiController, StudentApiController),
ScheduleApiController: Object.assign(ScheduleApiController, ScheduleApiController),
Student: Object.assign(Student, Student),
StudentDashboardController: Object.assign(StudentDashboardController, StudentDashboardController),
}

export default Api