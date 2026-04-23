<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\ReviewController;
use App\Http\Middleware\EnsureUserIsAdmin;

Route::middleware('throttle:5,1')->group(function () {
    Route::post('/signup', 'App\Http\Controllers\AuthController@register');
    Route::post('/signin', 'App\Http\Controllers\AuthController@login');
});

Route::middleware(['auth:sanctum', 'throttle:5,1'])->group(function () {
    Route::get('/me', 'App\Http\Controllers\AuthController@me');
    Route::post('/refresh', 'App\Http\Controllers\AuthController@refresh');
    Route::post('/signout', 'App\Http\Controllers\AuthController@logout');
});

Route::middleware(['auth:sanctum', EnsureUserIsAdmin::class, 'throttle:10,1'])->group(function () {
    Route::post('equipment', 'App\Http\Controllers\EquipmentController@store');
    Route::put('equipment/{id}', 'App\Http\Controllers\EquipmentController@update');
    Route::delete('equipment/{id}', 'App\Http\Controllers\EquipmentController@destroy');
});
