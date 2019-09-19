<?php

/*
 * 采集访问的IP、城市等数据
 * */

namespace App\Http\Controllers\Play;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Kit\Secret;
use App\Http\Controllers\OpenSafeCheck;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Exception;

class GetIp extends OpenSafeCheck{


    /*
     * 获取用户IP接口
     * /public/index.php/kit/get_that_ip
     * */
    public function get_that_ip(){
        header('Access-Control-Allow-Origin:*');

        // 获取当前请求的 User-Agent: 头部的内容。
        $_SERVER['HTTP_USER_AGENT']; // 当前返回结果：Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/45.0.2454.101 Safari/537.36

        // 获取当前请求的 Accept-Language: 头部的内容。
        $_SERVER['HTTP_ACCEPT_LANGUAGE'];  // 当前返回结果：zh-CN,zh;q=0.8

        $ip = $this->get_real_ip();
        $city = $this->getCity($ip);
        $device = $this->os_info().'、'.$this->browser_info().'、'.$this->lang_info();

        if ($city->{'status'} === 1){
            $address = 'localhost_ip';
        }elseif ($city->{'status'} === 2){
            $address = 'localhost_ip';
        }else{
            $address = $city->{'content'}->{'address_detail'}->{'city'};
        }

        $info = [
            'ip'=>$ip,
            'city'=>$address,
            'device'=>$device,
        ];

        return json_encode($info, JSON_UNESCAPED_UNICODE);

    }


    // 获取IP
    public function get_real_ip(){

        $ip = FALSE;
        //客户端IP 或 NONE
        if(!empty($_SERVER["HTTP_CLIENT_IP"])){
            $ip = $_SERVER["HTTP_CLIENT_IP"];
        }

        //多重代理服务器下的客户端真实IP地址（可能伪造）,如果没有使用代理，此字段为空
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ips = explode (", ", $_SERVER['HTTP_X_FORWARDED_FOR']);
            if ($ip) { array_unshift($ips, $ip); $ip = FALSE; }
            for ($i = 0; $i < count($ips); $i++) {
                if (!eregi ("^(10│172.16│192.168).", $ips[$i])) {
                    $ip = $ips[$i];
                    break;
                }
            }
        }
        //客户端IP 或 (最后一个)代理服务器 IP
        return ($ip ? $ip : $_SERVER['REMOTE_ADDR']);
    }

    // 获取所在城市
    public function getCity($ip){
        // 获取当前位置所在城市
        $getIp = $ip;
        $content = file_get_contents("http://api.map.baidu.com/location/ip?ak=2TGbi6zzFm5rjYKqPPomh9GBwcgLW5sS&ip={$getIp}&coor=bd09ll");
        return json_decode($content);
    }


    // 操作系统
    public function os_info() {
        if (!empty($_SERVER['HTTP_USER_AGENT'])) {
            $os = $_SERVER['HTTP_USER_AGENT'];
            if (preg_match('/win/i', $os)) {
                $os = 'Windows';
            } else if (preg_match('/mac/i', $os)) {
                $os = 'MAC';
            } else if (preg_match('/linux/i', $os)) {
                $os = 'Linux';
            } else if (preg_match('/unix/i', $os)) {
                $os = 'Unix';
            } else if (preg_match('/bsd/i', $os)) {
                $os = 'BSD';
            } else {
                $os = 'Other';
            }
            return $os;
        } else {
            return 'unknown-os';
        }
    }

    // 浏览器类型
    public function browser_info() {
        if (!empty($_SERVER['HTTP_USER_AGENT'])) {
            $br = $_SERVER['HTTP_USER_AGENT'];
            if (preg_match('/MSIE/i', $br)) {
                $br = 'MSIE';
            } else if (preg_match('/Firefox/i', $br)) {
                $br = 'Firefox';
            } else if (preg_match('/Chrome/i', $br)) {
                $br = 'Chrome';
            } else if (preg_match('/Safari/i', $br)) {
                $br = 'Safari';
            } else if (preg_match('/Opera/i', $br)) {
                $br = 'Opera';
            } else {
                $br = 'Other';
            }
            return $br;
        } else {
            return 'unknown-browser';
        }
    }

    // 浏览器语言
    public function lang_info() {
        if (!empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $lang = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
            $lang = substr($lang, 0, 5);
            if (preg_match('/zh-cn/i',$lang)) {
                $lang = '简体中文';
            } else if (preg_match('/zh/i',$lang)) {
                $lang = '繁体中文';
            } else {
                $lang = 'English';
            }
            return $lang;
        } else {
            return 'unknown-lang';
        }
    }



}
