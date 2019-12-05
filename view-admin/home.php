<?php
$title = '主页(统计)'; // 模块标题，每个页面自定义
$page_path = dirname(__FILE__); // 项目index的根目录
include $page_path.'/common/head.php';
?>


<!-- start-div -->
<div class="view-div clear" id="view-admin-div">

    <!--导航区-->
    <div class="dir-div">

    </div>

    <!--功能区-->
    <div class="user-list-tab-div">
        <div class="float-left">
            <div class="tab-item select-none tab-item-active">用户统计</div>

        </div>

        <div class="clear"></div>
    </div>

    <div class="table-div tab-div">
        <table id="backViewTable1" class="table table-striped">
            <thead>
            <tr>
                <th>ID</th>
                <th>用户名</th>
                <th>操作</th>

            </tr>
            </thead>
            <tbody class="tbody-style list-xxx">

            <!---->
            <tr>
                <td>-</td>
                <td>-</td>
                <td>-</td>

            </tr>

            </tbody>
        </table>
    </div>



</div>
<!-- end-div -->


<script>


    function test1() {

        let string = "aaskadaknkkmdamlmdq12819sjansjk";
        let bad_string = "m";
        let nice_string = "888";

        console_log(replace_string(string, bad_string, nice_string));

    }


</script>



<script>

    // 页面数据入口，登录用户的token验证通过后才会执行此函数
    function page_data_init(){
        test1();


    }

</script>


<?php
include $page_path.'/common/foot.php';
?>

