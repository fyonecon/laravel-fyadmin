<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Enhance\Log;
use App\Http\Kit\IpInfo;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Kit\Secret;
use Exception;

class UserLogin extends Controller {

    // 生成页面token
    public function get_app_token(Request $request){
        header('Access-Control-Allow-Origin:*');

        if (!is_post()){
            $back = [
                'state'=> 403,
                'msg'=> '此接口仅限POST，拒绝访问(UserLogin)',
                'content'=> '',
            ];
            exit(json_encode($back, JSON_UNESCAPED_UNICODE));
        }

        $secret = new Secret();
        $ip_info = new IpInfo();

        $app_class = $request->input('app_class');
        $page_cookie = $request->input('page_cookie');
        $url = $request->input('url');

        $time = date('YmdHis');

        $_os = $ip_info->os_info();
        $_browser_info = $ip_info->browser_info();
        $_lang_info = $ip_info->lang_info();
        $_ip = $ip_info->get_real_ip();
        $ip = ip2long($_ip);
        $client_info = $_os.'#@'.$_browser_info.'#@'.$_lang_info.'#@'.$_ip;

        $_token = $ip.'#&'.$time.'#&'.get_rand_string(rand(4, 9)).'#&';
        $user_token = $secret->encode($_token);

        // 限制访问过多次数的IP
        // 规则：每个IP < 1次/s
        $old_time = Db::table('app_token')
            ->where('ip', '=', $_ip)
            ->orderBy('time', 'desc')
            ->value('time');
        $old_time = $old_time*1;
        $new_time = $time*1;
        $stop = $new_time - $old_time;
        if ( $stop >= 1){

            $data = [
                'app_class'=> $app_class,
                'ip'=> $_ip,
                'url'=> $url,
                'time'=> $time,
                'page_cookie'=> $page_cookie,
                'user_token'=> $user_token,
                'client_info'=> $client_info,
            ];
            // 保存token
            $res = Db::table('app_token')->insertGetId($data);

            // 清除过期token
            $day = 18*30; // 默认保存18个月，单位：+day
            $end_time = date("Y-m-d", strtotime("-$day day"));
            $end_time = to_time($end_time);
            // 删除老数据
            $del_timeout_token = Db::table('app_token')->where('time', '<', $end_time)->delete();

            if (!$app_class || !$url){
                $state = 0;
                $msg = '参数不全，Token获取失败';
                $content = ['user_token'=> '', 'time'=> time(), 'dir'=> $res, 'del'=>$del_timeout_token, 'stop'=> $stop];
            }else{
                $state = 1;
                $msg = 'Token生成完成';
                $content = ['user_token'=> $user_token, 'time'=> time(), 'dir'=> $res, 'del'=>$del_timeout_token, 'stop'=> $stop];
            }

        }else{
            $state = 0;
            $msg = '请求频率太快，稍后再试';
            $content = ['stop'=>$stop];
        }

        $back = [
            'state'=>$state,
            'msg'=>$msg,
            'content'=>$content,
        ];

        return array_to_json($back);
    }


}
