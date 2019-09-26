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
use App\Http\Controllers\BlockRequest;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use App\Http\Controllers\Enhance\Log;
use App\Http\Kit\IpInfo;

class OpenController extends Controller{

    /*
     * 预先执行，安全检测
     * */
    public function __construct(Request $request){
        header('Access-Control-Allow-Origin:*');
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
            $log->write_log('OpenController debug_key', $info);

        }else{

            if (!is_post()){
                $back = [
                    'state'=> 403,
                    'msg'=> '此接口仅限POST，拒绝访问(Open)',
                    'content'=> [$ip->get_real_ip(), 'is_get()'],
                ];
                $log->write_log('OpenController !is_post()', $back);
                exit(json_encode($back, JSON_UNESCAPED_UNICODE));
            }else{
                // 其他操作

                // 缓存用户的IP
                $user_ip = $ip->get_real_ip();
                $block = new BlockRequest();
                $block->cache_user_ip($user_ip);

            }

        }

    }


}
