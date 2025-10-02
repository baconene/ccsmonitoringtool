<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Module;
use Illuminate\Http\Request;

class ModuleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(\App\Models\Module $module)
    {
        // eager load documents so frontend gets them
        $lessons = $module->lessons()->with('documents')->get();

        //Add Quiz/Activity if there is
       // $quizzes = $module->quizzes()->with('questions')->get();

        return response()->json($lessons);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
public function store(Request $request, Course $course)
    {
        $validated = $request->validate([
            'description' => 'required|string',
            'sequence' => 'nullable|integer',
        ]);

        $module = $course->modules()->create([
            'description' => $validated['description'],
            'sequence' => $validated['sequence'] ?? ($course->modules()->count() + 1),
            'completion_percentage' => 0,
        ]);

        return back()->with('success', 'Module created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Module $module)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Module $module)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
public function update(Request $request, \App\Models\Module $module)
{
    $data = $request->validate([
        'description' => 'nullable|string',
        'sequence' => 'required|integer',
        'completion_percentage' => 'nullable|integer',
    ]);

    $module->update($data);

    // Redirect back to the previous page (Inertia expects a redirect)
    return redirect()->back()->with('success', 'Module updated successfully.');
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(\App\Models\Module $module)
{
    $module->delete();

    // Return valid Inertia response or redirect back
    return redirect()->back()->with('success', 'Module removed successfully.');
}
}
