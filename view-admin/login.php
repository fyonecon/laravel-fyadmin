<?php
$page_path = dirname(__FILE__); // 项目index的根目录
include $page_path.'/common/config.php';
$_time = date('Y-m-d');

?>

<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>请登录-<?=$sys_name?></title>
    <script src="<?=$file_url?>static/js/jquery-1.11.3.min.js"></script>
    <script src="<?=$file_url?>static/js/common.js"></script>
    <script src="<?=$file_url?>static/js/md5.js"></script>
    <script src="<?=$file_url?>static/js/check.js"></script>

    <style>
        .body{
            margin: 0;
            background: #EEEEEE;
        }
        .user-login{
            width: 360px;
            min-height: 220px;
            max-height: 280px;
            position: fixed;
            z-index: 100;
            top: 0;
            bottom: 0;
            right: 0;
            left: 0;
            margin: auto;
            background: white;
            box-shadow: 0 0 5px #5a6268;
            border-radius: 3px;
        }
        .user-input{
            width: calc(100% - 14px);
            height: 32px;
            padding: 0 5px;
            font-size: 14px;
        }
        .user-input-item{
            padding: 10px 10px;
        }
        .user-title{
            text-align: center;
            font-size: 24px;
            letter-spacing: 4px;
            color: dodgerblue;
            font-weight: 700;
            -moz-user-select: none;
            -webkit-user-select: none;
            -ms-user-select: none;
            user-select: none;
            overflow: hidden;
            height: 36px;
        }
        .user-login-btn{
            padding: 7px 25px;
            color: white;
            background: dodgerblue;
            font-weight: 700;
            font-size: 16px;
            letter-spacing: 2px;
            /*border: 1px solid red;*/
            border-radius: 5px;
            cursor: pointer;
            -moz-user-select: none;
            -webkit-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }
        .user-login-btn:active{
            opacity: 0.6;
        }
        .user-login-btn:hover{
            opacity: 0.8;
        }
        .center{
            text-align: center;
        }
        .user-title-span{
            font-size: 14px;
            letter-spacing: 1px;
            opacity: 0.8;
        }
        .user-login-time{
            font-size: 12px;
            letter-spacing: 1px;
            color: lightblue;
            padding-top: 20px;
        }
        .user-input-check{
            width: 140px;
            height: 32px;
            padding: 0 5px;
            font-size: 14px;
        }
        .res-check{
            width: calc(100% - 140px - 20px);
            display: inline-block;
            -moz-user-select: none;
            -webkit-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }
        .show-check{
            padding: 0 10px;
            -moz-user-select: none;
            -webkit-user-select: none;
            -ms-user-select: none;
            user-select: none;
            cursor: pointer;
            letter-spacing: 2px;
            color: grey;
        }
        .show-check:active{
            opacity: 0.6;
        }
        .show-check:hover{
            opacity: 0.8;
        }

    </style>
</head>
<body class="body">

<div class="user-login">
    <div class="user-input-item user-title">
        请登录<span class="user-title-span"><?=$sys_name?><span class="user-login-time">（<?=$_time?>）</span></span>
    </div>
    <div class="user-input-item">
        <input class="user-login-name user-input" value="" placeholder="登录名" maxlength="20" type="text"/>
    </div>
    <div class="user-input-item">
        <input class="user-login-pwd user-input" value="" placeholder="密码" maxlength="20" type="password" />
    </div>
    <div class="user-input-item">
        <div class="res-check">
            <div class="show-check">12+23=</div>
        </div>
        <input class="user-login-pwd user-input-check" value="" placeholder="请输入左侧计算结果" maxlength="10" type="text" />
    </div>
    <div class="user-input-item center">
        <span class="user-login-btn">登录/Enter</span>

    </div>


</div>

<script>

    const api_url = "<?=$api_url?>";

    function clear() {
        $(".user-login-name").val("");
        $(".user-login-pwd").val("");
        $(".user-input-check").val("");
    }

    function login() {

        let back_url = getThisUrlParam("", "back_url");

        let name = $(".user-login-name").val().trim();
        let pwd = $(".user-login-pwd").val().trim();
        let num = $(".user-input-check").val().trim()*1;

        if (!name){
            alert_txt("登录名未填写", 2000);
            return;
        }
        if (!pwd){
            alert_txt("登录密码未填写", 2000);
            return;
        }else{
            pwd = hex_md5(pwd);
        }

        if (!num || num_res !== num){
            alert_txt("验证码计算结果不正确", 2000);
            make_num();
            return;
        }

        alert_txt("正在登录..", "long");

        /*请求数据*/
        $.ajax({
            url: api_url+"admin/login",
            type: "POST",
            dataType: "json",
            async: true,
            data: { // 字典数据
                login_name: name,
                login_pwd: pwd,
            },
            success: function(back, status){

                // 数据转换为json
                let res;
                let data = "";
                if(typeof back === "string"){
                    res = JSON.parse(back);
                    data = back;
                } else {
                    res= back;
                    data = JSON.stringify(back)
                }
                console.log("状态：" + status +"\n类型：" + typeof back + "。\n数据：" + data );

                make_num();
                clear();
                // 解析json
                if (res.state === 0){
                    alert_txt(res.msg, 2000);

                }else if (res.state === 1) {
                    console.log(res.msg);

                    let name = res.content.login_name;
                    let token = res.content.login_token;
                    let id = res.content.login_id;
                    let href = res.content.jump_url;
                    let time = 6*24*60*60*1000; // 6天过期

                    setCookie("login_name", name, time);
                    setCookie("login_token", token, time);
                    setCookie("login_id", id, time);

                    alert_txt(res.msg, 2000);

                    setTimeout(function () {
                        if (back_url){
                            back_url = decodeURI(back_url);
                            href = back_url;
                        }
                        console.log(href);
                        window.location.replace(href);
                    }, 1000);

                }else if (res.state === 2){
                    alert_txt(res.msg, 2000);
                }else {
                    alert_txt("超范围的state", 3000);
                }
            },
            error: function (xhr) {
                console.log(xhr);
                alert_txt("接口请求错误或者网络不通", 2500);
            }
        });

    }

    let num_res = 0;
    function make_num() {
        console.log("run-make_num");

        let num1= js_rand(0, 99);
        let num2= js_rand(1, 11);

        let num0 = js_rand(1, 3); // 加减乘除
        let mark = "";

        switch (num0) {
            case 1:
                num_res = num1-num2;
                mark = "减";
                break;
            case 2:
                num_res = num1+num2;
                mark = "加";
                break;
            case 3:
                num_res = num1*num2;
                mark = "乘";
                break;
            default:
                num_res = num1*num2;
                mark = "乘";
                break;
        }

        let div = num1+"&nbsp;"+mark+"&nbsp;"+num2+"&nbsp;"+"&nbsp;"+"等于";
        $(".show-check").html(div);

    }

    function isWeiXin(){
        var ua = window.navigator.userAgent.toLowerCase();
        if(ua.match(/MicroMessenger/i) == 'micromessenger'){
            return true;
        }else{
            return false;
        }
    }

    (function () {

        var wx = isWeiXin();
        if (wx){
            window.location.replace('wx-404.php');
        }

        $(document).on("click", ".user-login-btn", function () {
            login();
        });

        document.onkeydown = function(e){
            var ev = document.all ? window.event : e;
            if(ev.keyCode==13) {
                login();
            }
        };

        $(document).on("click", ".show-check", function () {
            make_num();
        });

        // 生成数字验证码
        make_num();

    })();

</script>


</html>
