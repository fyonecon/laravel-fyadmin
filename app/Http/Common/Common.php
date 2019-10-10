<?php
/**
 * 不需要封装的公共函数，公共参数配置
 * 框架任何文件都可调用
 * @param $txt
 * @return string
 */


function test_common($txt){
    return $txt.'=Common.php';
}

// 配置微信网页分享
function config_wxweb_share(){
    $info = [
        'appid'=> 'wxa10039a58c872225',
        'appsecret'=> '36f2a2f470add51145edf3179171502b',
    ];
    return $info;
}


// 配置七牛云
function config_qiniu(){
    $info = [
        'accessKey'=> 'sjLe9UAn3b8pNAqZW5CiKmNhiQYguqDr7_0_Iv7Q',
        'secretKey'=> 'CXUZS1F55dI6DimtMFOKLziJ4v34Wijo7NOnzu25',
        'domain'=> ['http:qiniu-test.meishid.cn/'], // 多域名卸载数组里即可
        'bucket'=> 'test', // bucket名字
    ];
    return $info;
}


// 配置自定义日志
function config_log(){
    $info = [
        'local_server_ip'=> server_info()['server_ip'], // 本机服务器IP ，动态获取
        'log_server_ip'=> '127.0.0.1', // 存放日志的服务器IP，没有的话就填127.0.0.1
        'timeout_day' => 14, // 多少天后自定删除，[7, 100]
        'log_key'=> debug_key(),
    ];
    return $info;
}


// 配置调试key，方便调试环境
function config_debug_key(){
    return date('dmY');
}

// 获取重要目录的绝对路径
function path_info(){
    $info = [
        'base_path'=> base_path(),
        'storage_path'=> storage_path(),
        'server_root'=> $_SERVER['DOCUMENT_ROOT'],
        'dir'=> __DIR__,
        'app_path'=> app_path(),
        'config_path'=> config_path(),
    ];

    return $info;
}


// 获取laravel项目主文件夹
function main_filename(){

    $len = strlen(path_info()['server_root']);
    $str = path_info()['base_path'];
    $res = substr($str, $len+1);

    return $res;
}

// 获取服务器信息
function server_info(){
    $server_ip =  $_SERVER['SERVER_ADDR'];
    $server_os = php_uname();
    $php_version = PHP_VERSION;
    $upload_size = get_cfg_var("upload_max_filesize")?get_cfg_var("upload_max_filesize"):"不允许上传文件";
    $do_timeout = get_cfg_var("max_execution_time")."秒";
    $server_time = date("Y-m-d H:i:s");

    $info = [
        'server_ip'=> $server_ip,
        'server_os'=> $server_os,
        'php_version'=> $php_version,
        'upload_size'=> $upload_size,
        'do_timeout'=> $do_timeout,
        'server_time'=> $server_time,
    ];

    return $info;
}


// 将laravel查询数据后返回的stdClass Object格式转换成array
function json_to_array($object_data){
    return json_decode(json_encode($object_data),true);
}
// 将array转换成json
function array_to_json($array_data){
    return json_encode($array_data, JSON_UNESCAPED_UNICODE);
}


// 密码加密算法，非对称
function pwd_encode($string){
    $salt = '-PwD2019_fy';
    $encode = md5($string.$salt);

    return $encode;
}


// 接口调试可跳过的安全检测的情况
function debug_key(){
    return config_debug_key();
}

// 生成毫秒时间戳
function get_millisecond() {
    list($microsecond , $time) = explode(' ', microtime()); //' '中间是一个空格
    return (float)sprintf('%.0f',(floatval($microsecond)+floatval($time))*1000);
}
// 统一日期格式，2019/1/5或2019/01/05或2019-1-5或2019-01-05统一保存成20190105010159
function to_time($_time){
    return date('YmdHis', strtotime($_time));
}
// 将时间转换成2019-01-05
function date_time($to_time){
    return date('Y-m-d H:i:s', strtotime($to_time));
}
// 当前格式化时间
function now_time(){
    return date('YmdHis');
}
// 开始时间
function start_time($day){
    return day_time($day);
}
// 结束时间
function end_time($day){
    return day_time($day);
}
// 计算过去N天的日期
function day_time($day){
    if ($day<0){
        $day = "$day";
    }else if ($day>0){
        $day = "+$day";
    }else{ // 包括其他格式错误的情况
        $day = "+0";
    }
    $back = [date("Y-m-d", strtotime("$day day")), date("YmdHis", strtotime("$day day")), $day];
    return $back;
}


// 生成token
// token = 时间戳#&随机字母数组
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


// post请求
function request_post($url='', $post_data=[]) { // 模拟post请求
    if (empty($url) || empty($post_data)) {
        return false;
    }
    $o = "";
    foreach ( $post_data as $k => $v ) {
        $o.= "$k=" . urlencode( $v ). "&" ;
    }
    $post_data = substr($o,0,-1);

    $post_url = $url;
    $curlPost = $post_data;
    $ch = curl_init();//初始化curl
    curl_setopt($ch, CURLOPT_URL,$post_url);//抓取指定网页
    curl_setopt($ch, CURLOPT_HEADER, 0);//设置header
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
    curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
    curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
    $data = curl_exec($ch);//运行curl
    curl_close($ch);

    //print_r($data);
    return $data;
}
// get请求
function request_get($get_url = ''){
    //初始化
    $curl = curl_init();
    //设置抓取的url
    curl_setopt($curl, CURLOPT_URL, $get_url);
    //设置头文件的信息作为数据流输出
    curl_setopt($curl, CURLOPT_HEADER, 1);
    //设置获取的信息以文件流的形式返回，而不是直接输出。
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    //执行命令
    $data = curl_exec($curl);
    //关闭URL请求
    curl_close($curl);

    return $data;
}


// 判断是否为post
if(!function_exists('is_post')){
    function is_post(){
        return isset($_SERVER['REQUEST_METHOD']) && strtoupper($_SERVER['REQUEST_METHOD'])=='POST';
    }
}
// 判断是否为get
if(!function_exists('is_get')){
    function is_get(){
        return isset($_SERVER['REQUEST_METHOD']) && strtoupper($_SERVER['REQUEST_METHOD'])=='GET';
    }
}
// 判断是否为ajax
if(!function_exists('is_ajax')){
    function is_ajax(){
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtoupper($_SERVER['HTTP_X_REQUESTED_WITH'])=='XMLHTTPREQUEST';
    }
}
// 判断是否为命令行模式
if(!function_exists('is_cli')) {
    function is_cli(){
        return (PHP_SAPI === 'cli' OR defined('STDIN'));
    }
}

// 判断是否是有效的url链接
function is_real_url($_url){ // 耗时任务
    $url = $_url;
    $response = get_headers($url);
    if(preg_match('/200/',$response[0])){
        //var_dump($response[0]);
        $back = true;
    }else{
        //var_dump('无效url资源！');
        $back = false;
    }
    return $back;
}

// 判断是否是url链接
function is_url($_url){
    $url = $_url;
    $pattern="#(http|https)://(.*\.)?.*\..*#i";
    if(preg_match($pattern, $url)){
        $back = true;
    }else{
        $back = false;
    }
    return $back;
}

/*
 * 判断字符串是否为 Json 格式
 * @param  string  $data  Json 字符串
 * @param  bool    $assoc 是否返回关联数组。默认返回对象
 * @return array|bool|object 成功返回转换后的对象或数组，失败返回 false
 */
function is_json($data = '', $assoc = false) {
    $data = json_decode($data, $assoc);
    if (($data && is_object($data)) || (is_array($data) && !empty($data))) {
        $back = true;
    }else{
        $back = false;
    }
    return $back;
}


// 过滤js
function filter_script($string_has_script){
    return preg_replace("/<(script.*?)>(.*?)<(\/script.*?)>/si","", $string_has_script);
}
// 过滤style
function filter_style($string_has_style){
    return preg_replace("/<(style.*?)>(.*?)<(\/style.*?)>/si","", $string_has_style);
}
// 过滤iframe
function filter_iframe($string_has_iframe){
    return preg_replace("/<(i?frame.*?)>(.*?)<(\/i?frame.*?)>/si","", $string_has_iframe);
}
// 清除html注释
function clear_note($string_has_note){
    return preg_replace("/<\!--.*?-->/si","", $string_has_note);
}
// 显示php、xml标签
function filter_php_xml($str_has_php){
    $str = str_replace("<?", "<_?", $str_has_php);
    $str = str_replace("?>", "?_>", $str);
    return $str;
}
// 一次性清除文章中的非法字符、标签
function filter_article($string){
    $string = filter_script($string);
    $string = filter_style($string);
    $string = filter_iframe($string);
    $string = clear_note($string);
    $string = filter_php_xml($string);

    return $string;
}


// 过滤特殊字符
function filter_key($key_array, $string){
    foreach ($key_array as $value){
        $string = str_replace($value, "", $string);
    }
    return [$string, $key_array];
}


// 验证Email
function check_email($string){
    if (!preg_match("/([\w\-]+\@[\w\-]+\.[\w\-]+)/", $string)) {
        $has = false;
    }else{
        $has = true;
    }
    return [$has, $string];
}
// 验证6-18位字以字母开头的符串中是否有字母、数字、下划线
function check_login_name($string){
    if(preg_match("/^[a-zA-Z]\w{5,17}$/", $string)) {
        $has = true;
    } else {
        $has = false;
    }
    return [$has, $string];
}
// 验证手机号
function check_phone($value){
    if(preg_match("/^1[3456789]{1}\d{9}$/", $value)){
        $has = true;
    }else{
        $has = false;
    }
    return [$has, $value];
}
// 验证身份证号
function check_id($value){
    if (!preg_match('/^\d{17}[0-9xX]$/', $value)) { //基本格式校验
        $has = false;
    }
    $parsed = date_parse(substr($value, 6, 8));
    if (!(isset($parsed['warning_count'])
        && $parsed['warning_count'] == 0)) { //年月日位校验
        $has = false;
    }
    $base = substr($value, 0, 17);
    $factor = [7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2];
    $tokens = ['1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2'];
    $checkSum = 0;
    for ($i=0; $i<17; $i++) {
        $checkSum += intval(substr($base, $i, 1)) * $factor[$i];
    }
    $mod = $checkSum % 11;
    $token = $tokens[$mod];

    $lastChar = strtoupper(substr($value, 17, 1));

    $has = ($lastChar === $token);
    return [$has, $value];
}
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


// 返回404
function back_404($txt = 'Route Error Or Page Not Found.'){
    header('HTTP/1.1 404 Not Found');
    header('Content-Type: text/html; charset=utf-8');

    echo '<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0">';
    echo '<title>404-'.$txt.'</title>';
    echo '<style>body{font-size: 18px;color: #555555;margin: 20px;background: #EEEEEE;font-weight: bold;text-align: center;letter-spacing: 2px;}</style>';

    exit($txt);
}


/*
 * 利用exec实现非阻塞请求，提高请求10%-20%的容量
 * 1. php.ini需要去除disable_functions=exec来开启可使用exec函数
 * 2. 利用了“命令行+api+参数”的请求过程，最终返回api的结果
 * 3. 注意大多数命令行win与linux的不同，混用可能会报错
 *
 * exec_non_blocking($api, 参数：键值对数组, 标记)
 * */
function exec_non_blocking($api, $data_array, $sign){

    $sign = $sign?$sign:get_millisecond();
    $data = '';

    foreach ($data_array as $key=>$value){
        $data = $data.$key.'='.$value.'&';
    }

    if (function_exists('exec')){
        try{
            exec("curl -d '$data' '$api'", $_out); // 参数、api都应该加引号
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

