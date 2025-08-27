<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request; 
use Inertia\Inertia;
use Inertia\Response;

class ExternalApiController extends Controller
{
    public function fetchData()
    {
        $url = config('services.external_api.url');
        $username = config('services.external_api.username');
        $password = config('services.external_api.password');

       

    $response = Http::withHeaders([
        'Accept' => 'application/json',
        'Authorization' => config('services.external_api.token'),
             ]) ->withoutVerifying()->get($url);

        // Handle error case
        if ($response->failed()) {
            return Inertia::render('Dashboard', [
                'error' => 'API request failed',
                'data' => null,
            ]);
        }

         // Return the API response data to the Inertia Dashboard view
        return Inertia::render('Dashboard', [
            'externalData' => $response->json(),
        ]);
    }
}

