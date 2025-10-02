<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Lesson;
use DB;
use Illuminate\Http\Request;

class LessonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
 public function store(Request $request)
    {
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'module_id'   => 'required|exists:modules,id',
            'documents'   => 'array',
            'documents.*.name' => 'required|string',
            'documents.*.file_path' => 'nullable|string',
            'documents.*.doc_type'  => 'required|string',
        ]);

        $lesson = Lesson::create([
            'title'       => $data['title'],
            'description' => $data['description'],
            'module_id'   => $data['module_id'],
        ]);

        DB::table('lesson_module')->insert([
            'lesson_id' => $lesson->id,
            'module_id' => $data['module_id'], 
        ]);

        // Attach documents (normalize if re-usable)
        if (!empty($data['documents'])) {
            foreach ($data['documents'] as $docData) {
                $doc = Document::firstOrCreate(
                    ['name' => $docData['name'], 'file_path' => $docData['file_path']],
                    ['doc_type' => $docData['doc_type']]
                );
                $lesson->documents()->attach($doc->id);
            }
        }

        return redirect()->back()->with('success', 'Lesson saved successfully!');
    }
    /**
     * Display the specified resource.
     */
    public function show(Lesson $lesson)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Lesson $lesson)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
public function update(Request $request, $lessonId)
    {
        // Validate input
        $data = $request->validate([
            'description' => 'required|string',
        ]);

        // Find the lesson
        $lesson = Lesson::findOrFail($lessonId);

        // Update the description
        $lesson->description = $data['description'];
        $lesson->save();

        // Return response
        return response()->json([
            'message' => 'Lesson updated successfully',
            'lesson' => $lesson,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Lesson $lesson)
    {
        //
    }
}
