<?php

namespace App\Http\Controllers\Enhance;

use App\Http\Controllers\EnhanceSafeCheck;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Exception;
use App\Http\Kit\IpInfo;

final class WebPayOrder extends EnhanceSafeCheck{

    final function __construct(Request $request){
        parent::__construct($request);

    }

    // 记录新订单
    final function create_pay_order(Request $request){

        $out_trade_no = $request->input('out_trade_no');
        $order_name = $request->input('order_name');
        $pay_amount = $request->input('pay_amount')*100;
        $user_id = $request->input('user_id');
        $play_class = $request->input('play_class');
        $xcx_play_relationship_id = $request->input('xcx_play_relationship_id');
        // $state = $request->input('state');

        // 已支付过该活动，就不需要再支付了
        // （如果可以重复参加该活动，可以在此更改）
        $multible_pay = false;

        if ($multible_pay == false){
            // 查询该用户是否已经支付过该拼团活动
            $user_had_pay_play = DB::table('xcx_pay_order')
                ->where('xcx_user_id', '=', $user_id)
                ->where('play_class', '=', $play_class)
                ->where('state', '=', 88)
                ->select('xcx_pay_order_id', 'xcx_play_relationship_id','out_trade_no', 'pay_order_safe_key')
                ->first();
        }else{
            $user_had_pay_play = 0;
        }

        if (!$user_had_pay_play){

            $has_xcx_pay_order = DB::table('xcx_pay_order')
                ->where('state', '<>', 2)
                ->where('out_trade_no', '=', $out_trade_no)
                ->select('xcx_pay_order_id', 'pay_order_safe_key')
                ->first();

            if (!$has_xcx_pay_order){

                $pay_order_safe_key = get_rand_string(rand(29, 31));

                $data = [
                    'xcx_user_id'=> $user_id,
                    'xcx_play_relationship_id'=> $xcx_play_relationship_id,
                    'play_class'=> $play_class,
                    'create_time'=> date('YmdHis'),
                    'state'=> 6, // 6未支付，88已支付
                    'out_trade_no'=> $out_trade_no,
                    'order_name'=> $order_name,
                    'pay_amount'=> $pay_amount.'', // 数字变str，防止出现.999999999
                    'pay_order_safe_key'=> $pay_order_safe_key,

                    'order_info'=> '',
                ];

                $xcx_pay_order_id = DB::table('xcx_pay_order')->insertGetId($data);

                $state = 1;
                $msg = 'create';
            }else{
                $has_xcx_pay_order = json_to_array($has_xcx_pay_order);

                $xcx_pay_order_id = $has_xcx_pay_order['xcx_pay_order_id'];
                $pay_order_safe_key = $has_xcx_pay_order['pay_order_safe_key'];

                $state = 1;
                $msg = 'update';
            }

        }else{
            $state = 88;
            $msg = '用户已支付过本次活动';

            $has_xcx_pay_order = json_to_array($user_had_pay_play);

            $xcx_pay_order_id = $has_xcx_pay_order['xcx_pay_order_id'];
            $out_trade_no = $has_xcx_pay_order['out_trade_no'];
            $pay_order_safe_key = $has_xcx_pay_order['pay_order_safe_key'];
        }


        $back = [
            'state'=> $state,
            'msg'=> $msg,
            'content'=> [
                'pay_order_id'=> $xcx_pay_order_id,
                'out_trade_no'=> $out_trade_no,
                'pay_order_safe_key'=> $pay_order_safe_key,
            ],
        ];

        return array_to_json($back);
    }

    // 更新订单信息
    // 付款、退款操作
    final function update_pay_order(Request $request){

        $out_trade_no = $request->input('out_trade_no');
        $pay_order_safe_key = $request->input('pay_order_safe_key');

        $state = $request->input('state');
        $money = $request->input('money');

        $data = [
            'update_time'=> date('YmdHis'),
            'state'=> $state, // 6未支付，88已支付
            'money'=> $money, // 订单实际付款价格
        ];

        $res1 = DB::table('xcx_pay_order')
            ->where('out_trade_no', '=', $out_trade_no)
            ->where('pay_order_safe_key', '=', $pay_order_safe_key)
            ->select('state', 'play_class')
            ->first();
        $res1 = json_to_array($res1);
        $that_state = $res1['state'];
        $that_play_class = $res1['play_class'];

        $dec_last_num = -2;
        $inc_last_num = -2;

        if ($state == 88){ // 更新为已支付订单
            $must_state = 6;
            // 库存-1
            $dec_last_num = DB::table('xcx_play_class')
                ->where('play_class', '=', $that_play_class)
                ->decrement('last_num', 1);

            $send_notice_sms = false; // true发送通知短信，false不发送通知短信

            if($send_notice_sms){

                // 开始-处理通知短信
                // 用户信息
                $log = new Log();
                $_user_info = DB::table('xcx_pay_order')
                    ->leftJoin('xcx_user', 'xcx_pay_order.xcx_user_id', '=', 'xcx_user.xcx_user_id')
                    ->where('xcx_pay_order.out_trade_no', '=', $out_trade_no)
                    ->select('xcx_user.xcx_user_id', 'xcx_user.xcx_user_phone', 'xcx_user.nick_name', 'xcx_user.avatar_url', 'xcx_pay_order.order_name')
                    ->first();
                $user_info = json_to_array($_user_info);

                try {
                    $user_id = $user_info['xcx_user_id'];
                    $user_phone = $user_info['xcx_user_phone'];
                    $order_name = $user_info['order_name'];

                    $log_info[] = ['user_info_yes'=> $user_info];
                }catch (Exception $exception){
                    $user_id = 0;
                    $user_phone = 0;
                    $order_name = 'error-array';
                    $log_info[] = ['user_info_error_array'=> $user_info];
                }

                try {
                    // 发送购买成功短信
                    $api =  config_api_url()['api_url'].'app/course_sms_notice';
                    $post_data = [
                        'user_token'=> 'tpl@request@token', // 白名单token
                        'user_phone'=> $user_phone,
                        'name'=> substr($user_phone, 6, 8),
                        'product'=> $order_name,
                        'teacher_phone'=> '18570080966',
                        'description'=> '考研资料',
                        'resource'=> 'relation-ca_pay-all',
                        'user_info'=> $user_id.'#@'.$user_phone.'#@'.$order_name,
                    ];
                    $result = request_post($api, $post_data);

                    $log_info[] = ['result1'=>$result];
                    $log_info[] = ['post_data1'=>$post_data];

                    $log->write_log('send_sms_notice-yes-②', $log_info);
                }catch (Exception $exception){
                    $log->write_log('send_sms_notice-error-③', $log_info);
                }
                // 结束-处理通知短信

            }


        }else if ($state == 5){ // 更新为已全额退款
            $must_state = 88;
            // 库存+1
            $inc_last_num = DB::table('xcx_play_class')
                ->where('play_class', '=', $that_play_class)
                ->increment('last_num', 1);
        }else{ // 更新为其他
            $must_state = 5;
        }

        $res2 = DB::table('xcx_pay_order')
            ->where('state', '=', $must_state)
            ->where('out_trade_no', '=', $out_trade_no)
            ->where('pay_order_safe_key', '=', $pay_order_safe_key)
            ->update($data);

        if ($res2){

            $state = 1;
            $msg = '订单信息已经更新';
            $content = [$dec_last_num, $inc_last_num];

        }else{
            if ($that_state == 88){
                $state = 1;
                $msg = '订单已支付（已完成支付）';
                $content = [$out_trade_no, $pay_order_safe_key];
            }else if ($that_state == 6){
                $state = 0;
                $msg = '用户订单还未完成支付（详情可联系客服）';
                $content = [$out_trade_no, $pay_order_safe_key];
            }else if ($that_state == 5){
                $state = 0;
                $msg = '用户订单已全额退回（详情可联系客服）';
                $content = [$dec_last_num, $inc_last_num];
            }else{
                $state = 0;
                $msg = '订单状态未改变';
                $content = [$out_trade_no, $pay_order_safe_key];
            }

        }

        $back = [
            'state'=> $state,
            'msg'=> $msg,
            'content'=> $content,
        ];

        return array_to_json($back);
    }


    // 订单支付状态
    final function status_pay_order(Request $request){

        $out_trade_no = $request->input('out_trade_no');
        $pay_order_safe_key = $request->input('pay_order_safe_key');

        $res1 = DB::table('xcx_pay_order')
            ->where('out_trade_no', '=', $out_trade_no)
            ->where('pay_order_safe_key', '=', $pay_order_safe_key)
            ->value('state');

        if ($res1 == 88){
            $state = 1;
            $msg = '订单已支付（已完成支付）';
        }else if ($res1 == 6){
            $state = 0;
            $msg = '用户订单还未完成支付（详情可联系客服）';
        }else if ($res1 == 5){
            $state = 0;
            $msg = '用户订单已全额退回（详情可联系客服）';
        }else{
            $state = 0;
            $msg = '订单参数不完整或不匹配';
        }
        $content = [$out_trade_no, $pay_order_safe_key];

        $back = [
            'state'=> $state,
            'msg'=> $msg,
            'content'=> $content,
        ];

        return array_to_json($back);
    }



}


