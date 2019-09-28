<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\OpenController;
use App\Http\Controllers\Admin\CheckLogin;
use App\Http\Kit\Secret;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Exception;

class AdminLogin extends OpenController {

    public function __construct(Request $request){
        parent::__construct($request);


    }


    /*
     * 用户登录
     * */
    public function login(Request $request){

        $user_login_name = $request->input('login_name');
        $user_login_pwd = pwd_encode($request->input('login_pwd'));

        $test_data = [
            $user_login_name,
            $user_login_pwd,
        ];

        $user_info = DB::table('admin_user')
            ->where('user_state', '=', 1)
            ->where('user_login_name', '=', $user_login_name)
            ->where('user_login_pwd', '=', $user_login_pwd)
            ->select('user_id', 'user_level', 'user_name', 'user_token1', 'user_token2', 'user_token3')
            ->first();
        $user_info = json_to_array($user_info);

        if ($user_info){

            $token_array = [
                $user_info['user_token1'],
                $user_info['user_token2'],
                $user_info['user_token3'],
            ];
            $key = ['user_token1', 'user_token2', 'user_token3'];

            // 生成token
            $new_token = make_token();
            // 对称加密token
            $secret = new Secret();
            //$secret = new Secret();
            $new_token = $secret->encode($new_token);

            // 替换不符合条件的token
            for ($i=0; $i<count($token_array); $i++){

                // 为空则添加
                if (empty($token_array[$i])){

                    // 保存token
                    $data = [
                        $key[$i]=>$new_token,
                    ];
                    $save_token = DB::table('admin_user')->where('user_login_name', $user_login_name)->update($data);

                    break; // 找到空位置则直接跳出
                }else{ // 过期则替换

                    try{ // token格式错误时
                        $old_token = $secret->decode($token_array[$i]);
                        $old_time = split_token($old_token)[0]*1;
                    }catch (Exception $e){

                        $old_time = 0;
                    }
                    $now_time = time();

                    if ($now_time-$old_time > 6*24*60*60){ // token过期

                        // 保存token
                        $data = [
                            $key[$i]=>$new_token,
                        ];
                        $save_token = DB::table('admin_user')->where('user_login_name', $user_login_name)->update($data);

                        break;
                    }else{

                        if ($i == count($token_array)-1){

                            // 保存token
                            $data = [
                                $key[rand(0, (count($token_array)-1))]=>$new_token,
                            ];
                            $save_token = DB::table('admin_user')->where('user_login_name', $user_login_name)->update($data);

                        }

                    }

                }

            }


            $login = [
                'login_id'=>$user_info['user_id'],
                'login_name'=>$user_login_name,
                'login_token'=>$new_token,
                'jump_url'=> 'home.php?nav=home',
            ];

            $state = 1;
            $msg = '登录成功';
            $content = $login;

        }else{
            $state = 0;
            $msg = '账号或者密码错误';
            $content = '';
        }

        $back = [
            'state'=>$state,
            'msg'=>$msg,
            'test_data'=>$test_data,
            'content'=>$content,
        ];

        return array_to_json($back);

    }


    /*
     * 保持登录状态/登录状态检测
     * */
    public function login_check(Request $request){

        $user_login_name = $request->input('login_name');
        $user_login_token = $request->input('login_token');

        if ($user_login_token || $user_login_name){
            $check_login = new CheckLogin();
            $back = $check_login->safe_check($user_login_name, $user_login_token);
        }else {

            $state = 0;
            $msg = '请先登录..';
            $content = [$user_login_name, $user_login_token];

            $back = [
                'state'=>$state,
                'msg'=>$msg,
                'login_href'=>'login.php?login=must',
                'content'=>$content,
            ];
        }

        return array_to_json($back);

    }





    /*
     * 用户所有登录下线
     * */
    public function all_user_layout(Request $request){
        $user_id = $request->input('login_id');

        $data = [
            'user_token1'=>'',
            'user_token2'=>'',
            'user_token3'=>'',
        ];

        $res = DB::table('admin_user')->where('user_id', $user_id)->update($data);

        if ($res){
            $state = 1;
            $msg = '该用户所有Token已经清除';
            $content = $res;
        }else{
            $state = 0;
            $msg = '该用户所有Token清除失败';
            $content = [$user_id, $res];
        }

        $back = [
            'state'=>$state,
            'msg'=>$msg,
            'content'=>$content,
        ];

        return array_to_json($back);

    }



}
