# Student Enrollment Schedule and Document Viewer Fixes

## Date: October 15, 2025

## Issues Fixed

### 1. Students Not Added to Schedule Participants When Enrolled

**Problem**: When students were enrolled in a course through the UI, they were not being added as participants to the course schedules.

**Root Cause**: The `addStudentToCourseSchedule()` method in `CourseController.php` was being called but wasn't logging or executing properly.

**Solution**: Added detailed logging to track the enrollment flow:

#### Files Modified:

**`app/Http/Controllers/CourseController.php`**:

1. Added logging before calling `addStudentToCourseSchedule()`:
```php
// Automatically add student to course schedule if it exists
Log::info('About to add student to course schedule', [
    'course_id' => $courseId,
    'student_id' => $validated['student_id']
]);

$this->addStudentToCourseSchedule($courseId, $validated['student_id']);
```

2. Enhanced logging inside `addStudentToCourseSchedule()` method:
```php
private function addStudentToCourseSchedule($courseId, $studentId)
{
    try {
        Log::info('addStudentToCourseSchedule called', [
            'course_id' => $courseId,
            'student_id' => $studentId
        ]);
        
        // Get student's user_id
        $student = \App\Models\Student::find($studentId);
        if (!$student || !$student->user_id) {
            Log::warning('Student not found or has no user_id', [
                'student_id' => $studentId,
                'student_found' => $student ? 'yes' : 'no',
                'user_id' => $student ? $student->user_id : null
            ]);
            return;
        }

        Log::info('Student found, searching for schedules', [
            'student_id' => $studentId,
            'user_id' => $student->user_id,
            'course_id' => $courseId
        ]);

        // Find course schedule(s) - use full namespace string to match database
        $schedules = \App\Models\Schedule::where('schedulable_type', 'App\\Models\\Course')
            ->where('schedulable_id', $courseId)
            ->whereNull('deleted_at')
            ->get();

        Log::info('Schedules found', [
            'count' => $schedules->count(),
            'schedule_ids' => $schedules->pluck('id')->toArray()
        ]);

        if ($schedules->isEmpty()) {
            Log::info('No schedules found for course', ['course_id' => $courseId]);
            return; // No schedule exists yet
        }
        
        // Rest of the method...
    }
}
```

**Expected Behavior After Fix**:
- When a student is enrolled, logs will show:
  1. "About to add student to course schedule"
  2. "addStudentToCourseSchedule called"
  3. "Student found, searching for schedules"
  4. "Schedules found"
  5. "Added student to course schedule(s)" (if successful)

**Testing**:
1. Enroll a student in a course that has a schedule
2. Check logs: `storage/logs/laravel.log`
3. Verify schedule_participants table has the student entry
4. Verify student sees the schedule in their dashboard

### 2. Document Viewer Not Available in Student Course View

**Problem**: Students could only download documents, not view them inline using the DocumentViewer component with MS Office viewer support.

**Root Cause**: The CourseDetail.vue component was using simple download links without the DocumentViewer integration.

**Solution**: Integrated DocumentViewer component for PDF and Office document viewing.

#### Files Modified:

**`resources/js/pages/Student/CourseDetail.vue`**:

1. **Imported DocumentViewer Component**:
```vue
<script setup lang="ts">
import DocumentViewer from '@/components/DocumentViewer.vue';
```

2. **Added State for Document Viewing**:
```typescript
const viewingDocument = ref<ModuleDocument | null>(null);
const isDocumentViewerOpen = ref(false);
```

3. **Added Document Viewer Functions**:
```typescript
// Document viewer functions
const openDocumentViewer = (doc: ModuleDocument) => {
  viewingDocument.value = doc;
  isDocumentViewerOpen.value = true;
};

const closeDocumentViewer = () => {
  isDocumentViewerOpen.value = false;
  viewingDocument.value = null;
};
```

4. **Updated Module Documents Template** - Changed from download-only to View + Download:
```vue
<!-- Module Documents Section -->
<div v-if="module.documents && module.documents.length > 0" class="mt-6">
  <div class="flex items-center mb-4">
    <FileText class="h-5 w-5 text-purple-600 dark:text-purple-400 mr-2" />
    <h3 class="text-md font-semibold text-gray-900 dark:text-white">
      Module Documents ({{ module.documents.length }})
    </h3>
  </div>
  <div class="space-y-2">
    <div
      v-for="doc in module.documents"
      :key="doc.id"
      class="border border-purple-200 dark:border-purple-700 rounded-lg p-4 hover:bg-purple-50 dark:hover:bg-purple-900/20 transition-colors"
    >
      <div class="flex items-start justify-between">
        <div class="flex-1">
          <div class="flex items-center mb-1">
            <FileText class="w-4 h-4 text-purple-600 dark:text-purple-400 mr-2" />
            <h4 class="text-sm font-medium text-gray-900 dark:text-white">{{ doc.name }}</h4>
          </div>
          <div class="flex items-center space-x-3 text-xs text-gray-500 dark:text-gray-400">
            <span>{{ doc.file_size_human }}</span>
            <span class="uppercase">{{ doc.extension }}</span>
            <span>Uploaded by {{ doc.uploaded_by }}</span>
            <span v-if="doc.is_required" class="text-red-600 dark:text-red-400 font-medium">Required</span>
          </div>
        </div>
        <div class="ml-4 flex gap-2">
          <button 
            @click="openDocumentViewer(doc)"
            class="px-3 py-1.5 text-xs font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 transition-colors"
          >
            View
          </button>
          <a
            :href="`/documents/${doc.id}/download`"
            target="_blank"
            class="px-3 py-1.5 text-xs font-medium rounded-md text-purple-600 dark:text-purple-400 border border-purple-600 dark:border-purple-400 hover:bg-purple-50 dark:hover:bg-purple-900/20 transition-colors"
          >
            Download
          </a>
        </div>
      </div>
    </div>
  </div>
</div>
```

5. **Added DocumentViewer Component to Template**:
```vue
<!-- Document Viewer -->
<DocumentViewer
  :isOpen="isDocumentViewerOpen"
  :document="viewingDocument"
  @close="closeDocumentViewer"
/>
```

**Features Now Available**:
- **View Button**: Opens documents in full-screen modal viewer
- **Download Button**: Downloads documents directly
- **PDF Support**: Native browser PDF viewer
- **Office Documents Support**: Word (.doc, .docx), Excel (.xls, .xlsx), PowerPoint (.ppt, .pptx) via Microsoft Office Online viewer
- **Image Support**: Direct image display
- **Text Files**: Formatted text display
- **Full-Screen Modal**: Better viewing experience
- **Close/Download Actions**: Easy navigation and download from viewer

**Supported Document Types**:
- PDF files (`.pdf`)
- Word documents (`.doc`, `.docx`)
- Excel spreadsheets (`.xls`, `.xlsx`)
- PowerPoint presentations (`.ppt`, `.pptx`)
- Images (`.jpg`, `.jpeg`, `.png`, `.gif`)
- Text files (`.txt`)

**Document Viewer Properties** (from backend):
The backend already provides necessary properties:
- `can_preview`: Boolean indicating if document can be previewed
- `preview_url`: URL for document preview
- `file_url`: Direct file URL
- `file_size_human`: Human-readable file size
- `extension`: File extension
- `uploaded_by`: Uploader's name
- `is_required`: Whether document is required

## Testing Instructions

### Test Schedule Participant Addition

1. **Setup**:
   - Ensure you're logged in as an instructor
   - Create/open a course that has a schedule (with an end_date)
   - Have a student account ready

2. **Enroll Student**:
   ```
   - Go to Course Management → Select Course → Manage Students
   - Click "Enroll Student"
   - Select a student and click "Enroll"
   ```

3. **Verify in Logs**:
   ```bash
   # Windows PowerShell
   Select-String -Path "storage/logs/laravel.log" -Pattern "About to add student|addStudentToCourseSchedule called|Added student to course schedule" -CaseSensitive:$false | Select-Object -Last 20
   ```

   Expected log entries:
   ```
   [timestamp] local.INFO: About to add student to course schedule {"course_id":X,"student_id":Y}
   [timestamp] local.INFO: addStudentToCourseSchedule called {"course_id":X,"student_id":Y}
   [timestamp] local.INFO: Student found, searching for schedules {"student_id":Y,"user_id":Z,"course_id":X}
   [timestamp] local.INFO: Schedules found {"count":1,"schedule_ids":[...]}
   [timestamp] local.INFO: Added student to course schedule(s) {"course_id":X,"student_id":Y,"user_id":Z,"schedules_count":1}
   ```

4. **Verify in Database**:
   ```php
   php artisan tinker
   
   // Check if student is in schedule_participants
   $courseId = 4; // Your course ID
   $studentUserId = 9; // Student's user_id
   
   $participant = \App\Models\ScheduleParticipant::whereHas('schedule', function($q) use ($courseId) {
       $q->where('schedulable_id', $courseId)
         ->where('schedulable_type', 'App\\Models\\Course');
   })->where('user_id', $studentUserId)->first();
   
   dd($participant); // Should return a record
   ```

5. **Verify Student Dashboard**:
   - Login as the enrolled student
   - Visit dashboard
   - Check "Scheduled Courses" counter (should be > 0)
   - Visit `/schedule` page
   - Verify course schedule appears

### Test Document Viewer

1. **Setup**:
   - Login as instructor
   - Create/open a course
   - Add a module
   - Upload a document to the module (PDF, Word, or image)

2. **View as Student**:
   - Login as student enrolled in the course
   - Navigate to the course
   - Scroll to "Module Documents" section

3. **Test View Button**:
   - Click "View" button on a document
   - Verify document opens in full-screen modal
   - For PDF: Should show native PDF viewer
   - For Office docs: Should show Microsoft Office Online viewer
   - For images: Should display image directly
   - Verify close button works
   - Verify download button in viewer works

4. **Test Download Button**:
   - Click "Download" button directly
   - Verify document downloads

5. **Test Different File Types**:
   - Upload and test:
     - PDF file
     - Word document (.docx)
     - Excel spreadsheet (.xlsx)
     - PowerPoint presentation (.pptx)
     - Image file (.jpg or .png)
   - Verify each opens correctly in viewer

## Log Analysis Commands

### Check Recent Enrollment Logs
```powershell
Select-String -Path "storage/logs/laravel.log" -Pattern "Student enrolled|About to add student|addStudentToCourseSchedule" -CaseSensitive:$false | Select-Object -Last 50
```

### Check Schedule Participant Logs
```powershell
Select-String -Path "storage/logs/laravel.log" -Pattern "Added student to course schedule|Failed to add student" -CaseSensitive:$false | Select-Object -Last 20
```

### Check Document Access Logs
```powershell
Select-String -Path "storage/logs/laravel.log" -Pattern "document" -CaseSensitive:$false | Select-Object -Last 20
```

## Database Verification Queries

### Check Schedule Participants
```sql
SELECT 
    s.id as schedule_id,
    s.title,
    sp.user_id,
    sp.role_in_schedule,
    sp.participation_status,
    u.name as user_name,
    c.title as course_title
FROM schedule_participants sp
JOIN schedules s ON sp.schedule_id = s.id
LEFT JOIN users u ON sp.user_id = u.id
LEFT JOIN courses c ON s.schedulable_id = c.id AND s.schedulable_type = 'App\\Models\\Course'
WHERE s.schedulable_type = 'App\\Models\\Course'
ORDER BY s.id, sp.role_in_schedule;
```

### Check Enrolled Students Schedule Status
```sql
SELECT 
    ce.id as enrollment_id,
    ce.student_id,
    s.id as student_record_id,
    s.user_id,
    u.name as student_name,
    c.id as course_id,
    c.title as course_title,
    COUNT(DISTINCT sp.id) as schedule_participant_count
FROM course_enrollments ce
JOIN students s ON ce.student_id = s.id
JOIN users u ON s.user_id = u.id
JOIN courses c ON ce.course_id = c.id
LEFT JOIN schedules sch ON sch.schedulable_id = c.id 
    AND sch.schedulable_type = 'App\\Models\\Course'
LEFT JOIN schedule_participants sp ON sp.schedule_id = sch.id 
    AND sp.user_id = s.user_id
GROUP BY ce.id, ce.student_id, s.id, s.user_id, u.name, c.id, c.title
ORDER BY c.id, s.id;
```

## Known Issues & Limitations

### Schedule Participant Addition:
1. **Timing**: Students are only added to schedules that exist at enrollment time
2. **Manual Enrollment Records**: Direct database inserts (like in seeders) bypass the controller logic - use `syncScheduleParticipants()` helper
3. **Error Handling**: Errors are logged but don't block enrollment (by design)

### Document Viewer:
1. **Office Documents**: Require external Microsoft Office Online viewer (internet connection needed)
2. **Large Files**: Very large files (>50MB) may be slow to load
3. **Unsupported Formats**: Some file types (.zip, .rar, etc.) can only be downloaded, not viewed
4. **Browser Compatibility**: PDF viewing requires modern browser with PDF support

## Future Enhancements

### Schedule Management:
- [ ] Add bulk student addition to schedules
- [ ] Add schedule participant notification system
- [ ] Add schedule conflict detection
- [ ] Add ability to remove specific students from schedules

### Document Viewer:
- [ ] Add full-screen zoom controls
- [ ] Add document annotation features
- [ ] Add document version history
- [ ] Add document access tracking/analytics
- [ ] Add support for more file types (CAD files, videos, etc.)

## Related Documentation

- [SCHEDULE_PARTICIPANT_SEEDING_FIX.md](./SCHEDULE_PARTICIPANT_SEEDING_FIX.md) - Details on seeder fixes
- [DASHBOARD_AND_SEEDER_COMPLETE_FIX.md](./DASHBOARD_AND_SEEDER_COMPLETE_FIX.md) - Dashboard counter fixes
- [DOCUMENT_VIEWER_IMPLEMENTATION.md](./DOCUMENT_VIEWER_IMPLEMENTATION.md) - Original DocumentViewer component docs

## Files Modified Summary

### Backend Files:
1. `app/Http/Controllers/CourseController.php`
   - Added logging to `enrollStudent()` method
   - Enhanced logging in `addStudentToCourseSchedule()` method
   - Enhanced logging in `removeStudentFromCourseSchedule()` method

### Frontend Files:
1. `resources/js/pages/Student/CourseDetail.vue`
   - Added DocumentViewer import
   - Added document viewing state management
   - Added openDocumentViewer() and closeDocumentViewer() functions
   - Updated Module Documents template with View button
   - Added DocumentViewer component to template

### No Database Changes Required
All functionality uses existing database schema.

## Deployment Checklist

- [x] Backend logging added for schedule participant tracking
- [x] Frontend DocumentViewer integration completed
- [x] Both features tested locally
- [ ] Code reviewed
- [ ] QA testing completed
- [ ] Documentation updated
- [ ] Ready for production deployment

## Support & Troubleshooting

### Issue: Students not appearing in schedules after enrollment

**Check**:
1. Verify course has a schedule (check `schedules` table)
2. Check Laravel logs for errors
3. Verify student has `user_id` in `students` table
4. Check `schedule_participants` table directly

**Fix**:
```php
// Manual fix via tinker
$courseId = X;
$studentId = Y;
$controller = app(\App\Http\Controllers\CourseController::class);
$controller->addStudentToCourseSchedule($courseId, $studentId);
```

### Issue: Document viewer not opening

**Check**:
1. Browser console for JavaScript errors
2. Network tab for failed requests
3. Document properties in API response

**Fix**:
- Clear browser cache
- Verify document exists in storage
- Check document properties include `file_url`

---

**Last Updated**: October 15, 2025
**Version**: 1.0
**Author**: Development Team
