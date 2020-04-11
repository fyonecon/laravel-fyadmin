<?php

/*
 * 推送短信提醒服务
 * */

namespace App\Http\Controllers\Enhance;

use App\Http\Controllers\EnhanceSafeCheck;
use App\Http\Kit\Secret;
use Exception;
use App\Http\Kit\AliSMS;
use App\Http\Kit\HxSMS;
use Illuminate\Http\Request;

final class SmsApi extends EnhanceSafeCheck{

    final function __construct(Request $request){
        parent::__construct($request);

        // 拦截非法域名
        $allow_origin = [ // 域名白名单
            // 业务
            'http://127.0.0.1:8080',
            // 其他域名

        ];
        $origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';
        if (in_array($origin, $allow_origin)) {
            header('Access-Control-Allow-Origin:' . $origin);
        } else {
            $back = [
                'state'=> 0,
                'mas'=> '域名[ '.$origin.' ]不在白名单',
                'content'=> [],
            ];
            exit(array_to_json($back));
        }

    }

    /*
     * 验证码短信
     * */
    final function send_code_sms(Request $request){
        $phone = $request->input('phone');  // 手机号
        $code = $request->input('code');    // 只要验证码

        $sms = new AliSMS();
        $back = $sms->sms_code_service($phone, $code);

        return array_to_json($back);

    }

    /*
     * 通知通信或验证码短信
     * */
    final function send_notice_sms(Request $request){
        $phone = $request->input('phone');  // 手机号
        $sms = $request->input('sms');      // 短信完整内容

        $hx = new HxSMS();
        $res = $hx->hx_send_sms($phone, $sms);

        return array_to_json($res);
    }


}
