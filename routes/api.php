<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');


// ppublic

Route::post('/v1/register', [AuthController::class, 'register']);
Route::post('/v1/login', [AuthController::class, 'login']);

// protected
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('/v1/user', [AuthController::class, 'user']);
    Route::post('/v1/logout', [AuthController::class, 'logout']);

    // Courses
    Route::get('/courses', [CourseController::class, 'index']);
    Route::post('/courses', [CourseController::class, 'store']);
    Route::get('/courses/{id}', [CourseController::class, 'show']);
    Route::put('/courses/{id}', [CourseController::class, 'update']); // update course

    Route::put('/courses/{id}/enroll', [CourseController::class, 'enroll']); // update course
    Route::put('/courses/{id}/unenroll', [CourseController::class, 'unenroll']); // update course
});
