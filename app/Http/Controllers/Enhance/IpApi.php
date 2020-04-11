<?php

/*
 * 采集访问的IP、城市等数据
 * */

namespace App\Http\Controllers\Enhance;

use App\Http\Controllers\EnhanceSafeCheck;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Exception;
use App\Http\Kit\IpInfo;

final class IpApi extends EnhanceSafeCheck {

    final function __construct(Request $request){
        parent::__construct($request);

    }


    /*
     * 获取用户IP接口
     * /public/index.php/enhance/get_that_ip
     * */
    final function get_that_ip(){
        $ip_info = new IpInfo();
        $ip = $ip_info->get_user_ip();

        return json_encode($ip, JSON_UNESCAPED_UNICODE);
    }


    /*
     * 查询IP归属地
     * */
    final function ip_city(Request $request){
        $user = $request->input('user');
        $ip = $request->input('ip');

        $white_user = ['public', 'all']; // 参数白名单

        if (!filter_var($ip, FILTER_VALIDATE_IP)){
            $state = 0;
            $msg = 'IP格式错误';
            $content = [$user, $ip];
        }else{

            //
            $has_ip = Db::table('ip_city')
                ->where('ip', '=', $ip)
                ->select('ip_id', 'ip', 'pv', 'country', 'province', 'city', 'isp', 'create_time', 'update_time', 'state')
                ->first();
            $has_ip = json_to_array($has_ip);

            if ($has_ip){
                // 有就 更新时间+更新PV
                if (isset($has_ip['ip_id']) && isset($has_ip['pv'])){
                    $ip_id = $has_ip['ip_id'];
                    $pv = $has_ip['pv'];

                    $data1 = [
                        'update_time'=> date('YmdHis'),
                        'pv'=> $pv+1,
                    ];

                    $ip_change = Db::table('ip_city')
                        ->where('ip_id', '=', $ip_id)
                        ->update($data1);

                }else{
                    $ip_change = 0;
                }

            }else{
                // 新增IP+查询城市
                $ip_info = new IpInfo();

                $info = $ip_info->get_ip_city($ip, 'ip.sb');
                $info = json_to_array($info);

                if (isset($info['country']) && isset($info['region']) && isset($info['city']) && isset($info['isp'])){
                    $country = $info['country'];
                    $province = $info['region'];
                    $city = $info['city'];
                    $isp = $info['isp']; // 运营商

                    $data2 = [
                        'ip'=> $ip,
                        'pv'=> 1,
                        'country'=> $country,
                        'province'=> $province,
                        'city'=> $city,
                        'isp'=> $isp,
                        'create_time'=> date('YmdHis'),
                    ];

                    $ip_change = Db::table('ip_city')->insert($data2);

                }else{ // 不能查询到IP的信息
                    $data2 = [
                        'ip'=> $ip,
                        'pv'=> 1,
                        'country'=> '-',
                        'province'=> '-',
                        'city'=> '-',
                        'isp'=> '待查询IP',
                        'state'=> 13,
                        'create_time'=> date('YmdHis'),
                    ];

                    $ip_change = Db::table('ip_city')->insert($data2);
                }

                if ($ip_change){
                    $has_ip = $data2;
                }else{
                    $has_ip = '';
                }

            }

            if ($ip_change){
                $state = 1;
                $msg = '查询成功';
                $content = $has_ip;
            }else{
                $state = 0;
                $msg = '无数据';
                $content = [$user, $ip];
            }

            if (!in_array($user, $white_user)){
                $state = 0;
                $msg = '必要参数不全（user参数不在白名单）';
                $content = [$user, $ip];
            }

        }


        $back = [
            'state'=>$state,
            'msg'=>$msg,
            'content'=>$content,
        ];

        return array_to_json($back);
    }





}
