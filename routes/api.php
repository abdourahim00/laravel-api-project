<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CoursController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\LeconsController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;


Route::controller(UserController::class)->group(function () {
    Route::post('/register', 'register');
    Route::post('/login', 'login')->name('login');
    Route::post('/regenerateOTP', 'regenerateOTP');
    Route::post('/verifyOTP', 'verifyOTP');
    Route::get('/user', 'getUser');
    Route::get('/getUserDetails/{id}', 'getUserDetails');

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

Route::middleware('auth:api')->controller(CoursController::class)->group(function () {
    Route::get('payment/success/{userId}', 'getCoursesBoughtByUser');
    Route::get('/transaction/{userId}', 'getTransactions');
});


Route::middleware('auth:api')->controller(UserController::class)->group(function () {
    Route::get('/logout', 'logout');
    Route::delete('/deleteUser/{id}', 'deleteUser');
    Route::patch('/updateUser/{id}', 'updateUser');
});

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
    Route::get('/getVideo/{id}', 'getVideoByCours');
});

Route::controller(LeconsController::class)->prefix('/lesson')->group(function () {
    Route::get('/getCountCours/{slug}', 'getCountLeconsOfCours');
    Route::post('/create', 'create');
    Route::post('/update/{id}', 'update');
    Route::delete('/delete/{id}', 'delete');
    Route::get('/', 'index');
});

Route::middleware('auth:api')->controller(OrderController::class)->prefix('/orders')->group(function () {
    Route::post('/', 'create');

});

Route::middleware('auth:api')->controller(PaymentController::class)->prefix('/payment')->group(function () {
    Route::get('/', 'index');
    Route::post('/ipn', 'handleIPN');
});

// Route::middleware('auth:api')->controller(PaymentController::class)->prefix('/payment')->group(function () {
//     Route::get('/', 'index');
//     Route::post('/ipn', 'handleIPN');
// });




