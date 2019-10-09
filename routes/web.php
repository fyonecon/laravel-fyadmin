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
    $time = date('Y-m-d');
    $route = 'null';
    return redirect("/route/?route=$route&time=$time");
});
Route::get('/route', function ($txt = '路由不正确。') {
    back_404($txt);
});

Route::get('/init', 'Enhance\InitSys@init_sys'); // 系统初始化检测，检测环境必要参数
Route::get('/common.js', 'Enhance\HtmlApi@common_js'); // 返回文件
Route::get('/ip.js', 'Enhance\HtmlApi@ip_js');




/*
 * Controller根目录
 * */
Route::group(['prefix' => 'safe'], function (){
    Route::match(['get', 'post'], '/', function () {
        $time = date('Y-m-d');
        $route = 'safe';
        return redirect("/route/?route=$route&time=$time");
    }); // 默认

    Route::match(['get', 'post'], '/open_redis', 'OpenController@open_redis');
});


/*
 * Test目录下的
 * 路由访问例子：public/index.php/test/get
 * */
Route::group(['prefix' => 'test'], function (){
    Route::match(['get', 'post'], '/', function () {
        $time = date('Y-m-d');
        $route = 'test';
        return redirect("/route/?route=$route&time=$time");
    }); // 默认

    Route::match(['get', 'post'], '/get', 'Test\Test1@get');
    Route::match(['get', 'post'], '/test1', 'Test\Test1@test1');
    Route::match(['get', 'post'], '/test', 'Test\Test2@test');
});


/*
 * Admin目录下的
 * */
Route::group(['prefix' => 'admin'], function (){
    Route::match(['get', 'post'], '/', function () {
        $time = date('Y-m-d');
        $route = 'admin';
        return redirect("/route/?route=$route&time=$time");
    }); // 默认

    Route::match(['get', 'post'], '/login', 'Admin\AdminLogin@login'); // 登录
    Route::match(['get', 'post'], '/login_check', 'Admin\AdminLogin@login_check'); // 登录状态检测
    Route::match(['get', 'post'], '/add_user', 'Admin\AdminUser@add_user');
    Route::match(['get', 'post'], '/list_user', 'Admin\AdminUser@list_user');
    Route::match(['get', 'post'], '/edit_user', 'Admin\AdminUser@edit_user');
    Route::match(['get', 'post'], '/del_user', 'Admin\AdminUser@del_user');
    Route::match(['get', 'post'], '/list_admin', 'Admin\AdminUser@list_admin');
});


/*
 * App目录下的
 * */
Route::group(['prefix' => 'app'], function (){
    Route::match(['get', 'post'], '/', function () {
        $time = date('Y-m-d');
        $route = 'app';
        return redirect("/route/?route=$route&time=$time");
    }); // 默认

    Route::match(['get', 'post'], '/get', 'App\LoginController@get');
});


/*
 * Enhance目录下的
 * */
Route::group(['prefix' => 'enhance'], function (){
    Route::match(['get', 'post'], '/', function () {
        $time = date('Y-m-d');
        $route = 'enhance';
        return redirect("/route/?route=$route&time=$time");
    }); // 默认

    Route::match(['get', 'post'], '/log', 'Enhance\Log@log'); // 写自定义日志接口，只用于服务器间的日志记录
    Route::match(['get', 'post'], '/get_that_ip', 'Enhance\IpApi@get_that_ip'); // 获取用户IP
    Route::match(['get', 'post'], '/upload_base64_file', 'Enhance\UploadFileApi@upload_base64_file'); // 上传文件base64法
    Route::match(['get', 'post'], '/upload_form_file', 'Enhance\UploadFileApi@upload_form_file'); // 上传文件form法
    Route::match(['get', 'post'], '/save_url_img', 'Enhance\UploadFileApi@save_url_img'); // 保存url地址的图片
    Route::match(['get', 'post'], '/save_base64_img', 'Enhance\UploadFileApi@save_base64_img'); // 保存base64格式的图片
});


/*
 * 其他
 * */

