<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['prefix' => 'auth'], function () {
    Route::post('signup', 'AuthController@signup');
    Route::post('login', 'AuthController@login');
    Route::post('logout', 'AuthController@logout');
    Route::post('updateBike', 'AuthController@updateBike');
    Route::post('updateName', 'AuthController@updateName');
    Route::post('updatePassword', 'AuthController@updatePassword');
    Route::post('updateEmail', 'AuthController@updateEmail');
});

Route::group(['prefix' => 'password'], function () {    
    Route::post('create', 'PasswordResetController@create');
    Route::post('find', 'PasswordResetController@find');
    Route::post('reset', 'PasswordResetController@reset');
});

Route::group(['prefix' => 'rides'], function () {
    Route::post('get', 'RideController@get');
    Route::post('create', 'RideController@create');
    Route::post('update', 'RideController@update');
});
