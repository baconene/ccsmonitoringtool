# Scheduling System Documentation

## Section 1: SQL-Style Schema

```sql
-- =====================================================
-- Schedule Types Lookup Table
-- =====================================================
CREATE TABLE schedule_types (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL UNIQUE,
    description TEXT,
    color VARCHAR(7), -- Hex color for UI display (#FF5733)
    icon VARCHAR(50), -- Icon identifier for UI
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- =====================================================
-- Core Schedules Table
-- =====================================================
CREATE TABLE schedules (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    schedule_type_id BIGINT UNSIGNED NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    location VARCHAR(255), -- Room number, building, or online link
    from_datetime DATETIME NOT NULL,
    to_datetime DATETIME NOT NULL,
    is_all_day BOOLEAN DEFAULT FALSE,
    is_recurring BOOLEAN DEFAULT FALSE,
    recurrence_rule TEXT, -- For future: RRULE format (iCal standard)
    status ENUM('scheduled', 'cancelled', 'completed', 'in_progress') DEFAULT 'scheduled',
    created_by BIGINT UNSIGNED NOT NULL, -- User who created the schedule
    
    -- Polymorphic relationship to schedulable entities
    schedulable_type VARCHAR(255), -- App\Models\Activity, App\Models\Course, etc.
    schedulable_id BIGINT UNSIGNED, -- ID of the related entity
    
    -- Metadata for future extensibility
    metadata JSON,
    
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    deleted_at TIMESTAMP, -- Soft deletes
    
    FOREIGN KEY (schedule_type_id) REFERENCES schedule_types(id) ON DELETE RESTRICT,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE,
    
    INDEX idx_from_datetime (from_datetime),
    INDEX idx_to_datetime (to_datetime),
    INDEX idx_status (status),
    INDEX idx_schedulable (schedulable_type, schedulable_id),
    INDEX idx_date_range (from_datetime, to_datetime)
);

-- =====================================================
-- Schedule Participants (Pivot Table)
-- =====================================================
CREATE TABLE schedule_participants (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    schedule_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    
    -- Role in this specific schedule (can differ from user's global role)
    role_in_schedule VARCHAR(50) NOT NULL, -- 'instructor', 'student', 'organizer', 'attendee'
    
    -- Participation status
    participation_status ENUM('invited', 'accepted', 'declined', 'tentative', 'attended', 'absent') DEFAULT 'invited',
    
    -- Optional: Response and attendance tracking
    response_datetime DATETIME,
    attended_at DATETIME,
    notes TEXT, -- Special notes for this participant
    
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    FOREIGN KEY (schedule_id) REFERENCES schedules(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    
    -- Ensure a user can only be added once per schedule
    UNIQUE KEY unique_schedule_user (schedule_id, user_id),
    
    INDEX idx_user_id (user_id),
    INDEX idx_schedule_id (schedule_id),
    INDEX idx_participation_status (participation_status)
);

-- =====================================================
-- Optional: Activity-specific schedule details
-- =====================================================
CREATE TABLE schedule_activities (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    schedule_id BIGINT UNSIGNED NOT NULL UNIQUE,
    activity_id BIGINT UNSIGNED NOT NULL,
    submission_deadline DATETIME,
    passing_score DECIMAL(5, 2),
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    FOREIGN KEY (schedule_id) REFERENCES schedules(id) ON DELETE CASCADE,
    FOREIGN KEY (activity_id) REFERENCES activities(id) ON DELETE CASCADE
);

-- =====================================================
-- Optional: Course-specific schedule details
-- =====================================================
CREATE TABLE schedule_courses (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    schedule_id BIGINT UNSIGNED NOT NULL UNIQUE,
    course_id BIGINT UNSIGNED NOT NULL,
    session_number INT, -- Lecture 1, 2, 3, etc.
    topics_covered TEXT, -- What will be covered in this session
    required_materials TEXT, -- Books, equipment needed
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    FOREIGN KEY (schedule_id) REFERENCES schedules(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
);

-- =====================================================
-- Optional: Adhoc schedule details (personal events)
-- =====================================================
CREATE TABLE schedule_adhoc (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    schedule_id BIGINT UNSIGNED NOT NULL UNIQUE,
    event_type VARCHAR(50), -- 'meeting', 'appointment', 'reminder', 'personal'
    privacy_level ENUM('public', 'private', 'confidential') DEFAULT 'private',
    reminder_minutes INT, -- Minutes before event to send reminder
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    FOREIGN KEY (schedule_id) REFERENCES schedules(id) ON DELETE CASCADE
);
```

## Section 2: Sample Insert Statements

```sql
-- =====================================================
-- 1. Insert Schedule Types
-- =====================================================
INSERT INTO schedule_types (name, description, color, icon) VALUES
('activity', 'Scheduled activities like quizzes and assignments', '#3B82F6', 'clipboard-list'),
('course', 'Course lectures and sessions', '#10B981', 'book-open'),
('adhoc', 'Personal or administrative events', '#F59E0B', 'calendar');

-- =====================================================
-- 2. Sample Activity Schedule
-- =====================================================
-- Quiz scheduled for 2 students and 1 instructor
INSERT INTO schedules (
    schedule_type_id,
    title,
    description,
    location,
    from_datetime,
    to_datetime,
    status,
    created_by,
    schedulable_type,
    schedulable_id
) VALUES (
    1, -- activity type
    'Mathematics Quiz 1',
    'Chapter 1-3 Quiz covering algebra and geometry fundamentals',
    'Room 101',
    '2025-10-15 09:00:00',
    '2025-10-15 10:00:00',
    'scheduled',
    4, -- instructor user_id
    'App\\Models\\Activity',
    1 -- activity_id
);

-- Add participants to the activity schedule
INSERT INTO schedule_participants (schedule_id, user_id, role_in_schedule, participation_status) VALUES
(1, 4, 'instructor', 'accepted'), -- Instructor
(1, 9, 'student', 'invited'),     -- Student 1
(1, 10, 'student', 'invited');    -- Student 2

-- Link to activity details
INSERT INTO schedule_activities (schedule_id, activity_id, submission_deadline, passing_score) VALUES
(1, 1, '2025-10-15 10:00:00', 70.00);

-- =====================================================
-- 3. Sample Course Schedule
-- =====================================================
-- Course lecture with 1 instructor and 3 students
INSERT INTO schedules (
    schedule_type_id,
    title,
    description,
    location,
    from_datetime,
    to_datetime,
    status,
    created_by,
    schedulable_type,
    schedulable_id
) VALUES (
    2, -- course type
    'Computer Programming 101 - Lecture 5',
    'Introduction to Object-Oriented Programming concepts',
    'Computer Lab B',
    '2025-10-16 14:00:00',
    '2025-10-16 16:00:00',
    'scheduled',
    5, -- instructor user_id
    'App\\Models\\Course',
    3 -- course_id
);

-- Add participants to the course schedule
INSERT INTO schedule_participants (schedule_id, user_id, role_in_schedule, participation_status) VALUES
(2, 5, 'instructor', 'accepted'),  -- Instructor
(2, 11, 'student', 'accepted'),    -- Student 1
(2, 12, 'student', 'accepted'),    -- Student 2
(2, 13, 'student', 'invited');     -- Student 3

-- Link to course details
INSERT INTO schedule_courses (schedule_id, course_id, session_number, topics_covered, required_materials) VALUES
(2, 3, 5, 'Classes, Objects, Inheritance, Polymorphism', 'Laptop, Python IDE installed');

-- =====================================================
-- 4. Sample Adhoc Schedule (Personal Event)
-- =====================================================
-- Personal event for one user
INSERT INTO schedules (
    schedule_type_id,
    title,
    description,
    location,
    from_datetime,
    to_datetime,
    is_all_day,
    status,
    created_by
) VALUES (
    3, -- adhoc type
    'Department Meeting',
    'Monthly faculty meeting to discuss curriculum updates',
    'Conference Room A',
    '2025-10-17 10:00:00',
    '2025-10-17 11:30:00',
    FALSE,
    'scheduled',
    4 -- user who created it
);

-- Add participant (just the creator in this case)
INSERT INTO schedule_participants (schedule_id, user_id, role_in_schedule, participation_status) VALUES
(3, 4, 'organizer', 'accepted');

-- Link to adhoc details
INSERT INTO schedule_adhoc (schedule_id, event_type, privacy_level, reminder_minutes) VALUES
(3, 'meeting', 'private', 30);
```

## Section 3: Database Design Notes

### Key Design Decisions:

1. **Polymorphic Relationship**: Uses `schedulable_type` and `schedulable_id` to link schedules to any entity (Activity, Course, etc.)

2. **Separate Detail Tables**: Optional extension tables (schedule_activities, schedule_courses, schedule_adhoc) store type-specific data

3. **Flexible Participant Roles**: `role_in_schedule` allows users to have different roles in different schedules

4. **Attendance Tracking**: Built-in support for invitation status, acceptance, and attendance

5. **Extensibility**: 
   - `metadata` JSON field for future custom fields
   - `recurrence_rule` for recurring events
   - Easy to add more tables like `schedule_rooms`, `schedule_tags`, etc.

6. **Performance**: Proper indexes on date ranges and foreign keys for fast calendar queries

7. **Soft Deletes**: Cancelled schedules can be preserved for historical records

### Query Patterns:

```sql
-- Get upcoming schedules for a user
SELECT s.*, st.name as type_name, st.color
FROM schedules s
INNER JOIN schedule_participants sp ON s.id = sp.schedule_id
INNER JOIN schedule_types st ON s.schedule_type_id = st.id
WHERE sp.user_id = ? 
  AND s.from_datetime >= NOW()
  AND s.status = 'scheduled'
ORDER BY s.from_datetime ASC;

-- Get all schedules in a date range (for calendar view)
SELECT s.*, st.name as type_name, st.color,
       GROUP_CONCAT(DISTINCT u.name) as participants
FROM schedules s
INNER JOIN schedule_types st ON s.schedule_type_id = st.id
INNER JOIN schedule_participants sp ON s.id = sp.schedule_id
INNER JOIN users u ON sp.user_id = u.id
WHERE s.from_datetime >= ?
  AND s.to_datetime <= ?
  AND sp.user_id = ?
GROUP BY s.id
ORDER BY s.from_datetime;
```

### Future Extensions:

- **Recurring Events**: Implement using `recurrence_rule` with iCal RRULE format
- **Room Management**: Add `schedule_rooms` table to track room bookings
- **Conflict Detection**: Query overlapping schedules for users or rooms
- **Notifications**: Trigger reminders based on schedule times
- **Calendar Sync**: Export to iCal/Google Calendar format
- **Timezone Support**: Store times in UTC, display in user's timezone
