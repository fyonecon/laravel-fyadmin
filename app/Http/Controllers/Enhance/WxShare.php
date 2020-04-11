<?php
namespace App\Http\Controllers\Enhance;

use App\Http\Controllers\EnhanceSafeCheck;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Exception;
use function GuzzleHttp\Promise\is_settled;

class WxShare extends EnhanceSafeCheck {

    public function __construct(Request $request){
        parent::__construct($request);

        // 拦截非法域名
        $allow_origin = [
            'http://127.0.0.1',
            'http://127.0.0.1:8080',
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


    public function get_wx_share_data(Request $request){
        $url = $request->input('url');

        $back = [
            'state'=> 1,
            'mas'=> '微信网页分享参数获取完成',
            'content'=> $this->get_wx_config($url),
            'js_execute_demo'=> '{your_api_url_domain}/enhance/this_page_wx_share.js?demo=20191223',
        ];
        return array_to_json($back);
    }


    /*
    * js接口名单、IP白名单、网页授权名单、业务域名名单 先设置好
    * */
    public function get_wx_config($url){

        // 需要在此文件夹下手动创建两个空文件：access_token.json和sapi_ticket.json，并给该json文件权限666即可
        $file = path_info()['base_path'].'/storage/wx_web_share/';

        $appid = config_wxweb_share()['appid'];
        $appsecret = config_wxweb_share()['appsecret'];

        $timestamp = time(); //
        $jsapi_ticket = $this->make_ticket($appid,$appsecret,$file);
        $nonceStr = $this->make_nonceStr($file); //

        if (empty($url)){
            exit('url参数不能为空，url必须对应授权页面的url');
        }

        $signature = $this->make_signature($nonceStr,$timestamp,$jsapi_ticket,$url); //

        // 返回数据
        $back = [
            'appid'=> $appid,
            'timestamp'=> $timestamp,
            'nonceStr'=> $nonceStr,
            'signature'=> $signature,
            'url'=> $url,
        ];

        return $back;
    }








    public function make_nonceStr($file){
        $codeSet = '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        for ($i = 0; $i<16; $i++) {
            $codes[$i] = $codeSet[mt_rand(0, strlen($codeSet)-1)];
        }
        $nonceStr = implode($codes);
        return $nonceStr;
    }

    public function make_signature($nonceStr,$timestamp,$jsapi_ticket,$url){
        $tmpArr = array(
            'noncestr' => $nonceStr,
            'timestamp' => $timestamp,
            'jsapi_ticket' => $jsapi_ticket,
            'url' => $url
        );
        ksort($tmpArr, SORT_STRING);
        $string1 = http_build_query( $tmpArr );
        $string1 = urldecode( $string1 );
        $signature = sha1( $string1 );
        return $signature;
    }

    public function make_ticket($appid,$appsecret,$file){
        // access_token 应该全局存储与更新，以下代码以写入到文件中做示例
        $data = json_decode(file_get_contents($file."access_token.json"));
        if ($data->expire_time < time()) {
            $TOKEN_URL="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appid."&secret=".$appsecret;
            $json = file_get_contents($TOKEN_URL);
            try {
                $result = json_decode($json,true);
            }catch (Exception $exception){
                print_r($json);
                exit();
            }

            if (isset($result['access_token'])){
                $access_token = $result['access_token'];
            }else{
                print_r($json);
                exit();
            }

            if ($access_token) {
                $data->expire_time = time() + 7000;
                $data->access_token = $access_token;
                $fp = fopen($file."access_token.json", "w");
                fwrite($fp, json_encode($data));
                fclose($fp);
            }
        }else{
            $access_token = $data->access_token;
        }

        // jsapi_ticket 应该全局存储与更新，以下代码以写入到文件中做示例
        $data = json_decode(file_get_contents($file."jsapi_ticket.json"));
        if ($data->expire_time < time()) {
            $ticket_URL="https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=".$access_token."&type=jsapi";
            $json = file_get_contents($ticket_URL);
            $result = json_decode($json,true);
            $ticket = $result['ticket'];
            if ($ticket) {
                $data->expire_time = time() + 7000;
                $data->jsapi_ticket = $ticket;
                $fp = fopen($file."jsapi_ticket.json", "w");
                fwrite($fp, json_encode($data));
                fclose($fp);
            }
        }else{
            $ticket = $data->jsapi_ticket;
        }

        return $ticket;
    }


}

