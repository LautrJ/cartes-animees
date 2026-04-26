<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ChildController;
use App\Http\Controllers\Api\ChildSeriesController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\SeriesController;
use App\Http\Controllers\Api\StripeWebhookController;
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

    Route::apiResource('children', ChildController::class);
    Route::prefix('children')->group(function () {
        // Enfants - parent
        Route::post('{child}/therapist', [TherapistInvitationController::class, 'attach']);
        Route::delete('{child}/therapist/{therapist}', [TherapistInvitationController::class, 'detach']);

        // Séries d'un enfant — parent
        Route::get('{child}/series', [SeriesController::class, 'index']);
        Route::get('{child}/series/{series}', [SeriesController::class, 'show']);
    });


    // Orthophoniste
    Route::prefix('therapist')->group(function () {
        Route::get('patients', [TherapistPatientController::class, 'index']);
        Route::get('patients/{child}', [TherapistPatientController::class, 'show']);
        Route::post('invitation-code', [TherapistInvitationController::class, 'regenerate']);

        // Gestion des séries des patients
        Route::post('patients/{child}/series/{series}', [ChildSeriesController::class, 'store']);
        Route::patch('patients/{child}/series/{series}', [ChildSeriesController::class, 'update']);
        Route::delete('patients/{child}/series/{series}', [ChildSeriesController::class, 'destroy']);
    });

    // Notifications
    Route::get('notifications', [NotificationController::class, 'index']);
    Route::patch('notifications/read-all', [NotificationController::class, 'markAllAsRead']);
    Route::patch('notifications/{id}', [NotificationController::class, 'markAsRead']);

    // Profil
    Route::get('profile', [ProfileController::class, 'show']);
    Route::put('profile', [ProfileController::class, 'update']);
    Route::patch('profile/password', [ProfileController::class, 'updatePassword']);
});

Route::post('stripe/webhook', [StripeWebhookController::class, 'handle']);
