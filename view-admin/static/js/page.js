

// 渲染nav选中
(function () {
    var nav = getThisUrlParam("", "nav");
    var div = ".nav-" + nav;
    setTimeout(function () {
        $(div).addClass("nav-item-active");
    }, 200);

})();



(function () {

    $(document).on("dblclick", ".user-logout", function () {
        alert_txt("正在退出..", "long");
        delCookie("login_name");
        delCookie("login_token");
        delCookie("login_id");

        refresh_page(1500);

    });

    $(document).on("dblclick", ".user-all-logout", function () {
        alert_txt("正在退出..", "long");

        /*请求数据*/
        $.ajax({
            url: api_url+"all_user_layout",
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
                var datas = data = "";
                if(typeof back === "string"){
                    datas = JSON.parse(back);
                    data = back;
                } else {
                    datas = back;
                    data = JSON.stringify(back)
                }
                console_log("类型：" + typeof back + "。\n数据：" + data +"。\n状态：" + status + "。");

                // 解析json
                if (datas.state===0){
                    alert_txt(datas.msg, 2000);

                }else if (datas.state===1) {
                    console_log(datas.msg);
                    alert_txt(datas.msg, 2000);

                    refresh_page(1500);
                }
            },
            error: function (xhr) {
                console.log(xhr);
                alert_txt("接口请求错误或者网络不通", 2500);
            }
        });

    });

    $(document).on('click', ".page-nav-title", function () {
        refresh_page(100);

    });

})();


// 检测是否api可用，或者叫是否登录过。
(function () {

    setTimeout(function () {
        if ($(".loading-div").hasClass("flex-center")){
            alert_txt("网速慢", 3000);
            return false;
        }else {
            console_log("前端检测正常-跳过提醒");
        }
    },15000);

    setTimeout(function () {
        if ($(".loading-div").hasClass("flex-center")){
            alert_txt("加载慢。可能系统运行遇到了错误。","long");
            setTimeout(function () {
                window.location.reload();
            }, 5000);
            return false;
        }else {
            console_log("系统检测正常-跳过提醒");
        }
    },25000);

})();




/*
* 开始-tab切换
* */

// tab切换
$(document).on("click", ".tab-item", function () {
    let that = $(this);
    let index = that.index();

    btn_url(index);
});

/*
* 配置方法：
*   1. 保证url_num数组的值与 class="location-div"中有对应的专属class。比如：url0
*   2. 调用do_you()函数
* */
const div_all_class = "tab-div"; // 该块的一个公共class
const item_all_class = "tab-item";
const item_class_active = "tab-item-active";
const div_hide = "hide"; // display=none的css，class名。 用于隐藏不去显示的div，来实现div定位
const parameter = "tab"; // 参数key
let url_num = []; // 参数value

function btn_url(p) { /*p值通常为下一个url_num[i]*/
    console_log("location=p="+p);
    write_url(p); // 更新参数value
    url_location(); // 更新视图显示
    // 其他
    try{
        do_url(p);
    }catch (e) {
        console_log("请调用function do_url(p){console.log(p);}来完成操作");
    }
}


/*
* 动处理当前url中div的位置
* */
function do_url(index) {
    console_log("location=点击的tab的index="+index);
    $(".tab-item").eq(index).addClass("tab-item-active").siblings(".tab-item").removeClass("tab-item-active");
    $(".tab-div").eq(index).removeClass("hide").siblings(".tab-div").addClass("hide");

    // 其他

}
function write_url(p) { /*将位置写入url*/
    let state = {page: p};
    let url_data = "#"+ parameter +"=" + p; // 需要更改的url地址（参数）
    push_new_url(state, url_data);
}
function url_location() { /*根据位置标记展示div*/
    let location = getThisUrlParam(window.location.href, parameter)*1;
    if (!location) {
        console_log("location=空"+parameter);
        write_url(url_num[0]); // 默认位置
        location = url_num[0];
    } else {
        console_log("location="+parameter+"=" + location);
    }

    do_url(location);

    // let location_div = document.getElementsByClassName(div_all_class);
    // let item_div = document.getElementsByClassName(item_all_class);
    // for (let i = 0; i < location_div.length; i++) { /*处理视图*/
    //
    //     location_div[i].classList.add(div_hide);
    //     item_div[i].classList.remove(item_class_active);
    //
    //     if (location_div[i].className.indexOf(location) > -1) {
    //         console_log("location=发现"+parameter+"地址=" + i);
    //         location_div[i].classList.remove(div_hide);
    //         item_div[i].classList.add("tab-item-active");
    //     }
    // }
    // setTimeout(function () { // 根据url参数展示对应的模块而隐藏其他模块
    //     try {
    //         item_div[location].classList.add(item_class_active);
    //         location_div[location].classList.remove(div_hide);
    //     }catch (e) { // tab标号超过范围时
    //         try {
    //             item_div[0].classList.add(item_class_active);
    //             location_div[0].classList.remove(div_hide);
    //         }catch (e) {
    //             console_log("无对应location");
    //         }
    //     }
    //
    // }, 100);
}

// 初始化参数
function init_url_location() {
    for (let i=0; i< $(".tab-item").length; i++){
        url_num.push(i);
    }
    setTimeout(function () {
        console_log("url_num="+url_num);
        if (!url_num){
            console_log("url_num=空="+url_num);
        } else {
            url_location(); // 进入页面时的位置初始化
        }
    }, 50);
}

(function () {
    init_url_location();
})();

/*向url写入新的div位置*/
function push_new_url(state, url_data){
    let _state = state;
    let _title = ""; // 默认为空即可
    let _url_data = url_data; // 需要更改的url地址（参数）
    history.pushState(_state, _title, _url_data); // 更新url参数

}
/*处理历史返回*/
window.onpopstate = function(e){
    if(e.state){
        //console.log(e.state); // 之前写入的url参数
        // 其他
        url_location(); // 寻找某个div位置
    }else{
        //console.log("历史返回全部用完");
        url_location(); // 寻找某个div位置
    }
};

/*结束-切换*/





(function () {

    $(document).on("mouseenter", ".sys-nav", function () {
        console_log("in1");
        var that = $(this);
        that.find(".fa-caret-down").removeClass("transform-0").addClass("transform-180");
        //that.find(".nav-list-item-box").addClass("animated");
        that.find(".nav-list-item-box").show(1000);
    });
    $(document).on("mouseleave", ".sys-nav", function () {
        console_log("out1");
        var that = $(this);
        that.find(".fa-caret-down").removeClass("transform-180").addClass("transform-0");
    });

    $(document).on("mouseenter", ".page-nav-user", function () {
        console_log("in1");
        var that = $(this);
        that.find(".fa-caret-down").removeClass("transform-0").addClass("transform-180");
        that.find(".page-nav-user-list").addClass("animated bounceInRight");
    });
    $(document).on("mouseleave", ".page-nav-user", function () {
        console_log("out1");
        var that = $(this);
        that.find(".fa-caret-down").removeClass("transform-180").addClass("transform-0");
    });


    var high_active = getThisUrlParam("", "nav");
    if (high_active === "sys_config"){
        $(".sys-nav").addClass("nav-item-active");
    }else {
        console_log("sys_config="+high_active);
    }


})();
