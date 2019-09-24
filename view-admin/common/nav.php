<?php



?>


<div class="view-nav page-nav select-none">

    <div class="page-nav-title pointer" data-toggle="tooltip" data-placement="bottom" title="点击可刷新页面"><?=$sys_name?></div>

    <a class="nav-a" href="<?=$web_url?>home.php?nav=home">
        <div class="nav-item nav-home"><i class="fa fa-right-padding fa-bar-chart"></i><?=$home?></div>
    </a>

    <div class="nav-item nav-list-box level-n level-2-not-do sys-nav">
        <div><i class="fa fa-right-padding fa-asterisk"></i>系统配置&nbsp;<i class="fa fa-left-padding fa-caret-down"></i></div>
        <div class="nav-list-item-box nav-hide">
            <a class="nav-a level-n level-2-not-do" href="<?=$web_url?>admin_user.php?nav=sys_config">
                <div class="nav-list-item-style"><i class="fa fa-right-padding fa-address-book"></i><?=$admin_user?></div>
            </a>
        </div>
    </div>


    <div class="page-nav-user">
        <div><span class="user-name zh-name">（未知用户）</span><i class="fa fa-left-padding fa-caret-down"></i></div>
        <div class="page-nav-user-list hide">
            <div class="page-nav-list-item user-logout red">双击退出登录</div>
            <div class="page-nav-list-item user-all-logout">双击所有登录下线</div>
        </div>
    </div>

    <div class="clear"></div>
</div>
