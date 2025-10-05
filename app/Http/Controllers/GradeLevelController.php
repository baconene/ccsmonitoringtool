<?php

namespace App\Http\Controllers;

use App\Models\GradeLevel;
use Illuminate\Http\Request;

class GradeLevelController extends Controller
{
    /**
     * Display a listing of active grade levels.
     */
    public function index()
    {
        $gradeLevels = GradeLevel::active()
            ->ordered()
            ->get(['id', 'name', 'display_name', 'level']);

        return response()->json([
            'grade_levels' => $gradeLevels
        ]);
    }

    /**
     * Store a newly created grade level.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:grade_levels',
            'display_name' => 'required|string|max:100',
            'level' => 'required|integer|unique:grade_levels',
            'is_active' => 'boolean',
        ]);

        $gradeLevel = GradeLevel::create($validated);

        return response()->json([
            'message' => 'Grade level created successfully',
            'grade_level' => $gradeLevel
        ], 201);
    }

    /**
     * Display the specified grade level.
     */
    public function show(GradeLevel $gradeLevel)
    {
        return response()->json([
            'grade_level' => $gradeLevel
        ]);
    }

    /**
     * Update the specified grade level.
     */
    public function update(Request $request, GradeLevel $gradeLevel)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:50|unique:grade_levels,name,' . $gradeLevel->id,
            'display_name' => 'sometimes|string|max:100',
            'level' => 'sometimes|integer|unique:grade_levels,level,' . $gradeLevel->id,
            'is_active' => 'sometimes|boolean',
        ]);

        $gradeLevel->update($validated);

        return response()->json([
            'message' => 'Grade level updated successfully',
            'grade_level' => $gradeLevel
        ]);
    }

    /**
     * Remove the specified grade level.
     */
    public function destroy(GradeLevel $gradeLevel)
    {
        $gradeLevel->delete();

        return response()->json([
            'message' => 'Grade level deleted successfully'
        ]);
    }
}
