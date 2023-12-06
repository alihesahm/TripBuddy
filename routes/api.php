<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\PlaceController;
use App\Http\Controllers\ResetPasswordController;
use App\Http\Middleware\AdminRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
Route::post('login',[AuthController::class,'login']);
Route::post('register',[AuthController::class,'register']);

Route::middleware('auth:sanctum')->group(function (){
    Route::post('logout',[AuthController::class,'logout']);

    Route::controller(PlaceController::class)->prefix('place')->group(function (){
        Route::get('','index');
        Route::post('','store');
        Route::get('admin','adminIndex')->middleware(AdminRole::class);
        Route::get('{place}/get','show');
        Route::patch('{place}/edit','update');
        Route::post('{place}/image','addImage')->middleware(AdminRole::class);
        Route::delete('{place}/image/{image_id}','deleteImage')->middleware(AdminRole::class);
        Route::post('approve/{place}','approve')->middleware(AdminRole::class);
        Route::controller(\App\Http\Controllers\CategoryController::class)->group(function (){
            Route::get('category/{category}','index');

        });

    });

    Route::controller(FavoriteController::class)->prefix('favorite')->group(function (){
        Route::get('','index');
        Route::post('{place}/add','store');
        Route::delete('{place}/delete','delete');
    });

    Route::controller(AppointmentController::class)->prefix('appointment')->group(function (){
        Route::get('','index');
        Route::get('{appointment}','show');
        Route::post('','store');
        Route::delete('{appointment}','delete');

    });
});

Route::controller(ResetPasswordController::class)->group(function (){
   Route::post('send-otp','sendOtp');
   Route::post('check-otp','checkOtp');
   Route::post('reset-password','resetPassword');
});



