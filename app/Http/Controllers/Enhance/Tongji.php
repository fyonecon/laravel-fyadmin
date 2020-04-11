<?php
namespace App\Http\Controllers\Enhance;

use App\Http\Controllers\EnhanceSafeCheck;
use App\Http\Kit\IpInfo;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Exception;

class Tongji extends EnhanceSafeCheck {

    public function __construct(Request $request){
        parent::__construct($request);

    }

    // 统计前端页面的访问IP
    public function save_referrer(Request $request){
        $referrer_url = $request->input('referrer_url');
        $referrer_url = filter_article($referrer_url);

        $now_url = $request->input('now_url');
        $now_url = filter_article($now_url);

        $_ip = $request->input('ip');
        $client_info = $request->input('client_info');

        $creat_time = date('YmdHis');

        $ip_info = new IpInfo();

        if (empty($referrer_url)){
            $referrer_url = get_url();
        }

        if (empty($now_url)){
            $now_url = get_url();
        }

        if (empty($_ip)){
            $_ip = $ip_info->get_real_ip();
        }

        if (empty($client_info)){
            $_ip = $ip_info->get_real_ip();
            $_os = $ip_info->os_info();
            $_browser_info = $ip_info->browser_info();
            $_lang_info = $ip_info->lang_info();
            $client_info = $_os.'#@'.$_browser_info.'#@'.$_lang_info.'#@'.$_ip;
        }

        $data = [
            'referrer_url'=> $referrer_url,
            'now_url'=> $now_url,
            'create_time'=> $creat_time,
            'client_info'=> $client_info,
            'ip'=> $_ip,
        ];

        if ($_ip == '127.0.0.1' || $_ip == 'localhost'){
            $res = -1;
        }else{

            $res = Db::table('referrer')->insertGetId($data);
        }

        if ($res){
            $state = 1;
            $msg = '成功或跳过';
            $content = $data;
        }else{
            $state = 0;
            $msg = '失败，可能是数据库操作失败';
            $content = $data;
        }

        $back = [
            'state'=>$state,
            'msg'=>$msg,
            'content'=>$content,
        ];

        return array_to_json($back);

    }


    // 统计后台管理系统的访问/登录IP
    public function save_admin_ip(Request $request){
        $ip = $request->input('ip');
        $referrer = $request->input('referrer');
        $now_url = $request->input('now_url');
        $client_info = $request->input('client_info');

        $data = [
            'ip'=> $ip,
            'referrer'=> $referrer,
            'now_url'=> $now_url,
            'create_time'=> date('YmdHis'),
            'client_info'=> $client_info,
        ];

        if ($ip){

            $res = DB::table('admin_login_ip')
                ->insertGetId($data);

            if ($res){
                $state = 1;
                $msg = '成功';
                $content = $res;
            }else{
                $state = 0;
                $msg = '失败';
                $content = $data;
            }

        }else{
            $state = 0;
            $msg = '参数不全';
            $content = $data;
        }

        $back = [
            'state'=>$state,
            'msg'=>$msg,
            'content'=>$content,
        ];

        return array_to_json($back);
    }







}
