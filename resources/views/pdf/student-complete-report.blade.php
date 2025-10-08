<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'DejaVu Sans', Arial, sans-serif; 
            font-size: 11px; 
            line-height: 1.3; 
            color: #333; 
            margin: 0;
            padding: 15px;
        }
        
        .header { 
            text-align: center; 
            border-bottom: 2px solid #2563eb; 
            padding-bottom: 10px; 
            margin-bottom: 15px; 
        }
        .header h1 { 
            color: #2563eb; 
            font-size: 20px; 
            margin-bottom: 3px; 
        }
        .header p { 
            color: #6b7280; 
            font-size: 10px; 
            margin-bottom: 2px;
        }
        
        .student-info { 
            background-color: #f9fafb; 
            padding: 10px; 
            border-radius: 3px; 
            margin-bottom: 15px; 
        }
        .student-info h2 { 
            color: #2563eb; 
            font-size: 14px; 
            margin-bottom: 8px; 
        }
        .info-row { 
            margin-bottom: 3px; 
            font-size: 10px;
        }
        .info-label { 
            font-weight: bold; 
            display: inline-block; 
            width: 100px; 
        }
        
        .summary-line { 
            background-color: #f9fafb; 
            padding: 12px; 
            border-radius: 5px; 
            margin-bottom: 20px; 
            text-align: center;
            border: 1px solid #e5e7eb;
        }
        .summary-inline { 
            display: inline-block; 
            margin: 0 15px; 
        }
        .summary-value { 
            font-size: 16px; 
            font-weight: bold; 
            color: #2563eb; 
            margin-right: 3px;
        }
        .summary-label { 
            font-size: 11px; 
            color: #6b7280; 
        }
        
        .course-summary { 
            margin-bottom: 20px;
            page-break-inside: avoid;
        }
        .course-header { 
            background-color: #2563eb; 
            color: white; 
            padding: 6px 10px; 
            border-radius: 3px; 
            margin-bottom: 8px; 
        }
        .course-header h3 { 
            font-size: 13px; 
            margin: 0; 
        }
        
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 10px; 
        }
        th, td { 
            border: 1px solid #d1d5db; 
            padding: 4px; 
            text-align: left; 
            font-size: 9px; 
        }
        th { 
            background-color: #f3f4f6; 
            font-weight: bold; 
        }
        
        .grade-excellent { color: #10b981; font-weight: bold; }
        .grade-good { color: #3b82f6; font-weight: bold; }
        .grade-average { color: #f59e0b; font-weight: bold; }
        .grade-poor { color: #dc2626; font-weight: bold; }
        
        .status-completed { color: #10b981; font-weight: bold; }
        .status-progress { color: #f59e0b; }
        .status-not-started { color: #6b7280; }
        
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
        
        .page-break { page-break-before: always; }
        .avoid-break { page-break-inside: avoid; }
        .compact-section { margin-bottom: 15px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $title }}</h1>
        <p>Learning Management System</p>
        <p>Generated on {{ $generated_at->format('F j, Y \a\t g:i A') }}</p>
    </div>

    <div class="student-info">
        <h2>Student Information</h2>
        <div class="info-row">
            <span class="info-label">Name:</span>
            <span>{{ $summary['student']['name'] }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Email:</span>
            <span>{{ $summary['student']['email'] }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Student ID:</span>
            <span>{{ $summary['student']['student_id'] }}</span>
        </div>
    </div>

    <div class="summary-line">
        <div class="summary-inline">
            <span class="summary-value">{{ $summary['overall_gpa'] }}</span>
            <span class="summary-label">Overall GPA</span>
        </div>
        <div class="summary-inline">
            <span class="summary-value">{{ $summary['total_courses'] }}</span>
            <span class="summary-label">Total Courses</span>
        </div>
        <div class="summary-inline">
            <span class="summary-value">{{ $summary['completed_courses'] }}</span>
            <span class="summary-label">Completed</span>
        </div>
        <div class="summary-inline">
            <span class="summary-value">{{ $summary['average_grade'] }}%</span>
            <span class="summary-label">Average Grade</span>
        </div>
    </div>

    <div class="compact-section avoid-break">
        <h3 style="color: #2563eb; margin-bottom: 10px; font-size: 14px;">Course Grades Summary</h3>
        <table>
            <thead>
                <tr>
                    <th style="width: 35%;">Course</th>
                    <th style="width: 15%;">Grade</th>
                    <th style="width: 10%;">Letter</th>
                    <th style="width: 15%;">Status</th>
                    <th style="width: 12%;">Modules</th>
                    <th style="width: 13%;">Activities</th>
                </tr>
            </thead>
            <tbody>
                @foreach($summary['courses'] as $course)
                    <tr>
                        <td style="font-weight: bold;">{{ $course['course']['title'] }}</td>
                        <td class="@if($course['overall_grade'] >= 90) grade-excellent @elseif($course['overall_grade'] >= 80) grade-good @elseif($course['overall_grade'] >= 70) grade-average @else grade-poor @endif">
                            {{ $course['overall_grade'] }}%
                        </td>
                        <td>{{ $course['overall_letter_grade'] }}</td>
                        <td class="@if($course['completion_status'] === 'completed') status-completed @elseif($course['completion_status'] === 'in_progress') status-progress @else status-not-started @endif">
                            {{ ucfirst(str_replace('_', ' ', $course['completion_status'])) }}
                        </td>
                        <td>{{ collect($course['modules'])->where('is_completed', true)->count() }}/{{ count($course['modules']) }}</td>
                        <td>{{ $course['activity_summary']['completed'] }}/{{ $course['activity_summary']['total'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @foreach($summary['courses'] as $course)
        <div class="course-summary">
            <div class="course-header">
                <h3>{{ $course['course']['title'] }} - Detailed Breakdown</h3>
            </div>

            <h4 style="margin-bottom: 8px; color: #2563eb; font-size: 12px;">Module Performance</h4>
            <table>
                <thead>
                    <tr>
                        <th style="width: 30%;">Module</th>
                        <th style="width: 12%;">Type</th>
                        <th style="width: 15%;">Score</th>
                        <th style="width: 10%;">Grade</th>
                        <th style="width: 18%;">Status</th>
                        <th style="width: 15%;">Activities</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($course['modules'] as $module)
                        <tr>
                            <td style="font-size: 8px;">{{ strlen($module['module_title']) > 40 ? substr($module['module_title'], 0, 37) . '...' : $module['module_title'] }}</td>
                            <td>{{ $module['module_type'] }}</td>
                            <td class="@if($module['module_score'] >= 90) grade-excellent @elseif($module['module_score'] >= 80) grade-good @elseif($module['module_score'] >= 70) grade-average @else grade-poor @endif">{{ $module['module_score'] }}%</td>
                            <td>{{ $module['module_letter_grade'] }}</td>
                            <td class="@if($module['is_completed']) status-completed @elseif($module['completion_status'] === 'in_progress') status-progress @else status-not-started @endif">
                                {{ ucfirst(str_replace('_', ' ', $module['completion_status'])) }}
                            </td>
                            <td>{{ collect($module['activities'])->where('is_completed', true)->count() }}/{{ count($module['activities']) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            @if(!empty($course['modules']) && collect($course['modules'])->pluck('activity_types')->flatten(1)->isNotEmpty())
                <div class="compact-section">
                    <h4 style="margin: 10px 0 6px 0; color: #2563eb; font-size: 11px;">Activity Summary</h4>
                    <div style="font-size: 8px; line-height: 1.3;">
                        @foreach($course['modules'] as $module)
                            @if(!empty($module['activity_types']))
                                <strong>{{ strlen($module['module_title']) > 30 ? substr($module['module_title'], 0, 27) . '...' : $module['module_title'] }}:</strong>
                                @foreach($module['activity_types'] as $activityType)
                                    {{ $activityType['type'] }} {{ $activityType['type_score'] }}% ({{ $activityType['completed_count'] }}/{{ $activityType['total_count'] }}){{ !$loop->last ? ', ' : '' }}
                                @endforeach
                                {{ !$loop->last ? ' | ' : '' }}
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    @endforeach

    <div class="footer">
        <p>Learning Management System - Generated {{ $generated_at->format('Y-m-d H:i:s') }}</p>
    </div>
</body>
</html>