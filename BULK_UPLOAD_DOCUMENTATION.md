# User Bulk Upload Feature - Documentation

## Overview
This feature allows administrators to upload multiple users at once using a CSV file. The system automatically creates user accounts and their role-specific profiles (Student or Instructor) based on the role_id provided.

## Files Created/Modified

### Backend Files
1. **`app/Services/UserBulkUploadService.php`** - Service class that processes CSV files and creates users
2. **`app/Http/Controllers/UserController.php`** - Added `uploadCSV()` method
3. **`routes/web.php`** - Added routes for:
   - `POST /api/users/bulk-upload` - CSV upload endpoint
   - `GET /instructor/{id}` - Instructor details page

### Frontend Files
1. **`resources/js/pages/RoleManagement.vue`** - Added CSV upload modal and functionality
2. **`resources/js/pages/Instructor/InstructorDetails.vue`** - New page to display instructor details
3. **`resources/js/components/UserListTable.vue`** - Added "View Details" button for instructors

### Sample Files
1. **`public/sample_bulk_upload.csv`** - Sample CSV template

## CSV Format Requirements

### Column Headers (Required)
```csv
role_id,name,email,password,grade_level_id,section,program,department,title,specialization,bio,office_location,phone,office_hours,hire_date,employment_type,status,salary,education_level,certifications,years_experience
```

### Role-Specific Required Fields

#### Admin (role_id = 1)
- **Required**: `role_id`, `name`, `email`, `password`
- **Optional**: All other fields are ignored

#### Instructor (role_id = 2)
- **Required**: `role_id`, `name`, `email`, `password`
- **Optional**: 
  - `title` - Job title (e.g., Professor, Associate Professor)
  - `department` - Department name
  - `specialization` - Area of expertise
  - `bio` - Biography text
  - `office_location` - Office room/building
  - `phone` - Contact phone number
  - `office_hours` - Available hours (e.g., "Mon-Fri 2PM-4PM")
  - `hire_date` - Date hired (format: MM/DD/YYYY or YYYY-MM-DD)
  - `employment_type` - Type of employment (e.g., Full-time, Part-time, Contract)
  - `status` - Employment status (e.g., active, inactive, on leave)
  - `salary` - Annual salary (numeric)
  - `education_level` - Highest education level (e.g., Ph.D., Master's, Bachelor's)
  - `certifications` - Comma-separated list or JSON array
  - `years_experience` - Number of years (numeric)

#### Student (role_id = 3)
- **Required**: `role_id`, `name`, `email`, `password`, `grade_level_id`
- **Optional**:
  - `section` - Class section
  - `program` - Academic program
  - `department` - Department name

## CSV Examples

### Admin Example
```csv
role_id,name,email,password,grade_level_id,section,program,department,title,specialization,bio,office_location,phone,office_hours,hire_date,employment_type,status,salary,education_level,certifications,years_experience
1,Admin User,admin@example.com,password123,,,,,,,,,,,,,,,,
```

### Instructor Example
```csv
role_id,name,email,password,grade_level_id,section,program,department,title,specialization,bio,office_location,phone,office_hours,hire_date,employment_type,status,salary,education_level,certifications,years_experience
2,Jane Instructor,jane.instructor@example.com,password123,,,,Computer Science,Professor,Artificial Intelligence,Expert in AI and ML,Room 301,555-0101,Mon-Fri 2PM-4PM,01/15/2020,Full-time,active,75000,Ph.D.,"Machine Learning,Deep Learning",5
```

### Student Example
```csv
role_id,name,email,password,grade_level_id,section,program,department,title,specialization,bio,office_location,phone,office_hours,hire_date,employment_type,status,salary,education_level,certifications,years_experience
3,John Student,john.student@example.com,password123,1,A,Computer Science,IT,,,,,,,,,,,,
```

## Usage Instructions

### 1. Prepare CSV File
- Use the provided sample template (`public/sample_bulk_upload.csv`)
- Ensure all required fields are filled for each role type
- Save as `.csv` format

### 2. Upload via UI
1. Navigate to **Role Management** page
2. Click **"Bulk Upload CSV"** button in the top-right corner
3. In the modal, click **"Select CSV File"** and choose your file
4. Click **"Upload CSV"** button
5. Wait for processing to complete
6. Review results:
   - **Success count**: Number of users successfully created
   - **Failed count**: Number of users that failed to create
   - **Error details**: Specific errors for each failed row

### 3. View Created Users
- After successful upload, the user list will automatically refresh
- **Students**: Click "View Details" to see student profile
- **Instructors**: Click "View Details" (purple link) to see instructor profile with courses
- **Admins**: Can edit/delete but no detail page

## Features

### Automatic ID Generation
- **Employee ID** for instructors: `EMP-YYYY-####` (e.g., EMP-2025-0001)
- **Student ID** for students: Auto-generated via `Student::generateStudentIdText()`
- **Enrollment Number** for students: `ENR-YYYY-####` (e.g., ENR-2025-0001)

### Data Validation
- Email format validation
- Duplicate email detection
- Required field validation per role
- Date format parsing (supports MM/DD/YYYY, YYYY-MM-DD, DD/MM/YYYY)
- Certification parsing (supports arrays, JSON, comma-separated strings)

### Error Handling
- Transaction-based processing (rollback on error)
- Detailed error messages with row numbers
- Logging of all errors for debugging
- Continues processing even if some rows fail

### Instructor Details Page
New dedicated page showing:
- Personal information (name, email, phone, office location)
- Professional details (title, department, specialization, bio)
- Employment information (hire date, type, status, salary, experience)
- Education & certifications
- Office hours
- List of courses taught with student counts
- Quick stats (total courses, total students)
- Contact card with quick actions

## API Endpoints

### POST /api/users/bulk-upload
Upload CSV file for bulk user creation

**Request:**
- Method: POST
- Content-Type: multipart/form-data
- Body: `csv_file` (file)

**Response:**
```json
{
  "message": "CSV processing completed",
  "results": {
    "success": 10,
    "failed": 2,
    "errors": [
      {
        "line": 5,
        "email": "duplicate@example.com",
        "error": "Email already exists"
      }
    ]
  }
}
```

### GET /instructor/{id}
View instructor details page

**Parameters:**
- `id` - User ID of the instructor

**Response:** Inertia.js page with instructor data and courses

## Database Changes

### Instructors Table
- `employee_id` - Auto-generated unique identifier
- `title`, `department`, `specialization` - Professional info
- `bio` - Biography text
- `office_location`, `phone`, `office_hours` - Contact/availability
- `hire_date`, `employment_type`, `status` - Employment details
- `salary`, `education_level`, `certifications`, `years_experience` - Compensation and qualifications

### Students Table
- `student_id_text` - Auto-generated unique identifier
- `grade_level_id` - Foreign key to grade_levels table
- `section`, `program`, `department` - Academic info
- `enrollment_number` - Auto-generated enrollment identifier
- `academic_year` - Current academic year
- `status` - Student status (default: 'active')

## Security Considerations

1. **Password Hashing**: All passwords are automatically hashed using `Hash::make()`
2. **Role-based Access**: Only admin users can access bulk upload feature
3. **File Validation**: Only CSV files are accepted (max 10MB)
4. **Email Uniqueness**: Prevents duplicate email addresses
5. **Transaction Safety**: Database changes are rolled back on error

## Troubleshooting

### Common Issues

**Issue**: "Invalid CSV format - no headers found"
- **Solution**: Ensure the CSV file has a header row with column names

**Issue**: "Missing required fields: role_id, name, email, or password"
- **Solution**: Check that all required columns are present and not empty

**Issue**: "Email already exists"
- **Solution**: Remove or update the duplicate email address in the CSV

**Issue**: "Invalid role_id"
- **Solution**: Use only 1 (Admin), 2 (Instructor), or 3 (Student)

**Issue**: "grade_level_id is required for students"
- **Solution**: Ensure all student rows have a valid grade_level_id

### Debug Logs
Check Laravel logs at `storage/logs/laravel.log` for detailed error information:
```
[timestamp] local.ERROR: CSV Upload Error - Row X {"error":"...","data":{...}}
```

## Future Enhancements
- Download CSV template from UI
- Preview CSV data before upload
- Update existing users via CSV
- Bulk delete via CSV
- Export users to CSV
- Support for Excel files (.xlsx)
- Email notifications for created users
- Progress bar for large uploads
- Async processing for very large files

## Support
For issues or questions, contact the development team or check the Laravel logs for detailed error information.
