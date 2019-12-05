<?php

namespace App\Http\Kit;

use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;
use AlibabaCloud\Ecs\Ecs;

class AliSMS {

    /*
     * 验证码短信
     * 请提前设置好短信的“签名”、“模板”
     * https://dysms.console.aliyun.com/dysms.htm
     * Download：https://github.com/aliyun/openapi-sdk-php
     * */
    public function sms_code_service($_phone_number, $_code_number){

        if (!$_phone_number || !$_code_number){ // 只做空值校验，不做格式校验
            $back = [
                'state'=> 0,
                'msg'=> 'sms_code_service-空值警告！',
                'content'=> '',
                'test_data'=> [$_phone_number, $_code_number, date('YmdHis')],
            ];
        }else{

            // 注意两个参数都为string类型
            $phone_number = (string)$_phone_number;
            $code_number = (string)$_code_number;

            // 以下全部代码在你设置好两个模板后就会有，然后直接从阿里云后台复制过来即可。
            AlibabaCloud::accessKeyClient('LTAIADXV', 'cBe6sUCPuAr')
                ->regionId('cn-hangzhou')
                ->asDefaultClient();

            try {
                $result = AlibabaCloud::rpc()
                    ->product('Dysmsapi')
                    // ->scheme('https') // https | http
                    ->version('2017-05-25')
                    ->action('SendSms')
                    ->method('POST')
                    ->host('dysmsapi.aliyuncs.com')
                    ->options([
                        'query' => [
                            'RegionId' => 'cn-hangzhou',
                            'PhoneNumbers' => $phone_number,                            // 用户手机号
                            'SignName' => '',                                       // 已经申请的短信签名
                            'TemplateCode' => '',                          // 已经申请的短信模板
                            'TemplateParam' => '{"code":"'.$code_number.'"}',           // 验证码
                        ],
                    ])
                    ->request();

                $back = [
                    'state'=> 1,
                    'msg'=> 'sms_code_service-请查看content发送结果',
                    'content'=> $result->toArray(),
                    'test_data'=> [$_phone_number, $_code_number, date('YmdHis')],
                ];

            } catch (ClientException $e) {
                $back = [
                    'state'=> 0,
                    'msg'=> 'sms_code_service-ClientException',
                    'content'=> $e->getErrorMessage() . PHP_EOL,
                    'test_data'=> [$_phone_number, $_code_number, date('YmdHis')],
                ];
            } catch (ServerException $e) {
                $back = [
                    'state'=> 0,
                    'msg'=> 'sms_code_service-ServerException',
                    'content'=> $e->getErrorMessage() . PHP_EOL,
                    'test_data'=> [$_phone_number, $_code_number, date('YmdHis')],
                ];
            }

        }

        return $back;
    }



}
