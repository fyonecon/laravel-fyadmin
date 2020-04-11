<?php

namespace App\Http\Controllers\Test;

use App\Http\Controllers\EnhanceSafeCheck;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Exception;

class Test2 extends EnhanceSafeCheck {

    public function __construct(Request $request){
        parent::__construct($request);

    }

    public function test2_database2(){

        print_r(DB::connection('mysql_py')->select('select * from py_cs'));

//        $database2 = DB::connection('mysql_py')->table('')
//            //->select('tag_name')
//            ->first();

        //print_r($database2);

    }


    public function test2_check_ip_city(){

        $key = '20200224-';

        if ($key = '20200224-a'){

            $ip_array = Db::table('phone_sms')
                ->select('phone_sms_id', 'ip')
                ->get();

            $res      = json_to_array($ip_array);
            $res      = group_array($res, 'ip');

            var_dump($res);

            $ip_back = 'init';

            for ($i=0; $i<count($res); $i++){
                $ip = $res[$i]['ip'];

                // 查询IP归属地
                try {
                    $local_server_ip = config_log()['local_server_ip']; // 本地/此服务器
                    $api = 'http://'.$local_server_ip.'/'.main_filename().'/public/index.php/api/enhance/ip_city';
                    $array = [
                        'ip'=> $ip,
                    ];
                    $ip_back = request_post($api, $array);
                }catch (Exception $exception){
                    $ip_back = 'exception';
                }

                var_dump($ip_back);
                echo '<br>';

                sleep(0.5);

            }

        }else{
            echo '<br>';
            var_dump('error-key');

        }


    }





}
