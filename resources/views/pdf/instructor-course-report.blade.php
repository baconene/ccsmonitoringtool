<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'DejaVu Sans', Arial, sans-serif; 
            font-size: 12px; 
            line-height: 1.4; 
            color: #333; 
        }
        
        .header { 
            text-align: center; 
            border-bottom: 2px solid #2563eb; 
            padding-bottom: 15px; 
            margin-bottom: 20px; 
        }
        .header h1 { 
            color: #2563eb; 
            font-size: 24px; 
            margin-bottom: 5px; 
        }
        .header p { 
            color: #6b7280; 
            font-size: 11px; 
        }
        
        .course-info { 
            background-color: #f9fafb; 
            padding: 15px; 
            border-radius: 5px; 
            margin-bottom: 20px; 
        }
        .course-info h2 { 
            color: #2563eb; 
            font-size: 16px; 
            margin-bottom: 10px; 
        }
        .info-row { 
            margin-bottom: 5px; 
        }
        .info-label { 
            font-weight: bold; 
            display: inline-block; 
            width: 120px; 
        }
        
        .stats-section { 
            background-color: #dbeafe; 
            padding: 15px; 
            border-radius: 5px; 
            margin-bottom: 20px; 
        }
        .stats-grid { 
            display: flex; 
            justify-content: space-between; 
            margin-top: 10px; 
        }
        .stat-box { 
            background-color: white; 
            padding: 10px; 
            border-radius: 5px; 
            text-align: center; 
            border: 1px solid #d1d5db; 
            width: 14%; 
        }
        .stat-value { 
            font-size: 16px; 
            font-weight: bold; 
            color: #2563eb; 
        }
        .stat-label { 
            font-size: 9px; 
            color: #6b7280; 
            margin-top: 3px; 
        }
        
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 15px; 
        }
        th, td { 
            border: 1px solid #d1d5db; 
            padding: 8px; 
            text-align: left; 
            font-size: 10px; 
        }
        th { 
            background-color: #f3f4f6; 
            font-weight: bold; 
        }
        tr:nth-child(even) { 
            background-color: #f9fafb; 
        }
        
        .grade-excellent { color: #10b981; font-weight: bold; }
        .grade-good { color: #3b82f6; font-weight: bold; }
        .grade-average { color: #f59e0b; font-weight: bold; }
        .grade-poor { color: #dc2626; font-weight: bold; }
        
        .status-completed { color: #10b981; font-weight: bold; }
        .status-progress { color: #f59e0b; }
        .status-not-started { color: #6b7280; }
        
        .distribution { 
            margin-top: 20px; 
            padding: 15px; 
            background-color: #f9fafb; 
            border-radius: 5px; 
        }
        .distribution h4 { 
            color: #2563eb; 
            margin-bottom: 10px; 
        }
        .distribution ul { 
            margin-left: 20px; 
        }
        .distribution li { 
            margin-bottom: 3px; 
            font-size: 10px; 
        }
        
        .footer { 
            position: fixed; 
            bottom: 15px; 
            left: 0; 
            right: 0; 
            text-align: center; 
            font-size: 9px; 
            color: #6b7280; 
            border-top: 1px solid #d1d5db; 
            padding-top: 5px; 
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $title }}</h1>
        <p>Learning Management System</p>
        <p>Generated on {{ $generated_at->format('F j, Y \a\t g:i A') }}</p>
    </div>

    <div class="course-info">
        <h2>Course Information</h2>
        <div class="info-row">
            <span class="info-label">Course:</span>
            <span>{{ $courseGrades['course']['title'] }}</span>
        </div>
        @if($courseGrades['course']['description'])
            <div class="info-row">
                <span class="info-label">Description:</span>
                <span>{{ $courseGrades['course']['description'] }}</span>
            </div>
        @endif
        <div class="info-row">
            <span class="info-label">Instructor:</span>
            <span>{{ $instructor->name }} ({{ $instructor->email }})</span>
        </div>
        <div class="info-row">
            <span class="info-label">Total Students:</span>
            <span>{{ $courseGrades['class_statistics']['total_students'] }}</span>
        </div>
    </div>

    <div class="stats-section">
        <h3 style="color: #2563eb; margin-bottom: 10px;">Class Statistics</h3>
        <div class="stats-grid">
            <div class="stat-box">
                <div class="stat-value">{{ $courseGrades['class_statistics']['average_grade'] }}%</div>
                <div class="stat-label">Class Average</div>
            </div>
            <div class="stat-box">
                <div class="stat-value">{{ $courseGrades['class_statistics']['highest_grade'] }}%</div>
                <div class="stat-label">Highest Grade</div>
            </div>
            <div class="stat-box">
                <div class="stat-value">{{ $courseGrades['class_statistics']['lowest_grade'] }}%</div>
                <div class="stat-label">Lowest Grade</div>
            </div>
            <div class="stat-box">
                <div class="stat-value">{{ $courseGrades['class_statistics']['passing_count'] }}</div>
                <div class="stat-label">Passing</div>
            </div>
            <div class="stat-box">
                <div class="stat-value">{{ $courseGrades['class_statistics']['failing_count'] }}</div>
                <div class="stat-label">Failing</div>
            </div>
            <div class="stat-box">
                <div class="stat-value">{{ $courseGrades['class_statistics']['completion_rate'] }}%</div>
                <div class="stat-label">Completion Rate</div>
            </div>
        </div>
    </div>

    <h3 style="color: #2563eb; margin-bottom: 10px;">Individual Student Grades</h3>
    <table>
        <thead>
            <tr>
                <th>Rank</th>
                <th>Student Name</th>
                <th>Email</th>
                <th>Grade</th>
                <th>Letter</th>
                <th>Status</th>
                <th>Modules</th>
                <th>Activities</th>
            </tr>
        </thead>
        <tbody>
            @foreach($courseGrades['students'] as $index => $student)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $student['student_name'] }}</td>
                    <td>{{ $student['student_email'] }}</td>
                    <td class="@if($student['overall_grade'] >= 90) grade-excellent @elseif($student['overall_grade'] >= 80) grade-good @elseif($student['overall_grade'] >= 70) grade-average @else grade-poor @endif">
                        {{ $student['overall_grade'] }}%
                    </td>
                    <td>{{ $student['overall_letter_grade'] }}</td>
                    <td class="@if($student['completion_status'] === 'completed') status-completed @elseif($student['completion_status'] === 'in_progress') status-progress @else status-not-started @endif">
                        {{ ucfirst(str_replace('_', ' ', $student['completion_status'])) }}
                    </td>
                    <td>{{ $student['completed_modules'] }}/{{ $student['module_count'] }}</td>
                    <td>{{ $student['activity_summary']['completed'] }}/{{ $student['activity_summary']['total'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @if($courseGrades['class_statistics']['total_students'] > 0)
        <div class="distribution">
            <h4>Grade Distribution</h4>
            @php
                $gradeRanges = [
                    'A (90-100%)' => collect($courseGrades['students'])->whereBetween('overall_grade', [90, 100])->count(),
                    'B (80-89%)' => collect($courseGrades['students'])->whereBetween('overall_grade', [80, 89.99])->count(),
                    'C (70-79%)' => collect($courseGrades['students'])->whereBetween('overall_grade', [70, 79.99])->count(),
                    'D (60-69%)' => collect($courseGrades['students'])->whereBetween('overall_grade', [60, 69.99])->count(),
                    'F (Below 60%)' => collect($courseGrades['students'])->where('overall_grade', '<', 60)->count(),
                ];
            @endphp
            
            <ul>
                @foreach($gradeRanges as $range => $count)
                    @if($count > 0)
                        <li>{{ $range }}: {{ $count }} students ({{ round(($count / $courseGrades['class_statistics']['total_students']) * 100, 1) }}%)</li>
                    @endif
                @endforeach
            </ul>
        </div>
    @endif

    <div class="footer">
        <p>Learning Management System - Generated {{ $generated_at->format('Y-m-d H:i:s') }} by {{ $instructor->name }}</p>
    </div>
</body>
</html>