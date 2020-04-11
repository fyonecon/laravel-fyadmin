<?php

namespace App\Http\Controllers\AppUser;

use App\Http\Controllers\UserSafeCheck;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Exception;
use App\Http\Kit\IpInfo;
use Illuminate\Support\Facades\DB;

class UserInfo extends UserSafeCheck {

    public function __construct(Request $request){
        parent::__construct($request);


    }

    // 获取用户信息
    public function get_user_info(Request $request){
        $xcx_user_id = $request->input('user_id');

        //
        $res = DB::table('xcx_user')
            ->where('state', '=', 1)
            ->where('xcx_user_id', '=', $xcx_user_id)
            ->select('xcx_user_id', 'xcx_user_photo', 'nick_name', 'avatar_url', 'gender', 'openid')
            ->first();

        if ($res){
            $state = 1;
            $msg = '查询完成';
            $content = $res;
        }else{
            $state = 0;
            $msg = '无用户数据';
            $content = '';
        }

        $back = [
            'state'=>$state,
            'msg'=>$msg,
            'content'=>$content,
        ];

        return array_to_json($back);

    }




}

