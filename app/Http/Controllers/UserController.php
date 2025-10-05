<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Course;
use App\Models\LessonCompletion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('role')->get();
        return response()->json($users);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', Rules\Password::defaults()],
            'role' => ['required', 'string', 'exists:roles,name'],
            'grade_level' => ['nullable', 'string', 'max:50'],
            'section' => ['nullable', 'string', 'max:50'],
        ]);

        $role = Role::where('name', $request->role)->first();

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $role->id,
            'grade_level' => $request->grade_level,
            'section' => $request->section,
        ]);

        return response()->json($user);
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'role' => ['required', 'string', 'exists:roles,name'],
            'grade_level' => ['nullable', 'string', 'max:50'],
            'section' => ['nullable', 'string', 'max:50'],
        ]);

        $role = Role::where('name', $request->role)->first();

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'role_id' => $role->id,
            'grade_level' => $request->grade_level,
            'section' => $request->section,
        ]);

        if ($request->filled('password')) {
            $request->validate([
                'password' => Rules\Password::defaults(),
            ]);
            
            $user->update([
                'password' => Hash::make($request->password),
            ]);
        }

        return response()->json($user);
    }

    public function destroy(User $user)
    {
        if ($user->id === Auth::id()) {
            return response()->json(['message' => 'Cannot delete your own account'], 403);
        }

        $user->delete();
        return response()->json(['message' => 'User deleted successfully']);
    }

    public function studentDetails($id)
    {
        $student = User::findOrFail($id);
        
        if ($student->role->name !== 'student') {
            return response()->json(['message' => 'User is not a student'], 404);
        }

        $enrolledCourses = $student->enrolledCourses()
            ->with(['lessons', 'completedLessons' => function ($query) use ($student) {
                $query->where('user_id', $student->id);
            }])
            ->get()
            ->map(function ($course) use ($student) {
                $totalLessons = $course->lessons->count();
                $completedLessons = $course->completedLessons->count();
                $progress = $totalLessons > 0 ? round(($completedLessons / $totalLessons) * 100) : 0;
                
                $lastActivity = LessonCompletion::where('user_id', $student->id)
                    ->whereIn('lesson_id', $course->lessons->pluck('id'))
                    ->latest()
                    ->first();

                return [
                    'id' => $course->id,
                    'title' => $course->title,
                    'progress' => $progress,
                    'total_lessons' => $totalLessons,
                    'completed_lessons' => $completedLessons,
                    'last_activity' => $lastActivity ? $lastActivity->created_at->diffForHumans() : null,
                ];
            });

        return response()->json([
            'student' => $student,
            'enrolledCourses' => $enrolledCourses,
        ]);
    }
}