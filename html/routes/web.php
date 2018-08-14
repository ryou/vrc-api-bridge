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

Route::get('/', function () {
    return view('welcome');
});

Route::post('/auth/login', 'AuthController@login');
Route::get('/auth/logout', 'AuthController@logout');

Route::get('/api/users/{name}/name', 'ApiController@getUserByName');
Route::get('/api/friends/{offline}', 'ApiController@getFriends');
Route::get('/api/worlds/{worldId}', 'ApiController@getWorldInfo');
Route::get('/api/worlds/{worldId}/{instanceId}', 'ApiController@getWorldInfoByInstanceId');
