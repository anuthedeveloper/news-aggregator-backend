<?php

use App\Http\Controllers\ArticleController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

use App\Http\Controllers\UserPreferenceController;
 
// Route::group([
//     'middleware' => 'api'
// ], function ($router) {
//     Route::post('/register', [AuthController::class, 'register'])->name('register');
//     Route::post('/login', [AuthController::class, 'login'])->name('login');
//     Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:api')->name('logout');
//     Route::post('/refresh', [AuthController::class, 'refresh'])->middleware('auth:api')->name('refresh');
//     Route::post('/me', [AuthController::class, 'me'])->middleware('auth:api')->name('me');
// });

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/articles/search', [ArticleController::class, 'searchArticles']);

Route::middleware('auth:api')->group(function() {
    Route::get('/user', [AuthController::class, 'user']);

    Route::get('/articles', [ArticleController::class, 'index']);
    
    Route::get('/preferences', [UserPreferenceController::class, 'getPreferences']);
    Route::post('/preferences', [UserPreferenceController::class, 'savePreferences']);

    Route::post('/logout', [AuthController::class, 'logout']);
});
