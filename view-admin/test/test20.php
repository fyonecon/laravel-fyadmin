<?php

// 替换二维数组中的手机号或其他字符串
function array_hide_value($array, $key, $_start, $_len, $tag = null){

    if (empty($tag)){
        $tag = '*';
    }

    $start = $_start-1;
    $len = $_len;

    $replace_tag = '';
    for ($t=0; $t<($_len); $t++){ // 生成连续占位符
        $replace_tag .= $tag;
    }

    for($i=0; $i<count($array); $i++){ // 替换键、值对
        $value = $array[$i][$key];
        $replace = substr($value, $start, $len);
        $new_value = str_replace($replace, $replace_tag, $value);
        echo $replace;
        echo '<hr>';
        $array[$i][$key] = $new_value; // 替换新值
    }

    return $array;
}

// 二维数组根据自定义时间格式来替换数组中的时间
function array_change_date($array, $key, $date_model=null){
    if (empty($date_model)){
        $date_model = 'Y-m-d';
    }

    for($i=0; $i<count($array); $i++){ // 替换键、值对
        $_time = $array[$i][$key];
        $new_value = date($date_model, strtotime($_time));
        $array[$i][$key] = $new_value; // 替换新值
    }

    return $array;
}

$array = [
    ['phone'=>'18112341131', 'name'=> 'test11', 'time'=> '20191102100702'],
    ['phone'=>'18212341232', 'name'=> 'test12', 'time'=> '20191112100702'],
    ['phone'=>'18312341333', 'name'=> 'test13', 'time'=> '20191103100702'],
    ['phone'=>'18412341434', 'name'=> 'test14', 'time'=> '20191222100702'],
    ['phone'=>'18512341535', 'name'=> 'test15', 'time'=> '20191202100702'],
];
$key = 'phone';

$res = array_hide_value($array, $key, 3, 7);

print_r($res);

$res2 = array_change_date($array, 'time', 'Y-m-d');
print_r($res2);
