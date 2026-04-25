<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ChildController;
use App\Http\Controllers\Api\TherapistInvitationController;
use App\Http\Controllers\Api\TherapistPatientController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login',    [AuthController::class, 'login']);
    Route::post('password/forgot', [AuthController::class, 'forgotPassword']);
    Route::post('password/reset',  [AuthController::class, 'resetPassword']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me',      [AuthController::class, 'me']);
    });
});

Route::middleware('auth:sanctum')->group(function () {

    // Enfants - parent
    Route::apiResource('children', ChildController::class);
    Route::post('children/{child}/therapist', [TherapistInvitationController::class, 'attach']);
    Route::delete('children/{child}/therapist/{therapist}', [TherapistInvitationController::class, 'detach']);

    Route::prefix('therapist')->group(function () {
        Route::get('patients', [TherapistPatientController::class, 'index']);
        Route::get('patients/{child}', [TherapistPatientController::class, 'show']);
        Route::post('invitation-code', [TherapistInvitationController::class, 'regenerate']);
    });
});
