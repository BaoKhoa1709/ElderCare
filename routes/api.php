<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CareGiverController;

Route::post('/register', [RegisteredUserController::class, 'register']);
Route::post('/login', [LoginController::class, 'login']);
Route::post('/caregivers', [CareGiverController::class, 'store'])->middleware('auth:sanctum');
Route::post('/caregivers/linkImage', [CareGiverController::class, 'linkImage'])->middleware('auth:sanctum');
Route::get('/caregivers/getByUid/{uid}', [CareGiverController::class, 'getByUid']);
Route::get('/caregivers/getAll', [CareGiverController::class, 'getAll']);
Route::post('/caregivers/updateCareNeedOrSkills', [CareGiverController::class, 'updateCareNeedOrSkills']);
Route::post('/caregivers/updateCertifications', [CareGiverController::class, 'updateCertifications']);
Route::post('/caregivers/updateSchedule', [CareGiverController::class, 'updateSchedule']);
