<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class FieldActivityReportsController extends Controller
{
    //
    public function fetchData()
    {
        $url = config('services.external_api.url');
        $username = config('services.external_api.username');
        $password = config('services.external_api.password');



        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Authorization' => ' Basic am9obi5iaXRpY29uQGVzYy1wYXJ0bmVycy5jb206RmFjZWJvb2suY29tMjAyNQ==',
        ])->withoutVerifying()->get($url);

        // Handle error case
        if ($response->failed()) {
            return Inertia::render('FieldActivity', [
                'error' => 'API request failed',
                'data' => null,
            ]);
        }

        // Return the API response data to the Inertia Dashboard view
        return Inertia::render('FieldActivity', [
            'externalData' => $response->json(),
        ]);
    }

    public function getActivities(Request $request)
    {
        $start = $request->query('fromStartDate');
        $end = $request->query('toStartDate');
        $activityType = $request->query('activity_type');

        // Example API call with filtering
        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Authorization' => config('services.external_api.token'),
        ])->withoutVerifying()->get(config('services.external_api.getActivities'), [
                    'fromStartDate' => $start,
                    'toStartDate' => $end,
                    //'activity_type' => $activityType,
                ]);

        return $response->json(); // return directly to Vue
    }
    public function getActivity(string $activityId)
    {
     // External API call using path param
    $response = Http::withHeaders([
        'Accept' => 'application/json',
        'Authorization' => config('services.external_api.token'),
    ])
    ->withoutVerifying()
    ->get(config('services.external_api.getActivity'), [
        'activityId' => $activityId,
    ]);

    return $response->json(); // Return JSON to frontend
    }

}
