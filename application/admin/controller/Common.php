<?php
namespace app\admin\controller;
use think\Db;
use think\facade\Cache;

class Common extends Error
{
    /** 
     * @api {post} /admin.php/admin/common/login  后台登录
     * @param int  page null
     * 
     * @return array
     */
    public function loginApi()
    {
       $param = input('post.');

       if(empty($param['admin']) || empty($param['password'])){
            return [501,'请输入用户名和密码'];
       }
       $admin = Db::table('zk_admininfo')->where('admin',$param['admin'])->find();
       if(strcmp(md5($param['password']),$admin['password']) != 0){
            return [501,'密码错误'];
       }

       $token = md5(time().$admin['id']);
       Cache::set($token,[
            'id'=>$admin['id'],
            'admin' =>$admin['admin'],
            'login_time' => date('Y-m-d H:i:s'),
       ],3600*2);
       return [200,['token'=>$token]];
    }

    public function loginInfoApi(){
        return [200,$this->s_user];
    }

    public function outApi(){
        Cache::rm($this->token);
        return [300,'退出成功'];
    }


    
}
