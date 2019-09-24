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
use App\Http\Controllers\Play\Log;
use App\Http\Kit\IpInfo;

class OpenController extends Controller{

    /*
     * 预先执行，安全检测
     * */
    public function __construct(Request $request){
        header('Access-Control-Allow-Origin:*');
        $method = $request->input('debug_api_method');
        // 记录调试日志
        $log = new Log();
        $ip = new IpInfo();
        if ($method == debug_api_method()){ // 跳过检测
            $info = [
                'debug_api_method'=> debug_api_method(),
                'method_input'=> $method,
                'ip'=> $ip->get_user_ip(),
            ];
            $log->write_log('debug_api_method', $info);
        }else{

            if (!is_post()){
                $back = [
                    'state'=> 403,
                    'msg'=> '此接口仅限POST，拒绝访问(Admin)',
                    'content'=> [$ip->get_user_ip(), 'is_get()'],
                ];
                $log->write_log('debug_api_method', $back);
                exit(json_encode($back, JSON_UNESCAPED_UNICODE));
            }else{
                // 其他操作

            }

        }

    }

    /*
     * 将IP加入Redis-list，以便统计
     * */



}
