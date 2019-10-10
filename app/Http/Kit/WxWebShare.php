<?php
/*
 * 微信网页分享
 * */

namespace App\Http\Kit;

use App\Http\Kit\Secret;
use Exception;

class WxWebShare{

    protected $file;

    public function get_share_config(){

        $appid = config_wxweb_share()['appid'];
        $appsecret = config_wxweb_share()['appsecret'];

        $timestamp = time();
        $jsapi_ticket = $this->make_ticket($appid, $appsecret);
        $nonceStr = $this->make_nonceStr();
        $url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        $signature = $this->make_signature($nonceStr,$timestamp,$jsapi_ticket,$url);

        $js = <<<EOF
            /*
            请提前将js回调域名加入公众号白名单。
            < script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js ></ script>"
            */
            
            wx.config({
                debug: false,
                appId: '<?=$appid?>',
                timestamp: <?=$timestamp?>,
                nonceStr: '<?=$nonceStr?>',
                signature: '<?=$signature?>',
                jsApiList: [
                    'checkJsApi',
                    'onMenuShareTimeline',
                    'onMenuShareAppMessage',
                    'onMenuShareQQ',
                    'onMenuShareWeibo',
                ]
            });
            
            wx.ready(function () {
                var shareData = {
                    title: '乐转文章-分享文章可得奖金!',
                    desc: '文章赚钱，收徒分成，快来看看吧！',
                    link: document.URL,
                    imgUrl: 'http://makeoss.oss-cn-hangzhou.aliyuncs.com/gudushiyanshi/images/wxshareimg.jpg'
                };
        
                wx.onMenuShareAppMessage(shareData);
                wx.onMenuShareTimeline(shareData);
                wx.onMenuShareQQ(shareData);
                wx.onMenuShareWeibo(shareData);
        
            });
EOF;

        $back = [
            'appid'=> $appid,
            'timestamp'=> $timestamp,
            'nonceStr'=> $nonceStr,
            'signature'=> $signature,
            'share_js_example'=> $js,
        ];

        return $back;
    }

    public function make_nonceStr(){
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
    public function make_ticket($appid,$appsecret){

        $this->file = path_info()['base_path'].'/storage/wx_web_share/';

        // access_token 应该全局存储与更新，以下代码以写入到文件中做示例
        $data = json_decode(file_get_contents($this->file."access_token.json"));
        if ($data->expire_time < time()) {
            $TOKEN_URL="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appid."&secret=".$appsecret;
            $json = file_get_contents($TOKEN_URL);
            $result = json_decode($json,true);
            $access_token = $result['access_token'];
            if ($access_token) {
                $data->expire_time = time() + 7000;
                $data->access_token = $access_token;
                $fp = fopen($this->file."access_token.json", "w");
                fwrite($fp, json_encode($data));
                fclose($fp);
            }
        }else{
            $access_token = $data->access_token;
        }

        // jsapi_ticket 应该全局存储与更新，以下代码以写入到文件中做示例
        $data = json_decode(file_get_contents($this->file."jsapi_ticket.json"));
        if ($data->expire_time < time()) {
            $ticket_URL="https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=".$access_token."&type=jsapi";
            $json = file_get_contents($ticket_URL);
            $result = json_decode($json,true);
            $ticket = $result['ticket'];
            if ($ticket) {
                $data->expire_time = time() + 7000;
                $data->jsapi_ticket = $ticket;
                $fp = fopen($this->file."jsapi_ticket.json", "w");
                fwrite($fp, json_encode($data));
                fclose($fp);
            }
        }else{
            $ticket = $data->jsapi_ticket;
        }

        return $ticket;
    }


}
