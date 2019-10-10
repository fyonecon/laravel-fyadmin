<?php

/*
 * 应用于负载均衡服务器集群中的统一系统日志记录；
 * 不对外开放Api，只用于服务器间的通讯。
 * */

namespace App\Http\Controllers\Enhance;

use App\Http\Controllers\Controller;
use App\Http\Kit\CustomLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Exception;

final class Log extends Controller {


    protected $local_server_ip = '';
    protected $log_server_ip = '';
    protected $path = '';

    /*
     * 本系统中请调用此函数
     * */
    final function write_log($title, $data){

        // 检查文件夹
        $this->path = path_info()['storage_path']."/custom_log/";
        if(!file_exists($this->path)) {
            //检查是否有该文件夹，如果没有就创建，并给予最高权限
            mkdir($this->path, 0755, true);
        }

        $this->local_server_ip = config_log()['local_server_ip']; // 本地/此服务器
        $this->log_server_ip = config_log()['log_server_ip']; // 日志服务器接口

        $api = 'http://'.$this->log_server_ip.'/'.main_filename().'/public/index.php/enhance/log';
        $array = [
            'title'=> json_encode($title, JSON_UNESCAPED_UNICODE),
            'data'=> json_encode($data, JSON_UNESCAPED_UNICODE),
            'local_server_ip'=> json_encode($this->local_server_ip, JSON_UNESCAPED_UNICODE),
        ];

        $back = request_post($api, $array);

        return $back;

    }


    /*
     * 写日志接口
     * 往日志服务器上写日志
     * */
    final function log(Request $request){
        header('Access-Control-Allow-Origin:*');

        $title = $request->input('title'); // 日志来源
        $data = $request->input('data'); // 日志内容
        $local_server_ip = $request->input('local_server_ip'); // 对方服务器IP

        $log = new CustomLog();
        $res = $log->set_log($title, $data, $local_server_ip);

        return $res;
    }





}
