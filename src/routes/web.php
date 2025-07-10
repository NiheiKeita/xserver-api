<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\WebPushController;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/test', [WebPushController::class, 'index']);
Route::post('/send-to-all', [WebPushController::class, 'sendToAll'])->withoutMiddleware(VerifyCsrfToken::class);
Route::post('/send-to-topic', [WebPushController::class, 'sendToTopic'])->withoutMiddleware(VerifyCsrfToken::class);
Route::post('/send-to-token', [WebPushController::class, 'sendToToken'])->withoutMiddleware(VerifyCsrfToken::class);
Route::post('/register-token', [WebPushController::class, 'registerToken'])->withoutMiddleware(VerifyCsrfToken::class);
