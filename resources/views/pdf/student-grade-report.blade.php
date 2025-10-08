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
        
        .student-info { 
            background-color: #f9fafb; 
            padding: 15px; 
            border-radius: 5px; 
            margin-bottom: 20px; 
        }
        .student-info h2 { 
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
            width: 100px; 
        }
        
        .grade-summary { 
            background-color: #dbeafe; 
            padding: 15px; 
            text-align: center; 
            margin-bottom: 20px; 
            border-radius: 5px; 
        }
        .overall-grade { 
            font-size: 32px; 
            font-weight: bold; 
            color: #2563eb; 
        }
        .letter-grade { 
            font-size: 20px; 
            margin: 5px 0; 
        }
        
        .course-section { 
            margin-bottom: 30px; 
            page-break-inside: avoid; 
        }
        .course-header { 
            background-color: #2563eb; 
            color: white; 
            padding: 10px; 
            border-radius: 5px; 
            margin-bottom: 15px; 
        }
        .course-header h3 { 
            font-size: 16px; 
            margin: 0; 
        }
        
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 15px; 
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
        
        .status-completed { color: #10b981; font-weight: bold; }
        .status-progress { color: #f59e0b; }
        .status-not-started { color: #6b7280; }
        .overdue { background-color: #fef2f2; color: #dc2626; }
        
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
            <span>{{ $grades['student']['name'] }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Email:</span>
            <span>{{ $grades['student']['email'] }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Student ID:</span>
            <span>{{ $grades['student']['student_id'] }}</span>
        </div>
    </div>

    <div class="course-section">
        <div class="course-header">
            <h3>{{ $grades['course']['title'] }}</h3>
            @if($grades['course']['description'])
                <p style="margin: 5px 0 0 0; opacity: 0.9; font-size: 12px;">{{ $grades['course']['description'] }}</p>
            @endif
        </div>

        <div class="grade-summary">
            <div class="overall-grade">{{ $grades['overall_grade'] }}%</div>
            <div class="letter-grade">{{ $grades['overall_letter_grade'] }}</div>
            <p style="margin: 5px 0 0 0;">
                Status: {{ ucfirst(str_replace('_', ' ', $grades['completion_status'])) }}
            </p>
        </div>

        <h4 style="margin-bottom: 10px; color: #2563eb;">Module Performance</h4>
        <table>
            <thead>
                <tr>
                    <th>Module</th>
                    <th>Type</th>
                    <th>Weight</th>
                    <th>Score</th>
                    <th>Grade</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($grades['modules'] as $module)
                    <tr>
                        <td>{{ $module['module_title'] }}</td>
                        <td>{{ $module['module_type'] }}</td>
                        <td>{{ $module['module_weight'] }}%</td>
                        <td>{{ $module['module_score'] }}%</td>
                        <td>{{ $module['module_letter_grade'] }}</td>
                        <td class="@if($module['is_completed']) status-completed @elseif($module['completion_status'] === 'in_progress') status-progress @else status-not-started @endif">
                            {{ ucfirst(str_replace('_', ' ', $module['completion_status'])) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <h4 style="margin: 20px 0 10px 0; color: #2563eb;">Activity Details</h4>
        <table>
            <thead>
                <tr>
                    <th>Module</th>
                    <th>Activity</th>
                    <th>Type</th>
                    <th>Score</th>
                    <th>Max</th>
                    <th>%</th>
                    <th>Grade</th>
                    <th>Status</th>
                    <th>Due Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($grades['modules'] as $module)
                    @foreach($module['activities'] as $activity)
                        <tr class="{{ $activity['is_overdue'] ? 'overdue' : '' }}">
                            <td>{{ $module['module_title'] }}</td>
                            <td>{{ $activity['activity_title'] }}</td>
                            <td>{{ $activity['activity_type'] }}</td>
                            <td>{{ $activity['score'] }}</td>
                            <td>{{ $activity['max_score'] }}</td>
                            <td>{{ $activity['percentage_score'] }}%</td>
                            <td>{{ $activity['letter_grade'] }}</td>
                            <td class="{{ $activity['is_completed'] ? 'status-completed' : 'status-not-started' }}">
                                @if($activity['is_overdue'])
                                    Overdue
                                @elseif($activity['is_completed'])
                                    Completed
                                @else
                                    Incomplete
                                @endif
                            </td>
                            <td>
                                @if($activity['due_date'])
                                    {{ \Carbon\Carbon::parse($activity['due_date'])->format('M j, Y') }}
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="footer">
        <p>Learning Management System - Generated {{ $generated_at->format('Y-m-d H:i:s') }}</p>
    </div>
</body>
</html>