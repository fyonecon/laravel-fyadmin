<?php

namespace App\Http\Controllers\Test;

use App\Http\Controllers\AdminSafeCheck;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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

    public function test1(){

        var_dump(laravel_path_info());

    }


}


