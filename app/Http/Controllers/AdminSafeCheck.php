<?php
/* 《Admin类控制器拦截》
 * Admin安全验证拦截
 * */

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Kit\Secret;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminSafeCheck extends Controller{

    /*
     * 预先执行，安全检测
     * */
    public function __construct(Request $request){

        header('Access-Control-Allow-Origin:*');

        $method = $request->input('method');

        if ($method == 'get'){
            // 跳过检测

        }else{

            if (!is_post()){
                $back = [
                    'state'=> 403,
                    'msg'=> '此接口仅限POST，拒绝访问(Admin)',
                    'content'=> '',
                ];
                exit(json_encode($back, JSON_UNESCAPED_UNICODE));
            }else{

                $app_version = $request->input('app_version');
                $app_version = $app_version*1;
                $app_class = $request->input('app_class');

                $user_login_id = $request->input('user_login_id');
                $user_login_token = $request->input('user_login_token');

                $app_class_array = ['ios', 'android', 'wx', 'web']; // 接口来源白名单

                if ($app_version < 1.0){ // 低版本不可操作接口
                    $back = [
                        'state'=> 301,
                        'msg'=> '请升级App(Admin-已屏蔽)',
                        'test_data'=> [
                            $app_version,
                            $app_class,
                            $user_login_id,
                            $user_login_token,
                        ],
                        'content'=> [],
                    ];
                    exit(json_encode($back, JSON_UNESCAPED_UNICODE));
                }else if (!in_array($app_class, $app_class_array)){ // 非白名单接口来源不可访问
                    $back = [
                        'state'=> 403,
                        'msg'=> '接口来源不正确，拒绝访问(Admin)',
                        'test_data'=> [
                            $app_version,
                            $app_class,
                            $user_login_id,
                            $user_login_token,
                        ],
                        'content'=> '',
                    ];
                    exit(json_encode($back, JSON_UNESCAPED_UNICODE));
                }else{ // 正常

                    // 校验用户信息
                    //$user_safe = $this->check_user_token($user_login_id, $user_login_token);

                }

            }

        }



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
            'test'=>test_common('text_common_func')
        ];
        return json_encode($back, JSON_UNESCAPED_UNICODE); // js接收TP返回来的是string，而Lvl返回object。
    }


}
