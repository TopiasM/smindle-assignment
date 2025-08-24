<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use App\Http\Middleware\IdempotencyMiddleware;
use App\Http\Controllers\MockApiController;

Route::prefix('v1')->group(function () {
    Route::post('order', [OrderController::class, 'create'])
      ->middleware(IdempotencyMiddleware::class);
});

Route::post('slow-api-test', function (Request $request) {
  $response = Http::withoutVerifying()->post('https://very-slow-api.test/orders', [
    'Item' => $request->input('Item', 'Test Item'),
    'Value' => $request->input('Value', 100),
    'Moment' => $request->input('Moment', now()->toDateTimeString()),
  ]);
  return response()->json(
    [
      'status' => 'success',
      'response' => $response->json(),
    ]
  );
});

Route::prefix('mock')->group(function () {
    Route::post('/orders', [MockApiController::class, 'orders']);
});
