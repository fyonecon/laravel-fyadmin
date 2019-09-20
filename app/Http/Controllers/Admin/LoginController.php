<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Kit\Secret;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\OpenSafeCheck;
use Exception;

class LoginController extends OpenSafeCheck {


    public function get(Request $request){
        $user = $request->input("user"); // 这里接收参数比thinkphp里面接收参数的input()要提前生命参数
        $name = $request->input("name");

        $msg = "get yes";
        $back = [
            "state"=>1,
            "msg"=>$msg,
            "name"=>$name,
            "user"=>$user,
            'test'=>test_common('text_func')
        ];
        return json_encode($back, JSON_UNESCAPED_UNICODE); // js接收TP返回来的是string，而Lvl返回object。
    }


    /*
     * 校验用户
     * */
    public function check_user_token($user_id, $user_token){

        $secret = new Secret();

        try{ // token格式错误时
            $old_token = $secret->decode($user_token);
            $old_time = split_token($old_token)[0]*1;
        }catch (Exception $e){

            $old_time = 0;
        }
        $now_time = time();

        $state = 0;
        $msg = '';
        $content = '';

        // 检测用户的token是否过期
        if ($now_time-$old_time <= 6*24*60*60){ // 未过期

            $user_info = Db::table('admin_user')
                ->where('user_state', '=', 1)
                ->where('user_id', '=', $user_id)
                ->select('user_id, user_level, user_login_name, user_name, user_token1, user_token2, user_token3')
                ->first();

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
                        $user_level_name = '普通管理员/用户';
                        break;
                    case 3:
                        $user_level_name = '仅可查看数据';
                        break;
                    default:
                        $user_level_name = '未知';
                        break;
                }

                for ($i=0; $i<count($token_array); $i++){

                    if ($user_token === $token_array[$i]){

                        $login = [
                            'login_id'=>$user_info['user_id'],
                            'login_token'=>$token_array[$i],
                            'user_name'=>$user_info['user_name']
                        ];

                        $state = 1;
                        $msg = '登录状态检测通过';
                        $content = $login;

                        break;
                    }else{

                        if ($i == count($token_array)-1){
                            $state = 2;
                            $msg = '用户Token可能已经更新，请重新登录';
                            $content = '';
                        }

                    }

                }

            }else{

                $state = 0;
                $msg = '用户不存在或者已删除用户';
                $content = '';
                $user_level = '不可用';
                $user_level_name = '';
            }

        }else{
            $state = 2;
            $msg = '请先登录..';
            $content = '';
            $user_level = '未知';
            $user_level_name = '';
        }

        $back = [
            'state'=>$state,
            'msg'=>$msg,
            'user_level'=> $user_level,
            'user_level_name'=> $user_level_name,
            'content'=>$content,
        ];

        return $back;

    }





}
