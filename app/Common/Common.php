<?php
/*
 * 不需要封装的公共函数，公共参数配置
 *
 * */



function test_common($test){
    return time()."test-".$test;
}


/**
 *@todo: 判断是否为post
 */
if(!function_exists('is_post')){
    function is_post()
    {
        return isset($_SERVER['REQUEST_METHOD']) && strtoupper($_SERVER['REQUEST_METHOD'])=='POST';
    }
}

/**
 *@todo: 判断是否为get
 */
if(!function_exists('is_get')){
    function is_get()
    {
        return isset($_SERVER['REQUEST_METHOD']) && strtoupper($_SERVER['REQUEST_METHOD'])=='GET';
    }
}

/**
 *@todo: 判断是否为ajax
 */
if(!function_exists('is_ajax')){
    function is_ajax()
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtoupper($_SERVER['HTTP_X_REQUESTED_WITH'])=='XMLHTTPREQUEST';
    }
}

/**
 *@todo: 判断是否为命令行模式
 */
if(!function_exists('is_cli')) {
    function is_cli()
    {
        return (PHP_SAPI === 'cli' OR defined('STDIN'));
    }
}


// 返回重要目录的绝对路径
function laravel_path_info(){
    $path = [
        base_path(),
        storage_path(),
        $_SERVER['DOCUMENT_ROOT'],
        [
            __DIR__,
            config_path(),
            app_path(),
        ],
    ];

    return $path;
}


// 密码加密算法，非对称
function pwd_encode($string){
    $salt = '-PwD2019';
    $encode = md5($string.$salt);

    return $encode;
}

// 统一日期格式，2019/1/5或2019/01/05或2019-1-5或2019-01-05统一保存成20190105
function to_time($_time){

    return date('YmdHis', strtotime($_time));
}

// 将时间转换成2019-01-05
function date_time($to_time){

    return date('Y-m-d H:i:s', strtotime($to_time));
}

// 生成token
// token = 时间戳&随机字母数组
function make_token(){
    $_time = time();
    $_rand = get_rand_string(17, 23);
    $token = $_time.'#&'.$_rand;
    return $token;
}

// 分解token
function split_token($token){
    $back = explode('#&', $token);
    return $back;
}

// 分页每页数据量
function page_limit(){

    return 30;
}

// 获取固定长度的数字字母随机数
function get_rand_string($len, $chars=null){
    if (is_null($chars)){
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
    }
    mt_srand(10000000*(double)microtime());
    for ($i = 0, $str = '', $lc = strlen($chars)-1; $i < $len; $i++){
        $str .= $chars[mt_rand(0, $lc)];
    }
    return $str;
}

// 二维，根据某个键的数值排序
function order_key_array($array, $key, $order){
    if (!$array){return [];}
    if (!$order){$order = 'desc';}

    // 选择排序法
    $temp = 0;
    for($i = 0;$i < count($array) - 1;$i++){
        $minVal = $array[$i][$key]; //假设$i就是最小值
        $minValIndex = $i;
        for($j = $i+1;$j < count($array);$j++){
            if ($order == 'desc'){
                if($minVal < $array[$j][$key]){ //从小到大排列
                    $minVal = $array[$j][$key]; //找最大值
                    $minValIndex = $j;
                }
            }else{
                if($minVal > $array[$j][$key]){ //从小到大排列
                    $minVal = $array[$j][$key]; //找最小值
                    $minValIndex = $j;
                }
            }
        }
        $temp = $array[$i][$key];
        $array[$i][$key] = $array[$minValIndex][$key];
        $array[$minValIndex][$key] = $temp;
    }
    $new_array = $array;

    return $new_array;
}


// 利用数组去重数组+json这样的一维数组，一般为直接从数据库查询的数组结果，去重某个键
// group_array(未去重数组, 要去重的json键名)
// 服务器环境不能使用group语法,所以做这个去重
function group_array($info, $db_key){

    $have = [];
    $array = [];
    for($m=0; $m<count($info); $m++){
        $has_id = $info[$m][$db_key];
        $array[] = $has_id;
    }
    $array = array_unique($array); // 返回 索引键=>id
    foreach ($array as $key=>$value){
        $have[] = $info[$key];
    }

    return $have;
}


// 两个键去重，两个键为相同值时去重
function group_arrays($info, $db_key1, $db_key2){

    $have = [];
    $array = [];
    $index = [];

    for($m=0; $m<count($info); $m++){
        $has1 = $info[$m][$db_key1];
        $has2 = $info[$m][$db_key2];

        if (in_array([$has1=>$has2], $array)){
            // 存在则跳过
        }else{
            $array[] = [$has1=>$has2];
            $index[] = $m;
        }
    }

    foreach ($index as $value){
        $have[] = $info[$value];
    }

    return $have;
}

