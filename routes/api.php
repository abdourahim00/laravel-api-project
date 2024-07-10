<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CoursController;
use App\Http\Controllers\CategoryController;

Route::controller(UserController::class)->group(function () {
    Route::post('/register', 'register');
    Route::post('/login', 'login');
    Route::post('/regenerateOTP', 'regenerateOTP');
    Route::post('/verifyOTP', 'verifyOTP');
});


Route::controller(CoursController::class)->group(function () {
    Route::post('/cours', 'create');
    Route::delete('/deleteCours/{id}', 'deleteCours');
    Route::get('/getCours', 'getCours');
    Route::post('/updateCours/{id}', 'updateCours');
    Route::get('/getDetailCours/{id}', 'getDetailCours');
    Route::get('/getCoursByCategory/{id}', 'getCoursByCategory');
});


Route::middleware('auth:api')->controller(UserController::class)->group(function () {
    Route::post('/logout', 'logout');
    Route::delete('/deleteUser/{id}', 'deleteUser');
    Route::patch('/updateUser/{id}', 'updateUser');
});

// Route::middleware('auth:api')->controller(CoursController::class)->group(function () {
//     Route::post('/cours', 'create');
//     Route::post('/buyCours/{id}', 'buyCours');
//     Route::delete('/deleteCours/{id}', 'deleteCours');
//     Route::post('/updateCours/{id}', 'updateCours');
// });

Route::controller(CategoryController::class)->group(function () {
    Route::post('/createCategory', 'create');
    Route::post('/updateCategory/{id}', 'update');
    Route::delete('/deleteCategory/{id}', 'delete');
    Route::get('/category', 'index');
    Route::get('/getCountCoursOfCategory/{id}', 'getCountCoursOfCategory');
});


