<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('email/verify/{id}', 'VerificationApiController@verify')->name('verificationapi.verify');
Route::get('email/resend', 'VerificationApiController@resend')->name('verificationapi.resend');

Route::post('register', 'Api\AuthController@register');
Route::post('login', 'Api\AuthController@login');

Route::group(['middleware' => 'auth:api'], function () {
    Route::get('logout', 'Api\AuthController@logout');
    Route::get('details', 'Api\AuthController@details');
    Route::post('update', 'Api\AuthController@update');
    Route::post('uploadImg', 'Api\AuthController@uploadImg');

    Route::get('room', 'Api\RoomController@index');
    Route::get('room/{id}', 'Api\RoomController@show');
    Route::post('room', 'Api\RoomController@store');
    Route::put('room/{id}', 'Api\RoomController@update');
    Route::delete('room/{id}', 'Api\RoomController@destroy');

    Route::get('reservation', 'Api\ReservationController@index');
    Route::get('reservation/{id}', 'Api\ReservationController@show');
    Route::get('reservation/user/{id}', 'Api\ReservationController@showByUserId');
    Route::post('reservation', 'Api\ReservationController@store');
    Route::put('reservation/{id}', 'Api\ReservationController@update');
    Route::delete('reservation/{id}', 'Api\ReservationController@destroy');
});
