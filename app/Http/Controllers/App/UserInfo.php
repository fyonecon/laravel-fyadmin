<?php

namespace App\Http\Controllers\App;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class UserInfo extends Controller{


    public function info(Request $request){

        $user = $request->input('user'); // 这里接收参数比thinkphp里面接收参数的input()要提前生命参数
        $name = $request->input('name');

        $msg = 'match yes';
        $back = [
            'state'=>1,
            'msg'=>$msg,
            'name'=>$name,
            'user'=>$user,
        ];
        return json_encode($back, JSON_UNESCAPED_UNICODE); // js接收TP返回来的是string，而Lvl返回object。
    }

    public function cache_redis(){

        $userinfo = "2018";
        Redis::set('user_key',$userinfo);
        if(Redis::exists('user_key')){
            $values = Redis::get('user_key');
        }else{
            $values = 8012;
        }
        print_r($values);

        echo "<br/>0<br/>";
    }


}

