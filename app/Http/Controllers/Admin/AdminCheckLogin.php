<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Kit\Secret;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Enhance\Log;
use Exception;

class AdminCheckLogin extends Controller{

    /*
     * 保持登录状态的安全检测，检查token是否可用
     * */
    public function safe_check($user_login_name, $user_login_token){

        $state = 0;
        $msg = '???';
        $content = '';

        $secret = new Secret();

        try{ // 验证token格式
            $old_token = $secret->decode($user_login_token);
            $old_time = split_token($old_token)[0]*1;
        }catch (Exception $e){

            $old_time = 0;
        }

        $now_time = time();

        // 检测用户的token是否过期
        if ($now_time-$old_time <= 6*24*60*60){ // 未过期

            $user_info = DB::table('admin_user')
                ->where('user_state', 1)
                ->where('user_login_name', $user_login_name)
                ->select('user_id', 'user_level', 'user_name', 'user_token1', 'user_token2', 'user_token3')
                ->first();

            $user_info = json_to_array($user_info);

            if ($user_info){

                $token_array = [
                    $user_info['user_token1'],
                    $user_info['user_token2'],
                    $user_info['user_token3'],
                ];

                $user_level = $user_info['user_level'];
                switch ($user_level){
                    case 0:
                        $user_level_name = '已冻结/不可用';
                        break;
                    case 1:
                        $user_level_name = '超级管理员';
                        break;
                    case 2:
                        $user_level_name = '普通管理员';
                        break;
                    case 3:
                        $user_level_name = '仅可看数据';
                        break;
                    default:
                        $user_level_name = '未知等级-'.$user_level;
                        break;
                }

                for ($i=0; $i<count($token_array); $i++){

                    if ($user_login_token === $token_array[$i]){

                        $login = [
                            'login_id'=>$user_info['user_id'],
                            'login_name'=>$user_login_name,
                            'login_token'=>$token_array[$i],
                            'login_nickname'=>$user_info['user_name'],
                            'login_level'=>$user_level,
                            'login_level_name'=> $user_level_name,
                        ];

                        $state = 1;
                        $msg = '登录状态检测通过';
                        $content = $login;

                        break;
                    }else{

                        if ($i == count($token_array)-1){
                            $state = 302;
                            $msg = '用户Token可能已经更新，请重新登录（授权）';
                            $content = '';

                        }

                    }

                }

            }else{
                $state = 0;
                $msg = '用户不存在';
                $content = '';
            }


        }else{
            $state = 0;
            $msg = '请先登录';
            $content = '';
        }

        $back = [
            'state'=>$state,
            'msg'=>$msg,
            'login_href'=>'login.php?login=must',
            'content'=>$content,
        ];

        return $back;

    }


}
