<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/register', [AuthController::class, 'register']);
Route::get('/verify-email/{token}', [AuthController::class, 'verifyEmail'])->name('verify.email');
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/update', [AuthController::class, 'updateProfile']);
    Route::delete('/delete', [AuthController::class, 'deleteAccount']);
    Route::post('/logout', [AuthController::class, 'logout']);
});