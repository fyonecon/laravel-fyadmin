<?php

namespace App\Http\Kit;

use Exception;


class HxSMS {

    /*
     * 发送短信【验证码或通知】
     * 需要提前申请好账号和模板才能发
     * 调用示例：hx_send_sms('173xxx', '【示例科技】你的验证码9999，5分钟内有效。')
     ** 每个@最多20字
     * 【】您的短信验证码是@，10分钟内有效，切勿泄露于其他人！退订回
     * 【】尊敬的@，您已成功购课@。联系助教老师@，更多@等你来拿！退订回T
     * */
    public function hx_send_sms($phone = '', $sms = '', $send_time = ''){

        $data = [
            'api'=> '', // 华信短信接口
            'action'=> 'send',          // 任务命令
            // 必填参数
            'account'=> '',    // 账号
            'password'=> '',   // 发送短信的密码
            'mobile'=> $phone,          // 接收短信的手机号，多个手机号用英文,连接起来
            'content'=> $sms,           // 短信内容
            // 选填参数
            'sendTime'=> $send_time,    // 定时发送时间，定时发送格式2010-10-24 09:08:10
        ];

        $test_data = [
            'mobile'=> $phone,
            'content'=> $sms,
            'sendTime'=> $send_time,
        ];

        if (empty($phone || empty($sms))){
            $state = 0;
            $msg = '手机号或短信内容不能为空';
            $content = '';
            $result = '（还未发送）';
        }else{

            // post方法
            $post_data = $data;
            $result = $this->request_post($data['api'], $post_data);
            $result = json_decode($result, true);

            if (isset($result['returnstatus'])){
                if ($result['returnstatus'] == 'Success'){
                    $state = 1;
                    $msg = '发送成功';
                    $content = '';
                }else if ($result['returnstatus'] == 'Faild'){
                    $state = 0;
                    $msg = '发送失败';
                    $content = $result;
                }else{
                    $state = 2;
                    $msg = '其他发送情况，发送是否成功未知';
                    $content = $result;
                }
            }else{
                $state = 2;
                $msg = '短信接口返回的array不可解析，发送是否成功未知';
                $content = $result;
            }

        }

        $test_data['result'] = $result;
        $back = [
            'state'=> $state,
            'msg'=> $msg,
            'content'=> $content,
            'test_data'=> $test_data,
        ];

        return $back;
    }


    // post请求
    public function request_post($url='', $post_data=[]) { // 模拟post请求
        if (empty($url) || empty($post_data)) {
            return false;
        }
        $o = "";
        foreach ( $post_data as $k => $v ) {
            $o.= "$k=" . urlencode( $v ). "&" ;
        }
        $post_data = substr($o,0,-1);

        $post_url = $url;
        $curlPost = $post_data;
        $ch = curl_init();//初始化curl
        curl_setopt($ch, CURLOPT_URL,$post_url);//抓取指定网页
        curl_setopt($ch, CURLOPT_HEADER, 0);//设置header
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
        $data = curl_exec($ch);//运行curl
        curl_close($ch);

        //print_r($data);
        return $data;
    }



    public function __call($func_name, $args){
        $txt = "class：".__CLASS__." ，函数不存在：$func_name ，参数：$args ";
        exit($txt);
    }


}

