<?php

//exec('curl -d "debug_key1=0" http://192.168.131.133/laravel60/public/index.php/admin/login_check', $out, $sign=get_millisecond());
//print_r([$out[0], $sign]);


/*
 * 利用exec实现非阻塞请求，提高请求20%的容量
 * 1. php.ini需要去除disable_functions=exec来开启可使用exec函数
 * 2. 利用了“命令行+api+参数”的请求过程，最终返回api的结果
 * 3. 注意大多数命令行win与linux的不同，混用可能会报错
 *
 * exec_non_blocking($api, 参数键值对数组, 标记运行了什么函数)
 * */
function exec_non_blocking($api, $data_array, $sign){

    $sign = $sign?$sign:get_millisecond();
    $data = '';

    foreach ($data_array as $key=>$value){
        $data = $data.$key.'='.$value.'&';
    }

    if (function_exists('exec')){
        try{
            exec("curl -d '$data' '$api'", $_out); // 注意参数、api都应该加引号
            $out = $_out[0];
            if (is_json($out)){
                $back = json_to_array($out);
            }else{
                $back = $out;
            }
        }catch (Exception $error){
            $back = $error;
        }

    }else{
        $back = 'php中的exec()函数未开启，请在php.ini需要去除disable_functions=exec来开启可使用exec()函数。';
    }

    return ['exec_data'=>$back, 'sign'=>$sign, 'test_data'=>[$api, $data_array], 'curl_way'=>'post']; // 统一返回json或string
}


// 测试
$data_array = [
    'debug_key'=>2
];
$sign = 0;
$back = exec_non_blocking('http://192.168.131.133/laravel60/public/index.php/admin/login_check', $data_array, $sign);

print_r($back);





/*开始-依赖函数*/
function get_millisecond() {
    list($microsecond , $time) = explode(' ', microtime()); //' '中间是一个空格
    return (float)sprintf('%.0f',(floatval($microsecond)+floatval($time))*1000);
}
function is_json($data = '', $assoc = false) {
    $data = json_decode($data, $assoc);
    if (($data && is_object($data)) || (is_array($data) && !empty($data))) {
        return true;
    }
    return false;
}
function json_to_array($object_data){
    return json_decode(json_encode($object_data),true);
}
/*结束-依赖函数*/
