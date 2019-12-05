
/*
*
* 共用函数，无所谓登不登录
*
* */


// 打印日志函数
const log = true;
function console_log(txt) {
    if (txt === 0 || txt === "0") {

    }else {
        if (!txt){
            txt = "空txt";
        }
    }
    log === true ? console.log("%c"+txt, "color:DeepSkyBlue;font-size:13px;"): "";
}

// cookie前缀
cookie_pre = "wx_article_";

var href_404 = "https://www.baidu.com";

// 公共变量
var username = getCookie(cookie_pre+"login_name");
var user_token = getCookie(cookie_pre+"token");

// 删除加载动画
function del_loading_div() {
    $(".loading-div").fadeOut(300);
    setTimeout(function () {
        $(".loading-div").remove();

    }, 300);
}


// 返回首页
function back_home() {
    window.location.href = "home.php?nav=home";
}




// 刷新页面
function refresh_page(second_waiting) {
    var second = 0;
    var _second = second_waiting*1;
    if (_second){
        second = _second;
    }
    setTimeout(function () {
        window.location.reload();
    }, second);
}

// 返回上一级
function back_page(second_waiting){
    var second = 0;
    var _second = second_waiting*1;
    if (_second){
        second = _second;
    }
    setTimeout(function () {
        window.history.go(-1);
    }, second);
}


/**
 * 判断是否含有script非法字符]
 * @param  {[type]}  str [要判断的字符串]
 * @return {Boolean}     [true：含有，验证不通过；false:不含有，验证通过]
 */
function hasIllegalChar(str) {
    return new RegExp(".*?script[^>]*?.*?(<\/.*?script.*?>)*", "ig").test(str);
}

// 管理用户等级的功能显示
function user_level(level) {
    $(".level-"+level+"-not-do").remove();
    setTimeout(function () {
        user_level(level);
    }, 800);
}

// 执行页面登录后的页面样式渲染//权限渲染
function page_style_init(user_name, _level, login_level_name) {
    $(".zh-name").html(user_name + "（"+ login_level_name +"）");

    user_level(_level);

    del_loading_div();
}

// 处理分页-整页
// paginition(_limit, _total, _uri1, _uri2, class_append列表渲染类, class_only分页div的唯一类, word1网址中的key（一般叫做page）, num1当前第几页page=,)
/*
* // 处理分页
                    var page = datas.page;
                    var limit = datas.limit;
                    var count = datas.data_count;
                    var tab = 0;
                    var url1 = web_url+'app_user.php?nav=app_user'; // 路由
                    var url2 = '&nickname='+s_nickname+'&start_time='+s_start_time+'&end_time='+s_end_time+'&order='+s_order+'&openid='+s_openid+'&user_had_rewarded='+s_user_had_rewarded+"&phone="+s_phone+"#tab="+tab; // 参数集
                    setTimeout(function () {
                        paginition(limit, count, url1, url2, "navigation-div", "navigation-page", "page_app_user", s_page)
                    }, 200);
*
* */


// 分页
// paging(当前第几页, 每页最大多少条数据, 总共有多少条数据, 向那个class标签插入分页, 分页参数的键, 不含分页id的键的网址)
function paging(now_page, limit, total, insert_div, page_key, uri) {
    let href = uri;
    now_page = now_page*1;
    limit = limit*1;
    total = total*1;

    let page_num = 0;
    if (limit === total){
        page_num = Math.floor(total/limit); // 页数
    }else {
        page_num = Math.floor(total/limit)+1; // 页数
    }

    let that_page = now_page;
    if (that_page>page_num){
        that_page=page_num;
    }

    let html = [];

    let first_href = href+"&"+page_key+"="+1;
    let first_a = '<a class="paging-a first_a" href="'+first_href+'" target="_self">首页</a>';
    let before_href = href+"&"+page_key+"="+(now_page-1);
    let before_a = '<a class="paging-a before_a" href="'+before_href+'" target="_self">上一页</a>';
    html.push(first_a);
    html.push(before_a);

    let auto_href = href+"&"+page_key+"="+now_page;
    let a = '<a class="paging-a auto_a" href="'+auto_href+'" target="_self">'+that_page+'/'+page_num+'</a>';
    html.push(a);

    let next_href = href+"&"+page_key+"="+(now_page+1);
    let next_a = '<a class="paging-a next_a" href="'+next_href+'" target="_self">下一页</a>';
    let end_href = href+"&"+page_key+"="+page_num;
    let end_a = '<a class="paging-a end_a" href="'+end_href+'" target="_self">尾页</a>';
    html.push(next_a);
    html.push(end_a);

    let div = html.join("");
    $("."+insert_div).html('<div class="paging-div"><span class="paging-total">共'+total+'条数据</span>'+div+'</div>');

    setTimeout(function () {
        if (that_page <= 1){
            $(".before_a").remove();
        }
        if (that_page >= page_num){
            $(".next_a").remove();
        }
    }, 200);

}


function make_notice(_json, _show_time) {

    if (document.getElementsByClassName("kd-notice-div").length === 0) {
        $("body").append('<div class="kd-notice-div"><div class="kd-notice-content"></div></div>');
    }

    let json = _json;
    let show_time = _show_time?_show_time:3000; // ms

    for (let i=0; i<json.length; i++){
        let time = i*1500;
        setTimeout(function () {
            let clear = new Date().getTime(); // 微秒时间戳标记不同的div
            $(".kd-notice-content").before('<div class="kd-notice-cell clear-'+ clear +'">' +
                json[i]["msg"] +
                '<div class="kd-notice-close">X</div>' +
                '</div>');
            $(".clear-"+clear).animate({marginTop: 0}, 800, function () {
                setTimeout(function () {
                    $(".clear-"+clear).animate({marginTop: -($(".clear-"+clear).height()+16)}, 800, function () {
                        $(".clear-"+clear).remove();
                    });
                }, show_time);
            });
        }, time);
    }
}
$(document).on("click", ".kd-notice-close", function () {
    let that = $(this);
    that.parent().slideUp(300);
});



// 黑名单关键词匹配检测机制
// 短string中匹配
var black_keykword_state = 0; // 黑名单关键词出现次数
var check_black_times = 0; // 运行函数黑名单关键词次数
function check_input_black_keyword(string, black_keyword_array){
    console_log("check_black_times-0="+check_black_times);
    for (var j=0; j<black_keyword_array.length; j++){
        var has_key = black_keyword_array[j].black_keyword;
        if (string.indexOf(has_key) !== -1){ // 首次出现过该关键词

            make_notice([
                {
                    "msg": "发现黑名单关键词="+has_key,
                },
            ], (100*j+5000));

            black_keykword_state += 1;
        }else {
            console_log("false未匹配到关键词");
        }

        if (j === black_keyword_array.length-1){
            check_black_times +=1;
            console_log("check_black_times-1="+check_black_times);
        }

    }

}

// 耗时string中匹配
var black_text_keykword_state = 0; // 黑名单关键词出现次数
var check_text_black_times = 0; // 运行函数黑名单关键词次数
function check_text_black_keyword(string, black_keyword_array, _class, call_func){
    console_log("check_text_black_times-0="+check_text_black_times);
    var new_text = string;
    for (var j=0; j<black_keyword_array.length; j++){
        var has_key = black_keyword_array[j].black_keyword;
        if (string.indexOf(has_key) !== -1){ // 首次出现过该关键词
            make_notice([{
                "msg": "发现黑名单关键词="+has_key,
            },], (100*j+5000));

            var new_key = '<span class="high-black-key">'+has_key+'</span>&nbsp;';
            new_text = new_text.replace(new RegExp(has_key, "g"), new_key); // 全局替换

            black_text_keykword_state += 1;
        }else {
            console_log("false未匹配到关键词");
        }

        if (j === black_keyword_array.length-1){
            check_text_black_times +=1;
            console_log("check_text_black_times-1="+check_text_black_times);
            if (call_func){
                call_func(_class, new_text);
            }else {
                console_log("跳过call_func(_class, new_text)");
            }

        }

    }
}


function alert_excel() {
    alert_txt("请保存Excel", 5000);
}


function cal_back_top() {
    let window_height = window.innerHeight;
    let scroll_height = $(window).scrollTop();
    let load_height = scroll_height - window_height + 300;
    // console_log([window_height, scroll_height, load_height]);
    if (load_height > 0){ // 滑过一个屏幕距离
        $(".back-top").removeClass("hide");
    }else {
        $(".back-top").addClass("hide");
    }
}
$(document).on("click", ".back-top", function () {
    $("html, body").animate({scrollTop: 0}, "fast");
});



/*
* 处理上传图片
*   <input type="file" class="article-cover" onchange="upload_img_n(this, 100)" accept="image/gif, image/jpg, image/png, image/jpeg, image/bmp, image/jpe" />
    <div class="upload_img_div" id="img-preview-box-100">
        <img class="img-preview-style img-n-100" data-img_name="" src="" alt="未上传图片" />
    </div>
* */

function upload_img_n(obj, n) {
    let files = obj.files;
    let id = "img-preview-box-"+n;
    let el = document.getElementById(id);

    img_file_reader_n(el, files, n);
}
function img_file_reader_n(el, files, n) {
    let img_class = "img-n-"+n;
    for (let i = 0; i < files.length; i++) {
        let img = document.createElement("img");
        img.classList.add(img_class);
        img.classList.add("img-preview-style");
        img.classList.add("img-upload-style");
        img.setAttribute("data-img_name", "");
        el.innerHTML = "";
        el.appendChild(img);
        let reader = new FileReader();
        reader.onload = function(e) {
            let base64 = e.target.result;
            img.src = base64;

            upload_cover_img(base64, img_class, n); // 上传图片
        };
        reader.readAsDataURL(files[i])
    }
}
function upload_cover_img(base64, _class){
    alert_txt("正在上传图片..", "long");

    if (!_class){
        alert_txt("未指定图片对于class", 2000);
        return;
    }

    // 先上传封面
    $.ajax({
        url: api_url+"app/save_base64_img",
        type: "POST",
        dataType: "json",
        async: true,
        data: { // 字典数据
            login_name: login_name,
            login_token: login_token,
            app_class: app_class,
            upload_token: 'laotie666',

            base64_img: base64,
        },
        success: function(back, status){

            // 数据转换为json
            let data = "";
            let text = "";
            if(typeof back === "string"){
                data = JSON.parse(back);
                text = back;
            } else {
                data = back;
                text = JSON.stringify(back);
            }
            console_log("类型：" + typeof back + "\n数据：" + text +"\n状态：" + status + "。");

            // 解析json
            if (data.state===0){
                console_log(data.msg);
                alert_txt("上传失败", 2500);

                $("."+_class).attr("src", "");
                $("."+_class).attr("data-img_name", "");
                $("."+_class).attr("alt", "图片上传失败");
            }else if (data.state===1) {
                console_log(data.msg);

                let img_src = data.content.img; // 默认取x3高画质压缩
                let domain = data.content.qiniu_info.qiniu_domain[0]; // 七牛绑定的域名
                console_log([img_src, domain]);
                console_log("==单张图片上传成功，开始执行img的js来源替换==");

                $("."+_class).attr("src", domain + img_src);
                $("."+_class).attr("data-img_name", img_src);
                $("."+_class).attr("alt", "图片地址404");

                console_log("img_class="+_class);
                console_log($("."+_class));

                alert_txt("该图片上传成功", 1500);

            }else if (data.state===2) {
                console_log(data.msg);
                alert_txt("图片格式错误，不是正确编码的base64图片", 2500);

                $("."+_class).attr("src", "");
                $("."+_class).attr("data-img_name", "");
                $("."+_class).attr("alt", "图片上传失败");
            }else {
                alert_txt("未知错误", 2500);

                $("."+_class).attr("src", "");
                $("."+_class).attr("data-img_name", "");
                $("."+_class).attr("alt", "图片上传失败");
            }
        },
        error: function (xhr) {
            console.log(xhr);
            alert_txt("接口请求错误或者网络不通", 2500);
        }
    });

}


function replace_string(string, bad_value, nice_value) {
    let reg = "/"+ bad_value +"/g";
    let new_string = string.replace(eval(reg), nice_value);

    return new_string;
}


function replace_img_domain(timer, domain, max, num) {
    if (!domain){domain = "还没有设置主域名replace_img_domain(timer, domain)/"}
    if (!max){max = 100;}
    if (max>10000){max = 10000}
    if (max<50){max=50}
    if (!num){num=0;}
    if (!timer){timer = 200;}
    if (timer<90){timer = 90;}
    if (timer > 2000){timer = 2000;}

    let img = document.getElementsByTagName("img");
    let len = img.length;
    let has_string = "bkt.clouddn.com/";

    for (let i=0; i<len; i++){
        let that_src = img[i].getAttribute("src");

        let has_eq = that_src.indexOf(has_string);
        if (has_eq !== -1){ // 含有
            let img_name = that_src.split("m/")[1];
            let new_img = domain + "" +img_name;

            img[i].classList.add("auto_replace_src");
            img[i].setAttribute("src", new_img);

        }else {
            console.log("replace_img_domain()-跳过");
        }

    }

    if (num < max){
        setTimeout(function () {
            num += 1;
            replace_img_domain(timer, domain, max, num);
        }, timer);
    }else {
        console.log("终止，num="+num);
    }

}

// replace_img_domain(500, "//img.ggvs.cn/" );
