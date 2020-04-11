<?php

/*
 * 处理上传文件的过程
 * */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Enhance\Log;
use App\Http\Controllers\EnhanceSafeCheck;
use App\Http\Kit\QiniuConfig;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Exception;

class UploadFile extends EnhanceSafeCheck {

    public function __construct(Request $request){
        parent::__construct($request);


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
    final function upload_form_file(Request $request){
        header('Access-Control-Allow-Origin:*');
        $file_tag_id = $request->input('file_tag_id');

        if(is_post()){

            $fileErr=$_FILES['file']['error'];
            if($fileErr){
                $state = 0;
                $msg = "文件上传出错，请注意：1.文件最大可上传大小；2.服务器上传目录777。";
                $content = "";
            }else{//控制上传类型以及大小
                $file_size = $_FILES["file"]["size"];
                if($file_size < 4096*10000){ // 40M

                    /*开始*/
                    $upload_path = path_info()['storage_path']."/upload_file/files/"; // 文件保存目录
                    $type = array("mp3", "mp4", "pdf", "ppt", "pptx", "doc", "docx", "xls", "xlsx", "zip", "7z", "rar", "exe", "dmg", "rtf", "txt");//允许上传文件的类型

                    $ext = strtolower($this->file_ext($_FILES['file']['name']));
                    $file_zh_name = $_FILES['file']['name'];

                    if (!in_array($ext, $type)) {
                        $text = implode(",", $type);

                        $state = 0;
                        $msg = "文件类型不支持，文件类型范围：".$text;
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

                                // 保存数据
                                $data = [
                                    'file_tag_id'=>$file_tag_id,
                                    'file_zh_name'=>$file_zh_name,
                                    'file_name'=>$filename,
                                    'file_ext'=>$ext,
                                    'file_size'=>$file_size,
                                    'server_path'=>$file,
                                    'create_time'=> to_time(date('YmdHis')),
                                ];
                                $res = Db::table('file_upload')->insert($data);

                                $state = 1;
                                $msg = "文件操作成功";
                                $content =  ["file_name"=>$filename.'.'.$ext, "file_size"=>$file_size, "server_path"=>$file];

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
                    $msg = "文件过大，文件限制在40M内。";
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

//        $back = [
//            "state"=> $state,
//            "msg"=> $msg,
//            "content"=> $content,
//        ];


        // return json_encode($back, JSON_UNESCAPED_UNICODE);
        $time = time();
        $back_url = "/cswd/view-admin/file_upload_do.php?nav=cswd_config&upload=go&state=$state&msg=$msg&time=$time";
        header('Location: '.$back_url);
        exit();

    }


    // 获取文件后缀名
    public function file_ext($filename){
        return substr(strchr($filename, '.'), 1);
    }

    //生成文件名
    public function file_name_create($ext){
        $file_name = date('Ymd_His_').$ext.'_'.uniqid().get_rand_string(rand(3, 5));
        return $file_name;
    }






}
