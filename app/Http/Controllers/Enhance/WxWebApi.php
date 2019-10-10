<?php
/*
 * 微信网页相关
 * */

namespace App\Http\Controllers\Enhance;

use App\Http\Controllers\OpenController;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Exception;
use App\Http\Kit\WxWebShare;

class WxWebApi extends OpenController {

    public function __construct(Request $request){
        parent::__construct($request);

    }

    // 获取微信网页分享的参数
    public function wx_web_share(){

        $share = new WxWebShare();
        $back = $share->get_share_config();

        return array_to_json($back);
    }




}
