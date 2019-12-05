<?php

$head_path = dirname(dirname(__FILE__)); // 项目index的根目录
include $head_path.'/common/config.php';
?>

<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

    <title><?=isset($title)?$title:'未知' ?>-<?=$sys_name?></title>
    <link rel="shortcut icon" href="<?=$file_url?>static/favicon.ico" type="image/x-icon"/>

    <meta name="robots" content="noindex, nofollow"/>
    <meta name="google" content="notranslate"/>
    <meta name="keywords" content="长沙文都、湖南文都"/>
    <meta name="description" content="长沙文都考研后台管理系统"/>
    <meta name="author" content="github.com/fyonecon" />

    <link href="<?=$file_url?>static/css/common.css?<?=$head_time?>" rel="stylesheet"/>
    <link href="<?=$file_url?>static/css/all.css?<?=$head_time?>" rel="stylesheet"/>
    <link href="<?=$file_url?>static/css/style.css?<?=$head_time?>" rel="stylesheet"/>

    <script src="<?=$file_url?>static/js/jquery-1.11.3.min.js" type="text/javascript"></script>

    <script src="<?=$file_url?>static/js/common.js?<?=$head_time?>" type="text/javascript"></script>
    <script src="<?=$file_url?>static/js/check.js?<?=$head_time?>" type="text/javascript"></script>
    <script src="<?=$file_url?>static/js/all.js?<?=$head_time?>" type="text/javascript"></script>
    <script src="<?=$file_url?>static/js/md5.js?<?=$head_time?>" type="text/javascript"></script>
    <script src="<?=$file_url?>static/js/qrcode.js?<?=$head_time?>" type="text/javascript"></script>

    <link href="<?=$file_url?>static/pl/bootstrap4.1.3-dist/css/bootstrap.min.css" rel="stylesheet"/>
    <script src="<?=$file_url?>static/pl/bootstrap4.1.3-dist/js/bootstrap.min.js" type="text/javascript"></script>

    <link rel="stylesheet" type="text/css" href="<?=$file_url?>static/pl/jc_date/jcDate.css" media="all" />
    <script type="text/javascript" src="<?=$file_url?>static/pl/jc_date/jquery-migrate-1.2.1.min.js"></script>
    <script type="text/javascript" src="<?=$file_url?>static/pl/jc_date/jQuery-jcDate.js" charset="utf-8"></script>

    <link href="<?=$file_url?>static/pl/font-awesome-4.7.0/css/font-awesome.min.css" rel="stylesheet"/>
    <link href="<?=$file_url?>static/css/animate.min.css" rel="stylesheet"/>
    <script src="<?=$file_url?>static/pl/swiper/swiper.min.js"></script>
    <link href="<?=$file_url?>static/pl/swiper/swiper.min.css?<?=$head_time?>" rel="stylesheet">

    <script>
        /*
        * js兼容到安卓5.1和iOS10.3
        * */
        const web_url = "<?=$web_url?>";
        const file_url = "<?=$file_url?>";
        const img_url = "<?=$img_url?>";
        const api_url = "<?=$api_url?>";
        const app_class = "<?=$app_class?>";

    </script>
</head>
<body class="<?=$head_time?> body">

<?php
include  $head_path.'/common/nav.php';
?>

<div class="loading-div flex-center select-none" id="loading-div">
    <div class="loading-hidden-bg"></div>
    <div class="loading-icon"></div>
</div>
