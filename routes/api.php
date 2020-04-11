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
 * web.php自带csrf，若想接口api对多域名或前后端彻底分离：
 * 1. 可以将相同的接口写法写在api.php里面（接口：public/index.php/api/（prefix/接口名））；
 * 2. 或者设置csrf白名单（接口：public/index.php/；
 * */


Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});



/*
 * Test目录下，不需要token，不需要域名拦截
 * */
Route::group(['prefix' => 'test'], function (){
    Route::match(['get', 'post', 'options'], '/', function () {
        $time = date('Y-m-d');
        $route = 'test';
        return redirect("/route/?route=$route&time=$time", 301);
    }); // 默认

    Route::match(['get', 'post', 'options'], '/test2_check_ip_city', 'Test\Test2@test2_check_ip_city'); // 获取用户IP

    Route::match(['get', 'post', 'options'], '/test2_database2', 'Test\Test2@test2_database2');

});



/*
 * Enhance目录下，不需要token验证，只做域名拦截
 * */
Route::group(['prefix' => 'enhance'], function (){
    Route::match(['get', 'post', 'options'], '/', function () {
        $time = date('Y-m-d');
        $route = 'enhance';
        return redirect("/route/?route=$route&time=$time", 301);
    }); // 默认

    Route::match(['get', 'post', 'options'], '/log', 'Enhance\Log@log'); // 写自定义日志接口，只用于服务器间的日志记录
    Route::match(['get', 'post', 'options'], '/get_that_ip', 'Enhance\IpApi@get_that_ip'); // 获取用户IP
    Route::match(['get', 'post', 'options'], '/ip_city', 'Enhance\IpApi@ip_city'); // 查询IP归属地
    Route::match(['get', 'post', 'options'], '/upload_base64_file', 'Enhance\UploadFileApi@upload_base64_file'); // 上传文件base64法
    Route::match(['get', 'post', 'options'], '/upload_form_file', 'Enhance\UploadFileApi@upload_form_file'); // 上传文件form法
    Route::match(['get', 'post', 'options'], '/save_url_img', 'Enhance\UploadFileApi@save_url_img'); // 保存url地址的图片
    Route::match(['get', 'post', 'options'], '/save_base64_img', 'Enhance\UploadFileApi@save_base64_img'); // 保存base64格式的图片

    Route::match(['get', 'post', 'options'], '/get_wx_share_data', 'Enhance\WxShare@get_wx_share_data'); // 微信分享

    Route::match(['get', 'post', 'options'], '/send_code_sms', 'Enhance\SmsApi@send_code_sms');
    Route::match(['get', 'post', 'options'], '/send_notice_sms', 'Enhance\SmsApi@send_notice_sms');

    Route::match(['get', 'post', 'options'], '/this_page_wx_share.js', 'Enhance\HtmlApi@this_page_wx_share');

    Route::match(['get', 'post', 'options'], '/get_referrer.js', 'Enhance\HtmlApi@get_referrer');

    // 统计
    Route::match(['get', 'post', 'options'], '/save_referrer', 'Enhance\Tongji@save_referrer');

    // web端更新订单
    Route::match(['get', 'post', 'options'], '/create_pay_order', 'Enhance\WebPayOrder@create_pay_order');
    Route::match(['get', 'post', 'options'], '/update_pay_order', 'Enhance\WebPayOrder@update_pay_order');
    Route::match(['get', 'post', 'options'], '/status_pay_order', 'Enhance\WebPayOrder@status_pay_order');

    Route::match(['get', 'post', 'options'], '/save_admin_ip', 'Enhance\Tongji@save_admin_ip');

});


/*
 * admin后台管理系统，不可用于用户端
 * */
Route::group(['prefix' => 'admin'], function (){
    Route::match(['get', 'post', 'options'], '/', function () {
        $time = date('Y-m-d');
        $route = 'admin';
        return redirect("/route/?route=$route&time=$time", 301);
    }); // 默认

    Route::match(['get', 'post', 'options'], '/login', 'Admin\AdminLogin@login'); // 登录
    Route::match(['get', 'post', 'options'], '/login_check', 'Admin\AdminLogin@login_check');
    Route::match(['get', 'post', 'options'], '/all_user_layout', 'Admin\AdminLogin@all_user_layout');

    Route::match(['get', 'post', 'options'], '/add_user', 'Admin\AdminUser@add_user');
    Route::match(['get', 'post', 'options'], '/list_user', 'Admin\AdminUser@list_user');
    Route::match(['get', 'post', 'options'], '/edit_user', 'Admin\AdminUser@edit_user');
    Route::match(['get', 'post', 'options'], '/del_user', 'Admin\AdminUser@del_user');
    Route::match(['get', 'post', 'options'], '/list_admin', 'Admin\AdminUser@list_admin');


});


/*
 * AppWeb或其他公开接口目录下的，只能用于用户端
 * */
Route::group(['prefix' => 'app_web'], function (){
    Route::match(['get', 'post', 'options'], '/', function () {
        $time = date('Y-m-d');
        $route = 'app';
        return redirect("/route/?route=$route&time=$time", 301);
    }); // 默认

    // 生成访问token
    Route::match(['get', 'post', 'options'], '/get_app_token', 'AppWeb\UserLogin@get_app_token');


});



/*
 * AppUser或其他需要用户登录的接口目录下的，只能用于小程序、iOS、Android用户端
 * */
Route::group(['prefix' => 'app_user'], function (){
    Route::match(['get', 'post', 'options'], '/', function () {
        $time = date('Y-m-d');
        $route = 'app';
        return redirect("/route/?route=$route&time=$time", 301);
    }); // 默认

    // 小程序各版本加壳
    Route::match(['get', 'post', 'options'], '/micro_app_api_v', 'AppUser\UserLogin@micro_app_api_v');

    // 生成访问token，用户注册和登录
    Route::match(['get', 'post', 'options'], '/micro_app_login', 'AppUser\UserLogin@micro_app_login');

    Route::match(['get', 'post', 'options'], '/get_user_info', 'AppUser\UserInfo@get_user_info');

});



