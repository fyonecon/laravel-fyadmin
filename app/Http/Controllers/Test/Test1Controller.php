<?php

namespace App\Http\Controllers\Test;

use App\Http\Controllers\AdminSafeCheck;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Play\GetIp;
use Illuminate\Http\Request;
use App\Http\Controllers\Play\Log;

class Test1Controller extends AdminSafeCheck {


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

    // http://localhost/laravel58/public/index.php/test/test1?method=get
    public function test1(){

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


}


