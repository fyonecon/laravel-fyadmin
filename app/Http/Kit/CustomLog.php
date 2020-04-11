<?php

/*
 * 自定义日志记录函数，可用于负载均衡中
 * 1. 最多21天日志保存记录
 * 2. 存放在指定IP的服务器上
 * */

namespace App\Http\Kit;

use Exception;

class CustomLog{

    protected $path = '';

    // 写入日志
    public function set_log($where, $data, $local_server_ip){

        // 检查文件夹
        $this->path = path_info()['storage_path']."/custom_log/";
        if(!file_exists($this->path)) {
            //检查是否有该文件夹，如果没有就创建，并给予最高权限
            mkdir($this->path, 0755, true);
        }
        $string = '服务器IP：'.$local_server_ip.'；日志来源：'.$where.'；日志内容：'.$data;
        //设置路径目录信息
        $name = $this->create_log_name();
        $url = $this->path.$name.'_cache.log';
        $dir_name = dirname($url);
        //目录不存在就创建
        if (!file_exists($dir_name)) {
            $res = mkdir(iconv("UTF-8", "GBK", $dir_name), 0777, true);
        }
        //打开文件资源通道 不存在则自动创建
        $fp = fopen($url, "a");
        fwrite($fp, date("Y-m-d H:i:s ").var_export($string, true)."\r\n\n");      // 写入文件
        fclose($fp);//关闭资源通道

        // 删除过期文件
        $timeout_day = config_log()['timeout_day'];
        if ($timeout_day < 7){
            $timeout_day = 7;
        }else if ($timeout_day > 400){
            $timeout_day = 400;
        }
        $len = $timeout_day*50;
        for ($i=0; $i<$len; $i++){
            $that_day = $timeout_day+$i+1;
            $that_time = date("Y-m-d", strtotime("-$that_day day"));

            $that_file = 'log_'.$that_time.'_cache.log';
            $file = $this->path.$that_file;

            if (file_exists($file)){
                unlink($file); // 删除该文件
            }else{
                // 没有则跳过
            }

        }

        return ['set_log_file_has_done.', $name, date('YmdHis')];

    }

    // 生成log文件的名字
    public function create_log_name(){
        $file_name = 'log_'.date('Y-m-d');
        return $file_name;
    }


    public function __call($func_name, $args){
        $txt = "class：".__CLASS__." ，函数不存在：$func_name ，参数：$args ";
        exit($txt);
    }


}
