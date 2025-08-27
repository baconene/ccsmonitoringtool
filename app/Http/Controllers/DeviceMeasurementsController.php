<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class DeviceMeasurementsController extends Controller
{
    public function show(Request $request, $id)
    {
        $url      = config('services.external_api.getDeviceMeasurements');
        $username = config('services.external_api.username');
        $password = config('services.external_api.password');

        // Validate required config values
        if (!$url || !$username || !$password) {
            return response()->json([
                'status'  => 'error',
                'message' => 'API configuration missing. Please check config/services.php and your .env file.'
            ], 500);
        }

        // Build payload with defaults if query params are not provided
        $payload = [
            "input" => [
                "deviceConfigId" => $id,
                "startPeriod"    => $request->query('startPeriod', '2025-07-01'),
                "endPeriod"      => $request->query('endPeriod', '2025-07-30'),
                "frequency"      => $request->query('frequency', 'D'),
            ]
        ];

        try {
            // Make POST request with Basic Auth
           $response = Http::withBasicAuth($username, $password)
    ->withoutVerifying() // 🔥 skips SSL verification
    ->post($url, $payload);

            if ($response->successful()) {
                return response()->json([
                    'status' => 'success',
                    'data'   => $response->json()
                ]);
            }

            return response()->json([
                'status'  => 'error',
                'message' => $response->body()
            ], $response->status());

        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
