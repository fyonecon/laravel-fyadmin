<?php
/*
 * 《域拦截》
 * 不做Token验证的接口拦截（如用户登录控制器、文章浏览控制器）：
 * 1. 拦截请求频次；
 * 2. 拦截请求域名（不做拦截，任意域）；
 * 3. 记录请求IP+区域；
 * */

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Kit\IpInfo;
use App\Http\Kit\Secret;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class OpenSafeCheck{

    /*
     * 预先执行，安全检测
     * 1. 记录请求IP
     * */
    public function __construct(Request $request){
        header('Access-Control-Allow-Origin:*');



    }

    /*
     * 将IP加入Redis-list，以便统计
     * */



}
