<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomToolsController;
 
    Route::post('/tools/openai/analyze', [CustomToolsController::class, 'openAiAnalyzerApi'])
        ->name('tools.api.openai.analyze');

Route::get('/debug-check', function () {
    return response()->json(['status' => 'api.php is working']);
});
