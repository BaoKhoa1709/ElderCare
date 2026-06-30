<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisteredUserController;

Route::post('/register', [RegisteredUserController::class, 'register']);
