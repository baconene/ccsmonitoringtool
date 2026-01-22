# User Bulk Upload CSV Templates

## Overview
This directory contains CSV templates for bulk uploading users to the LEMA Learning Management System. Users can be created in bulk by uploading a CSV file with the proper format and required fields.

## Available Templates

### 1. `users_bulk_upload_template.csv`
**Use this for:** Starting from scratch with just headers
- Contains only column headers
- Best for creating custom uploads
- Supports all user types (Admin, Instructor, Student)

### 2. `users_admin_example.csv`
**Use this for:** Creating administrator users
- Includes 1 example admin user
- Shows recommended field values for admins
- Reference template for admin creation

### 3. `users_instructor_example.csv`
**Use this for:** Creating instructor/faculty users
- Includes 5 example instructor users
- Shows how to fill professional information fields
- Reference template for instructor creation

### 4. `users_student_example.csv`
**Use this for:** Creating student users
- Includes 10 example student users
- Shows how to fill student-specific fields
- Reference template for student creation

## CSV Format

### Required Columns (Must Always Include)
```csv
role_id,name,email,password,grade_level_id,section,program,department,title,specialization,bio,office_location,phone,office_hours,hire_date,employment_type,status,salary,education_level,certifications,years_experience
```

### Column Definitions

| Column | Type | Required | Role | Description |
|--------|------|----------|------|-------------|
| `role_id` | Integer | Yes | All | 1=Admin, 2=Instructor, 3=Student |
| `name` | String | Yes | All | Full name of user |
| `email` | String | Yes | All | Unique email address |
| `password` | String | Yes | All | User's password (plain text) |
| `grade_level_id` | Integer | Conditional | Student | Student grade level ID (required for students) |
| `section` | String | Optional | Student | Student class section (e.g., A, B, C) |
| `program` | String | Optional | All | Academic program or specialization |
| `department` | String | Optional | All | Department name |
| `title` | String | Optional | Admin/Instructor | Job title (e.g., Professor, Lecturer) |
| `specialization` | String | Optional | Instructor | Area of expertise |
| `bio` | String | Optional | Instructor | Biography or description |
| `office_location` | String | Optional | Instructor | Office room/building |
| `phone` | String | Optional | Instructor | Contact phone number |
| `office_hours` | String | Optional | Instructor | Available hours (e.g., "Mon-Fri 2PM-4PM") |
| `hire_date` | Date | Optional | Instructor | Date hired (format: MM/DD/YYYY or YYYY-MM-DD) |
| `employment_type` | String | Optional | Instructor | Type of employment (e.g., Full-time, Part-time, Contract) |
| `status` | String | Optional | Instructor | Employment status (e.g., active, inactive, on leave) |
| `salary` | Numeric | Optional | Instructor | Annual salary |
| `education_level` | String | Optional | Instructor | Highest degree (e.g., Ph.D., Master's Degree) |
| `certifications` | String | Optional | Instructor | Certifications (comma-separated list) |
| `years_experience` | Numeric | Optional | Instructor | Years of professional experience |

## Role-Specific Requirements

### Admin (role_id = 1)
**Required Fields:**
- `role_id` = 1
- `name`
- `email`
- `password`

**Optional Fields:**
- All other fields (not used for admins)

**Example:**
```csv
1,Admin User,admin@lema.edu,SecurePass@2025,,,,,Administrator,System Management,Manages the system,Room 101,555-0101,Mon-Fri 8AM-5PM,01/01/2020,Full-time,active,85000,Master's Degree,"CompTIA Security+,ITIL",8
```

### Instructor (role_id = 2)
**Required Fields:**
- `role_id` = 2
- `name`
- `email`
- `password`

**Recommended Fields:**
- `department` - Department name
- `title` - Job title
- `specialization` - Area of expertise
- `bio` - Biography
- `office_location` - Office location
- `phone` - Contact phone
- `office_hours` - Available hours
- `hire_date` - Date hired
- `employment_type` - Full-time, Part-time, Contract
- `status` - active, inactive, on leave
- `education_level` - Highest degree
- `certifications` - Professional certifications

**Example:**
```csv
2,Dr. Sarah Johnson,sarah.johnson@lema.edu,InstructorPass@2025,,,Artificial Intelligence,Computer Science,Professor,Machine Learning,Expert in ML,Building A Room 301,555-0201,Mon-Fri 2PM-4PM,06/15/2018,Full-time,active,95000,Ph.D.,"AWS Certified,TensorFlow Certified",10
```

### Student (role_id = 3)
**Required Fields:**
- `role_id` = 3
- `name`
- `email`
- `password`
- `grade_level_id` - Must be a valid grade level ID in the system

**Optional Fields:**
- `section` - Class section (A, B, C, etc.)
- `program` - Academic program

**Example:**
```csv
3,Alex Martinez,alex.martinez@lema.edu,StudentPass@2025,1,A,Computer Science,IT,,,,,,,,,,,
```

## Bulk Upload Instructions

### Step 1: Prepare Your CSV File
1. Start with one of the template files
2. Add your user data following the column format
3. Ensure all required fields are filled
4. Leave optional fields empty if not needed (don't delete columns)
5. Save as `.csv` format

### Step 2: Upload via Dashboard
1. Go to **Role Management** page
2. Click **"Bulk Upload CSV"** button (top-right corner)
3. Select your CSV file or drag & drop
4. Review the preview
5. Click **"Upload CSV"** button
6. Wait for processing to complete

### Step 3: Review Results
- **Success Count**: Number of users successfully created
- **Failed Count**: Number of users with errors
- **Error Details**: Specific error messages for each failed row

## Tips & Best Practices

### Data Entry
- **Email**: Must be unique; no duplicates allowed
- **Password**: Store securely; avoid common passwords
- **Phone**: Use format like "555-0101" for consistency
- **Dates**: Use MM/DD/YYYY or YYYY-MM-DD format
- **Status**: Use lowercase (active, inactive, on leave)
- **Employment Type**: Use Full-time, Part-time, or Contract

### CSV Format
- **Line Breaks**: Avoid line breaks in fields (use semicolons instead)
- **Commas in Text**: Enclose field in quotes if it contains commas
- **Quotes**: Escape quotes by doubling them ("")
- **Empty Fields**: Leave blank (don't skip columns)

### Examples of Properly Formatted Fields

**Field with comma:**
```csv
2,"Johnson, Dr. Sarah",sarah.johnson@lema.edu,Pass@2025,...
```

**Certifications list:**
```csv
2,Sarah Johnson,sarah.johnson@lema.edu,Pass@2025,,,,,Professor,AI,"AWS Certified,TensorFlow Certified",Microsoft Azure",...
```

**Field with quotes:**
```csv
2,Sarah Johnson,sarah.johnson@lema.edu,Pass@2025,...,"She said ""Hello""",..."
```

## Common Issues & Solutions

| Issue | Solution |
|-------|----------|
| "Invalid CSV format - no headers found" | Ensure first row contains all column headers |
| "Missing required fields" | Check that role_id, name, email, password are present |
| "Email already exists" | Remove or change duplicate email addresses |
| "Invalid role_id" | Use only 1 (Admin), 2 (Instructor), or 3 (Student) |
| "grade_level_id is required for students" | All student rows must have a valid grade_level_id |
| "File too large" | Maximum file size is 10MB; split into smaller batches |

## File Size Limits
- **Maximum file size**: 10MB
- **Recommended batch size**: 1000 users per file
- For larger uploads, split into multiple CSV files

## Security Notes

### Password Security
- Passwords are sent as plain text in CSV
- Always use HTTPS connection
- Delete CSV file after upload
- Never share CSV files with sensitive data

### Data Privacy
- Ensure compliance with GDPR/FERPA regulations
- Limit access to CSV templates containing real data
- Archive uploaded files according to retention policy

## Example Downloads

All templates are available in:
- `users_bulk_upload_template.csv` - Empty template
- `users_admin_example.csv` - Admin example
- `users_instructor_example.csv` - Instructor examples
- `users_student_example.csv` - Student examples

## Support

For issues with bulk upload:
1. Check the error messages in the results
2. Review this guide for format requirements
3. Verify required fields are present
4. Check system logs: `storage/logs/laravel.log`
5. Contact system administrator for additional help

## API Endpoint

If uploading programmatically:

**Endpoint:** `POST /api/users/bulk-upload`

**Content-Type:** `multipart/form-data`

**Parameters:**
- `csv_file` (required): The CSV file to upload

**Response Example:**
```json
{
  "success": true,
  "message": "Successfully uploaded 15 users",
  "results": {
    "success": 15,
    "failed": 0,
    "errors": []
  }
}
```

## Troubleshooting

### Upload fails with encoding error
- Save CSV as UTF-8 without BOM
- Use a text editor (not Excel) for encoding consistency

### Special characters not displaying correctly
- Ensure CSV is saved in UTF-8 encoding
- Check email addresses don't contain special characters

### Can't open CSV file after download
- File can be opened with any text editor or spreadsheet application
- Try opening with Google Sheets or LibreOffice Calc

## Updates & Version History

- **v1.0** (Jan 2026): Initial release
- **v1.1**: Added comprehensive documentation
- **v2.0**: Added role-based example templates

---

**Last Updated:** January 23, 2026

For the latest templates, visit the LEMA LMS documentation.
