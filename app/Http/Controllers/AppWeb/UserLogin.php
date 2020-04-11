<?php

/*
 * 不需要用户账号密码登录
 * */

namespace App\Http\Controllers\AppWeb;

use App\Http\Controllers\Controller;
use App\Http\Controllers\LoginSafeCheck;
use App\Http\Controllers\Enhance\Log;
use App\Http\Kit\IpInfo;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Kit\Secret;
use Exception;

class UserLogin extends LoginSafeCheck {

    public function __construct(Request $request){
        parent::__construct($request);


    }

    // 生成页面token
    public function get_app_token(Request $request){

        $secret = new Secret();
        $ip_info = new IpInfo();

        $app_class = $request->input('app_class'); // must
        $url = $request->input('url'); // must

        $app_class = filter_article($app_class);
        $url = filter_article($url);

        $app_url = get_url(); // 校验是否是app端在访问
        if (empty($app_url)){
            $app_url = 'app-api-??';
        }

        $page_cookie = $request->input('page_cookie'); // maybe

        //$time = date('YmdHis');
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
                $ip_back = 'exception';
            }
        }
        $ip = ip2long($_ip);

        $client_info = $request->input('client_info');
        $client_info = filter_article($client_info);
        if (!$client_info){
            $_os = $ip_info->os_info();
            $_browser_info = $ip_info->browser_info();
            $_lang_info = $ip_info->lang_info();
            $client_info = $_os.'#@'.$_browser_info.'#@'.$_lang_info.'#@'.$_ip;
        }

        $_token = $ip.'#&'.$time.'#&'.get_rand_string(rand(4, 9)).'#&'.date('md');
        $user_token = $secret->encode($_token);

        // 限制访问过多次数的IP
        // 规则：每个IP > 1次/200ms
        $old_time = Db::table('app_token')
            ->where('ip', '=', $_ip)
            ->orderBy('create_time', 'desc')
            ->value('create_time');
        $old_time = $old_time*1;
        $new_time = $time*1;
        $stop = $new_time - $old_time;
        if ( $stop >= 100){ // ms

            $white_ip = ['127.0.0.1', 'localhost'];

            if (in_array($_ip, $white_ip)){ // 跳过保存白名单域名
                $res = 1;
            }else{
                $data = [
                    'app_class'=> $app_class,
                    'ip'=> $_ip,
                    'url'=> $url,
                    'app_url'=> $app_url,
                    'create_time'=> $time,
                    'page_cookie'=> $page_cookie,
                    'user_token'=> $user_token,
                    'client_info'=> $client_info,
                ];
                // 保存token
                $res = Db::table('app_token')->insertGetId($data);
            }

            // 清除过期token
            $day = 36*30; // 默认保存36个月，单位：+day
            $end_time = date("Y-m-d", strtotime("-$day day"));
            // $end_time = to_time($end_time);
            $end_time = to_time($end_time).'000';
            // 删除老数据
            $del_timeout_token = Db::table('app_token')
                ->where('create_time', '<', $end_time)
                ->delete();

            if (!$app_class || !$url){
                $state = 0;
                $msg = '参数不全，Token获取失败';
                $content = ['user_token'=> '', 'create_time'=> time(), 'dir'=> $res, 'del'=>$del_timeout_token, 'stop'=> $stop,'ip_back'=> $ip_back];
            }else{

                /*$state = 1;
                $msg = 'Token生成完成';
                $content = ['user_token'=> $user_token, 'create_time'=> time(), 'dir'=> $res, 'del'=>$del_timeout_token, 'stop'=> $stop,'ip_back'=> string_to_array($ip_back)];*/

                // 查询已拉黑IP
                $has_ip_state = DB::table('app_token')
                    ->where('ip', '=', $_ip)
                    ->orderBy('create_time', 'asc')
                    ->value('state');

                if ($has_ip_state == 3){ // 拉黑

                    $state = 0;
                    $msg = '恶意IP已拉黑。';
                    $content = ['user_token'=> '', 'create_time'=> '', 'dir'=> '', 'del'=>'', 'stop'=> '', 'ip_back'=> ''];

                    $change_ip_state = DB::table('app_token')
                        ->where('ip', '=', $_ip)
                        ->update(['state', 3]);

                }else{

                    // 设置自动拉黑IP功能
                    $_ip_back = string_to_array($ip_back);

                    if (isset($_ip_back['content']['pv']) && isset($_ip_back['content']['state'])){
                        $that_ip_has_pv = $_ip_back['content']['pv'];
                        $that_ip_has_state = $_ip_back['content']['state'];
                    }else{
                        $that_ip_has_pv = 1;
                        $that_ip_has_state = 1;
                    }

                    if ($that_ip_has_state == 10){ // 排除白名单IP
                        $that_ip_has_pv = 1;
                    }

                    if ($that_ip_has_pv > 6000 || $that_ip_has_pv == 99 || $that_ip_has_pv == 153 || $that_ip_has_pv == 177 || $that_ip_has_pv == 202 || $that_ip_has_pv == 270 || $that_ip_has_pv == 280 || $that_ip_has_pv == 298 || $that_ip_has_pv == 312 || $that_ip_has_pv == 999 || $that_ip_has_pv == 1000){

                        $state = 0;
                        $msg = '该IP访问次数过多，已经被屏蔽。';
                        $content = ['user_token'=> '', 'create_time'=> '', 'dir'=> '', 'del'=>$del_timeout_token, 'stop'=> '', 'ip_back'=> ''];

                    }else{
                        $state = 1;
                        $msg = 'Token生成完成';
                        $content = ['user_token'=> $user_token, 'create_time'=> time(), 'dir'=> $res, 'del'=>$del_timeout_token, 'stop'=> $stop,'ip_back'=> $ip_back];
                    }

                }

            }

        }else{
            $state = 0;
            $msg = '请求频率太快，稍后再试';
            $content = ['stop'=>$stop,'ip_back'=> $ip_back];
        }

        $back = [
            'state'=>$state,
            'encode'=> 'utf-8',
            'msg'=>$msg,
            'content'=>$content,
        ];

        return array_to_json($back);
    }


}
