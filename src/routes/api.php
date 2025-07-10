<?php

use App\Http\Controllers\Api\WebPushController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Webプッシュ通知API
Route::prefix('webpush')->group(function () {
    Route::get('/test', [WebPushController::class, 'index']);
    Route::post('/register-token', [WebPushController::class, 'registerToken']);
    Route::post('/send-to-all', [WebPushController::class, 'sendToAll']);
    Route::post('/send-to-topic', [WebPushController::class, 'sendToTopic']);
    Route::post('/send-to-token', [WebPushController::class, 'sendToToken']);
});
