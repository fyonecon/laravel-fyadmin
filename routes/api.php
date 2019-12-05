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

/*
 * web.php自带csrf，若想接口api对多域名：
 * 1. 可以将相同的接口写法写在api.php里面（接口：public/index.php/api/）；
 * 2. 或者设置csrf白名单（接口：public/index.php/；
 * */

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

/*
 * 用法和
 * */

Route::group(['prefix' => 'admin'], function (){
    Route::match(['get', 'post', 'options'], '/', function () {
        $time = date('Y-m-d');
        $route = 'admin';
        return redirect("/route/?route=$route&time=$time");
    }); // 默认

    Route::match(['get', 'post', 'options'], '/login', 'Admin\AdminLogin@login'); // 登录
    Route::match(['get', 'post', 'options'], '/login_check', 'Admin\AdminLogin@login_check'); // 登录状态检测
    Route::match(['get', 'post', 'options'], '/add_user', 'Admin\AdminUser@add_user');
    Route::match(['get', 'post', 'options'], '/list_user', 'Admin\AdminUser@list_user');
    Route::match(['get', 'post', 'options'], '/edit_user', 'Admin\AdminUser@edit_user');
    Route::match(['get', 'post', 'options'], '/del_user', 'Admin\AdminUser@del_user');
    Route::match(['get', 'post', 'options'], '/list_admin', 'Admin\AdminUser@list_admin');

    


});


/*
 * App或其他公开接口目录下的
 * */
Route::group(['prefix' => 'app'], function (){
    Route::match(['get', 'post', 'options'], '/', function () {
        $time = date('Y-m-d');
        $route = 'app';
        return redirect("/route/?route=$route&time=$time");
    }); // 默认

    Route::match(['get', 'post', 'options'], '/get', 'App\LoginController@get');

    // 生成访问token
    Route::match(['get', 'post', 'options'], '/get_app_token', 'App\UserLogin@get_app_token');

    


});
