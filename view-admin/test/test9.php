<?php


// 判断是否是url链接
function is_url($_url){ // 耗时任务
    $url = $_url;
    $pattern="#(http|https)://(.*\.)?.*\..*#i";
    if(preg_match($pattern, $url)){
        $back = true;
    }else{
        $back = false;
    }
    return $back;
}


print_r(is_url('http://baiud.com?i=1'));
