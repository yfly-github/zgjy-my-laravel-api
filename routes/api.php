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

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::group(['prefix' => 'v1','namespace'=>'api','middleware'=>['throttle:20,1']],function(){

    Route::group(['middleware'=>['auth:api']],function(){
        Route::resource('user','Usercontroller');
    });
});
Route::group(['prefix' => 'user','namespace'  => 'api'],function(){
    Route::post('userlogin','LoginController@login');
    Route::post('userregister','LoginController@register');
    Route::post('refresh_token','LoginController@refresh_token');
});








