<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\ReviewController;

Route::middleware('throttle:5,1')->group(function () {
    Route::post('/signup', 'App\Http\Controllers\AuthController@register');
    Route::post('/signin', 'App\Http\Controllers\AuthController@login');
});

Route::middleware(['auth:sanctum', 'throttle:5,1'])->group(function () {
    Route::post('/createReview', 'App\Http\Controllers\ReviewController@store');
    Route::get('/me', 'App\Http\Controllers\AuthController@me');
    Route::post('/refresh', 'App\Http\Controllers\AuthController@refresh');
    Route::post('/signout', 'App\Http\Controllers\AuthController@logout');
});
