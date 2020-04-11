<?php

/*
 * 获取IP即IP城市
 * */

namespace App\Http\Kit;

use Exception;

class IpInfo{

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
                if ($ips[$i] == '127.0.0.1' || $ips[$i] == 'localhost'){
                    $ip = '127.0.0.1';
                    break;
                }else{

                    // UC浏览器会拦截eregi()
                    if (is_uc()){
                        $ip = $ips[$i];
                    }else{
                        $w_ip = eregi("^(10│172.16│192.168).", $ips[$i]);
                        if (!$w_ip) {
                            $ip = $ips[$i];
                            break;
                        }
                    }

                }
            }
        }
        //客户端IP 或 (最后一个)代理服务器 IP
        return ($ip ? $ip : $_SERVER['REMOTE_ADDR']);
    }




    public function get_user_ip(){

        // 获取当前请求的 User-Agent: 头部的内容。
        $_SERVER['HTTP_USER_AGENT']; // 当前返回结果：Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/45.0.2454.101 Safari/537.36

        // 获取当前请求的 Accept-Language: 头部的内容。
        $_SERVER['HTTP_ACCEPT_LANGUAGE'];  // 当前返回结果：zh-CN,zh;q=0.8

        $ip_info = new IpInfo();

        $ip = $ip_info->get_real_ip();
        $city = $ip_info->get_ip_city($ip, 'baidu');
        $device = $ip_info->os_info().'、'.$ip_info->browser_info().'、'.$ip_info->lang_info();

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

        return $info;

    }


    // 获取IP对应的城市-
    public function get_ip_city($ip, $class = 'baidu'){

        if ($class == 'baidu'){ // 限中国，不稳定
            $url = "http://api.map.baidu.com/location/ip?ak=2TGbi6zzFm5rjYKqPPomh9GBwcgLW5sS&ip={$ip}&coor=bd09ll";
            $content = request_option($url, 'get', [], true);
            // {"status":1,"message":"Internal Service Error:ip[7.193.13.255] loc failed"}
        }else if ($class == 'tianqiapi'){ // 大陆IP库，日免费5000次，速度快
            $url = "https://ip.tianqiapi.com/?ip={$ip}";
            $content = request_option($url, 'get', [], true);
            // {"ip":"27.193.13.255","country":"\u4e2d\u56fd","province":"\u5c71\u4e1c\u7701","city":"\u9752\u5c9b\u5e02","isp":"\u8054\u901a"}
        }else if($class == 'ip.sb'){ // 全球IP库，免费库，速度中
            $url = "https://api.ip.sb/geoip/$ip";
            $content = request_option($url, 'get', [], true);
            // {"organization":"Mountain View Communications","longitude":143.2104,"timezone":"Australia\/Sydney","isp":"Mountain View Communications","offset":39600,"asn":13335,"asn_organization":"CLOUDFLARENET","country":"Australia","ip":"1.1.1.1","latitude":-33.494,"continent_code":"OC","country_code":"AU"}
            // {"organization":"China Telecom","longitude":112.3792,"city":"Yiyang","timezone":"Asia\/Shanghai","isp":"China Telecom","offset":28800,"region":"Hunan","asn":4134,"asn_organization":"No.31,Jin-rong Street","country":"China","ip":"223.146.234.72","latitude":26.3889,"continent_code":"AS","country_code":"CN","region_code":"HN"}
        }else{
            $content = '';
        }

        return $content;
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


    public function __call($func_name, $args){
        $txt = "class：".__CLASS__." ，函数不存在：$func_name ，参数：$args ";
        exit($txt);
    }

}
