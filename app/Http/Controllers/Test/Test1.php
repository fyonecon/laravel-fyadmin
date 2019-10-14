<?php

namespace App\Http\Controllers\Test;

use App\Http\Controllers\AdminSafeCheck;
use App\Http\Controllers\AppSafeCheck;
use App\Http\Controllers\OpenController;
use App\Http\Controllers\Enhance\GetIp;
use Illuminate\Http\Request;
use App\Http\Controllers\Enhance\Log;

class Test1 extends AdminSafeCheck {


    public function __construct(Request $request){
        parent::__construct($request);

    }


    public function get(Request $request){
        $user = $request->input("user"); // 这里接收参数比thinkphp里面接收参数的input()要提前生命参数
        $name = $request->input("name");

        $msg = "get yes";
        $back = [
            "state"=>1,
            "msg"=>$msg,
            "name"=>$name,
            "user"=>$user,
            'test'=>test_common('text_func')
        ];
        return json_encode($back, JSON_UNESCAPED_UNICODE); // js接收TP返回来的是string，而Lvl返回object。
    }

    // http://localhost/laravel58/public/index.php/test/test?method=get
    public function test(){

        var_dump(path_info());

        var_dump(strlen(path_info()['server_root']));

        $len = strlen(path_info()['server_root']);
        $str = path_info()['base_path'];
        $res = substr($str, $len+1);

        var_dump($res);

        echo $res;

        $ip = new GetIp();

        $data = $ip->get_that_ip();


        $log = new Log();
        $back = $log->write_log("test1", $data);

        print_r($back);

        var_dump(config_qiniu()['accessKey']);

    }


    public function test1(){
        echo 'test1';

    }


    public function test2(){
        echo 'test2';

    }

    public function test3(){
        echo 'test3';

    }

    public function test4(){
        echo 'test4';

    }

    public function test5(){
        echo 'test5';

    }





}


