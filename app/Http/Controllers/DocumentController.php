<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;

class DocumentController extends Controller
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
     * Upload documents with dynamic model binding.
     */
    public function upload(Request $request)
    {
        $request->validate([
            'model_type' => 'required|in:course,activity,lesson,module,report,project,assessment',
            'foreign_key_id' => 'required|integer',
            'foreign_key_name' => 'required|string',
            'visibility' => 'required|in:public,students,instructors,private',
            'is_required' => 'boolean',
            'files.*' => 'required|file|max:20480', // 20MB max
        ]);

        $uploadedDocuments = [];

        foreach ($request->file('files') as $file) {
            // Store file
            $path = $file->store('documents/' . $request->model_type, 'public');
            
            // Create base document
            $document = Document::create([
                'name' => $file->getClientOriginalName(),
                'original_name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'extension' => $file->getClientOriginalExtension(),
                'document_type' => $request->model_type,
                'uploaded_by' => auth()->id(),
            ]);

            // Create relationship based on model type
            $this->createDocumentRelationship(
                $request->model_type,
                $document->id,
                $request->foreign_key_id,
                $request->foreign_key_name,
                $request->visibility,
                $request->boolean('is_required', false)
            );

            $uploadedDocuments[] = $document;
        }

        return back()->with('success', count($uploadedDocuments) . ' document(s) uploaded successfully');
    }

    /**
     * Create document relationship based on model type.
     */
    private function createDocumentRelationship(
        string $modelType,
        int $documentId,
        int $foreignKeyId,
        string $foreignKeyName,
        string $visibility,
        bool $isRequired
    ) {
        $modelClass = match($modelType) {
            'course' => \App\Models\CourseDocument::class,
            'activity' => \App\Models\ActivityDocument::class,
            'lesson' => \App\Models\LessonDocument::class,
            'module' => \App\Models\ModuleDocument::class,
            default => null,
        };

        if ($modelClass) {
            try {
                $data = [
                    'document_id' => $documentId,
                    $foreignKeyName => $foreignKeyId,
                    'visibility' => $visibility,
                    'is_required' => $isRequired,
                ];
                
                // Add order field for models that support it
                if (in_array($modelType, ['course', 'activity', 'lesson', 'module'])) {
                    $data['order'] = 0;
                }
                
                $result = $modelClass::create($data);
                
                \Log::info('Document relationship created', [
                    'model_type' => $modelType,
                    'model_class' => $modelClass,
                    'document_id' => $documentId,
                    'foreign_key' => $foreignKeyName,
                    'foreign_key_value' => $foreignKeyId,
                    'result' => $result->toArray(),
                ]);
            } catch (\Exception $e) {
                \Log::error('Failed to create document relationship', [
                    'model_type' => $modelType,
                    'model_class' => $modelClass,
                    'error' => $e->getMessage(),
                    'data' => $data ?? [],
                ]);
                throw $e;
            }
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Document $document)
    {
        //
    }

    /**
     * Download a document.
     */
    public function download(Document $document)
    {
        // Check permissions here if needed
        
        $filePath = storage_path('app/public/' . $document->file_path);
        
        if (!file_exists($filePath)) {
            abort(404, 'File not found');
        }
        
        return response()->download($filePath, $document->original_name);
    }

    /**
     * View a document inline (for preview).
     */
    public function view(Document $document)
    {
        // Check permissions here if needed
        
        $filePath = storage_path('app/public/' . $document->file_path);
        
        if (!file_exists($filePath)) {
            abort(404, 'File not found');
        }
        
        return response()->file($filePath, [
            'Content-Type' => $document->mime_type,
            'Content-Disposition' => 'inline; filename="' . $document->original_name . '"'
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Document $document)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Document $document)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Document $document)
    {
        // Check permissions
        if (auth()->id() !== $document->uploaded_by && !auth()->user()->hasRole('admin')) {
            abort(403, 'Unauthorized');
        }
        
        $document->delete(); // Soft delete
        
        return back()->with('success', 'Document deleted successfully');
    }
}
