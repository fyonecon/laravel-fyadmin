<?php

/*
 * 七牛云参数配置
 * */

namespace App\Http\Controllers\Kit;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Kit\Secret;
use App\Http\Controllers\OpenSafeCheck;
use Exception;
use Illuminate\Http\Request;
use Qiniu\Auth;
use Qiniu\Storage\UploadManager;
use Qiniu\Storage\BucketManager;

require_once laravel_path_info()[0].'/vendor/qiniu/autoload.php';

class QiniuConfig extends Controller{

    /*
     * 向七牛云上传文件【post Api】，支持【一切有格式】的文件，并返回文件名
     * 接口示例：http://xxxxxx/public/?s=/qiniuapi/index/qiniu_upload_api
     *
     * $file_path：文件服务器中路径，例如：D:\wamp64\www\wxmail\h5/letter/niming.png 写法：ROOT_PATH."h5/letter/niming.png"
     * $qiniu_bucket：七牛bucket
     *
     * */
    public function qiniu_upload_api($file_path, $file_name){

        if($_REQUEST){

            $accessKey      = 'sjLe9UAn3b8pNAqZW5CiKmNhiQYguqDr7_0_Iv7Q'; // 请替换成你自己的
            $secretKey      = 'CXUZS1F55dI6DimtMFOKLziJ4v34Wijo7NOnzu25'; // 请替换成你自己的
            $domain         = ['http:qiniu-test.meishid.cn/']; // 七牛云的主网址
            $qiniu_bucket   = 'test'; // 七牛上面的文件夹，需要自己手动创建

            if(!$file_path || !$qiniu_bucket){
                return array("status"=>0,"msg"=>"file_path or qiniu_bucket is null");
            }

            $bucket = $qiniu_bucket;

            $auth = new Auth($accessKey,$secretKey);
            $token = $auth->uploadToken($bucket);
            $uploadMgr = new UploadManager();

            $files = $file_path; // 文件服务器中路径
            $pattern = substr(strrchr($files, '.'), 1); // 正则文件格式
            if (!$pattern){
                return array("state"=>0, "content"=>"pattern is null");
            }

            $tmpArr = array($files);
            foreach ($tmpArr as $k => $value) {
                $filePath = $value;
                $key = $file_name.".".$pattern; // 文件保存的路径及其文件名
                $res = $uploadMgr->putFile($token, $key, $filePath); // 上传

                if ($res){ //成功上传
                    return array("state"=>1, "msg"=>"qiniu-upload is success", "file"=>$res[0]['key'], "file_info"=>$res, "qiniu_domain"=>$domain); // 返回文件名
                }else{
                    return array("state"=>0,"msg"=>"qiniu-upload is error");
                }
            }

        }else{
            return array( "state"=>0, "msg"=>"REQUEST is error");
        }
    }



}
