<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'DashboardController@index')->name('home');

Auth::routes();

Route::group(['prefix' => 'dashboard'], function () {
    Route::get('/', 'DashboardController@index');
    Route::get('exportExcel', 'DashboardController@exportExcel');
    Route::get('exportCSV', 'DashboardController@exportCSV');
});

