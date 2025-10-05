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
            'title' => 'nullable|string|max:255',
            'description' => 'required|string',
            'sequence' => 'nullable|integer',
            'module_type' => 'nullable|string|in:Lessons,Activities,Mixed,Quizzes,Assignments,Assessment',
            'module_percentage' => 'nullable|numeric|min:0|max:100',
        ]);

        $module = $course->modules()->create([
            'title' => $validated['title'] ?? null,
            'description' => $validated['description'],
            'sequence' => $validated['sequence'] ?? ($course->modules()->count() + 1),
            'completion_percentage' => 0,
            'module_type' => $validated['module_type'] ?? 'Mixed',
            'module_percentage' => $validated['module_percentage'] ?? null,
            'created_by' => auth()->id(),
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
        'title' => 'nullable|string|max:255',
        'description' => 'nullable|string',
        'sequence' => 'required|integer',
        'completion_percentage' => 'nullable|integer',
        'module_type' => 'nullable|string|in:Lessons,Activities,Mixed,Quizzes,Assignments,Assessment',
        'module_percentage' => 'nullable|numeric|min:0|max:100',
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

    /**
     * Add activities to a module.
     */
    public function addActivities(Request $request, \App\Models\Module $module)
    {
        $validated = $request->validate([
            'activity_ids' => 'required|array',
            'activity_ids.*' => 'exists:activities,id',
        ]);

        // Get the next order number
        $maxOrder = $module->moduleActivities()->max('order') ?? 0;

        foreach ($validated['activity_ids'] as $index => $activityId) {
            // Check if activity is already attached
            $exists = $module->moduleActivities()
                ->where('activity_id', $activityId)
                ->exists();

            if (!$exists) {
                $module->moduleActivities()->create([
                    'activity_id' => $activityId,
                    'module_course_id' => $module->course_id,
                    'order' => $maxOrder + $index + 1,
                ]);
            }
        }

        return redirect()->back()->with('success', 'Activities added successfully.');
    }

    /**
     * Remove an activity from a module.
     */
    public function removeActivity(\App\Models\Module $module, $activityId)
    {
        $module->moduleActivities()
            ->where('activity_id', $activityId)
            ->delete();

        return redirect()->back()->with('success', 'Activity removed successfully.');
    }

    /**
     * Upload documents to a module.
     */
    public function uploadDocuments(Request $request, \App\Models\Module $module)
    {
        $request->validate([
            'documents' => 'required|array',
            'documents.*' => 'file|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,txt,jpg,jpeg,png|max:10240', // 10MB max
            'description' => 'nullable|string',
        ]);

        $uploadedFiles = [];

        foreach ($request->file('documents') as $file) {
            $path = $file->store('module-documents', 'public');
            
            // Create document record (you may need to adjust this based on your Document model)
            $document = \App\Models\Document::create([
                'name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'doc_type' => $file->getClientOriginalExtension(),
                'uploaded_by' => auth()->id(),
            ]);

            $uploadedFiles[] = $document;
        }

        return redirect()->back()->with('success', count($uploadedFiles) . ' document(s) uploaded successfully.');
    }
}
