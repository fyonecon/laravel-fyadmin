<?php

/*
 * 需要用户账号密码登录，才能访问接口
 * */

namespace App\Http\Controllers\AppUser;

use App\Http\Controllers\Controller;
use App\Http\Controllers\LoginSafeCheck;
use App\Http\Controllers\Enhance\Log;
use App\Http\Kit\IpInfo;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Kit\Secret;
use App\Http\Kit\SecretUnicode;
use Exception;

class UserLogin extends LoginSafeCheck {

    public function __construct(Request $request){
        parent::__construct($request);


    }


    // 小程序审核加壳处理，返回处于的审核状态
    // $app_class = 'kaoyanbeikaozhushou-v1'; // 区别在哪个版本当中
    //

    public function micro_app_api_v(Request $request){
        $app_class_v = $request->input('app_class_v');

        if ($app_class_v == 'kaoyanbeikaozhushou-v1_0'){
            $state = 1;
            $msg = '当前处于s核状态，只展示s核数据';
        }else{
            $state = 0;
            $msg = 'app_class_v参数未能完成匹配';
        }

        $back = [
            'state'=> $state,
            'msg'=> $msg,
            'help'=> '[state=1, s核已过，生产版；state=0，正在s核；state=2，w规被封；state=3，开发板]',
            'content'=> ['app_class_v'=> $app_class_v],
        ];

        return array_to_json($back);
    }


    // 小程序【用户登录、注册账户】
    // 验证完手机号，返回user_app_login_token
    public function micro_app_login(Request $request){

        $secret = new Secret();
        $ip_info = new IpInfo();
        $secret_u = new SecretUnicode();

        // must
        // 接口访问的种类
        $app_class = $request->input('app_class');
        // 用户登录手机号
        $user_phone = $request->input('user_phone');

        // maybe
        // 父级用户手机号
        $previous_user_phone = $request->input('previous_user_phone');
        // 用户分组
        $group_id = $request->input('group_id');
        // 用户昵称
        $nick_name = $request->input('nick_name');
        // 特殊情况下需要使用openid登录的
        $openid = $request->input('openid');
        $appid = $request->input('appid'); // 传过来的appid需要使用md5加密，然后后端再md5加密一次
        $openid = md5($appid).'@@'.$openid;
        // 用户头像
        $avatar_url = $request->input('avatar_url');
        // 用户性别
        $gender = $request->input('gender');

        if (!$nick_name){
            $nick_name = '用户'.substr($user_phone, 5, 8);
        }

        // 处理用户IP
        $time = get_date_millisecond();
        $ip_back = 'init';
        $_ip = $request->input('real_ip'); // 一般不以前端传过来的IP为准
        if (!$_ip){ // 直接接口来获取IP
            $_ip = $ip_info->get_real_ip();
            // 查询IP归属地
            try {
                $local_server_ip = config_log()['local_server_ip']; // 本地/此服务器
                $api = 'http://'.$local_server_ip.'/'.main_filename().'/public/index.php/api/enhance/ip_city';
                $array = [
                    'ip'=> $_ip,
                ];
                $ip_back = request_post($api, $array);
            }catch (Exception $exception){
                $ip_back = 'exception-ip_city';
            }
        }
        $ip = ip2long($_ip);

        // 检测app_class
        if ($app_class){

            // 检测是不是手机号
            $white_user_phone = [ // 白名单手机号
                '18511112222',
                '18812345678',
            ];
            if (in_array($user_phone, $white_user_phone)){
                $ok_phone = 1;
            }else{
                $ok_phone = check_phone($user_phone)[0];
            }
            if ($ok_phone){

                // 检测是否存在该手机号用户，返回用户id
                $check_login = new UserCheckLogin();
                $check = $check_login->check_user_app_login('xcx', $user_phone, $previous_user_phone, $group_id, $_ip, $nick_name, $avatar_url, $gender, $openid, $white_user_phone);
                $has_user_id = $check['user_id'];

                // 生成token
                if ($has_user_id){
                    // $_token = $ip.'#&'.$time.'#&'.get_rand_string(rand(5, 7)).'#&'.$secret_u->encode_unicode($user_phone);
                    $_token = $ip.'#&'.$time.'#&'.$user_phone.'#&'.rand(10, 99);
                    $user_app_token = $secret->encode($_token);

                    // 保存或更新用户token
                    $user_info = DB::table('xcx_user')
                        ->where('state', '<>', 2)
                        ->where('xcx_user_phone', '=', $user_phone)
                        ->select('xcx_user_id', 'xcx_user_phone', 'nick_name', 'avatar_url', 'gender', 'xcx_group_id', 'xcx_user_token1', 'xcx_user_token2')
                        ->first();
                    $user_info = json_to_array($user_info);
                    if ($user_info['xcx_user_token1'] && !$user_info['xcx_user_token2']){
                        $data3 = [
                            'xcx_user_token2'=> $user_app_token,
                        ];
                    }else if (!$user_info['xcx_user_token1'] && $user_info['xcx_user_token2']){
                        $data3 = [
                            'xcx_user_token1'=> $user_app_token,
                        ];
                    }else if($user_info['xcx_user_token1'] && $user_info['xcx_user_token2']){
                        $data3 = [
                            'xcx_user_token1'=> $user_app_token,
                            'xcx_user_token2'=> '',
                        ]; // 第三者登录会挤下已登录的两个
                    }else{
                        $data3 = [
                            'xcx_user_token1'=> $user_app_token,
                        ];
                    }

                    $update_token = DB::table('xcx_user')
                        ->where('state', '<>', 2)
                        ->where('xcx_user_phone', '=', $user_phone)
                        ->update($data3);

                    if ($update_token){
                        $state = 1;
                        $msg = 'UserAppToken生成完成';
                        $content = ['user_app_token'=> $user_app_token, 'user_id'=> $has_user_id, 'xcx_user_phone'=> $user_phone, 'create_time'=> time(), 'ip_back'=> $ip_back];
                    }else{
                        $state = 0;
                        $msg = 'UserAppToken更新失败';
                        $content = ['user_app_token'=> '', 'user_id'=> $has_user_id, 'xcx_user_phone'=> $user_phone, 'create_time'=> time(), 'ip_back'=> $ip_back];
                    }

                }else{
                    $state = 0;
                    $msg = '系统出现错误（不能生成Token）';
                    $content = ['user_app_token'=> '', 'user_id'=> $has_user_id, 'xcx_user_phone'=> $user_phone, 'create_time'=> time(), 'ip_back'=> $ip_back];
                }

            }else{
                $state = 0;
                $msg = '手机号格式不正确（仅限大陆手机号）';
                $content = ['user_app_token'=> '', 'user_id'=> '', 'create_time'=> time(), 'ip_back'=> $ip_back, 'xcx_user_phone'=> $user_phone];
            }

        }else{
            $state = 0;
            $msg = '非法的app_class';
            $content = ['user_app_token'=> '', 'user_id'=> '', 'create_time'=> time(), 'ip_back'=> $ip_back, 'app_class'=> $app_class];
        }


        $back = [
            'state'=>$state,
            'encode'=> 'utf-8',
            'msg'=>$msg,
            'content'=>$content,
        ];

        return array_to_json($back);
    }


    // 软件（iOS、Android）用户登录【用户登录、注册账户】
    public function soft_app_login(){

        if (!is_post()){
            $back = [
                'state'=> 403,
                'msg'=> 'User_App_Token接口仅限POST（GET、OPTIONS等请求方法由于安全自检的原因而不允许使用），拒绝访问(UserAppLogin)',
                'content'=> '',
            ];
            exit(json_encode($back, JSON_UNESCAPED_UNICODE));
        }

        $secret = new Secret();
        $ip_info = new IpInfo();
        $secret_u = new SecretUnicode();

        // 待

    }





}
