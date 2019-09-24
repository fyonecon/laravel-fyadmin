<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AdminSafeCheck;
use App\Http\Kit\Secret;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Exception;

class AdminUser extends AdminSafeCheck{

    /*
     * 添加用户
     * 功能：管理员添加用户
     * */
    public function add_user(Request $request){

        $father_user_id = $request->input('father_user_id');
        $user_level = $request->input('user_level');
        $user_name = $request->input('user_name');
        $user_login_name = $request->input('user_login_name');
        $user_login_pwd = $request->input('user_login_pwd');
        $user_remark = $request->input('user_remark');

        $create_time = to_time(date('YmdHis'));

        // 检测用户登录名是否已经存在
        $has = $this->has_user_login_pwd($user_login_name);

        if ($has){

            $state = 0;
            $msg = '用户名已存在，无法执行。';
            $content = '';

        }else{

            // 用户密码加密
            $user_login_pwd = pwd_encode($user_login_pwd);

            $data = [
                'father_user_id'=> $father_user_id,
                'user_level'=> $user_level,
                'user_name'=> $user_name,
                'user_login_name'=> $user_login_name,
                'user_login_pwd'=> $user_login_pwd,
                'user_remark'=> $user_remark,

                'create_time'=> $create_time,
            ];

            $res = Db::table('admin_user')->insert($data);

            if ($res){
                $state = 1;
                $msg = '新增成功';
                $content = $res;
            }else{
                $state = 0;
                $msg = '新增失败';
                $content = '';
            }

        }

        $back = [
            'state'=>$state,
            'msg'=>$msg,
            'content'=>$content,
        ];

        return json_encode($back, JSON_UNESCAPED_UNICODE);

    }

    /*
     * 用户列表
     * 接口 admin/user/list_user&user_id=all
     * 功能：输出某user_id的用户数据；
     *      全部用户的数据
     * */
    public function list_user(Request $request){

        $user_id = $request->input('user_id');

        $page = $request->input('page');
        if (!$page){
            $page = 1;
        }

        if ($user_id === 'all'){

            $res = Db::table('admin_user')->alias('a')
                ->where('a.user_state', 1)
                ->where('a.user_level', 2)
                ->order('a.user_id' ,'desc')
                ->page($page, page_limit())
                ->select('a.user_id, a.user_name, a.user_login_name, a.user_remark, a.create_time, a.update_time, a.father_user_id')
                ->select();

            $data_count = Db::table('admin_user')->alias('a')
                ->where('a.user_state', 1)
                ->where('a.user_level', 2)
                ->order('a.user_id' ,'desc')

                ->count('a.user_id');

        }else{

            $res = Db::table('admin_user')->alias('a')
                ->where('a.user_id', $user_id)
                ->where('a.user_state', 1)
                ->where('a.user_level', 2)
                ->order('a.user_id' ,'desc')
                ->select('a.user_id, a.user_name, a.user_login_name, a.user_remark, a.create_time, a.update_time')
                ->find();

            $data_count = count($res);
        }

        if ($res){
            $state = 1;
            $msg = '获取完成';
            $content = $res;
        }else{
            $state = 0;
            $msg = '获取失败';
            $content = '';
        }

        $back = [
            'state'=>$state,
            'msg'=>$msg,
            'page'=>$page,
            'limit'=>page_limit(),
            'data_count'=> $data_count,
            'content'=>$content,
        ];

        return json_encode($back, JSON_UNESCAPED_UNICODE);

    }

    /*
     * 修改用户
     * */
    public function edit_user(Request $request){

        $user_id = $request->input('user_id');

        $user_name = $request->input('user_name');
        $user_login_name = $request->input('user_login_name');
        $user_login_pwd = $request->input('user_login_pwd');
        $user_remark = $request->input('user_remark');

        $update_time = to_time(date('YmdHis'));

        // 检测用户登录名是否已经存在
        $has = $this->has_user_login_pwd($user_login_name);

        if ($has){

            $state = 0;
            $msg = '用户名已存在，无法执行。';
            $content = '';

        }else{

            // 用户密码加密
            $user_login_pwd = pwd_encode($user_login_pwd);

            $data = [

                'user_name'=> $user_name,
                'user_login_name'=> $user_login_name,
                'user_login_pwd'=> $user_login_pwd,
                'user_remark'=> $user_remark,

                'update_time'=> $update_time,
            ];

            $res = Db::table('admin_user')->where('user_id', $user_id)->update($data);

            if ($res){
                $state = 1;
                $msg = '更新成功';
                $content = $res;
            }else{
                $state = 0;
                $msg = '更新失败';
                $content = '';
            }



        }



        $back = [
            'state'=>$state,
            'msg'=>$msg,
            'content'=>$content,
        ];

        return json_encode($back, JSON_UNESCAPED_UNICODE);

    }

    /*
     * 删除用户，假删除
     * */
    public function del_user(Request $request){

        $user_id = $request->input('user_id');

        $update_time = to_time(date('YmdHis'));

        $data = [
            'user_state'=> 2,
            'update_time'=> $update_time,
        ];

        $res = Db::table('admin_user')->where('user_id', $user_id)->update($data);

        if ($res){
            $state = 1;
            $msg = '删除成功';
            $content = $res;
        }else{
            $state = 0;
            $msg = '删除失败，可能是没有该用户。';
            $content = '';
        }

        $back = [
            'state'=>$state,
            'msg'=>$msg,
            'content'=>$content,
        ];

        return json_encode($back, JSON_UNESCAPED_UNICODE);

    }

    /*
     * 检测用户登录名是否已经存在
     * */
    public function has_user_login_pwd($user_login_pwd){

        $res = Db::table('admin_user')->where('user_login_pwd', $user_login_pwd)->value('user_id');

        if ($res){
            return true;
        }else{
            return false;
        }
    }

    /*
     * 超级管理员列表
     * */
    public function list_admin(Request $request){

        $user_id = $request->input('user_id');

        $page = $request->input('page');
        if (!$page){
            $page = 1;
        }

        if ($user_id === 'all'){

            $res = Db::table('admin_user')->alias('a')
                ->where('a.user_state', 1)
                ->where('a.user_level', 1)
                ->order('a.user_id' ,'desc')
                ->page($page, page_limit())
                ->select('a.user_id, a.user_name, a.user_login_name, a.user_remark, a.create_time, a.update_time, a.father_user_id')
                ->select();

            $data_count = Db::table('admin_user')->alias('a')
                ->where('a.user_state', 1)
                ->where('a.user_level', 1)
                ->order('a.user_id' ,'desc')

                ->count('a.user_id');

        }else{

            $res = Db::table('admin_user')->alias('a')
                ->where('a.user_id', $user_id)
                ->where('a.user_state', 1)
                ->where('a.user_level', 1)
                ->order('a.user_id' ,'desc')
                ->select('a.user_id, a.user_name, a.user_login_name, a.user_remark, a.create_time, a.update_time')
                ->find();

            $data_count = count($res);
        }

        if ($res){
            $state = 1;
            $msg = '获取完成';
            $content = $res;
        }else{
            $state = 0;
            $msg = '获取失败';
            $content = '';
        }

        $back = [
            'state'=>$state,
            'msg'=>$msg,
            'page'=>$page,
            'limit'=>page_limit(),
            'data_count'=> $data_count,
            'content'=>$content,
        ];

        return json_encode($back, JSON_UNESCAPED_UNICODE);

    }



}
