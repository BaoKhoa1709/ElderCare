<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CareGiverController;
use App\Http\Controllers\CareSeekerController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [RegisteredUserController::class, 'register']);
Route::post('/login', [LoginController::class, 'login']);
Route::post('/forgot-password', [LoginController::class, 'forgotPassword']);
Route::get('/notifications', [NotificationController::class, 'getAll'])->middleware('auth:sanctum');
Route::get('/notifications/match', [NotificationController::class, 'match'])->middleware('auth:sanctum');
Route::post('/caregivers', [CareGiverController::class, 'store'])->middleware('auth:sanctum');
Route::post('/care-seekers', [CareSeekerController::class, 'store'])->middleware('auth:sanctum');
Route::post('/caregivers/linkImage', [CareGiverController::class, 'linkImage'])->middleware('auth:sanctum');
Route::get('/caregivers/getByUid/{uid}', [CareGiverController::class, 'getByUid']);
Route::get('/caregivers/getAll', [CareGiverController::class, 'getAll']);
Route::post('/caregivers/updateCareNeedOrSkills', [CareGiverController::class, 'updateCareNeedOrSkills']);
Route::post('/caregivers/updateCertifications', [CareGiverController::class, 'updateCertifications']);
Route::post('/caregivers/updateSchedule', [CareGiverController::class, 'updateSchedule']);
Route::post('/booking', [BookingController::class, 'store'])->middleware('auth:sanctum');
Route::get('/booking/getByUid/{uid}', [BookingController::class, 'getByUid']);
Route::get('/booking/getAll', [BookingController::class, 'getAll']);
Route::post('/booking/updateStatus', [BookingController::class, 'updateStatus'])->middleware('auth:sanctum');
Route::delete('/booking/{uid}', [BookingController::class, 'delete'])->middleware('auth:sanctum');
