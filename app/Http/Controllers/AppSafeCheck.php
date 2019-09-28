<?php

/**
 * https://github.com/fyonecon/laravel-fyadmin
 * 《App目录拦截器》
 *  App安全验证拦截，各控制器需要继承于此。
 * */

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\BlockRequest;
use App\Http\Kit\Secret;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class AppSafeCheck extends Controller{

    /*
     * 预先执行，安全检测
     * */
    protected function __construct(Request $request){
        header('Access-Control-Allow-Origin:*');

        $debug_key = $request->input('debug_key');
        if ($debug_key == debug_key()){ // 跳过检测


        }else{

            if (!is_post()){
                $back = [
                    'state'=> 403,
                    'msg'=> '此接口仅限POST，拒绝访问(App)',
                    'content'=> '',
                ];
                exit(json_encode($back, JSON_UNESCAPED_UNICODE));
            }else{

                $app_version = $request->input('app_version');
                $app_version = $app_version*1;
                $app_class = $request->input('app_class');

                $user_login_id = $request->input('user_login_id');
                $user_login_token = $request->input('user_login_token');

                $app_class_array = ['ios', 'android', 'wx', 'web']; // 接口来源白名单

                if ($app_version < 1.0){ // 低版本不可操作接口
                    $back = [
                        'state'=> 301,
                        'msg'=> '请升级App(App)',
                        'test_data'=> [
                            $app_version,
                            $app_class,
                            $user_login_id,
                            $user_login_token,
                        ],
                        'content'=> [
                            'apk'=> 'xxx.com/apk-v1.0.apk',
                            'ipa'=> 'xx.com/ios',
                        ],
                    ];
                    exit(json_encode($back, JSON_UNESCAPED_UNICODE));
                }else if (!in_array($app_class, $app_class_array)){ // 非白名单接口来源不可访问
                    $back = [
                        'state'=> 403,
                        'msg'=> '接口来源不正确，拒绝访问(App)',
                        'test_data'=> [
                            $app_version,
                            $app_class,
                            $user_login_id,
                            $user_login_token,
                        ],
                        'content'=> '',
                    ];
                    exit(json_encode($back, JSON_UNESCAPED_UNICODE));
                }else{ // 正常

                    // 校验用户信息
                    //$user_safe = $this->check_user_token($user_login_id, $user_login_token);

                }

            }

        }


    }

}

