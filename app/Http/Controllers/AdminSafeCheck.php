<?php

/**
 *  https://github.com/fyonecon/laravel-fyadmin
 * 《Admin目录拦截器》
 *  Admin安全验证拦截，各控制器需要继承于此。
 * */

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Enhance\Log;
use App\Http\Controllers\BlockRequest;
use App\Http\Kit\IpInfo;
use App\Http\Kit\Secret;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Admin\CheckLogin;

class AdminSafeCheck extends Controller{

    /*
     * 预先执行，安全检测
     * */
    public function __construct(Request $request){
        header('Access-Control-Allow-Origin:*');

        $post = $request->input();
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
            $log->write_log('AdminSafeCheck debug_key', $info);

        }else{

            if (!is_post()){
                $back = [
                    'state'=> 403,
                    'msg'=> '此接口仅限POST，拒绝访问(Admin)',
                    'content'=> '',
                ];
                $log_info = $log->write_log('AdminSafeCheck !is_post', $back);
                exit(json_encode([$back, $log_info], JSON_UNESCAPED_UNICODE));
            }else{
                $app_class = $post['app_class'];
                $user_login_name = $post['login_name'];
                $user_login_token = $post['login_token'];

                $app_class_array = ['web', 'admin']; // 接口来源白名单

                if (!in_array($app_class, $app_class_array)){ // 非白名单接口来源不可访问
                    $back = [
                        'state'=> 403,
                        'msg'=> '接口来源不正确，拒绝访问(Admin)',
                        'test_data'=> [
                            $app_class,
                            $user_login_name,
                            $user_login_token,
                        ],
                        'content'=> '',
                    ];
                    $log->write_log('AdminSafeCheck 403', $back);
                    exit(json_encode($back, JSON_UNESCAPED_UNICODE));
                }else{ // 正常

                    // 校验用户信息
                    $check_login = new CheckLogin();
                    $user_safe = $check_login->safe_check($user_login_name, $user_login_token);

                    $user_safe_state = $user_safe['state'];
                    $user_safe_msg = $user_safe['msg'];
                    if ($user_safe_state == 1){

                        $block = new BlockRequest();
                        $back = $block->block_request('AdminSafeCheck', $user_login_name);

                        $block_state = $back['state'];
                        $block_msg = $back['msg'];
                        $block_content = $back['content'];

                        if ($block_state == 0){
                            $_back = [
                                'state'=> $block_state,
                                'msg'=> $block_msg,
                                'content'=> $block_content,
                            ];
                            $log->write_log('AdminSafeCheck Block_request()', $_back);
                            exit(json_encode($_back, JSON_UNESCAPED_UNICODE));
                        }else{
                            // 检测通过


                        }

                    }else {
                        // 抛出错误
                        $back = [
                            'state'=> $user_safe_state,
                            'msg'=> $user_safe_msg,
                            'content'=> $user_safe,
                        ];
                        $log->write_log('AdminSafeCheck CheckLogin', $back);
                        exit(json_encode($back, JSON_UNESCAPED_UNICODE));
                    }

                }

            }

        }

    }



}
