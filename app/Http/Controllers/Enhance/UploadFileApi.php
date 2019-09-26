<?php

/*
 * 文件上传接口：
 * 1.保存网址类型图片；
 * 2.保存base64数据流文件；
 * 3.form保存数据流文件（支持wx.UploadFile()，支持web-form提交）；
 * */

namespace App\Http\Controllers\Enhance;

use App\Http\Controllers\Controller;
use App\Http\Kit\QiniuConfig;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Exception;

class UploadFileApi extends Controller {

    /*
     * 预先执行，安全检测
     * */
    public function __construct(Request $request){
        //parent::__construct($request);
        header('Access-Control-Allow-Origin:*');

        $upload_token = $request->input('upload_token');

        $token_array = ["test2019", "test"];

        // 拦截请求方法
        if (!is_post()){
            $back = [
                'state'=> 403,
                'msg'=> '此接口仅限POST，拒绝访问(UploadFile)',
                'content'=> '',
            ];
            //exit(json_encode($back, JSON_UNESCAPED_UNICODE));
        }

        // 检测白名单文件上传Token
        if (!in_array($upload_token, $token_array)){
            $back = [
                'state'=> 0,
                'msg'=> 'upload_token验证失败，文件无法上传(UploadFile)',
                'content'=> '',
            ];
            //exit(json_encode($back, JSON_UNESCAPED_UNICODE));
        }

        // 检测文件夹及文件夹权限
        $path = path_info()['storage_path']."/upload_file/"; //文件绝对路径
        $filename = $path;
        if(!file_exists($filename)){
            //检查是否有该文件夹，如果没有就创建，并给予最高权限
            mkdir($filename, 0777, true);

            $base64_img = $path.'/base64_img/';
            $url_img = $path.'/url_img/';
            $files = $path.'/files/';

            mkdir($base64_img, 0777, true);
            mkdir($url_img, 0777, true);
            mkdir($files, 0777, true);

            if (file_exists($filename)){
                $back = [
                    'state'=> 0,
                    'msg'=> '自动创建文件上传目录完成，但是数据提交请再试一次(UploadFile)',
                    'content'=> '',
                ];
                exit(json_encode($back, JSON_UNESCAPED_UNICODE));
            }else{
                $back = [
                    'state'=> 0,
                    'msg'=> '自动创建文件上传目录创建失败，请手动创建(UploadFile)',
                    'content'=> '',
                ];
                exit(json_encode($back, JSON_UNESCAPED_UNICODE));
            }

        }


    }


    /*
     * base64格式转图片并保存到本地，然后上传到七牛云
     * post方法：接口：save_base64_img
     *           方法：前端传入正确base64图片数据流即可
     * */
    public function save_base64_img(Request $request){
        header('Access-Control-Allow-Origin:*');
        $base64 = $request->input('base64_img');
        $x4 = $request->input('x4'); // 是否上传原图到七牛云，$x4=x4则上传

        if (!$base64){
            return array("status"=>0,"msg"=>"base64 is null", "content"=> []);
        }

        $path = path_info()['storage_path']."/upload_file/base64_img";
        $file = "/".date('Ymd', time())."/";

        //匹配出图片的格式
        if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64, $result)){
            $type = $result[2];

            $new_file = $path.$file;
            if(!file_exists($new_file)){
                //检查是否有该文件夹，如果没有就创建，并给予最高权限
                mkdir($new_file, 0755, true);
            }

            $file_name = $this->file_name_create($type); // 随机生成文件名

            $file_name_x1 = $file_name.'_x1.'.$type;
            $file_name_x2 = $file_name.'_x2.'.$type;
            $file_name_x3 = $file_name.'_x3.'.$type;
            $file_name_x4 = $file_name.'_x4.'.$type;

            $x4_path = $file_name.$file_name_x4; // 文件存放地址

            if (file_put_contents($x4_path, base64_decode(str_replace($result[1], '', $base64)))){ // 在服务器创建文件

                // x1低画质，x2中画质，x3高画质，x4原图
                $x1_path = $file_name.$file_name_x1;
                $this->compressed_image($x4_path, $x1_path, 300, 70);

                $x2_path = $file_name.$file_name_x2;
                $this->compressed_image($x4_path, $x2_path, 450, 80);

                $x3_path = $file_name.$file_name_x3;
                $this->compressed_image($x4_path, $x3_path, 600, 90);

                $qiniu = new QiniuConfig(); // 实例化七牛云

                $res_x1 = $qiniu->qiniu_upload_api($x1_path, $file_name.'_x1');
                $res_x2 = $qiniu->qiniu_upload_api($x2_path, $file_name.'_x2');
                $res_x3 = $qiniu->qiniu_upload_api($x3_path, $file_name.'_x3');
                if ($x4 == 'x4'){
                    $res_x4 = $qiniu->qiniu_upload_api($x4_path, $file_name.'_x4');
                }else{
                    $res_x4 = ['x4'];
                }

                $file_name = [$file_name_x1, $file_name_x2, $file_name_x3, $file_name_x4];
                $x_path = [$x1_path, $x2_path, $x3_path, $x4_path];
                $qiniu_info = [$res_x1, $res_x2, $res_x3, $res_x4];

                // 记录日志
                $log = new Log();
                $log->write_log('save_base64_img', $qiniu_info);

                $state = 1;
                $msg = "文件操作成功；x1低画质，x2中画质，x3高画质，x4原图；七牛云返回值情况请查看键qiniu_info。";
                $content =  ["file_name"=>$file_name, "qiniu_info"=>$res_x3];

            }else{
                $state = 0;
                $msg = "在服务器本地创建文件失败，原因是：父级目录没有777权限";
                $content =  [];
            }

        }else{
            $state = 0;
            $msg = "不是base64字符串编码的图片";
            $content =  [];

            // 记录日志
            $log = new Log();
            $log->write_log('save_base64_img', $msg);
        }

        $back = [
            "state"=> $state,
            "msg"=> $msg,
            "content"=> $content,
        ];

        return json_encode($back, JSON_UNESCAPED_UNICODE);
    }


    /*
     * 通过img的网址，保存到本地然后上传到七牛云
     * $img_url 下载文件地址
     * post方法：接口：save_url_img
     *           方法：前端传入图片的网址地址即可
     */
    public function save_url_img(Request $request) {
        header('Access-Control-Allow-Origin:*');

        $img_url = $request->input('img_url');
        $x4 = $request->input('x4'); // 是否上传原图到七牛云，$x4=x4则上传

        if (trim($img_url) == '') {
            return array("status"=>0,"msg"=>"img_url is null", 'content'=> []);
        }

        $path = path_info()['storage_path']."/upload_file/url_img"; //文件绝对路径
        $file = "/".date('Ymd', time())."/";

        $pattern = substr(strrchr($img_url, '.'), 1); // 正则文件格式
        $filename = $path.$file;
        if(!file_exists($filename)){
            //检查是否有该文件夹，如果没有就创建，并给予最高权限
            mkdir($filename, 0755, true);
        }

        if (!$pattern){
            $state = 0;
            $msg = "文件似乎无后缀。";
            $content =  [];

            // 记录日志
            $log = new Log();
            $log->write_log('save_url_img', [$img_url, $msg]);
        }else{

            $img_name = $this->file_name_create($pattern); // 随机生成文件名

            $file_name_x1 = $img_name.'_x1.'.$pattern;
            $file_name_x2 = $img_name.'_x2.'.$pattern;
            $file_name_x3 = $img_name.'_x3.'.$pattern;
            $file_name_x4 = $img_name.'_x4.'.$pattern;

            $x4_path = $filename.$file_name_x4; // 文件存放地址

            // 1. 下载并保存图片
            $img = file_get_contents($img_url);
            file_put_contents($x4_path, $img);

            // 2. 压缩图片
            // x1低画质，x2中画质，x3高画质，x4原图
            $x1_path = $filename.$file_name_x1;
            $this->compressed_image($x4_path, $x1_path, 300, 70);

            $x2_path = $filename.$file_name_x2;
            $this->compressed_image($x4_path, $x2_path, 450, 80);

            $x3_path = $filename.$file_name_x3;
            $this->compressed_image($x4_path, $x3_path, 600, 90);

            // 3. 上传到七牛云
            $qiniu = new QiniuConfig(); // 实例化七牛云

            $res_x1 = $qiniu->qiniu_upload_api($x1_path, $img_name.'_x1');
            $res_x2 = $qiniu->qiniu_upload_api($x2_path, $img_name.'_x2');
            $res_x3 = $qiniu->qiniu_upload_api($x3_path, $img_name.'_x3');
            if ($x4 == 'x4'){
                $res_x4 = $qiniu->qiniu_upload_api($x4_path, $img_name.'_x4');
            }else{
                $res_x4 = ['x4'];
            }

            // 4. 返回结果
            $file_name = [$file_name_x1, $file_name_x2, $file_name_x3, $file_name_x4];
            $x_path = [$x1_path, $x2_path, $x3_path, $x4_path];
            $qiniu_info = [$res_x1, $res_x2, $res_x3, $res_x4];

            // 记录日志
            $log = new Log();
            $log->write_log('save_url_img', $qiniu_info);

            $state = 1;
            $msg = "文件操作成功；x1低画质，x2中画质，x3高画质，x4原图；七牛云返回值情况请查看键qiniu_info。";
            $content =  ["file_name"=>$file_name, "qiniu_info"=>$res_x3];
        }

        $back = [
            "state"=> $state,
            "msg"=> $msg,
            "content"=> $content,
        ];

        return json_encode($back, JSON_UNESCAPED_UNICODE);
    }




    /*
     * 上传多文件类型-base64提交法
     * 任意文件以base64数据流传过来，保存成文件
     * $file_info = [0, "5C6B46D1681F52C4E326E84E49A9B97F.jpg", "image", "jpeg", 1541855, "data:image/jpeg;base64,/9j/4xxx"]
     * */
    public function upload_base64_file(Request $request){
        header('Access-Control-Allow-Origin:*');

        $file_info = $request->input('file_info');
        $x4 = $request->input('x4'); // 是否上传原图到七牛云，$x4=x4则上传

        if (!$file_info){
            return array("status"=>0,"msg"=>"file_info is null", 'content'=> [
                'file_info of Example'=>['i#&#file_name#&#file_class#&#file_type#&#file_size#&#file_base64']
            ]);
        }

        try{

            $file_info = explode('#&#', $file_info);

            $file_base64 = $file_info[5]; // 文件的base64编码
            $file_size = $file_info[4]; // 文件字节
            $file_type = $file_info[3]; // 文件后缀
            $file_class = $file_info[2]; // 文件类型video、image、stylesheet、javascript...
            $file_name = $file_info[1]; // 文件名称

            // 特殊文件格式转换
            switch ($file_type){
                case 'javascript':
                    $_file_type = 'js';
                    break;
                case 'vnd.openxmlformats-officedocument.wordprocessingml.document':
                    $_file_type = 'docx';
                    break;
                case 'vnd.openxmlformats-officedocument.presentationml.presentation':
                    $_file_type = 'pptx';
                    break;
                case 'nd.openxmlformats-officedocument.spreadsheetml.sheet':
                    $_file_type = 'xlsx';
                    break;
                case 'plain':
                    $_file_type = 'txt';
                    break;
                default:
                    $_file_type = $file_type;

            }

            if ($file_size < 2048*10000){ // 20M

                // 开始-保存文件
                $path = path_info()['storage_path']."/upload_file/files/"; //文件绝对路径

                $new_name = $this->file_name_create($file_class); // 随机生成文件名;
                $new_file = $path.$new_name.'.'.$_file_type;

                try{
                    $replace_str = 'data:'.$file_class.'/'.$file_type.';base64,'; // 只取base64码部分

                    $save = file_put_contents($new_file, base64_decode(str_replace($replace_str, '', $file_base64))); // 在服务器创建文件
                    if ($save){

                        $img_ext = ['jpeg', 'png', 'gif'];
                        if (in_array($file_type, $img_ext)) { // 是图片则压缩

                            $file_name_x1 = $new_name.'_x1.'.$file_type;
                            $file_name_x2 = $new_name.'_x2.'.$file_type;
                            $file_name_x3 = $new_name.'_x3.'.$file_type;
                            $file_name_x4 = $new_name.'_x4.'.$file_type;

                            $x4_path = $new_file;

                            // x1低画质，x2中画质，x3高画质，x4原图
                            $x1_path = $path.$file_name_x1;
                            $this->compressed_image($x4_path, $x1_path, 300, 70);

                            $x2_path = $path.$file_name_x2;
                            $this->compressed_image($x4_path, $x2_path, 450, 80);

                            $x3_path = $path.$file_name_x3;
                            $this->compressed_image($x4_path, $x3_path, 600, 90);

                            $qiniu = new QiniuConfig(); // 实例化七牛云

                            $res_x1 = $qiniu->qiniu_upload_api($x1_path, $new_name.'_x1');
                            $res_x2 = $qiniu->qiniu_upload_api($x2_path, $new_name.'_x2');
                            $res_x3 = $qiniu->qiniu_upload_api($x3_path, $new_name.'_x3');
                            if ($x4 == 'x4'){
                                $res_x4 = $qiniu->qiniu_upload_api($x4_path, $new_name.'_x4');
                            }else{
                                $res_x4 = ['x4'];
                            }

                            // 4. 返回结果
                            $file_name = [$file_name_x1, $file_name_x2, $file_name_x3, $file_name_x4];
                            $x_path = [$x1_path, $x2_path, $x3_path, $x4_path];
                            $qiniu_info = [$res_x1, $res_x2, $res_x3, $res_x4];

                            $state = 1;
                            $msg = "文件操作成功；x1低画质，x2中画质，x3高画质，x4原图；七牛云返回值情况请查看键qiniu_info。";
                            $content =  ["file_name"=>$file_name, "file_size"=>$file_size, "qiniu_info"=>$res_x3];

                        }else{ // 保存文件

                            // 上传文件到七牛云
                            $qiniu = new QiniuConfig();
                            $res = $qiniu->qiniu_upload_api($new_file, $new_name);

                            $state = 1;
                            $msg = "文件操作成功；七牛云返回值情况请查看键qiniu_info。";
                            $content =  ["file_name"=>$new_name.'.'.$_file_type, "file_size"=>$file_size, "server_info"=>$new_file, "qiniu_info"=>$res];

                            // 记录日志
                            $log = new Log();
                            $log->write_log('upload_base64_file', $res);

                        }

                    }else{
                        $state = 0;
                        $msg = "在服务器本地创建文件失败，原因是：父级目录没有777权限";
                        $content =  [];
                    }

                }catch (Exception $error){
                    $state = 0;
                    $msg = "不能保存为文件。保存文件的路径格式为：路径/文件名.文件格式。";
                    $content =  [];
                }
                // 结束-保存文件

            }else{
                $state = 0;
                $msg = "文件过大，文件限制在20M内。";
                $content = [
                    "文件类型"=> $file_type,
                    "文件大小"=> $file_size,
                ];

                // 记录日志
                $log = new Log();
                $log->write_log('upload_base64_file', [$file_size, $msg]);
            }



        } catch (Exception $error){
            $state = 0;
            $msg = "数组解析错误。";
            $content =  $file_info;
        }


        $back = [
            "state"=> $state,
            "msg"=> $msg,
            "content"=> $content,
        ];

        return json_encode($back, JSON_UNESCAPED_UNICODE);

    }


    /*
     * 上传多文件类型-form提交法
     * 支持小程序wx.UploadFile()；支持form提交
         <form action="xxx/upload_file/upload_many_type_file" method="post" enctype="multipart/form-data">
            <label>上传文件：.mp3、.js、.css、.png、.jpg、.jpeg、.gif</label>
            <input type="file" name="file" id="file"><br>
            <br><br>
            <input type="submit" name="submit" value="提交">
         </form>
     * */
    public function upload_form_file(Request $request){
        header('Access-Control-Allow-Origin:*');
        $x4 = $request->input('x4'); // 是否上传原图到七牛云，$x4=x4则上传

        if(is_post()){

            $fileErr=$_FILES['file']['error'];
            if($fileErr){
                $state = 0;
                $msg = "文件上传出错，请注意：1.文件最大可上传大小；2.服务器上传目录777。";
                $content = "";
            }else{//控制上传类型以及大小
                $file_size = $_FILES["file"]["size"];
                if($file_size < 2048*10000){ // 20M

                    /*开始*/
                    $upload_path = path_info()['storage_path']."/upload_file/files/"; // 文件保存目录
                    $type = array("mp3", "js", "css", "jpg", "jpeg", "png", "bmp", "ico", "gif", "mp4");//允许上传文件的类型

                    $ext = strtolower($this->file_ext($_FILES['file']['name']));
                    if (!in_array($ext, $type)) {
                        $text = implode(",", $type);

                        $state = 0;
                        $msg = "文件类型在：".$text;
                        $content = "";

                    } else {
                        do {
                            $filename = $this->file_name_create($ext); // 随机生成文件名
                            $that_file = $filename.'.'.$ext;
                            $file = $upload_path.$that_file; // 文件存放地址
                        } while (file_exists($file));
                        if (move_uploaded_file($_FILES['file']['tmp_name'], $file)) {//上传
                            if (is_uploaded_file($_FILES['file']['tmp_name'])) { // 已存在
                                $state = 0;
                                $msg = "文件已存在";
                                $content =  [$that_file];

                            } else { // 上传成功

                                $img_ext = ['jpg', 'jpeg', 'png', 'gif'];
                                if (in_array($ext, $img_ext)){ // 是图片则压缩

                                    $file_name_x1 = $filename.'_x1.'.$ext;
                                    $file_name_x2 = $filename.'_x2.'.$ext;
                                    $file_name_x3 = $filename.'_x3.'.$ext;
                                    $file_name_x4 = $filename.'_x4.'.$ext;

                                    $x4_path =  $file;

                                    // x1低画质，x2中画质，x3高画质，x4原图
                                    $x1_path = $upload_path.$file_name_x1;
                                    $this->compressed_image($x4_path, $x1_path, 300, 70);

                                    $x2_path = $upload_path.$file_name_x2;
                                    $this->compressed_image($x4_path, $x2_path, 450, 80);

                                    $x3_path = $upload_path.$file_name_x3;
                                    $this->compressed_image($x4_path, $x3_path, 600, 90);

                                    $qiniu = new QiniuConfig(); // 实例化七牛云

                                    $res_x1 = $qiniu->qiniu_upload_api($x1_path, $filename.'_x1');
                                    $res_x2 = $qiniu->qiniu_upload_api($x2_path, $filename.'_x2');
                                    $res_x3 = $qiniu->qiniu_upload_api($x3_path, $filename.'_x3');
                                    if ($x4 == 'x4'){
                                        $res_x4 = $qiniu->qiniu_upload_api($x4_path, $filename.'_x4');
                                    }else{
                                        $res_x4 = ['x4'];
                                    }

                                    $file_name = [$file_name_x1, $file_name_x2, $file_name_x3, $file_name_x4];
                                    $x_path = [$x1_path, $x2_path, $x3_path, $x4_path];
                                    $qiniu_info = [$res_x1, $res_x2, $res_x3, $res_x4];

                                    $state = 1;
                                    $msg = "文件操作成功；x1低画质，x2中画质，x3高画质，x4原图；七牛云返回值情况请查看键qiniu_info。";
                                    $content =  ["file_name"=>$file_name,"file_size"=>$file_size, "qiniu_info"=>$res_x3];

                                }else{ // 不压缩
                                    // 上传到七牛
                                    $qiniu = new QiniuConfig();
                                    $res = $qiniu->qiniu_upload_api($file, $filename);

                                    $state = 1;
                                    $msg = "文件操作成功；七牛云返回值情况请查看键qiniu_info。";
                                    $content =  ["file_name"=>$filename.'.'.$ext, "file_size"=>$file_size, "server_info"=>$file, "qiniu_info"=>$res];

                                }

                                // 记录日志
                                $log = new Log();
                                $log->write_log('upload_form_file', [$content, $msg]);

                            }
                        }else{
                            $state = 0;
                            $msg = "文件夹可能不存在或777";
                            $content =  [$that_file, $upload_path];
                        }
                    }
                    /*结束*/


                }else{
                    $state = 0;
                    $msg = "文件过大，文件限制在20M内。";
                    $content = [
                        "文件类型"=>  $_FILES['file']['type'],
                        "文件大小"=> $_FILES['file']['size'],
                    ];

                    // 记录日志
                    $log = new Log();
                    $log->write_log('upload_form_file', [$file_size, $msg]);
                }
            }

        }else{
            $state = 0;
            $msg = "小程序请使用wx.UploadFile()上传文件，或web使用form+post上传。";
            $content = "";

            // 记录日志
            $log = new Log();
            $log->write_log('upload_form_file', [$msg]);
        }

        $back = [
            "state"=> $state,
            "msg"=> $msg,
            "content"=> $content,
        ];

        return json_encode($back, JSON_UNESCAPED_UNICODE);

    }


    //获取文件后缀名
    public function file_ext($filename){
        return substr(strchr($filename, '.'), 1);
    }

    //生成文件名
    public function file_name_create($ext){
        $file_name = date('Ymd_His_').$ext.'_'.uniqid().get_rand_string(rand(3, 5));
        return $file_name;
    }

    /**
     * desription 判断是否gif动画
     * @param sting $image_file图片路径
     * @return boolean t 是 f 否
     */
    public function check_gifcartoon($image_file){
        $fp = fopen($image_file,'rb');
        $image_head = fread($fp,1024);
        fclose($fp);
        return preg_match("/".chr(0x21).chr(0xff).chr(0x0b).'NETSCAPE2.0'."/",$image_head)?false:true;
    }

    /*
     * 压缩图片，等比例压缩
     * 耗运存
     * compressed_image(图片路径, 图片压缩后的保存路径, 图片宽px)
     * */
    public function compressed_image($imgsrc, $imgdst, $max_width, $quality) {
        list($width, $height, $type) = getimagesize($imgsrc);

        $max_width = $max_width?$max_width:600; // 图片最大宽px
        $quality = $quality?$quality:90; // 质量、压缩图片容量大小 (0, 100]

        if($width >= $max_width){
            $per = $max_width / $width;//计算比例
            $new_width = $width * $per;
            $new_height = $height * $per;
        }

        switch($type){
            case 1:
                $giftype = $this->check_gifcartoon($imgsrc);
                if($giftype){
                    header('Content-Type:image/gif');
                    $image_wp=imagecreatetruecolor($new_width, $new_height);
                    $image = imagecreatefromgif($imgsrc);
                    imagecopyresampled($image_wp, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
                    imagejpeg($image_wp, $imgdst, $quality);
                    imagedestroy($image_wp);
                }
                break;
            case 2:
                header('Content-Type:image/jpeg');
                $image_wp=imagecreatetruecolor($new_width, $new_height);
                $image = imagecreatefromjpeg($imgsrc);
                imagecopyresampled($image_wp, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
                imagejpeg($image_wp, $imgdst, $quality);
                imagedestroy($image_wp);
                break;
            case 3:
                header('Content-Type:image/png');
                $image_wp=imagecreatetruecolor($new_width, $new_height);
                $image = imagecreatefrompng($imgsrc);
                imagecopyresampled($image_wp, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
                imagejpeg($image_wp, $imgdst, $quality);
                imagedestroy($image_wp);
                break;
        }



    }




}
