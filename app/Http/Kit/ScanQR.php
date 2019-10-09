<?php
/**
 * 识别二维码图片的二维码内容
 */

namespace App\Http\Kit;

use Exception;

if (is_file(path_info()['base_path'].'/vendor/phpqrread/QrReader.php')){
    require_once path_info()['base_path'].'/vendor/phpqrread/QrReader.php';
}else{
    exit('/vendor/phpqrread/QrReader.php文件不存在。');
}

class ScanQR {

    public function scan_qr_img($qr_img, $check_url = false){

        if (!$qr_img){

            $back = [
                'qr_img'=> $qr_img,
                'qr_class'=> '',
                'qr_content'=> '',
            ];

        }else{

            $scan_qr = new \QrReader($qr_img);  // 图片路径
            $qr_content = $scan_qr->text();     // 返回识别后的文本

            if ($check_url == true){
                if (is_numeric($qr_content*1)){
                    $qr_class = 'number';
                }else if (is_url($qr_content)){
                    $qr_class = 'url';
                }else{
                    $qr_class = 'txt';
                }
            }else{
                if (is_numeric($qr_content*1)){
                    $qr_class = 'number';
                }else{
                    $qr_class = 'txt';
                }
            }

            $back = [
                'qr_img'=> $qr_img,     // 图片地址
                'qr_class'=> $qr_class, // 文本可能的类型，[txt, url, number, other]
                'qr_content'=> $qr_content, // 二维码内容
            ];
        }


        return $back;
    }




}
