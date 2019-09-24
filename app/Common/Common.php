<?php
/*
 * 不需要封装的公共函数，公共参数配置
 * 全部文件都可调用
 * */

function test_common($txt){
    return $txt.'=Common.php';
}

// 配置微信网页分享
function config_wxweb_share(){
    $info = [
        'appid'=> 'wxa10039a58c872225',
        'appsecret'=> '36f2a2f470add51145edf3179171502b',
        'http_host'=> '', //
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
    ];
    return $info;
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
function debug_api_method(){

    return date('Ymd');
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


// post请求
function request_post($url='', $post_data=[]) { // 模拟post请求
    if (empty($url) || empty($post_data)) {
        return false;
    }

    $o = "";
    foreach ( $post_data as $k => $v )
    {
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
    //return json_decode($data, true);
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




