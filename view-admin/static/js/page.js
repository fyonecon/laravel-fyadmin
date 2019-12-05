

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
const parameter = "tab";
function url_location() { // 更新视图
    let location = getThisUrlParam("", parameter)*1;
    console_log("操作切换tab="+location);

    $(".tab-item").eq(location).addClass("tab-item-active").siblings(".tab-item").removeClass("tab-item-active");
    $(".tab-div").eq(location).removeClass("hide").siblings(".tab-div").addClass("hide");

}
function init_url_location() {
    if ($(".tab-item").length<=1){
        console_log("跳过tab自动渲染");
    }else {
        url_location();
    }
}
// 点击tab切换
$(document).on("click", ".tab-item", function () {
    let that = $(this);
    let index = that.index();

    if ($(".tab-item").length<=1){
        console_log("跳过url渲染");
    }else {
        window.location.hash = "#"+parameter+"="+index; // 更新url
        setTimeout(function () { // 更新视图
            init_url_location();
        }, 20);
    }
});
(function () {
    init_url_location(); // 初始化
})();
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
    }else if(high_active === "cswd_config"){
        $(".cswd_config").addClass("nav-item-active");
    }else {
        console_log("sys_config="+high_active);
    }


})();



$(document).on("click", ".select-item-span", function () {
    let that = $(this);
    let that_id = that.data('item_id');
    console_log(that_id);

    if (that.hasClass("select-item-span-active")){
        that.removeClass("select-item-span-active");
        console_log("remove");
    }else {
        if (!that.hasClass("select-item-span-more")){
            console_log("单选模式");
            that.siblings().removeClass("select-item-span-active"); // 则单选
        }else {
            console_log("多选模式");
        }

        that.addClass("select-item-span-active");
        console_log("add");
    }

});


/*
*   页面载入后和滚动条运动调用点
* */
window.onload = function(){
    cal_back_top();

};
window.onscroll = function () {
    cal_back_top();

};

