<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AdminSafeCheck;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Exception;

class XCXUser extends AdminSafeCheck {

    public function __construct(Request $request){
        parent::__construct($request);


    }

    // 用户列表
    public function xcx_user(Request $request){

        $xcx_user_id = $request->input('xcx_user_id');
        $xcx_user_phone = $request->input('xcx_user_phone');

        $page = $request->input('page');
        if (!$page){
            $page=1;
        }
        $page = $page-1;
        if ($page > 1000){ //
            $page = 1000;
        }
        $limit = page_limit()*5;

        if ($xcx_user_id == 'all'){

            $search_map = [
                ['xcx_user_id', 'like', "%%"],
                ['xcx_user_phone', 'like', "%$xcx_user_phone%"],
            ];

            $res = DB::table('xcx_user')
                ->where('state', '=', 1)
                ->where($search_map)
                ->orderBy('xcx_user_id', 'desc')
                ->limit($limit)
                ->offset($page*$limit)
                ->select('xcx_user_id', 'previous_xcx_user_id', 'create_xcx_user_phone', 'xcx_user_phone', 'nick_name', 'avatar_url', 'create_time', 'update_time', 'create_ip', 'update_ip', 'xcx_group_id')
                ->get();

            $total = DB::table('xcx_user')
                ->where('state', '=', 1)
                ->where($search_map)
                ->orderBy('xcx_user_id', 'desc')
                ->select('xcx_user_id')
                ->count();

        }else{
            $search_map = [
                ['xcx_user_id', 'like', "%$xcx_user_id%"],
                ['xcx_user_phone', 'like', "%$xcx_user_phone%"],
            ];
            $res = DB::table('xcx_user')
                ->where('state', '=', 1)
                ->where($search_map)
                ->select('xcx_user_id', 'create_xcx_user_phone', 'xcx_user_phone', 'nick_name', 'avatar_url', 'create_time', 'update_time', 'create_ip', 'update_ip', 'xcx_group_id')
                ->first();

            $total = 1;
        }

        $test_data = [$search_map];

        if ($res){

            $state = 1;
            $msg = '用户列表查询完成';
            $content = $res;
        }else{
            $state = 0;
            $msg = '无数据';
            $content = '';
        }

        $back = [
            'state'=>$state,
            'msg'=>$msg,
            'test_data'=>$test_data,
            'paging'=>[
                'total'=>$total,
                'limit'=>$limit,
                'page'=>$page+1,
            ],
            'content'=>$content,
        ];

        return array_to_json($back);
    }

    // 用户支付列表
    public function xcx_user_pay(Request $request){

        $pay_state = $request->input('pay_state');
        $play_class = $request->input('play_class');

        $page = $request->input('page');
        if (!$page){
            $page=1;
        }
        $page = $page-1;
        if ($page > 1000){ //
            $page = 1000;
        }
        $limit = page_limit()*5;

        $pay_state_array = [5, 6, 88, 'all']; // 5已全额退款，6订单还未支付，88订单已支付

        $total = 1;

        if (in_array($pay_state, $pay_state_array)){

            if ($pay_state == 'all' || $pay_state == ''){
                $search_map = [
                    ['xcx_pay_order.state', 'like', "%%"],
                    ['xcx_pay_order.play_class', 'like', "%$play_class%"],
                ];
            }else{
                $search_map = [
                    ['xcx_pay_order.state', '=', $pay_state],
                    ['xcx_pay_order.play_class', 'like', "%$play_class%"],
                ];
            }

            $res = DB::table('xcx_pay_order')
                ->leftJoin('xcx_user', 'xcx_pay_order.xcx_user_id' , '=', 'xcx_user.xcx_user_id')
                ->leftJoin('xcx_play_class', 'xcx_pay_order.play_class', '=', 'xcx_play_class.play_class')
                ->where($search_map)
                ->orderBy('xcx_pay_order.update_time', 'desc')
                ->orderBy('xcx_pay_order.xcx_pay_order_id', 'desc')
                ->limit($limit)
                ->offset($page*$limit)
                ->select('xcx_pay_order.xcx_pay_order_id', 'xcx_pay_order.money', 'xcx_pay_order.pay_amount', 'xcx_pay_order.state', 'xcx_pay_order.order_name', 'xcx_pay_order.play_class', 'xcx_pay_order.out_trade_no', 'xcx_pay_order.pay_order_safe_key', 'xcx_pay_order.create_time', 'xcx_pay_order.update_time', 'xcx_pay_order.xcx_user_id', 'xcx_pay_order.xcx_play_relationship_id', 'xcx_user.nick_name', 'xcx_user.avatar_url', 'xcx_user.xcx_user_phone', 'xcx_user.create_ip', 'xcx_user.update_ip', 'xcx_play_class.play_class_name')
                ->get();

            $total = DB::table('xcx_pay_order')
                ->leftJoin('xcx_user', 'xcx_pay_order.xcx_user_id' , '=', 'xcx_user.xcx_user_id')
                ->leftJoin('xcx_play_class', 'xcx_pay_order.play_class', '=', 'xcx_play_class.play_class')
                ->where($search_map)
                ->orderBy('xcx_pay_order.xcx_pay_order_id', 'desc')
                ->count();

            if ($res){
                $test_data[] = count($res);

                $state = 1;
                $msg = '用户付款列表查询完成';
                $content = $res;
            }else{
                $state = 0;
                $msg = '无数据';
                $content = '';
            }

        }else{
            $state = 0;
            $msg = '非法的state值';
            $content = '';
        }



        $back = [
            'state'=>$state,
            'msg'=>$msg,
            'paging'=>[
                'total'=>$total,
                'limit'=>$limit,
                'page'=>$page+1,
            ],
            'content'=>$content,
        ];

        return array_to_json($back);

    }


    // 删除用户未支付订单、冻结用户已支付订单
    public function do_pay_order(Request $request){
        $xcx_pay_order_id = $request->input('xcx_pay_order_id');
        $that_state = $request->input('state');

        $state_array = [2, 3];

        $that_txt = '未知操作';

        if ($that_state == 2){
            $that_txt = '删除';
        }else if ($that_state == 3){
            $that_txt = '冻结';
        }

        if (in_array($that_state, $state_array)){
            $data = [
                'state'=> $that_state,
                'update_time'=> date('YmdHis'),
            ];

            $res = DB::table('xcx_pay_order')
                ->where('state', '<>', 2)
                ->where('xcx_pay_order_id', '=', $xcx_pay_order_id)
                ->update($data);

            if ($res){
                $state = 1;
                $msg = '状态更新成功：'.$that_txt;
                $content = $res;
            }else{
                $state = 0;
                $msg = '状态未发生变化';
                $content = '';
            }
        }else{
            $state = 0;
            $msg = 'state参数不在白名单';
            $content = '';
        }

        $back = [
            'state'=>$state,
            'msg'=>$msg,
            'content'=>$content,
        ];

        return array_to_json($back);
    }

    // 微信公众号支付全额退款
    // https://m.cswendu.com/wx/h5/pay/api_order_refund2.php?out_trade_no=wdwx20200317092821iyk79lb&total_fee=990&refund_fee=990
    public function wx_refund(Request $request){

        $refund_api_class = $request->input('refund_api_class'); // 用于切换哪种活动退款api

        $refund_token = $request->input('refund_token');
        $refund_token_array = ['18570080966', '18612251776']; // 退款验证密钥

        $out_trade_no = $request->input('out_trade_no');
        $pay_order_safe_key = $request->input('pay_order_safe_key');
        $total_fee = $request->input('total_fee'); // 分
        $refund_fee = $request->input('money'); // 分

        if (in_array($refund_token, $refund_token_array)){
            if ($total_fee >= $refund_fee){

                // 验证out_trade_no与pay_order_safe_key是否匹配
                $has_trade = DB::table('xcx_pay_order')
                    ->where('state', '=', 88)
                    ->where('out_trade_no', '=', $out_trade_no)
                    ->where('pay_order_safe_key', '=', $pay_order_safe_key)
                    ->value('xcx_pay_order_id');

                if ($has_trade){

                    // 执行退款
                    if ($refund_api_class == 'cswx'){
                        $api = config_wx_refund()['wx_web_refund_api_cswx'];
                    }else{ // 错误class或默认class时
                        $api = config_wx_refund()['wx_web_refund_api'];
                    }
                    $array = [
                        'out_trade_no'=> $out_trade_no,
                        'pay_order_safe_key'=> $pay_order_safe_key,
                        'total_fee'=> $total_fee,
                        'refund_fee'=> $refund_fee,
                        'order_refund_key'=> 'wx'.date('Y_m-d'),
                    ];
                    $_refund_back = request_post($api, $array);
                    $refund_back = json_to_array($_refund_back);

                    if (isset($refund_back['state']) && isset($refund_back['msg'])){
                        if ($refund_back['state']){
                            $state = 1;
                            $msg = $refund_back['msg'];
                        }else{
                            $state = 0;
                            $msg = '退款接口发送错误，请查看系统日志。';
                        }
                    }
                    $content = [$refund_back, $_refund_back];

                }else{
                    $state = 0;
                    $msg = '订单信息验证失败或不存在此订单';
                    $content = '';
                }

            }else{
                $state = 0;
                $msg = '参数取值错误';
                $content = '';
            }
        }else{
            $state = 0;
            $msg = '必要参数不在白名单';
            $content = '';
        }

        $back = [
            'state'=>$state,
            'msg'=>$msg,
            'content'=>$content,
        ];

        return array_to_json($back);
    }



}
