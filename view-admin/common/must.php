<?php

/*
 * 用户登录状态校验
 * */

?>


<script>


    (function () {
        console_log([login_name, login_token, login_id]);

        // alert_txt("系统升级中...", "long");
        // return;

        // 登录状态检测
        /*请求数据*/
        $.ajax({
            url: api_url+"admin/login_check",
            type: "POST",
            dataType: "json",
            async: true,
            data: { // 字典数据
                login_name: login_name,
                login_token: login_token,
                login_id: login_id,

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

                // 解析json
                if (res.state === 0){
                    alert_txt(res.msg, 2000);
                    let login_href = res.login_href;
                    let old_href = "&back_url="+encodeURI(window.location.href);
                    console_log([login_href, old_href]);

                    setTimeout(function () {
                        window.location.replace(login_href+old_href);
                    }, 1500);

                }else if (res.state === 1) {
                    console_log(res.msg);
                    let info = res.content;
                    login_nickname = info.login_nickname;
                    login_level = info.login_level;
                    let login_level_name = info.login_level_name;

                    page_data_init(); // 登录校验完成后开始执行页面数据渲染
                    page_style_init(login_nickname, login_level, login_level_name); // 执行登录后的页面样式渲染

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

    })();


</script>
