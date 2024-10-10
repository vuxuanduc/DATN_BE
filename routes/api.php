<?php

use App\Http\Controllers\api\Client\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\Client\BannerController;
use App\Http\Controllers\api\Client\PostController;
use App\Http\Controllers\api\Client\UserController;
use App\Http\Middleware\VerifyCsrfToken;
use PHPUnit\Framework\Attributes\Group;
use App\Http\Controllers\api\Client\TeacherController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


Route::prefix('auth')->group(function () {
    Route::post('/signup', [AuthController::class, 'signup']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/refresh-token', [AuthController::class, 'refreshToken']);
    Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
    Route::post('/resend-otp', [AuthController::class, 'resendOtp']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/verify-otp-resetpassword', [AuthController::class, 'verifyOtpForResetPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
    });
    Route::prefix('user')->group(function () {
        Route::get('/profile', [UserController::class, 'show']);
        Route::post('/profile', [UserController::class, 'updateProfile']);
        Route::post('/change-password', [UserController::class, 'changePassword']);
    });
    // Route::post('/reset-password', [AuthController::class, 'resetPassword']);
});

# ===================== ROUTE FOR BANNERS ===========================
Route::get('/banners', [BannerController::class, 'getBanners']);
# ===================== ROUTE FOR POSTS ===========================

//Lay danh sach bai viet
Route::prefix('posts')->group(function () {
    Route::get('', [PostController::class, 'getPosts']);
    Route::post('', [PostController::class, 'store']);
    Route::get('/{id}', [PostController::class, 'show']);
    Route::put('/{id}', [PostController::class, 'update']);
    Route::delete('/{id}', [PostController::class, 'destroy']);
});

// Danh sach teacher
Route::prefix('teachers')->group(function () {
    // Danh sach teacher
    Route::get('/', [TeacherController::class, 'getTeachers']);
    // Danh sách khóa học của một teacher cụ thể
    Route::get('/list-courses/{id}', [TeacherController::class, 'getCoursesIsTeacher']);
    // Tìm kiếm giảng viên;
    Route::get('/search-teacher', [TeacherController::class, 'searchTeachers']);
});


