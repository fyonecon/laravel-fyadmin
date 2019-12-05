
<div class="view-nav page-nav select-none">

    <!---->
    <div class="page-nav-title pointer" data-toggle="tooltip" data-placement="bottom" title="点击可刷新页面"><?=$sys_name?></div>

    <!---->
    <a class="nav-a" href="<?=$web_url?>home.php?nav=home">
        <div class="nav-item nav-home"><i class="fa fa-right-padding fa-bar-chart"></i><?=$home?></div>
    </a>

    <!---->
    <a class="nav-a" href="<?=$web_url?>wendu_news.php?nav=wendu_news">
        <div class="nav-item nav-wendu_news"><i class="fa fa-right-padding fa-newspaper-o"></i><?=$wendu_news?></div>
    </a>

    <!---->
    <a class="nav-a" href="<?=$web_url?>ask_question_list.php?nav=ask_question_list">
        <div class="nav-item nav-ask_question_list"><i class="fa fa-right-padding fa-question-circle-o"></i><?=$ask_question_list?></div>
    </a>

    <!---->
    <a class="nav-a" href="<?=$web_url?>baoming.php?nav=baoming">
        <div class="nav-item nav-baoming"><i class="fa fa-right-padding fa-phone-square"></i><?=$baoming?></div>
    </a>

    <!---->
    <div class="nav-item nav-list-box level-n level-3-not-do cswd_config">
        <div><i class="fa fa-right-padding fa-desktop"></i>网站配置&nbsp;<i class="fa fa-left-padding fa-caret-down"></i></div>
        <div class="nav-list-item-box nav-hide">
            <!---->
            <a class="nav-a level-n" href="<?=$web_url?>teacher_staff.php?nav=cswd_config">
                <div class="nav-list-item-style"><i class="fa fa-right-padding fa-address-card-o"></i><?=$teacher_staff?></div>
            </a>
            <!---->
            <a class="nav-a level-n" href="<?=$web_url?>course_comment.php?nav=cswd_config">
                <div class="nav-list-item-style"><i class="fa fa-right-padding fa-table"></i><?=$course_comment?></div>
            </a>
            <!---->
            <a class="nav-a level-n" href="<?=$web_url?>course.php?nav=cswd_config">
                <div class="nav-list-item-style"><i class="fa fa-right-padding fa-table"></i><?=$course?></div>
            </a>
            <!---->
<!--            <a class="nav-a level-n" href="--><?//=$web_url?><!--baoming.php?nav=cswd_config">-->
<!--                <div class="nav-list-item-style"><i class="fa fa-right-padding fa-phone-square"></i>--><?//=$baoming?><!--</div>-->
<!--            </a>-->
            <!---->
            <a class="nav-a level-n" href="<?=$web_url?>campus.php?nav=cswd_config">
                <div class="nav-list-item-style"><i class="fa fa-right-padding fa-bullseye"></i><?=$campus?></div>
            </a>
            <!---->
<!--            <a class="nav-a level-n hide" href="--><?//=$web_url?><!--ask_question_list.php?nav=cswd_config">-->
<!--                <div class="nav-list-item-style"><i class="fa fa-right-padding fa-question-circle-o"></i>--><?//=$ask_question_list?><!--</div>-->
<!--            </a>-->
            <!---->
<!--            <a class="nav-a level-n hide" href="--><?//=$web_url?><!--ask_question_tag.php?nav=cswd_config">-->
<!--                <div class="nav-list-item-style"><i class="fa fa-right-padding fa-reorder"></i>--><?//=$ask_question_tag?><!--</div>-->
<!--            </a>-->
            <!---->
            <a class="nav-a level-n" href="<?=$web_url?>file_upload.php?nav=cswd_config">
                <div class="nav-list-item-style"><i class="fa fa-right-padding fa-file-archive-o"></i><?=$file_upload?></div>
            </a>
            <a class="nav-a level-n" href="<?=$web_url?>data_config.php?nav=cswd_config">
                <div class="nav-list-item-style"><i class="fa fa-right-padding fa-cubes"></i><?=$data_config?></div>
            </a>
        </div>
    </div>

    <!---->
    <div class="nav-item nav-list-box level-n level-2-not-do level-3-not-do sys-nav">
        <div><i class="fa fa-right-padding fa-asterisk"></i>系统配置&nbsp;<i class="fa fa-left-padding fa-caret-down"></i></div>
        <div class="nav-list-item-box nav-hide">
            <a class="nav-a level-n level-3-not-do" href="<?=$web_url?>admin_user.php?nav=sys_config">
                <div class="nav-list-item-style"><i class="fa fa-right-padding fa-address-book"></i><?=$admin_user?></div>
            </a>
        </div>
    </div>

    <!---->
    <div class="page-nav-user">
        <div><span class="user-name zh-name">（未知用户）</span><i class="fa fa-left-padding fa-caret-down"></i></div>
        <div class="page-nav-user-list hide">
            <div class="page-nav-list-item user-logout red">双击退出登录</div>
            <div class="page-nav-list-item user-all-logout">双击所有登录下线</div>
        </div>
    </div>

    <div class="clear"></div>
</div>
