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

/*
 * web.php自带csrf，若想接口api对多域名或前后端彻底分离：
 * 1. 可以将相同的接口写法写在api.php里面（接口：public/index.php/api/）；
 * 2. 或者设置csrf白名单（接口：public/index.php/；
 * */

/*
 * 默认路由
 * */
Route::match(['get', 'post', 'options'], '/', function () {
    $time = date('Y-m-d');
    $route = 'n-u-l_l';
    return redirect("/route/?route=$route&help='/index.php/api/prefix_name/func_name?time=xxx'", 301);
});
Route::match(['get', 'post', 'options'], '/route', function ($txt = '路由不正确(Api Director Error )') {
    back_404($txt);
});


