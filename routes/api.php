<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

Route::prefix('v1')->group(function () {
    Route::post('order', [OrderController::class, 'create']);
});

Route::post('slow-api-test', function (Request $request) {
  $response = Http::withoutVerifying()->get('https://very-slow-api.test/orders');
  return response()->json(
    [
      'status' => 'success',
      'response' => $response->json(),
    ]
  );
});

Route::prefix('mock')->group(function () {
    Route::get('/orders', function () {
        return response()->json([
            'message' => 'This is a very slow API response.',
            'status' => 'success',
        ]);
    });
});
