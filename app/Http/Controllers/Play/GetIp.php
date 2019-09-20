<?php

/*
 * 采集访问的IP、城市等数据
 * */

namespace App\Http\Controllers\Play;

use App\Http\Controllers\Controller;
use App\Http\Kit\Secret;
use App\Http\Kit\IpInfo;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Exception;

class GetIp extends Controller{


    /*
     * 获取用户IP接口
     * /public/index.php/kit/get_that_ip
     * */
    public function get_that_ip(){
        $ip_info = new IpInfo();
        $ip = $ip_info->get_user_ip();

        return json_encode($ip, JSON_UNESCAPED_UNICODE);
    }









}
