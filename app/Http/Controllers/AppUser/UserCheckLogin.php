<?php

namespace App\Http\Controllers\AppUser;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Enhance\Log;
use App\Http\Kit\IpInfo;
use App\Http\Kit\SecretUnicode;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Kit\Secret;
use Exception;

class UserCheckLogin extends Controller {


    /*
     * 保持登录状态的安全检测，检查手机号是否可用
     * */
    public function check_user_app_login($user_app_class, $user_phone, $previous_user_phone, $group_id, $_ip, $nick_name, $avatar_url, $gender, $openid, $white_user_phone){

        if (in_array($user_app_class, ['xcx', 'h5_play'])){

            if (in_array($user_phone, $white_user_phone)){ // 用openid作为用户识别登录时

                $has_user_id = DB::table('xcx_user')
                    ->where('state', '<>', 2)
                    ->where('openid', '=', $openid)
                    ->value('xcx_user_id');

                $has_previous_user_id = DB::table('xcx_user')
                    ->where('xcx_user_phone', '=', $previous_user_phone)
                    ->value('xcx_user_id');

                if (!$has_user_id){
                    $data1 = [
                        'create_xcx_user_phone'=> $user_phone,
                        'xcx_user_phone'=> $user_phone,
                        'previous_xcx_user_id'=> $has_previous_user_id,
                        'xcx_group_id'=> $group_id,
                        'nick_name'=> $nick_name,
                        'openid'=> $openid,
                        'avatar_url'=> $avatar_url,
                        'gender'=> $gender,
                        'create_time'=> date('YmdHis'),
                        'create_ip'=> $_ip,
                    ];
                    $user_id = DB::table('xcx_user')->insertGetId($data1);
                }else{

                    if ($avatar_url && $nick_name){
                        $data2 = [
                            'update_time'=> date('YmdHis'),
                            'update_ip'=> $_ip,
                            'nick_name'=> $nick_name,
                            'openid'=> $openid,
                            'avatar_url'=> $avatar_url,
                            'gender'=> $gender,
                        ];
                    }else{
                        $data2 = [
                            'update_time'=> date('YmdHis'),
                            'update_ip'=> $_ip,
                        ];
                    }

                    DB::table('xcx_user')
                        ->where('openid', '=', $openid)
                        ->update($data2);

                    $user_id = $has_user_id;
                }

                $state = 1;
                $msg = '用openid账户查询完毕（查询即注册、登录）';

            }else{ // 用用户手机号作为用户识别时

                $has_user_id = DB::table('xcx_user')
                    ->where('state', '<>', 2)
                    ->where('xcx_user_phone', '=', $user_phone)
                    ->value('xcx_user_id');

                $has_previous_user_id = DB::table('xcx_user')
                    ->where('xcx_user_phone', '=', $previous_user_phone)
                    ->value('xcx_user_id');

                if (!$has_user_id){
                    $data1 = [
                        'create_xcx_user_phone'=> $user_phone,
                        'xcx_user_phone'=> $user_phone,
                        'previous_xcx_user_id'=> $has_previous_user_id,
                        'xcx_group_id'=> $group_id,
                        'nick_name'=> $nick_name,
                        'openid'=> $openid,
                        'avatar_url'=> $avatar_url,
                        'gender'=> $gender,
                        'create_time'=> date('YmdHis'),
                        'create_ip'=> $_ip,
                    ];
                    $user_id = DB::table('xcx_user')->insertGetId($data1);
                }else{
                    if ($avatar_url && $nick_name){
                        $data2 = [
                            'update_time'=> date('YmdHis'),
                            'update_ip'=> $_ip,
                            'nick_name'=> $nick_name,
                            'openid'=> $openid,
                            'avatar_url'=> $avatar_url,
                            'gender'=> $gender,
                        ];
                    }else{
                        $data2 = [
                            'update_time'=> date('YmdHis'),
                            'update_ip'=> $_ip,
                        ];
                    }
                    DB::table('xcx_user')
                        ->where('xcx_user_id', '=', $has_user_id)
                        ->update($data2);

                    $user_id = $has_user_id;
                }

                $state = 1;
                $msg = '用user_phone账户查询完毕（查询即注册、登录）';

            }

        }else{
            $state = 0;
            $msg = '未指明user_class，验证终止';
            $user_id = 0;
        }

        $back = [
            'state'=>$state,
            'msg'=>$msg,
            'user_id'=> $user_id,
        ];

        return $back;

    }


    // 检测token是否可用
    public function check_user_app_token($_user_phone, $user_app_token, $user_id){

        $secret = new Secret();
        $secret_u = new SecretUnicode();

        $state = 0;
        $msg = '???';
        $content = [];

        if (empty($_user_phone)){
            $user_phone = DB::table('xcx_user')
                ->where('state', '<>', 2)
                ->where('xcx_user_id', '=',$user_id)
                ->value('xcx_user_phone');
        }else{
            $user_phone = $_user_phone;
        }

        // $user_phone = $secret_u->encode_unicode($user_phone);

        try{ // 验证token格式
            $old_token = $secret->decode($user_app_token);
            $old_time = split_token($old_token)[1]*1;
            $has_user_phone = split_token($old_token)[2];
        }catch (Exception $e){
            $old_token = 0;
            $old_time = 0;
            $has_user_phone = 0;
        }

        if ($has_user_phone == $user_phone){

            $now_time = $time = get_date_millisecond();

            // 检测用户的token是否过期
            if ($now_time-$old_time <= 300*24*60*60*1000){ // 未过期

                $user_info = DB::table('xcx_user')
                    ->where('state', '<>', 2)
                    ->where('xcx_user_phone', $user_phone)
                    ->select('xcx_user_id', 'xcx_user_phone', 'nick_name', 'avatar_url', 'gender', 'xcx_group_id', 'xcx_user_token1', 'xcx_user_token2')
                    ->first();

                $user_info = json_to_array($user_info);

                if ($user_info){

                    $token_array = [
                        $user_info['xcx_user_token1'],
                        $user_info['xcx_user_token2'],
                    ];

                    for ($i=0; $i<count($token_array); $i++){

                        if ($user_app_token === $token_array[$i]){

                            $login = [
                                'login_token'=>$token_array[$i],
                                'xcx_user_id'=>$user_info['xcx_user_id'],
                                'xcx_user_phone'=>$user_info['xcx_user_phone'],
                                'nick_name'=>$user_info['nick_name'],
                                'avatar_url'=>$user_info['avatar_url'],
                                'gender'=>$user_info['gender'],
                                'xcx_group_id'=>$user_info['xcx_group_id'],
                            ];

                            $state = 1;
                            $msg = '登录状态检测通过';
                            $content = $login;

                            break;
                        }else{

                            if ($i == count($token_array)-1){
                                $state = 302;
                                $msg = '用户Token可能已经更新，请重新登录（授权）';
                                $content = [];
                            }

                        }

                    }

                }else{
                    $state = 0;
                    $msg = '用户不存在';
                    $content = [];
                }


            }else{
                $state = 0;
                $msg = '请先登录';
                $content = [];
            }

        }else{
            $state = -1;
            $msg = '数据库无此手机号，建议新建或更新手机号用户';
            $content = [$user_id];
        }

        $back = [
            'state'=>$state,
            'msg'=>$msg,
            'content'=> $content,
        ];

        return $back;

    }





}
