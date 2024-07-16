<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CoursController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\LeconsController;


Route::controller(UserController::class)->group(function () {
    Route::post('/register', 'register');
    Route::post('/login', 'login');
    Route::post('/regenerateOTP', 'regenerateOTP');
    Route::post('/verifyOTP', 'verifyOTP');
});


Route::controller(CoursController::class)->group(function () {
    Route::post('/cours', 'create');
    Route::delete('/deleteCours/{slug}', 'deleteCours');
    Route::get('/listCours', 'index');
    Route::post('/updateCours/{slug}', 'updateCours');
    Route::get('/coursesRecommended/{id}', 'coursesRecommended');
    Route::get('/getDetails/{slug}', 'getDetails');
    // Route::get('/getDetailCours/{id}', 'getDetailCours');
    // Route::get('/getCoursByCategory/{id}', 'getCoursByCategory');
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
    Route::post('/updateCategory/{slug}', 'update');
    Route::delete('/deleteCategory/{slug}', 'delete');
    Route::get('/category', 'index');
    Route::get('/getCountLessonsOfCategory/{slug}', 'getCountLessonsOfCategory');
});

Route::controller(VideoController::class)->prefix('/video')->group(function () {
    Route::post('/create', 'create');
    Route::post('/update/{slug}', 'updateVideo');
    Route::delete('/delete/{slug}', 'deleteVideo');
    Route::get('/', 'index');
});

Route::controller(LeconsController::class)->prefix('/lesson')->group(function () {
    Route::get('/getCountCours/{slug}', 'getCountLeconsOfCours');
    Route::post('/create', 'create');
    Route::post('/update/{id}', 'update');
    Route::delete('/delete/{id}', 'delete');
    Route::get('/', 'index');
});

