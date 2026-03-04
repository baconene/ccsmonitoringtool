# Admin Configuration Manager - Quick Start Guide

## Overview
The Admin Configuration Manager is a new admin portal page that allows administrators to manage three key system configurations:
1. **Grade Levels** - Define education levels (Year 1, Grade 1, etc.) used across courses
2. **Activity Types** - Create and manage activity categories (Quiz, Assignment, Assessment, Exercise)
3. **Question Types** - Configure question type templates (multiple-choice, true-false, short-answer, etc.)

## Features

### Grade Levels Management
**Fields:**
- **Name**: Unique identifier (e.g., "Year 1", "Grade 1")
- **Display Name**: User-friendly name (e.g., "First Year", "Grade 1")
- **Level**: Numeric order (1, 2, 3, etc.) for proper sequencing
- **Is Active**: Toggle to activate/deactivate grade levels

**Operations:**
- ✅ Create new grade levels
- ✅ Edit existing grade levels
- ✅ Delete grade levels (with validation to prevent deletion if assigned to courses)
- ✅ View all grade levels in organized table

### Activity Types Management
**Fields:**
- **Name**: Unique activity type name (e.g., "Quiz", "Assignment")
- **Description**: Optional detailed description
- **Model Class**: Optional PHP model class path (e.g., "App\Models\Quiz")

**Operations:**
- ✅ Create new activity types
- ✅ Edit existing activity types
- ✅ Delete activity types (with validation to prevent deletion if used in activities)
- ✅ View all activity types in organized table

### Question Types Management
**Fields:**
- **Type**: Unique question type identifier (e.g., "multiple-choice", "true-false")
- **Description**: Optional description of the question type

**Operations:**
- ✅ Create new question types
- ✅ Edit existing question types
- ✅ Delete question types
- ✅ View all question types in organized table

## Access & Routes

### URL
- Main page: `/admin/configuration`
- Accessible only to users with **admin role**

### Required Permissions
- Role: `admin`

### Routes Available
```php
// View configuration page
GET /admin/configuration

// Grade Levels
POST    /admin/grade-levels                    (Create)
PUT     /admin/grade-levels/{gradeLevel}      (Update)
DELETE  /admin/grade-levels/{gradeLevel}      (Delete)

// Activity Types
POST    /admin/activity-types                  (Create)
PUT     /admin/activity-types/{activityType}  (Update)
DELETE  /admin/activity-types/{activityType}  (Delete)

// Question Types
POST    /admin/question-types                  (Create)
PUT     /admin/question-types/{questionType}  (Update)
DELETE  /admin/question-types/{questionType}  (Delete)
```

## Navigation
The configuration page is accessible from the admin sidebar:
1. Log in as an admin user
2. Look for "Admin Configuration" in the left sidebar (with Settings icon)
3. Click to open the configuration management page

## User Interface

### Tab-Based Layout
The page features three tabs for easy navigation:
- **Grade Levels**: Manage education levels
- **Activity Types**: Manage activity categories
- **Question Types**: Manage question templates

### Each Tab Contains
1. **Form Section**: Add or edit items with clear field labels and validation messages
2. **Table Section**: View all items with inline Edit and Delete buttons

### Validation
- All required fields marked with *
- Unique constraint validation (prevents duplicate names)
- Referential integrity validation:
  - Can't delete grade levels assigned to courses
  - Can't delete activity types used in activities
- Error messages displayed in real-time

## Workflow Examples

### Adding a New Grade Level
1. Navigate to Admin Configuration → Grade Levels tab
2. Fill in the form:
   - Name: "Year 2"
   - Display Name: "Second Year"
   - Level: 2
   - Check "Active"
3. Click "Add Grade Level"
4. Grade level appears in the table below

### Editing an Activity Type
1. Go to Activity Types tab
2. Click "Edit" button on the activity type you want to modify
3. Form populates with current values
4. Make your changes
5. Click "Update Activity Type"
6. Changes are saved and reflected in the table

### Deleting a Question Type
1. Navigate to Question Types tab
2. Locate the question type you want to delete
3. Click "Delete" button
4. Confirm the deletion in the popup
5. Question type is removed from the system

## Technical Details

### Controller
**File**: `app/Http/Controllers/AdminConfigurationController.php`

Methods:
- `index()`: Display configuration page with all data
- `storeGradeLevel()`: Create new grade level
- `updateGradeLevel()`: Update existing grade level
- `destroyGradeLevel()`: Delete grade level with validation
- `storeActivityType()`: Create new activity type
- `updateActivityType()`: Update existing activity type
- `destroyActivityType()`: Delete activity type with validation
- `storeQuestionType()`: Create new question type
- `updateQuestionType()`: Update existing question type
- `destroyQuestionType()`: Delete question type

### Vue Component
**File**: `resources/js/pages/Admin/AdminConfiguration.vue`

Features:
- Reactive form state management
- Tab-based interface
- Real-time error display
- Inline edit/delete functionality
- Confirmation dialogs for destructive actions

### Models Used
- `App\Models\GradeLevel`
- `App\Models\ActivityType`
- `App\Models\QuestionType`

## Validation Rules

### Grade Levels
```
name: required, unique, max:255
display_name: required, max:255
level: required, integer, min:1
is_active: boolean
```

### Activity Types
```
name: required, unique, max:255
description: nullable, max:1000
model: nullable, max:255
```

### Question Types
```
type: required, unique, max:255
description: nullable, max:1000
```

## Error Handling

The system includes comprehensive error handling:
- **Validation Errors**: Displayed inline on the form
- **Referential Integrity**: Prevents deletion of items in use
- **Duplicate Prevention**: Unique constraint validation
- **Try-Catch Blocks**: All database operations wrapped in error handling

Error messages are user-friendly and explain what went wrong.

## Best Practices

1. **Ordering Grade Levels**: Use the level field to ensure proper sorting
2. **Clear Names**: Use descriptive names for activity and question types
3. **Documentation**: Include descriptions for less obvious types
4. **Active Status**: Deactivate rather than delete grade levels if they might be used later
5. **Model Classes**: When setting model classes, ensure the path is correct and the class exists

## Future Enhancements

Possible additions to the configuration system:
- Bulk upload/import configurations from CSV
- Reorder items via drag-and-drop
- Search and filter capabilities
- Export configurations as backup
- Template presets for common configurations
- Audit trail for configuration changes

## Support

For issues or questions about the Admin Configuration Manager:
1. Check the validation messages in the form
2. Verify your user role is "admin"
3. Ensure you're accessing the correct routes
4. Review the database constraints and relationships
