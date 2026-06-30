<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\LoginController;

Route::post('/register', [RegisteredUserController::class, 'register']);
Route::post('/login', [LoginController::class, 'login']);
