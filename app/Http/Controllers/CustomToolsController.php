<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class CustomToolsController extends Controller
{
    public function toolsDashboard()
    {
        return Inertia::render('Tools');
    }

    public function neptuneFileAnalyzer()
    {   
        
        return Inertia::render('NeptuneFileAnalyzerLayout');
    }

    public function jsonFileReader()
    {
        return Inertia::render('JsonFileReaderLayout');
    }

    public function jsonComparator()
    {
        return Inertia::render('JsonComparatorLayout');
    }

    public function intervalGenerator()
    {
           return Inertia::render('IntervalGeneratorLayout');
    }

    public function openAiAnalyzer()
    {
        return Inertia::render('OpenAiAnalyzerLayout');
    }

public function openAiAnalyzerApi(Request $request)
{
    $validated = $request->validate([
        'json1' => 'required|array',
        'json2' => 'required|array',
    ]);

    $json1 = json_encode($validated['json1'], JSON_PRETTY_PRINT);
    $json2 = json_encode($validated['json2'], JSON_PRETTY_PRINT);

    $prompt = <<<EOT
Compare the following two JSON datasets and highlight any meaningful differences. Focus on differences in values, missing keys, or structural changes.

JSON 1:
$json1

JSON 2:
$json2
EOT;

    try {
        $response = $this->callOpenAi($prompt, 'gpt-4');

        if (!$response->ok()) {
            $fallback = $this->callOpenAi($prompt, 'gpt-3.5-turbo');
            if ($fallback->ok()) {
                return response()->json($fallback->json());
            }

            $errorBody = $fallback->json();
            $message = $errorBody['error']['message'] ?? 'Unknown error from OpenAI.';

            return response()->json([
                'message' => 'Failed to get response from OpenAI.',
                'errorMessage' => $message,
            ], 500);
        }

        return response()->json($response->json());

    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Error communicating with OpenAI.',
            'errorMessage' => $e->getMessage(),
        ], 500);
    }
}


    /**
     * Sends prompt to OpenAI with specified model
     */
    private function callOpenAi(string $prompt, string $model)
    {
        return Http::withoutVerifying()->withHeaders([
            'Authorization' => 'Bearer ' . config('services.openai.key'),
            'Content-Type' => 'application/json',
        ])->post('https://api.openai.com/v1/chat/completions', [
            'model' => $model,
            'messages' => [
                ['role' => 'user', 'content' => $prompt],
            ],
            'temperature' => 0.7,
            'max_tokens' => 1000,
        ]);
    }
}
