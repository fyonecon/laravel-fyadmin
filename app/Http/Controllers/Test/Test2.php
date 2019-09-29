<?php

namespace App\Http\Controllers\Test;


use App\Http\Controllers\Controller;
use App\Http\Controllers\Enhance\IpApi;
use App\Http\Kit\MakeImg;
use App\Http\Kit\MakeQR;
use Illuminate\Http\Request;

class Test2 extends Controller{


    public function test(){
        echo '==';

        //$make_qt = new MakeQR();

        //return array_to_json($make_qt->make_qr_img('',false, 'https://cdnaliyun.oss-cn-hangzhou.aliyuncs.com/images/test-logo.png', true));


        $make_img = new MakeImg();

//        return array_to_json($make_img->complex_img_img('https://cdnaliyun.oss-cn-hangzhou.aliyuncs.com/images/cover1.jpg', 'https://cdnaliyun.oss-cn-hangzhou.aliyuncs.com/images/test-logo.png', ['x'=>20, 'y'=>20, 'opacity'=> 80], 0));


        return array_to_json($make_img->complex_img_txt('https://cdnaliyun.oss-cn-hangzhou.aliyuncs.com/images/cover1.jpg', '3te8p37trpriu3w', ['red'=>255, 'green'=>255, 'blue'=>255, 'alpha'=>0, 'size'=>50, 'angle'=>0, 'x'=>100, 'y'=>70], 0));

    }






}
