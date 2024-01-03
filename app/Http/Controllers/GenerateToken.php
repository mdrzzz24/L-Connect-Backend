<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class GenerateToken extends Controller
{
    //
    public function forwardData(Request $request)
    {
        // Validate the incoming request data if necessary

        // Get the data from the request
        $requestData = $request->all();

        // Forward the data to the specified endpoint
        $response = Http::post('103.233.100.50:55001/api/kedaireka/get-token', $requestData);

        // Process the response if needed
        $responseData = $response->json();

        // Return the response
        return response()->json($responseData);
    }
}
