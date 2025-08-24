<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Sleep;

class MockApiController extends Controller
{
    var $validationArray = [
        "Item"  => "required|string|max:255",
        "Value" => "required|numeric|min:0",
        "Moment" => "required|date",
    ];

    public function orders()
    {
        try {
            $requestData = request()->validate($this->validationArray);


            // Random delay for variable response time
            $timeout = rand(5, 30);
            \Log::info("Sleep for {timeout} seconds", ['timeout' => $timeout]);
            Sleep::for($timeout)->seconds();
            

            if(rand(1,5) === 1) { // 20% chance for failure
                \Log::info("Simulated API failure(20% chance)");
                abort(500, 'Simulated API failure');
            }

            return response()->json([
                'status' => 'success',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['status' => 0, 'message' => $e->getMessage()], 500);
        }
    }
}
