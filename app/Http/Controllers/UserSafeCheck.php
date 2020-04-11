<?php

/**
 *  https://github.com/fyonecon/laravel-fyadmin
 * 《不做接口数据校验的拦截器》
 * 不做用户Token验证的接口拦截（如用户登录、文章浏览等）需继承于此：
 * 1. 拦截请求频次；
 * 2. 拦截请求域名（不做拦截，任意域）；
 * 3. 记录请求IP+区域；
 * */

namespace App\Http\Controllers;

use App\Http\Kit\Secret;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use App\Http\Controllers\Enhance\Log;
use App\Http\Kit\IpInfo;
use App\Http\Controllers\AppUser\UserCheckLogin;

class UserSafeCheck extends Controller{

    /*
     * 预先执行，安全检测
     * 继承该class需要运行__construct
     * */
    protected function __construct(Request $request){

        $debug_key = $request->input('debug_key');
        // 记录调试日志
        $log = new Log();
        $ip = new IpInfo();
        if ($debug_key == debug_key()){ // 跳过检测
            $info = [
                'debug_key'=> debug_key(),
                'debug_key_input'=> $debug_key,
                'ip'=> $ip->get_real_ip(),
            ];
            $log->write_log('WebSafeCheck debug_key', $info);

        }else{

            if (is_post() || is_options()){

                // 安全校验
                $app_version = $request->input('app_version');
                $user_id = $request->input('user_id');
                $user_phone = $request->input('user_phone');
                $user_app_token = $request->input('user_app_token');

                $white_token_array = ['tpl@request@app_user_token', 'safe_uSeR20200328']; // 白名单token

                if (in_array($user_app_token, $white_token_array)){
                    // 白名单token验证通过

                }else{

                    $user_login = new UserCheckLogin();
                    $check_app_token = $user_login->check_user_app_token($user_phone, $user_app_token, $user_id);

                    $state = $check_app_token['state'];
                    $msg = $check_app_token['msg'];

                    if ($state == 1){
                        // token验证通过


                    }else if ($state == 0){
                        // 验证不通过
                        $back = [
                            'state'=> 0,
                            'encode'=> 'utf-8',
                            'msg'=> $msg,
                            'content'=> '',
                        ];
                        exit(json_encode($back, JSON_UNESCAPED_UNICODE));
                    }else{
                        $back = [
                            'state'=> 403,
                            'encode'=> 'utf-8',
                            'msg'=> $msg,
                            'content'=> '',
                        ];
                        exit(json_encode($back, JSON_UNESCAPED_UNICODE));
                    }

                }

            }else{
                $back = [
                    'state'=> 403,
                    'encode'=> 'utf-8',
                    'msg'=> '此接口仅限POST或OPTIONS，拒绝访问(UserSafe)',
                    'content'=> [$ip->get_real_ip(), is_post()],
                ];
                exit(json_encode($back, JSON_UNESCAPED_UNICODE));
            }


        }

    }


}
