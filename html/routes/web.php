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

Route::post('/auth/login', 'AuthController@login');
Route::get('/auth/logout', 'AuthController@logout');

Route::get('/api/1/users/{name}/name', 'ApiController@getUserByName');
Route::get('/api/1/auth/user/friends', 'ApiController@getFriends');
Route::get('/api/1/worlds/{worldId}', 'ApiController@getWorldInfo');
Route::get('/api/1/worlds/{worldId}/{instanceId}', 'ApiController@getWorldInfoByInstanceId');
Route::get('/api/1/favorites', 'ApiController@getFavorites');
