<?php

/*
 * 推送短信提醒服务
 * */

namespace App\Http\Controllers\Enhance;

use App\Http\Controllers\OpenController;
use App\Http\Kit\Secret;
use Exception;
use App\Http\Kit\AliSMS;
use Illuminate\Http\Request;

final class SmsApi extends OpenController{

    final function __construct(Request $request){
        parent::__construct($request);

    }

    /*
     * 验证码短信
     * */
    final function send_code_sms($_phone_number, $_code_number){

        $sms = new AliSMS();
        $back = $sms->sms_code_service($_phone_number, $_code_number);

        return array_to_json($back);

    }



}
