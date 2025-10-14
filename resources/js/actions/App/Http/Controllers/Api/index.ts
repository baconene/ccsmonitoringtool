import ActivityTypeController from './ActivityTypeController'
import DashboardApiController from './DashboardApiController'
import CourseApiController from './CourseApiController'
import StudentApiController from './StudentApiController'
import ScheduleApiController from './ScheduleApiController'
import ScheduleController from './ScheduleController'
import Student from './Student'
const Api = {
    ActivityTypeController: Object.assign(ActivityTypeController, ActivityTypeController),
DashboardApiController: Object.assign(DashboardApiController, DashboardApiController),
CourseApiController: Object.assign(CourseApiController, CourseApiController),
StudentApiController: Object.assign(StudentApiController, StudentApiController),
ScheduleApiController: Object.assign(ScheduleApiController, ScheduleApiController),
ScheduleController: Object.assign(ScheduleController, ScheduleController),
Student: Object.assign(Student, Student),
}

export default Api