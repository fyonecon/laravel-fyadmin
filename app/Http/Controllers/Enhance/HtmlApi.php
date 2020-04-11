<?php

/**
* 专门生成静态文件（js、css、json）
*/
namespace App\Http\Controllers\Enhance;

use App\Http\Controllers\EnhanceSafeCheck;
use Exception;
use App\Http\Kit\IpInfo;
use Illuminate\Http\Request;

final class HtmlApi extends EnhanceSafeCheck{

    final function __construct(Request $request){
        parent::__construct($request);

    }


    final function ip_js(){
        $ip_info = new IpInfo();
        $ip = $ip_info->get_user_ip();
        $user_ip = $ip['ip'];
        $user_city = $ip['city'];
        $user_device = $ip['device'];
$js = <<<EOP
    const ip = "$user_ip";
    const city = "$user_city";
    const device = "$user_device";
EOP;
        echo $js;
        exit();

    }


    final function common_js(Request $request){

        $page_time = time();
        $debug = 'true';

$js = <<<EOF
    /*开始-公共函数*/
    const page_time = "$page_time";
    const debug = $debug;
    const view = {
        "log": function (txt) {
            if (txt === 0 || txt === "0") {}else {if (!txt){txt = "空txt";} }
            debug === true ? console.log(txt): "";
        },
        "write_js": function (js_src_array, call_func) {
            if (js_src_array.constructor !== Array){
                view.log("js_src_array不是数组。");
                return;
            }
            let had_onload = 0;
            let head = document.head || document.getElementsByTagName("head")[0];
            for (let i=0; i<js_src_array.length; i++){
                let script = document.createElement("script");
                script.setAttribute("class", "write-js");
                script.setAttribute("src", js_src_array[i]+ "?" + page_time);
                head.appendChild(script);
                script.onload = function () {
                    had_onload++;
                    if (had_onload === js_src_array.length) {
                        try {
                            call_func(true);
                        }catch (e) {
                            view.log("可选回调函数没有设置。");
                        }
                    }
                };
            }
        },
        "write_css": function (css_src_array, call_func) { // 写入外部js
            if (css_src_array.constructor !== Array){
                view.log("css_src_array不是数组。");
                return;
            }
            let had_onload = 0;
            let head = document.head || document.getElementsByTagName("head")[0];
            for (let i=0; i<css_src_array.length; i++){
                let link = document.createElement("link");

                link.setAttribute("id", "depend-css");
                link.setAttribute("href",css_src_array[i] + "?" + page_time);
                link.setAttribute("rel", "stylesheet");
                head.appendChild(link);

                had_onload++;

                if (had_onload === css_src_array.length) {
                    try {
                        call_func(true);
                    }catch (e) {
                        view.log("可选回调函数没有设置。");
                    }
                }
            }
        },
        "get_url_param": function (url, key) {
            let url_str = "";
            if(!url){ url_str = window.location.href; } else {url_str = url; }
            let regExp = new RegExp("([?]|&|#)" + key + "=([^&|^#]*)(&|$|#)");
            let result = url_str.match(regExp);
            if (result) {
                return decodeURIComponent(result[2]); // 转义还原参数
            }else {
                return "";
            }
        },
        "class_write_html": function (only_class_name, html) {
            document.getElementsByClassName(only_class_name)[0].innerHTML = html;
        },
        "id_write_html": function (id_name, html) {
            document.getElementById(id_name).innerHTML = html;
        },
        "set_cookie": function (name, value, time) {
            if (!time){
                time = 1*24*60*60*1000; // 默认1天
            }
            let exp = new Date();
            exp.setTime(exp.getTime() + time);
            document.cookie = name + "="+ escape (value) + ";expires=" + exp.toGMTString();
        },
        "get_cookie": function (name) {
            let arr,reg=new RegExp("(^| )"+name+"=([^;]*)(;|$)");
            if(arr=document.cookie.match(reg)){
                return unescape(arr[2]);
            } else{
                return null;
            }
        },
        "del_cookie": function (name) {
            let exp = new Date();
            exp.setTime(exp.getTime() - 1);
            let cval=getCookie(name);
            if(cval!=null) {
                document.cookie = name + "=" + cval + ";expires=" + exp.toGMTString();
            }
        },
        "base64_encode": function (string) {
            return btoa(string);
        },
        "base64_decode": function (string) {
            return atob(string);
        },

    };
    /*结束-公共函数*/

    function js_init(){
        view.log("js-init");
    }

    (function(){
        view.log("TJ-js-is-Running.");
        view.write_js(["https://static.runoob.com/assets/jquery/2.0.3/jquery.min.js"], js_init);
    })();

EOF;
        echo $js;
        exit();

    }


    final function this_page_wx_share(){

        $js = <<<EOF

    function wx_share_data(res) {

        let appid = res.appid;
        let timestamp = res.timestamp*1;
        let nonceStr = res.nonceStr;
        let signature = res.signature;

        let wx_script = document.createElement('script');
        let head = document.head || document.getElementsByTagName("head")[0];
        wx_script.setAttribute("src", "http://res.wx.qq.com/open/js/jweixin-1.2.0.js");
        head.appendChild(wx_script);
        wx_script.onload = function(){

            wx.config({
                debug: false, // 是否开启调试
                appId: appid,
                timestamp: timestamp,
                nonceStr: nonceStr,
                signature: signature,
                jsApiList: [
                    "checkJsApi",
                    "onMenuShareTimeline",
                    "onMenuShareAppMessage",
                    "onMenuShareQQ",
                    "onMenuShareWeibo",
                ]
            });

            wx.ready(function () {
                let shareData = {
                    title: "分享文章!",
                    desc: "快来看看吧！",
                    link: document.URL,
                    imgUrl: "http://makeoss.oss-cn-hangzhou.aliyuncs.com/gudushiyanshi/images/wxshareimg.jpg",
                    success: function () {
                        console.log("分享成功");
                    },
                    cancel: function () {
                        console.log("分享取消");
                    }
                };

                wx.onMenuShareAppMessage(shareData);
                wx.onMenuShareTimeline(shareData);
                wx.onMenuShareQQ(shareData);
                wx.onMenuShareWeibo(shareData);

            });

        };

    }

    (function () {

        let url = encodeURI(window.location.href);

        // 获取授权参数
        // 开始-Fetch-请求数据
        const post_api = "http://api.cswendu.com/cswd/public/index.php/api/enhance/get_wx_share_data"; // 接口
        const map = new Map([ // 要提交数据
            ["url", url],
        ]);
        let body = "";
        for (let [k, v] of map) { body += k+"="+v+"&"; } // 拼装数据，限制2MB最佳
        fetch(post_api, {
            method: "post",     // get/post
            mode: "cors",       // same-origin/no-cors/cors
            cache: "no-cache",
            headers: {
                "Content-type": "application/x-www-form-urlencoded; charset=UTF-8",
            },
            body: body,         // body示例格式："key1=val1&key2=val2"
        }).then(function (response){
            if (response.status === 200){return response;}
        }).then(function (data) {
            return data.text();
        }).then(function(text){ // 返回接口数据
            // 统一格式校验
            let back = null;
            let res = null;
            if (typeof text === "string"){
                back = text;
                res = JSON.parse(text);
            }else if (typeof text === "object"){
                back = JSON.stringify(text);
                res = text;
            }else {console.log("未知类型=" + typeof text)}
            console.log("接口返回类型：\n" + typeof text + "\n数据：\n" + back);

            if (res.state === 0){
                console.log(res.msg);
            }else if(res.state === 1){
                try {
                    wx_share_data(res.content);
                }catch (e) {
                    console.log("wx_share_data(res)函数不存在，已经跳过执行");
                }
            }else{
                console.log(res.msg);
            }

        }).catch(function(error){
            let error_info = "Fetch遇到错误：" + error;
            console.log("%c"+error_info, "color:red;font-weight:bold;font-size:18px;");
        });
        // 结束-Fetch

    })();

EOF;
        echo $js;
        exit();

    }


    // 统计前端referrer
    final function get_referrer(){

        $js = <<<EOF

    (function () {

        try{
            document.getElementsByClassName("s_e-o-div")[0].classList.add("hide");
        }catch(e){
            console.log("s_e-o-div jump");
        }


    })();

EOF;

        echo $js;
        exit();

    }




}
