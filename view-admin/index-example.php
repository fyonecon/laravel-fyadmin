<?php
$title = 'example'; // 模块标题，每个页面自定义
$page_path = dirname(__FILE__); // 项目index的根目录
include $page_path.'/common/head.php';
?>


<!-- start-div -->
<div class="view-div clear" id="view-admin-div">

    index-example




</div>
<!-- end-div -->



<script>

    // 页面数据入口，登录用户的token验证通过后才会执行此函数
    function page_data_init(){



    }

</script>


<?php
include $page_path.'/common/foot.php';
?>

