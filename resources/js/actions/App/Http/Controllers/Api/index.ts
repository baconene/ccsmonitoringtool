import DashboardApiController from './DashboardApiController'
import CourseApiController from './CourseApiController'
import StudentApiController from './StudentApiController'
import ScheduleApiController from './ScheduleApiController'
const Api = {
    DashboardApiController: Object.assign(DashboardApiController, DashboardApiController),
CourseApiController: Object.assign(CourseApiController, CourseApiController),
StudentApiController: Object.assign(StudentApiController, StudentApiController),
ScheduleApiController: Object.assign(ScheduleApiController, ScheduleApiController),
}

export default Api