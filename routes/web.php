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
 * web.php自带csrf，若想接口api对多域名：
 * 1. 可以将相同的接口写法写在api.php里面（接口：public/index.php/api/）；
 * 2. 或者设置csrf白名单（接口：public/index.php/；
 * */

/*
 * 默认路由
 * */
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
    Route::match(['get', 'post', 'options'], '/', function () {
        $time = date('Y-m-d');
        $route = 'safe';
        return redirect("/route/?route=$route&time=$time");
    }); // 默认

    Route::match(['get', 'post', 'options'], '/open_redis', 'OpenController@open_redis');
});


/*
 * Test目录下的
 * 路由访问例子：public/index.php/test/get
 * */
Route::group(['prefix' => 'test'], function (){
    Route::match(['get', 'post', 'options'], '/', function () {
        $time = date('Y-m-d');
        $route = 'test';
        return redirect("/route/?route=$route&time=$time");
    }); // 默认

    Route::match(['get', 'post', 'options'], '/get', 'Test\Test1@get');
    Route::match(['get', 'post', 'options'], '/test', 'Test\Test1@test');
    Route::match(['get', 'post', 'options'], '/test1', 'Test\Test1@test1');
    Route::match(['get', 'post', 'options'], '/test2', 'Test\Test1@test2');
    Route::match(['get', 'post', 'options'], '/test3', 'Test\Test1@test3');
    Route::match(['get', 'post', 'options'], '/test4', 'Test\Test1@test4');
    Route::match(['get', 'post', 'options'], '/test5', 'Test\Test1@test5');
});


/*
 * Admin目录下的
 * */
//Route::group(['prefix' => 'admin'], function (){
//    Route::match(['get', 'post', 'options'], '/', function () {
//        $time = date('Y-m-d');
//        $route = 'admin';
//        return redirect("/route/?route=$route&time=$time");
//    }); // 默认
//
//    Route::match(['get', 'post', 'options'], '/login', 'Admin\AdminLogin@login'); // 登录
//    Route::match(['get', 'post', 'options'], '/login_check', 'Admin\AdminLogin@login_check'); // 登录状态检测
//    Route::match(['get', 'post', 'options'], '/add_user', 'Admin\AdminUser@add_user');
//    Route::match(['get', 'post', 'options'], '/list_user', 'Admin\AdminUser@list_user');
//    Route::match(['get', 'post', 'options'], '/edit_user', 'Admin\AdminUser@edit_user');
//    Route::match(['get', 'post', 'options'], '/del_user', 'Admin\AdminUser@del_user');
//    Route::match(['get', 'post', 'options'], '/list_admin', 'Admin\AdminUser@list_admin');
//
//    Route::match(['get', 'post', 'options'], '/add_teacher', 'Admin\TeacherStaff@add_teacher');
//    Route::match(['get', 'post', 'options'], '/edit_teacher', 'Admin\TeacherStaff@edit_teacher');
//    Route::match(['get', 'post', 'options'], '/del_teacher', 'Admin\TeacherStaff@del_teacher');
//    Route::match(['get', 'post', 'options'], '/list_teacher', 'Admin\TeacherStaff@list_teacher');
//
//});


/*
 * Enhance目录下的
 * */
Route::group(['prefix' => 'enhance'], function (){
    Route::match(['get', 'post', 'options'], '/', function () {
        $time = date('Y-m-d');
        $route = 'enhance';
        return redirect("/route/?route=$route&time=$time");
    }); // 默认

    Route::match(['get', 'post', 'options'], '/log', 'Enhance\Log@log'); // 写自定义日志接口，只用于服务器间的日志记录
    Route::match(['get', 'post', 'options'], '/get_that_ip', 'Enhance\IpApi@get_that_ip'); // 获取用户IP
    Route::match(['get', 'post', 'options'], '/upload_base64_file', 'Enhance\UploadFileApi@upload_base64_file'); // 上传文件base64法
    Route::match(['get', 'post', 'options'], '/upload_form_file', 'Enhance\UploadFileApi@upload_form_file'); // 上传文件form法
    Route::match(['get', 'post', 'options'], '/save_url_img', 'Enhance\UploadFileApi@save_url_img'); // 保存url地址的图片
    Route::match(['get', 'post', 'options'], '/save_base64_img', 'Enhance\UploadFileApi@save_base64_img'); // 保存base64格式的图片
    Route::match(['get', 'post', 'options'], '/wx_web_share', 'Enhance\WxWebApi@wx_web_share'); // 微信网页分享
});


/*
 * 其他
 * */

