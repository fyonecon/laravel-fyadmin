
<?php
$foot_path = dirname(dirname(__FILE__)); // 项目index的根目录
include $foot_path.'/common/must.php';

?>

<div id="back-top" class="back-top select-none hide animated">Top</div>
<div class="foot-div select-none">
    <div class="foot-txt">2019/10-<?=$foot_time?> &nbsp;&nbsp;<?=$sys_foot?></div>
</div>

<script src="<?=$file_url?>static/js/page.js?<?=$head_time?>"></script>

</body>
</html>

