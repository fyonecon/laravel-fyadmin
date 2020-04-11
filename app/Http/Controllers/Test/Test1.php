<?php

namespace App\Http\Controllers\Test;

use App\Http\Controllers\AdminSafeCheck;
use App\Http\Controllers\LoginSafeCheck;
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


        $json = '{
	"state": 1,
	"msg": "完成",
	"help": "tag_id_string值格式如\"#@1#@12\"",
	"content": {
		"num": 9,
		"question": [{
			"model": "single",
			"index": "1000001",
			"title": "你想考[专硕]还是[学硕]？",
			"option": [{
				"item": "A. 专硕",
				"score": "##1000001#&专硕#@2"
			}, {
				"item": "B. 学硕",
				"score": "##1000001#&学硕#@1"
			}]
		}, {
			"model": "single",
			"index": "1000002",
			"title": "你目前就读的院校级别？",
			"option": [{
				"item": "A. 211",
				"score": "##1000002#&211#@3"
			}, {
				"item": "B. 985",
				"score": "##1000002#&985#@2"
			}, {
				"item": "C. 34所自主划线",
				"score": "##1000002#&34所自主划线#@1"
			}, {
				"item": "D. 其他",
				"score": "##1000002#&其他#@"
			}]
		}, "single", "2000002", "你的英语最高水平？", [{
			"item": "A. 六级",
			"score": "##2000002#&六级#@1"
		}, {
			"item": "B. 四级",
			"score": "##2000002#&四级#@2"
		}, {
			"item": "C. 四级以下",
			"score": "##2000002#&四级以下#@3"
		}, {
			"item": "D. 未考",
			"score": "##2000002#&未考#@"
		}], {
			"model": "single",
			"index": "1000003",
			"title": "你目前的最高学历？",
			"option": [{
				"item": "A. 统招本科",
				"score": "##1000003#&统招本科#@0"
			}, {
				"item": "B. 非统招本科",
				"score": "##1000003#&非统招本科#@0"
			}, {
				"item": "C. 大专",
				"score": "##1000003#&大专#@0"
			}, {
				"item": "D. 其他",
				"score": "##1000003#&其他#@"
			}]
		}, {
			"model": "single",
			"index": "1000004",
			"title": "你每天可用于考研复习的时间？",
			"option": [{
				"item": "A. 6小时以上",
				"score": "##1000004#&6h+#@0"
			}, {
				"item": "B. 4-6小时",
				"score": "##1000004#&4_6h#@0"
			}, {
				"item": "C. 2-4小时",
				"score": "##1000004#&2_4h#@0"
			}, {
				"item": "D. 小于2小时",
				"score": "##1000004#&##2h-#@"
			}]
		}, {
			"model": "input",
			"index": "1000007",
			"title": "你目前就读的大学是？",
			"score": "##1000007#&@@"
		}]
	}
}';


    }





}


