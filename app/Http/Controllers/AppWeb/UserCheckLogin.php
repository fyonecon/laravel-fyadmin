<?php

namespace App\Http\Controllers\AppWeb;

use App\Http\Controllers\Controller;
use App\Http\Kit\Secret;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Exception;

class UserCheckLogin extends Controller{

    // 检查验证token
    public function check_app_token($user_token){
        $secret = new Secret();

        try{ // 验证token格式
            $string_token_info = $secret->decode($user_token);
            $token_info = split_token($string_token_info);
        }catch (Exception $exception){
            $token_info = [];
        }

        if (!$token_info){
            $state = 0;
            $msg = '非法token';
        }else{

            try{
                $that_ip = $token_info[0];
                $that_time = $token_info[1];
                if (strlen($that_time)<= 14){
                    $that_time = $that_time.'000';
                }
                // $that_time = time_to($that_time);
                $ip = long2ip($that_ip);

                //$now_time = time();
                $now_time = get_date_millisecond();

                // 检测用户的token是否过期
                if ($now_time-$that_time <= 12*60*60*1000) { // 未过期
                    $state = 1;
                    $msg = 'token正常';
                }else{ // 过期
                    $state = 0;
                    $msg = "token过期：$now_time-$that_time"; // "token过期：20200117151120624-20200117151120"
                }

            }catch (Exception $exception){
                $state = 0;
                $msg = 'token不正确['.$user_token.']，无法完成接口请求';
            }

        }

        $back = [
            'state'=> $state,
            'msg'=> $msg,
        ];

        return $back;
    }

}
