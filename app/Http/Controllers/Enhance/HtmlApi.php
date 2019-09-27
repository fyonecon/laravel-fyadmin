<?php

/**
* 专门生成静态文件（js、css、json）
*/
namespace App\Http\Controllers\Enhance;

use App\Http\Controllers\Controller;
use Exception;
use App\Http\Kit\IpInfo;
use Illuminate\Http\Request;

class HtmlApi extends Controller{

    public function common_js(Request $request){

        $type = $request->input('type');

        if ($type == 'js'){

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
        }else{
            $this->back_404('文件类型不正确');
        }

    }


    public function back_404($txt){
        header('HTTP/1.1 404 Not Found');
        header('Content-Type: text/html; charset=utf-8');
        echo '<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0">';
        echo '<title>404-'.$txt.'</title>';
        echo '<style>body{font-size: 18px;color: #555555;margin: 20px;background: #EEEEEE;font-weight: bold;text-align: center;letter-spacing: 2px;}</style>';
        exit($txt);
    }



}
