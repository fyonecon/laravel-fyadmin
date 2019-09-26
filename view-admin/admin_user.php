<?php
$title = '管理管理员'; // 模块标题，每个页面自定义
include './common/head.php';
?>


<!-- start-div -->
<div class="view-div clear" id="view-admin-div">

    <!--导航区-->
    <div class="dir-div">
        <a class="dir-a">系统配置</a>
        <span class="dir-span"> > </span>
        <a class="dir-a">管理用户</a>
    </div>

    <!--功能区-->
    <div class="user-list-tab-div">
        <div class="float-left">
            <div class="tab-item select-none tab-item-active">用户列表</div>
            <div class="tab-item select-none">管理员列表</div>
        </div>

        <div class="float-right">
            <button class="btn level-n level-2-not-do btn-right btn-success btn-sm add-user-info" href="#" role="button">新增用户</button>
            <button class="btn level-n level-2-not-do btn-right btn-success btn-sm add-admin-info" href="#" role="button" style="cursor: no-drop;" onclick="alert_txt('不可操作。', 3000);">新增管理员</button>
        </div>

        <div class="clear"></div>
    </div>

    <!--用户列表-->
    <div class="user-list-div tab-div level-n level-2-not-do">

        <div class="list">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>用户中文名</th>
                        <th>用户登录名</th>
                        <th>用户密码</th>
                        <th>所属管理员</th>
                        <th>备注</th>
                        <th class="">操作</th>
                    </tr>
                </thead>
                <tbody class="tbody-style list-user-tbody">

                    <!---->
                    <tr>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td class="">
                            <span class="list-operation-span select-none bg-blue">修改</span>
                            <span class="list-operation-span select-none bg-red">删除</span>
                        </td>
                    </tr>

                </tbody>
            </table>
            <div class="navigation-div"></div>
        </div>

    </div>

    <!--管理员列表-->
    <div class="user-list-div tab-div hide level-n level-2-not-do">

        <div class="list">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>管理员中文名</th>
                    <th>管理员录名</th>
                    <th>管理员密码</th>
<!--                    <th>所属管理员</th>-->
                    <th>备注</th>
                    <th class="">操作</th>
                </tr>
                </thead>
                <tbody class="tbody-style list-admin-tbody">

                <!---->
                <tr>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td class="">
<!--                        <span class="list-operation-span select-none bg-blue">修改</span>-->
<!--                        <span class="list-operation-span select-none bg-red">删除</span>-->
                    </td>
                </tr>

                </tbody>
            </table>
        </div>

    </div>

    <!--填写信息-->
    <div class="user-info-div fixed-div-box hide level-n level-2-not-do">
        <div class="fixed-div-nav">
            <div class="fixed-div-title">新增用户</div>
            <div class="fixed-div-close"><i class="fa fa-close"></i></div>
            <div class="clear"></div>
        </div>
        <div class="fixed-div-content">
            <!---->
            <div class="input-div">
                <div class="input-title">用户中文名</div>
                <div class="important-tip">*</div>
                <input class="input-style input-user_name" value="" maxlength="20" type="text" placeholder="请输入"/>

                <div class="input-tips"><i class="fa fa-right-padding fa-warning"></i>便于区分用户</div>
                <div class="clear"></div>
            </div>
            <!---->
            <div class="input-div">
                <div class="input-title">用户登录名</div>
                <div class="important-tip">*</div>
                <input class="input-style input-user_login_name" value="" maxlength="20" type="text" placeholder="字母、数组，区分大小写，最大20位"/>
                <div class="input-tips"><i class="fa fa-right-padding fa-warning"></i>不能与已有用户登录名重复，格式：字母、数组，区分大小写，最大20位</div>
                <div class="clear"></div>
            </div>
            <!---->
            <div class="input-div">
                <div class="input-title">用户登录密码</div>
                <div class="important-tip">*</div>
                <input class="input-style input-user_login_pwd" value="" maxlength="18" type="password" placeholder="字母、数组，最大18位"/>
                <div class="input-tips"><i class="fa fa-right-padding fa-warning"></i>格式：母、数组，最大18位</div>
                <div class="clear"></div>
            </div>
            <!---->
            <div class="input-div">
                <div class="input-title">备注</div>
                <div class="important-tip tip-hide">*</div>
                <input class="input-style input-user_remark" value="" maxlength="" type="text" placeholder="请输入"/>
                <div class="input-tips"><i class="fa fa-right-padding fa-warning"></i>最多200字</div>
                <div class="clear"></div>
            </div>


            <div class="fixed-btn-div">
                <div class="btn add-user-btn btn-primary">新增用户</div>
                <div class="btn edit-user-btn btn-info hide" data-user_id="">修改用户信息</div>
            </div>
        </div>



    </div>

    <div class="div-bg hide"></div>

</div>
<!-- end-div -->


<script>

    // 增加用户
    function add_user(father_user_id, user_level, user_name, user_login_name, user_login_pwd, user_remark) {

        /*请求数据*/
        $.ajax({
            url: api_url+"admin/add_user",
            type: "POST",
            dataType: "json",
            async: true,
            data: { // 字典数据
                login_name: login_name,
                login_token: login_token,
                app_class: app_class,

                father_user_id: father_user_id,
                user_level: user_level,
                user_name: user_name,
                user_login_name: user_login_name,
                user_login_pwd: user_login_pwd,
                user_remark: user_remark,
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
                    setTimeout(function () {
                        close_fixed_user_div();
                    }, 2000);

                }
            },
            error: function (xhr) {
                console.log(xhr);
                alert_txt("接口请求错误或者网络不通", 2500);
            }
        });


    }

    // 修改用户
    function edit_user(user_id, user_name, user_login_name, user_login_pwd, user_remark) {

        /*请求数据*/
        $.ajax({
            url: api_url+"admin/edit_user",
            type: "POST",
            dataType: "json",
            async: true,
            data: { // 字典数据
                login_name: login_name,
                login_token: login_token,
                app_class: app_class,

                user_id: user_id,
                user_name: user_name,
                user_login_name: user_login_name,
                user_login_pwd: user_login_pwd,
                user_remark: user_remark,
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
                    refresh_page(2000);
                }
            },
            error: function (xhr) {
                console.log(xhr);
                alert_txt("接口请求错误或者网络不通", 2500);
            }
        });

    }

    // 删除用户
    function del_user(user_id) {

        /*请求数据*/
        $.ajax({
            url: api_url+"admin/del_user",
            type: "POST",
            dataType: "json",
            async: true,
            data: { // 字典数据
                login_name: login_name,
                login_token: login_token,
                app_class: app_class,

                user_id: user_id,
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

                    $(".user_id-"+user_id).hide(500).remove();
                }
            },
            error: function (xhr) {
                console.log(xhr);
                alert_txt("接口请求错误或者网络不通", 2500);
            }
        });

    }

    // 用户列表
    function list_user(page) {

        /*请求数据*/
        $.ajax({
            url: api_url+"admin/list_user",
            type: "POST",
            dataType: "json",
            async: true,
            data: { // 字典数据
                login_name: login_name,
                login_token: login_token,
                app_class: app_class,

                page: page,

                user_id: "all",
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


                    var user_id = 0;
                    var user_name = "";
                    var user_login_name = "";
                    var user_remark = "";
                    var create_time = 0;
                    var update_time = 0;
                    var father_user_id = 0;

                    $(".list-user-tbody").html("");

                    for (let i=0; i<datas.content.length; i++){
                        var info = datas.content[i];

                        user_id = info.user_id;
                        user_name = info.user_name;
                        user_login_name = info.user_login_name;
                        user_remark = info.user_remark;
                        create_time = info.create_time;
                        update_time = info.update_time;
                        father_user_id = info.father_user_id;

                        let div = '<tr class="user_id-'+ user_id +'" data-user_id='+ user_id +'>' +
                            '<td>'+ user_id +'</td>' +
                            '<td class="td-user_name">'+ user_name +'</td>' +
                            '<td class="td-user_login_name">'+ user_login_name +'</td>' +
                            '<td>（已加密）</td>' +
                            '<td class="father_user_id" data-father_user_id='+ father_user_id +'>'+ father_user_id +'</td>' +
                            '<td class="td-user_remark">'+ user_remark +'</td>' +
                            '<td class="">' +
                            '    <span class="list-operation-span select-none bg-blue btn-edit_user">修改</span>' +
                            '    <span class="list-operation-span select-none bg-red btn-del_user">双击删除</span>' +
                            '</td>' +
                            '</tr>';

                        $(".list-user-tbody").append(div);

                    }


                    // 处理分页
                    var page = datas.paging.page;
                    var limit = datas.paging.limit;
                    var count = datas.paging.data_count;
                    var tab = getThisUrlParam("", "tab");
                    tab = tab?tab:0;
                    var url = web_url+"user.php?nav=user#tab="+tab;
                    setTimeout(function () {
                        paginition(limit, count, url, "navigation-div", "navigation-page", "page_user", page)
                    }, 200);

                }
            },
            error: function (xhr) {
                console.log(xhr);
                alert_txt("接口请求错误或者网络不通", 2500);
            }
        });

    }


    // 管理员列表
    function list_admin(page) {

        /*请求数据*/
        $.ajax({
            url: api_url+"admin/list_admin",
            type: "POST",
            dataType: "json",
            async: true,
            data: { // 字典数据
                login_name: login_name,
                login_token: login_token,
                app_class: app_class,

                page: page,

                user_id: "all",
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

                    var user_id = 0;
                    var user_name = "";
                    var user_login_name = "";
                    var user_remark = "";
                    var create_time = 0;
                    var update_time = 0;
                    var father_user_id = 0;

                    $(".list-admin-tbody").html("");

                    for (let i=0; i<datas.content.length; i++){
                        var info = datas.content[i];

                        user_id = info.user_id;
                        user_name = info.user_name;
                        user_login_name = info.user_login_name;
                        user_remark = info.user_remark;
                        create_time = info.create_time;
                        update_time = info.update_time;
                        father_user_id = info.father_user_id;

                        let div = '<tr class="user_id-'+ user_id +'" data-user_id='+ user_id +'>' +
                            '<td>'+ user_id +'</td>' +
                            '<td class="td-user_name">'+ user_name +'</td>' +
                            '<td class="td-user_login_name">'+ user_login_name +'</td>' +
                            '<td>（已加密）</td>' +
                            // '<td class="father_user_id" data-father_user_id='+ father_user_id +'>'+ father_user_id +'</td>' +
                            '<td class="td-user_remark">'+ user_remark +'</td>' +
                            '<td>（不可操作）</td>'+
                            '</tr>';

                        $(".list-admin-tbody").append(div);

                    }

                }
            },
            error: function (xhr) {
                console.log(xhr);
                alert_txt("接口请求错误或者网络不通", 2500);
            }
        });

    }


</script>


<script>

    // 关闭窗口并初始化窗口
    function close_fixed_user_div() {

        $(".user-info-div").addClass("hide");
        $(".div-bg").addClass("hide");

        $(".input-user_name").val("");
        $(".input-user_login_name").val("");
        $(".input-user_login_pwd").val("");
        $(".input-user_remark").val("");

    }

    // 开启窗口
    function open_fixed_user_div(title) {
        $(".user-info-div").removeClass("hide");
        $(".div-bg").removeClass("hide");

        $(".fixed-div-title").text(title);
    }

    (function () {

        $(document).on("click", ".add-user-info", function () {

            open_fixed_user_div("新增用户信息");

        });

        $(document).on("click", ".fixed-div-close", function () {

            close_fixed_user_div();
            $(".add-user-btn").removeClass("hide");
            $(".edit-user-btn").addClass("hide");

        });

        // 新增用户信息
        $(document).on("click", ".add-user-btn", function () {
            alert_txt("正在提交..", "long");

            var father_user_id = login_id;
            var user_level = 2;
            var user_name = $(".input-user_name").val().trim();
            var user_login_name = $(".input-user_login_name").val().trim();
            var user_login_pwd = hex_md5($(".input-user_login_pwd").val().trim()); // 不管后台加不加密+
            var user_remark = $(".input-user_remark").val().trim();

            if (!user_name){

                alert_txt("请填写用户中文名", 2000);
                return;
            }
            if (!user_login_name){

                alert_txt("登录名不能为空", 2000);
                return;
            }
            if (!user_login_pwd){

                alert_txt("密码不能为空", 2000);
                return;
            }


            var test_data = {
                father_user_id: father_user_id,
                user_level: user_level,
                user_name: user_name,
                user_login_name: user_login_name,
                user_login_pwd: user_login_pwd,
                user_remark: user_remark,
            };
            console_log(test_data);

            add_user(father_user_id, user_level, user_name, user_login_name, user_login_pwd, user_remark);

        });

        // 修改用户
        $(document).on("click", ".btn-edit_user", function () {
            var that = $(this);
            var user_id = that.parent("td").parent("tr").data("user_id");
            console_log(user_id);

            open_fixed_user_div("修改用户信息");

            $(".add-user-btn").addClass("hide");
            $(".edit-user-btn").removeClass("hide");

            // 传递user_id
            $(".edit-user-btn").data("user_id", user_id);

            // 自动填写数据
            var selector = $(".user_id-"+user_id);
            $(".input-user_name").val(selector.find(".td-user_name").text());
            $(".input-user_login_name").val(selector.find(".td-user_login_name").text());
            $(".input-user_login_pwd").val("");
            $(".input-user_remark").val(selector.find(".td-user_remark").text());

        });
        $(document).on("click", ".edit-user-btn", function () {
            var that = $(this);
            var user_id = $(".edit-user-btn").data("user_id");
            console_log(user_id);

            if (!user_id){
                alert_txt("user_id为空，无法指定具体哪条数据", "long");
                return;
            }

            alert_txt("正在提交..", "long");

            var user_name = $(".input-user_name").val().trim();
            var user_login_name = $(".input-user_login_name").val().trim();
            var user_login_pwd = hex_md5($(".input-user_login_pwd").val().trim()); // 不管后台加不加密+
            var user_remark = $(".input-user_remark").val().trim();

            if (!user_name){

                alert_txt("请填写用户中文名", 2000);
                return;
            }
            if (!user_login_name){

                alert_txt("登录名不能为空", 2000);
                return;
            }
            if (!user_login_pwd){

                alert_txt("密码不能为空", 2000);
                return;
            }

            var test_data = {
                user_id: user_id,
                user_name: user_name,
                user_login_name: user_login_name,
                user_login_pwd: user_login_pwd,
                user_remark: user_remark,
            };
            console_log(test_data);

            edit_user(user_id, user_name, user_login_name, user_login_pwd, user_remark)

        });

        // 删除用户
        $(document).on("dblclick", ".btn-del_user", function () {
            var that = $(this);
            var user_id = that.parent("td").parent("tr").data("user_id");

            console_log(user_id);
            del_user(user_id);

        });


    })();


</script>


<script>

    // 处理分页
    let page_user = getThisUrlParam("", "page_user");
    let page_admin = getThisUrlParam("", "page_admin");

    // 页面数据入口
    function page_data_init(){
        list_user(page_user);
        list_admin(page_admin);
    }

</script>


<?php
include './common/foot.php';
?>

