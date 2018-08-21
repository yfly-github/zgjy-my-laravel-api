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

Route::group(['prefix' => 'v1','namespace'=>'Api','middleware'=>['throttle:20,1']],function(){

    Route::group(['middleware'=>['auth:api']],function(){
        Route::resource('user','Usercontroller');
    });
});
Route::group(['prefix' => 'user','namespace'  => 'Api'],function(){

    /**用户调用userlogin/refresh_token路由时，X-RateLimit-Remaining 将会消耗两次，
     *namespace Laravel\Passport\RouteRegistrar类forAccessTokens()已经包含了throttle中间件，
     *此时再次指定中间件相当于throttle中间件执行了两次，故X-RateLimit-Remaining每次减少2个，
     * 解决方法去掉其一
    **/
    Route::post('userlogin','LoginController@login');

    Route::post('userregister','LoginController@register');
    Route::post('refresh_token','LoginController@refresh_token');
});








