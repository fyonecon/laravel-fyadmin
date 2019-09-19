let wx = { // 公共函数
    "timestamp": function() {
        return new Date().getTime();
    },
    "set_data": function (key, value){
        localStorage.setItem(key,value);
        if (localStorage.getItem(key)){
            return true;
        }else {
            return false;
        }
    },
    "get_data": function (key, test) {
        if (test || test === 0){
            console.log("注意，你使用了get_data函数。。");
            return false;
        }
        let value = localStorage.getItem(key);
        if (value){
            return value;
        }else {
            return null;
        }
    },
    "del_data": function (key) {
        let del = localStorage.removeItem(key);
        if (del){
            return true;
        }else {
            return false;
        }
    },
    "clear_data": function () {
        let clear = localStorage.clear();
        if (clear){
            return true;
        }else {
            return false;
        }
    },
    "get_url_param": function (url, key) {
        let url_str = "";
        if(!url){
            url_str = window.location.href;
        }else {
            url_str = url;
        }
        let regExp = new RegExp("([?]|&|#)" + key + "=([^&|^#]*)(&|$|#)");
        let result = url_str.match(regExp);
        if (result) {
            return decodeURIComponent(result[2]); // 转义还原参数
        } else {
            return null;
        }
    }
};

function check_user_login(login_time, openid, nickname, headimgurl, first_step_app_user_id) {

    // start-请求数据
    // POST跨域
    const post_api = api_url+"user_login";
    const map = new Map([ // 要提交数据
        ["login_time", login_time],
        ["openid", openid],
        ["nickname", nickname],
        ["headimgurl", headimgurl],
        ["first_step_app_user_id", first_step_app_user_id],
    ]);
    // 拼装post要提交的数据
    let body = "";
    for (let [k, v] of map) { body += k+"="+v+"&"; }
    //console.log(body);

    fetch(post_api, {
        method: "post",     // get/post
        mode: "cors",       // same-origin/no-cors/cors
        cache: "no-cache",  // 不缓存
        headers: {
            "Content-type": "application/x-www-form-urlencoded; charset=UTF-8",
        },
        body: body,         // 格式："key1=val1&key2=val2"
    }).then(function (response){
        if (response.status === 200){
            return response;
        }
    }).then(function (data) {
        return data.text();
    }).then(function(text){
        // 格式校验
        let back = null;
        let res = null;
        if (typeof text === "string"){ // TP一般返回string
            back = text;
            res = JSON.parse(text);
        }else if (typeof text === "object"){ // laraval一般返回object
            back = JSON.stringify(text);
            res = text;
        }
        //console.log("类型：" + typeof text + "\n数据：" + back);
		
        // 解析与渲染数据 res
        if (res.state === 0){
            console.log(res.msg);
            console.log("处理用户信息失败，页面数据初始化被拦截。");
        }else if (res.state === 1){
            console.log(res.msg);

            app_user_token = res.app_user_token;
            app_user_id = res.content.app_user_id;

            wx.set_data("app_user_token", app_user_token);
            wx.set_data("app_user_id", app_user_id);

            setTimeout(function () {
                // 登录或者创建用户成功，即进行页面数据渲染
                page_data_init();
            }, 100);

        }else{
            console.log("超范围的state。");
        }

    }).catch(function(error){
        console.log("Fetch错误：" + error);
    });
    // end-请求数据

}


(function () { // 用户是否登录安全校验

    let login_time = wx.get_data("login_time");
    openid = wx.get_data("openid");
    let nickname = wx.get_data("nickname");
    let headimgurl = wx.get_data("headimgurl");

    // 直系推荐人的id。在推荐链接中把直系推荐人写入本地即可。
    let first_step_app_user_id = wx.get_data("first_step_app_user_id");
    first_step_app_user_id = first_step_app_user_id?first_step_app_user_id:4;

    console.log([login_time, openid, nickname, headimgurl, first_step_app_user_id]);

    // 微信浏览器拦截
    let ua = window.navigator.userAgent.toLowerCase();
    if (ua.match(/MicroMessenger/i) == 'micromessenger'){

        // document.write("请在控制台查看信息");

        if (!openid){ // 不存在用户或者新设备用户就去微信授权页
            console.log("需要授权：可能是新设备或新用户");
            window.location.replace("//wx.ggvs.cn/wx/");

        }else{ // 用户微信授权成功或者用户已经登录
            console.log("已登录：用户微信授权成功或者用户已经登录");
            check_user_login(login_time, openid, nickname, headimgurl, first_step_app_user_id);
        }

    }else {
        document.write("请在微信中打开页面");
    }



})();