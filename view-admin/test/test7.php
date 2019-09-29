<?php

$base64 = base64_encode('abc123');

// 验证base64，并返回base64对应的[是否是base64编码，文件类型、文件格式]
function check_base64($base64){

    $key = ['-', '.']; // 防止application时匹配不出来后缀，需要先过滤特殊字符

    if ($base64 == base64_encode(base64_decode($base64))){
        $has = true;
        $class = 'string';
        $ext = [''];
    }else if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64, $result)){
        $has = true;
        $class = 'image';
        $ext = [$result[2]];
    }else if (preg_match('/^(data:\s*text\/(\w+);base64,)/', $base64, $result)){
        $has = true;
        $class = 'text';
        $ext = [$result[2]];
    }else if (preg_match('/^(data:\s*audio\/(\w+);base64,)/', $base64, $result)){
        $has = true;
        $class = 'audio';
        $ext = [$result[2]];
    }else if (preg_match('/^(data:\s*video\/(\w+);base64,)/', $base64, $result)){
        $has = true;
        $class = 'audio';
        $ext = [$result[2]];
    }else if (preg_match('/^(data:\s*application\/(\w+);base64,)/', filter_key($key, $base64)[0], $result)){
        $has = true;
        $class = 'application';
        $ext = [$result[2], $key];
    }else{ // 未知
        $has = false;
        $class = '';
        $ext = [''];
    }

    return [$has, $class, $ext];
}
// 过滤特殊字符
function filter_key($key_array, $str){
    foreach ($key_array as $value){
        $str = str_replace($value, "", $str);
    }
    return [$str, $key_array];
}

$res = check_base64("data:application/octetstream;base64,77u/W0ludGVyb…lc2lkPTZGNDc3NUYzMDJFRDg3MjIhMjA");

var_dump($res);

echo "<hr/>";

$res = check_base64("data:text/css;base64,LyoNCiAgIOWxgOmDqOagt+W8j");

var_dump($res);

