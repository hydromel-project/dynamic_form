<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FormController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\ResponseController;
use App\Http\Controllers\SupervisorController;
use App\Http\Controllers\AuthController;

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

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::apiResource('forms', FormController::class);

    Route::apiResource('forms.questions', QuestionController::class)->shallow();

    Route::post('/responses/start', [ResponseController::class, 'start']);
    Route::post('/responses/{session_token}/save', [ResponseController::class, 'save']);
    Route::post('/responses/{session_token}/submit', [ResponseController::class, 'submit']);
    Route::get('/responses/{session_token}', [ResponseController::class, 'show']);

    Route::prefix('supervisor')->group(function () {
        Route::get('/responses', [SupervisorController::class, 'index']);
        Route::get('/responses/{id}', [SupervisorController::class, 'show']);
        Route::get('/export', [SupervisorController::class, 'export']);
    });
});
