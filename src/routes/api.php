<?php

use App\Http\Controllers\Api\RepositoryController;
use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\BranchController;
use App\Http\Controllers\Api\PullRequestController;
use App\Http\Controllers\Api\CommitController;
use Illuminate\Http\Request;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// 認証が必要なAPIルート
Route::middleware(['auth:sanctum'])->group(function () {
    // リポジトリ管理
    Route::apiResource('repositories', RepositoryController::class);
    Route::post('repositories/{repository}/users', [RepositoryController::class, 'addUser']);
    Route::delete('repositories/{repository}/users/{user}', [RepositoryController::class, 'removeUser']);

    // リポジトリ内の記事管理
    Route::prefix('repositories/{repository}')->group(function () {
        Route::apiResource('articles', ArticleController::class);
        Route::get('articles/{article}/history', [ArticleController::class, 'history']);
        Route::get('articles/{article}/versions/{version}', [ArticleController::class, 'version']);

        // ブランチ管理
        Route::apiResource('branches', BranchController::class);

        // プルリクエスト管理
        Route::apiResource('pull-requests', PullRequestController::class);
        Route::patch('pull-requests/{pullRequest}/merge', [PullRequestController::class, 'merge']);
        Route::patch('pull-requests/{pullRequest}/close', [PullRequestController::class, 'close']);

        // コミット管理
        Route::apiResource('commits', CommitController::class);
    });
});

// 公開API（認証不要）
Route::get('repositories', [RepositoryController::class, 'publicIndex']);
Route::get('repositories/{repository}', [RepositoryController::class, 'publicShow']);
