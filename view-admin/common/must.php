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
        // 开始-Fetch-请求数据
        const post_api = api_url + "admin/login_check"; // 接口
        const map = new Map([ // 要提交数据
            ["login_name", login_name],
            ["login_token", login_token],
            ["login_id", login_id],
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
            console_log("接口返回类型：\n" + typeof text + "\n数据：\n" + back);

            // 解析与渲染数据 res
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
                // replace_img_domain(500, "//img.ggvs.cn/" ); // 用于全局替换img的主域名

            }else if (res.state === 2){
                alert_txt(res.msg, 2000);
            }else if (res.state === 302){
                alert_txt("需要重新登录", 2000);
                setTimeout(function () {
                    let old_url = window.location.href;
                    old_url = encodeURI(old_url);
                    window.location.replace("./login.php?login=must&back_url="+old_url);
                }, 1500);
            } else{
                console.log("超范围的state(state="+ res.state +")");
                alert_txt("超范围的state(state="+ res.state +")", 5000);
                refresh_page(5000);
            }

        }).catch(function(error){
            let error_info = "Fetch遇到错误：" + error;
            console.log("%c"+error_info, "color:red;font-weight:bold;font-size:18px;");
            alert_txt(error_info, 3000);
        });
        // 结束-Fetch

    })();


</script>
