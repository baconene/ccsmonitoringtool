<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

class InvoiceCloudController extends Controller
{
    // -----------------------------
    // Simple example: Check Biller Status
    // -----------------------------
    public function getCustomerInfo()
    {
        // Example hardcoded Customer GUID (not used in this method but kept for reference)
        $guiId = "6769ecfe-7814-4f9f-988b-2ab69cb39107";

        // InvoiceCloud endpoint to check biller API status
        $url = "https://www.invoicecloud.com/api/v1/biller/status";

        // API key provided by InvoiceCloud (from biller portal)
        $generatedApiKey = "Rxau5PG40QGYgqb0B6Xo/IE4ikF9UC6WP04s+6jbJ/UaSdanqJotYrwfR4i0qR50nntFfx8LohHlAUmDlTGeeq/INnq68p2A/BGK39+lqGc=";

        // Convert API key to Base64 (though withBasicAuth does this automatically)
        $encodedApiKey = base64_encode($generatedApiKey);

        // Make GET request to InvoiceCloud API
        $response = Http::withBasicAuth($generatedApiKey, "") // API key as username, blank password
            ->withoutVerifying()   // ⚠️ disable SSL verification for local dev (not recommended in prod)
            ->acceptJson()         // Expect JSON response
            ->get($url);

        // Return JSON response to frontend
        return $response->json();
    }

    // -----------------------------
    // Retrieve Customer GUID and then fetch account info
    // -----------------------------
    public function retrieveCustomerGuid()
    {
        try {
            // Step 1: Call InvoiceCloud API to retrieve a Customer GUID
            $urlRetrieve = "https://www.invoicecloud.com/api/v1/customer/RetrieveCustomerGuid";

            // Example payload to send in POST body
            $payload = [
                "AccountNumber" => "0073943371",          // Example account number
                "EmailAddress"  => "sshimala23@esc-testing.com", // Example email
                "Username"      => ""                     // Optional username, left blank
            ];

            // API key provided by InvoiceCloud
            $generatedApiKey = "Rxau5PG40QGYgqb0B6Xo/IE4ikF9UC6WP04s+6jbJ/UaSdanqJotYrwfR4i0qR50nntFfx8LohHlAUmDlTGeeq/INnq68p2A/BGK39+lqGc=";

            // Make POST request to get the Customer GUID
            $responseGuid = Http::withBasicAuth($generatedApiKey, "")
                ->withoutVerifying()   // ⚠️ disable SSL verification for local dev
                ->acceptJson()
                ->post($urlRetrieve, $payload);

            // Handle error if first request fails
            if (!$responseGuid->successful()) {
                return response()->json([
                    'error'  => 'Failed to fetch CustomerGuid',
                    'status' => $responseGuid->status(),
                    'body'   => $responseGuid->body(),
                ], $responseGuid->status());
            }

            // Extract CustomerGuid from the response
            $customerGuid = $responseGuid->json()['CustomerGuid'] ?? null;

            // If CustomerGuid is missing, return error
            if (!$customerGuid) {
                return response()->json([
                    'error' => 'CustomerGuid not found in response',
                    'body'  => $responseGuid->json(),
                ], 422);
            }
            return $customerGuid;
         }      catch (\Exception $e) {
            // Catch unexpected exceptions and return error
            return response()->json([
                'error'   => 'Exception occurred',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    
    public function retrieveAccountInfo()
    {
        $customerGuid = $this->retrieveCustomerGuid();
    try{
       if (!$customerGuid) {
            return response()->json([
                'error' => 'Failed to retrieve CustomerGuid',
            ], 422);
        }
  $generatedApiKey = "Rxau5PG40QGYgqb0B6Xo/IE4ikF9UC6WP04s+6jbJ/UaSdanqJotYrwfR4i0qR50nntFfx8LohHlAUmDlTGeeq/INnq68p2A/BGK39+lqGc=";

        // Step 2: Use the CustomerGuid to call another API and fetch account info
        $urlAccount = "https://www.invoicecloud.com/api/v1/cp/{$customerGuid}/account";

            $responseAccount = Http::withBasicAuth($generatedApiKey, "")
                ->withoutVerifying()   // ⚠️ disable SSL verification for local dev
                ->acceptJson()
                ->get($urlAccount);

            // Return both CustomerGuid and account info if successful
            if ($responseAccount->successful()) {
                return response()->json([
                    'CustomerGuid' => $customerGuid,
                    'Account'      => $responseAccount->json(),
                ], 200);
            }

            // Handle error if second request fails
            return response()->json([
                'error'  => 'Failed to fetch account info',
                'status' => $responseAccount->status(),
                'body'   => $responseAccount->body(),
            ], $responseAccount->status());

        } catch (\Exception $e) {
            // Catch unexpected exceptions and return error
            return response()->json([
                'error'   => 'Exception occurred',
                'message' => $e->getMessage(),
            ], 500);
        }
    }


    public function updateCustomerRecord(Request $request)
    {
        try {
            // Endpoint for SOAP/ASMX service
            $url = "https://www.invoicecloud.com/portal/webservices/CloudManagement.asmx/UpdateCustomerRecord";

            // Build payload (you can replace hardcoded values with $request->input())
            $payload = [
                "BillerGUID"          => "6769ecfe-7814-4f9f-988b-2ab69cb39107",
                "WebServiceKey"       => "a4a9583f-f13c-4ab2-9529-f66cbbb11fa3",
                "AccountNumber"       => "0073943371",
                "CustomerName"        => "sshimala Bacon Test",
                "CustomerName2"       => "sshimala Bacon Test 2",
                "Address1"            => "Bacon Address",
                "Address2"            => "",
                "City"                => "Carmel",
                "State"               => "CA",
                "Zip"                 => "4603",
                "Phone"               => "2195085008",
                "EmailAddress"        => "sshimala23@esc-testing.com",
                "EmailAddressCC"      => "sshimala23@esc-testing.com",
                "CustomerVATNumber"   => "",
                "RegistrationValue"   => "",
                "PIN"                 => "",
                "BlockPaymentsCC"     => "false",
                "BlockPaymentsEFT"    => "false",
            ];

            // Send POST request as form-urlencoded
            $response = Http::asForm()
                ->withoutVerifying() // ⚠️ remove this in production
                ->post($url, $payload);

            // Return raw XML instead of JSON
            return response($response->body(), $response->status())
                ->header('Content-Type', 'text/xml; charset=utf-8');

        } catch (\Exception $e) {
            return response()->make(
                '<?xml version="1.0" encoding="utf-8"?><error>' . htmlspecialchars($e->getMessage()) . '</error>',
                500,
                ['Content-Type' => 'text/xml; charset=utf-8']
            );
        }
    }
}
