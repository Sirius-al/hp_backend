<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/queue', function () {
    Artisan::call('queue:work');
});


Route::get('/clear-cache', function() { $exitCode = Artisan::call('cache:clear');  return "Cache is cleared"; });
Route::get('/config-cache', function() { $exitCode = Artisan::call('config:cache');  return "Cache is cleared"; });
Route::get('/route-cache', function() { $exitCode = Artisan::call('route:cache');  return "Cache is cleared"; });

Route::get('/clear-sdsd', function() { $exitCode = Artisan::call('route:clear');  return "Cache is cleared"; });



