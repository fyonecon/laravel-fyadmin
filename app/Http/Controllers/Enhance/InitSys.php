<?php

/**
 *  系统参数初始化
 * */

namespace App\Http\Controllers\Enhance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class InitSys Extends Controller{

    protected function init_sys(){

        $test = [
            '七牛云接口'=> 'http://localhost/laravel60/public/index.php/enhance/save_url_img?img_url=https://www.ggvs.cn/2019-09-09_11-44-02_5d75ca82be144.jpeg&upload_token=test2019&debug_key=29092019',
            'admin登录接口'=> 'http://localhost/laravel60/public/index.php/admin/login?debug_api_method=20190924',
            '生成js接口'=> 'http://localhost/laravel60/public/index.php/common.js?type=js',
        ];


        return array_to_json($test);
    }



}
