<?php
/**
 * 生成带Logo的二维码图片
 */

namespace App\Http\Kit;

use Exception;

if (is_file(path_info()['base_path'].'/vendor/phpqrcode/phpqrcode.php')){
    require_once path_info()['base_path'].'/vendor/phpqrcode/phpqrcode.php';
}else{
    exit('vendor/phpqrcode/phpqrcode.php文件不存在。');
}

class MakeQR {


    // 生成二维码图
    // make_qr_img(二维码内容, 二维码是否要带上logo, logo的网址或者绝对地址, 是否开启七牛云)
    public function make_qr_img($_content, $_has_logo, $_logo_src ,$qiniu_state){
        $qrcode = new \QRcode();

        $has_logo = $_has_logo; // 二维码中是否带有Logo

        $content = $_content?$_content:'（未设置二维码内容）'; // 二维码内容
        $level = 'Q'; // 二维码容错度：L、M、Q、H，识别不出来用容错高的
        $img_size = 9; // 二维码画布大小：N*21px
        $img_margin = 1; // 二维码边缘空白
        $file_path = path_info()['base_path'].'/storage/upload_file/qr_img/'; // 图片存放文件夹
        $img_name = $this->file_name_create('qr'); // 生成图的名称
        $img_ext = '.png';
        $img_path = $file_path.$img_name.$img_ext; // 图片地址

        if ($has_logo == true) { //带有logo
            $logo_src = $_logo_src?$_logo_src:'https://cdnaliyun.oss-cn-hangzhou.aliyuncs.com/images/test-logo.png'; //logo图片地址，可url、可绝对地址

            if (!$logo_src){
                $img_path = '（参数不完整）';
            }else{
                $qrcode::png($content, $img_path, $level, $img_size, $img_margin); // 自动生成二维码图片
                $QR = $img_path; //二维码图地址

                $QR = imagecreatefromstring(file_get_contents($QR));
                $logo = imagecreatefromstring(file_get_contents($logo_src));
                $QR_width = imagesx($QR); // 二维码图片宽度
                $QR_height = imagesy($QR); // 二维码图片高度
                $logo_width = imagesx($logo); // logo图片宽度
                $logo_height = imagesy($logo); // logo图片高度
                $logo_qr_width = $QR_width / 4;
                $scale = $logo_width / $logo_qr_width;
                $logo_qr_height = $logo_height / $scale;
                $from_width = ($QR_width - $logo_qr_width) / 2;

                // 生成圆角logo
                $logo = $this->img_radius($logo_src, $logo_width/2);

                imagecopyresampled($QR, $logo, $from_width, $from_width, 0, 0, $logo_qr_width, $logo_qr_height, $logo_width, $logo_height);

                //输出图片
                imagepng($QR, $img_path);
            }

        } else { //不带logo
            $qrcode::png($content, $img_path, $level, $img_size, $img_margin);
        }

        if ($qiniu_state == true){
            // 上传到七牛
            $qiniu = new QiniuConfig();
            $res = $qiniu->qiniu_upload_api($img_path, $img_name);

            $back = [
                'img_name'=> $img_name,
                'img_ext'=> $img_ext,
                'img'=> $img_name.$img_ext,
                'qiniu'=> [$res, [$img_path, $img_name]],
                'img_path'=> $img_path,
                'img_data'=> [$_content, $_has_logo, $_logo_src],
            ];
        }else{
            $back = [
                'img_name'=> $img_name,
                'img_ext'=> $img_ext,
                'img'=> $img_name.$img_ext,
                'qiniu'=> '',
                'img_path'=> $img_path,
                'img_data'=> [$_content, $_has_logo, $_logo_src],
            ];
        }

        return $back;
    }


    // 将图片切成圆角
    public function img_radius($imgpath = '', $radius = 15) {
        $ext     = pathinfo($imgpath);
        $src_img = null;
        switch ($ext['extension']) {
            case 'jpg':
                $src_img = imagecreatefromjpeg($imgpath);
                break;
            case 'png':
                $src_img = imagecreatefrompng($imgpath);
                break;
        }
        $wh = getimagesize($imgpath);
        $w  = $wh[0];
        $h  = $wh[1];
        // $radius = $radius == 0 ? (min($w, $h) / 2) : $radius;
        $img = imagecreatetruecolor($w, $h);
        //这一句一定要有
        imagesavealpha($img, true);
        //拾取一个完全透明的颜色,最后一个参数127为全透明
        $bg = imagecolorallocatealpha($img, 255, 255, 255, 127);
        imagefill($img, 0, 0, $bg);
        $r = $radius; //圆 角半径
        for ($x = 0; $x < $w; $x++) {
            for ($y = 0; $y < $h; $y++) {
                $rgbColor = imagecolorat($src_img, $x, $y);
                if (($x >= $radius && $x <= ($w - $radius)) || ($y >= $radius && $y <= ($h - $radius))) {
                    //不在四角的范围内,直接画
                    imagesetpixel($img, $x, $y, $rgbColor);
                } else {
                    //在四角的范围内选择画
                    //上左
                    $y_x = $r; //圆心X坐标
                    $y_y = $r; //圆心Y坐标
                    if (((($x - $y_x) * ($x - $y_x) + ($y - $y_y) * ($y - $y_y)) <= ($r * $r))) {
                        imagesetpixel($img, $x, $y, $rgbColor);
                    }
                    //上右
                    $y_x = $w - $r; //圆心X坐标
                    $y_y = $r; //圆心Y坐标
                    if (((($x - $y_x) * ($x - $y_x) + ($y - $y_y) * ($y - $y_y)) <= ($r * $r))) {
                        imagesetpixel($img, $x, $y, $rgbColor);
                    }
                    //下左
                    $y_x = $r; //圆心X坐标
                    $y_y = $h - $r; //圆心Y坐标
                    if (((($x - $y_x) * ($x - $y_x) + ($y - $y_y) * ($y - $y_y)) <= ($r * $r))) {
                        imagesetpixel($img, $x, $y, $rgbColor);
                    }
                    //下右
                    $y_x = $w - $r; //圆心X坐标
                    $y_y = $h - $r; //圆心Y坐标
                    if (((($x - $y_x) * ($x - $y_x) + ($y - $y_y) * ($y - $y_y)) <= ($r * $r))) {
                        imagesetpixel($img, $x, $y, $rgbColor);
                    }
                }
            }
        }
        return $img;
    }


    //生成文件名
    public function file_name_create($ext){
        $file_name = date('Ymd_His_').$ext.'_'.uniqid().get_rand_string(rand(3, 5));
        return $file_name;
    }




    public function __call($func_name, $args){
        $txt = "class：".__CLASS__." ，函数不存在：$func_name ，参数：$args ";
        exit($txt);
    }

}
