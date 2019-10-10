<?php
/*
 * 请求次数超额的接口。
 * 与Kernel.php中的throttle不同。
 * */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use App\Http\Controllers\Enhance\Log;
use App\Http\Kit\IpInfo;


class BlockRequest extends Controller{

    /*
     * 总的接口访问次数计数
     * */
    public function block_request($prefix, $_key){

        $key = $prefix;
        $user_key = $key.'##'.$_key;
        $user_value = 'null';

        if(Redis::exists($user_key)){ // 存在则查
            $request_num = Redis::get($user_key);
            $request_num = $request_num+1;
        }else{ // 不存在则初试
            $request_num = 1;
        }
        Redis::set($user_key, $request_num);

        $user_value = $user_key.'##'.$request_num;


        $back = [
            'state'=> 1,
            'msg'=> '完成缓存',
            'content'=> [$user_value],
        ];

        return $back;

    }

    /*
     * 缓存用户Ip，并累积IP访问次数
     * */
    public function cache_user_ip($user_ip){
        $key = 'user_ip';
        $user_key = $key.'##'.$user_ip;

        $user_value = 'null';

        if(Redis::exists($user_key)){ // 存在则查
            $request_num = Redis::get($user_key);
            $request_num = $request_num+1;
        }else{ // 不存在则初试
            $request_num = 1;
        }
        Redis::set($user_key, $request_num);
        $user_value = $user_key.'##'.$request_num;


        return $user_value;
    }





}
