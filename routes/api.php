<?php

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

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', function ($api) {
    $api->post('login', 'App\Http\Controllers\UserController@login');
    $api->post('register', 'App\Http\Controllers\UserController@register');
    $api->post('activate', 'App\Http\Controllers\UserController@activate');
    $api->post('send-activation', 'App\Http\Controllers\UserController@sendActivation');
    $api->get('validate-email', 'App\Http\Controllers\UserController@validateEmail');

    $api->post('password/forgot', 'App\Http\Controllers\UserController@forgotPassword');
    $api->post('password/reset', 'App\Http\Controllers\UserController@resetPassword');

    $api->group(['guard' => 'jwtauth'], function ($api) {
        $api->get('refresh-token', 'App\Http\Controllers\AuthController@refreshToken');

        $api->group(['middleware' => 'adminGuard'], function ($api) {
            $api->get('user', 'App\Http\Controllers\UserController@get');
        });

        $api->group(['middleware' => 'UserGuard'], function ($api) {
            $api->post('change-password', 'App\Http\Controllers\UserController@changePassword');
        });

        $api->group(['middleware' => 'membershipGuard'], function ($api) {
            $api->get('user/{id}', 'App\Http\Controllers\UserController@get');
            $api->patch('user/{id}', 'App\Http\Controllers\UserController@update');
        });
    });

    $api->post('user/import', 'App\Http\Controllers\UserController@import');
});