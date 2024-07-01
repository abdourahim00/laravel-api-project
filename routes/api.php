<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CoursController;


// Route::post('/register', [UserController::class, 'register']);


// Route::post('/login', [UserController::class, 'login']);
// // Route::post('/login', [UserController::class, 'login']);

// // Route::post('/confirmOTP', [UserController::class, 'confirmOTP']);
// Route::post('/regenerateOTP', [UserController::class, 'regenerateOTP']);

// Route::post('/verifyOTP', [UserController::class, 'verifyOTP']);


Route::controller(UserController::class)->group(function () {
    Route::post('/register', 'register');
    Route::post('/login', 'login');
    Route::post('/regenerateOTP', 'regenerateOTP');
    Route::post('/verifyOTP', 'verifyOTP');
});

Route::middleware('auth:api')->controller(UserController::class)->group(function () {
    Route::post('/logout', 'logout');
    Route::post('/deleteUser/{id}', 'deleteUser');
    Route::post('/updateUser/{id}', 'updateUser');

});

Route::middleware('auth:api')->controller(CoursController::class)->group(function () {
    Route::post('/cours', 'create');
    Route::post('/buyCours/{id}', 'buyCours');
    Route::post('/deleteCours/{id}', 'deleteCours');
    Route::post('/updateCours/{id}', 'updateCours');
});


