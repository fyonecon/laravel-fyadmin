<?php
/**
 * 将图片与图片合成，图片与文字合成
 */

namespace App\Http\Kit;

use Exception;

class MakeImg {


    // 将图片与图片合成（给背景图添加水印）
    public function complex_img_img($_bg_img, $_add_img, $_parameter, $qiniu){

        // $_parameter = ['x'=>20, 'y'=>20, 'opacity'=> 80];

        $bg_img = $_bg_img; // 背景图
        $add_img = $_add_img; // 水印图
        $x = $_parameter['x']; // 水印图的x坐标
        $y = $_parameter['y']; // 水印图的y坐标
        $opacity = $_parameter['opacity'];; // [0, 100]，水印图的透明度

        $file_path = path_info()['base_path'].'/storage/upload_file/make_img/'; // 图片存放文件夹
        $img_name = $this->file_name_create('complex_img'); // 生成图的名称
        $img_ext = ''; // 图片文件后缀

        //创建图片的实例
        $dst = imagecreatefromstring(file_get_contents($bg_img));
        $src = imagecreatefromstring(file_get_contents($add_img));
        //获取水印图片的宽高
        list($src_w, $src_h, $src_type) = getimagesize($add_img);
        //将水印图片复制到目标图片上，最后个参数50是设置透明度，这里实现半透明效果，两个20是控制水印坐标位置
        imagecopymerge($dst, $src, $x, $y, 0, 0, $src_w, $src_h, $opacity);
        //如果水印图片本身带透明色，则使用imagecopy方法
        //imagecopy($dst, $src, 10, 10, 0, 0, $src_w, $src_h);
        //输出图片
        list($dst_w, $dst_h, $dst_type) = getimagesize($bg_img);
        switch ($dst_type) {
            case 1: // GIF
                $img_ext = '.gif';
                $img_path = $file_path.$img_name.$img_ext;
                $dst_type = 'gif';
                imagegif($dst, $img_path);
                break;
            case 2: // JPG
                $img_ext = '.jpeg';
                $img_path = $file_path.$img_name.$img_ext;
                $dst_type = 'jpeg';
                imagejpeg($dst, $img_path);
                break;
            case 3: // PNG
                $img_ext = '.png';
                $img_path = $file_path.$img_name.$img_ext;
                $dst_type = 'png';
                imagepng($dst, $img_path);
                break;
            default: // 404
                $img_path = '';
                $img_ext = '.null';
                $dst_type = 'null-type';
                break;
        }

        imagedestroy($dst);
        imagedestroy($src);

        if ($qiniu == true){
            // 上传到七牛
            $qiniu = new QiniuConfig();
            $res = $qiniu->qiniu_upload_api($img_path, $img_name);

            $back = [
                'img_path'=> $img_path,
                'img_info'=> [
                    'bg_img'=>[[$dst_w, $dst_h, $dst_type], $_bg_img],
                    'add_img'=>[[$src_w, $src_h, $src_type], $_add_img],
                ],
                'qiniu'=>$res,
            ];

        }else{
            $back = [
                'img_path'=> $img_path,
                'img_info'=> [
                    'bg_img'=>[[$dst_w, $dst_h, $dst_type], $_bg_img],
                    'add_img'=>[[$src_w, $src_h, $src_type], $_add_img],
                ],
                'qiniu'=>'',
            ];
        }

        return $back;
    }


    // 将图片与文字合成
    public function complex_img_txt($_bg_img, $_content, $_parameter, $qiniu){

        //$_parameter = ['red'=>255, 'green'=>255, 'blue'=>255, 'alpha'=>0, 'size'=>50, 'angle'=>0, 'x'=>100, 'y'=>70];

        $src = $_bg_img; // 背景图地址
        $font = path_info()['base_path'].'/storage/font/'."simkai.ttf"; // 字体
        $content = $_content; // 文字内容

        $red = $_parameter['red'];
        $green = $_parameter['green'];
        $blue = $_parameter['blue'];
        $alpha = $_parameter['alpha'];
        $size = $_parameter['size'];
        $angle = $_parameter['angle'];
        $x = $_parameter['x'];
        $y = $_parameter['y'];

        $info = getimagesize($src);
        $type = image_type_to_extension($info[2],false);
        // 在内存中创建和图像类型一样的图像
        $fun = "imagecreatefrom".$type;
        //5.图片复制到内存
        $image = $fun($src);

        $file_path = path_info()['base_path'].'/storage/upload_file/make_img/'; // 图片存放文件夹
        $img_name = $this->file_name_create('complex_txt'); // 生成图的名称
        $img_ext = $type; // 图片文件后缀

        // 设置字体颜色和透明度
        $color = imagecolorallocatealpha($image, $red, $green, $blue, $alpha);
        // 写入文字 (图片资源，字体大小，旋转角度，坐标x，坐标y，颜色，字体文件，内容)
        imagettftext($image, $size, $angle, $x, $y, $color, $font, $content);

        switch ($type){
            case 'gif': // GIF
                $img_ext = '.gif';
                $img_path = $file_path.$img_name.$img_ext;
                imagegif($image, $img_path);
                break;
            case 'jpeg': // JPG
                $img_ext = '.jpeg';
                $img_path = $file_path.$img_name.$img_ext;
                imagejpeg($image, $img_path);
                break;
            case 'png': // PNG
                $img_ext = '.png';
                $img_path = $file_path.$img_name.$img_ext;
                imagepng($image, $img_path);
                break;
            default: // 404
                $img_path = '';
                $img_ext = '.null';
                break;
        }

        $img_info = [$type, [$_bg_img, $_content, $_parameter, $qiniu]];

        imagedestroy($image);

        if ($qiniu == true){
            // 上传到七牛
            $qiniu = new QiniuConfig();
            $res = $qiniu->qiniu_upload_api($img_path, $img_name);

            $back = [
                'img_path'=> $img_path,
                'img_info'=> $img_info,
                'qiniu'=>$res,
            ];

        }else{
            $back = [
                'img_path'=> $img_path,
                'img_info'=> $img_info,
                'qiniu'=>'',
            ];
        }

        return $back;
    }


    //生成文件名
    public function file_name_create($ext){
        $file_name = date('Ymd_His_').$ext.'_'.uniqid().get_rand_string(rand(3, 5));
        return $file_name;
    }



}
