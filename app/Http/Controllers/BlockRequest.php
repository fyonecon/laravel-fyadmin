<?php
/*
 * 拦截爬虫或者请求次数超额的接口
 * */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use App\Http\Controllers\Enhance\Log;
use App\Http\Kit\IpInfo;


class BlockRequest extends Controller{

    /*
     *
     * */
    public function block_request($key){


        $back = [
            'state'=> 1,
            'msg'=> '=',
            'content'=> [$key],
        ];

        return $back;

    }

}
