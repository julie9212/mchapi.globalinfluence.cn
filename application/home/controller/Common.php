<?php
namespace app\home\controller;
use think\Db;
use think\facade\Cache;


class Common extends Error
{
	/** 
     * @api {post} /index.php/home/common/login 登录
     * @param int  
     * 
     * @return array
     */
    public function loginApi()
    {
        $param = input('post.');
        // return [200,['param' => $param]];
        if(empty($param['phone']) || empty($param['password'])){
            return [501,'请输入手机号和密码'];
        }

        $user = Db::table('zk_user')->where('is_delete',0)->where('phone',$param['phone'])->find();
        if(!$user || $user['status'] == 2){
            return [501,'该账户未开通或已禁用，请联系管理员'];
        }

        if(strcmp(md5($param['password']),$user['password']) !=0){
            return [501,'密码错误'];
        }

        $token = md5(time().$user['id']);
        Cache::set($token,[
            'id'=>$user['id'],
            'username' => $user['username'],
            'head_url' => $user['head_url'],
            'login_time' => date('Y-m-d H:i:s')
        ],3600*24);

        return [200,['token' => $token]];
    }

    /** 
     * @api {post} /index.php/home/common/register 注册
     * @param int  
     * 
     * @return array
     */
    public function registerApi()
    {
        $param = input('post.');

        $user = Db::table('zk_user')->where('phone',$param['phone'])->find();
        if($user){
            return [501,'该用户已存在'];
        }

        $codeCache = Cache::get('code');
        if($param['code'] != $codeCache){
            return [501,'验证码错误，请重新提交'];
        }
        
        $row = Db::table('zk_user')->insert([
            'username' => "zk_".time(),
            'password' => md5($param['password']),
            'phone' => $param['phone'],
            'vip' => 1,
            'status' => 1,
            'time' => time(),
            'is_delete' => 0,
        ]);
        if($row <= 0){
            return [501,'注册失败，请重新提交'];
        }

        return [200,true];
    }

    /** 
     * @api {post} /index.php/home/common/pass 修改密码
     * @param int  
     * 
     * @return array
     */
    public function passApi()
    {
        $param = input('post.');
        // return [200,'pass接口'];

        $user = Db::table('zk_user')->where('phone',$param['phone'])->find();
        if(!$user){
            return [501,'该用户不存在'];
        }

        $codeCache = Cache::get('code');
        if($param['code'] != $codeCache){
            return [501,'验证码错误，请重新提交'];
        }

        $row = Db::table('zk_user')->where('phone',$param['phone'])->update([
            'password' => md5($param['password'])
        ]);
        if($row <= 0){
            return [501,'修改失败，请确认后重新提交'];
        }

        return [200,true];
    }


    // 登录获取信息
    public function loginInfoApi()
    {
        if (!$this->s_user) {
            $this->s_user = false;
        }
        return [200,$this->s_user];
    }

    // 退出
    public function outApi()
    {
        Cache::rm($this->token);
        return [300,'退出成功'];
    }



    // 修改密码
    public function changePassApi()
    {
        $param = input('post.');

        $user = Db::table('zk_user')->where('is_delete',0)->where('status',1)->find();
        if(!$user){
            return [501,'用户信息错误，请重新登录'];
        }

        if(strcmp(md5($param['oldpassword']),$user['password']) != 0){
            return [501,'原密码错误'];
        }

        $row = Db::table('zk_user')->where('id',$this->s_user['id'])->update([
            'password' => (md5($param['password'])),
        ]);

        if($row == 0){
            return [501,'修改失败'];
        }
        // Cache::rm($this->token);
        return [300,'修改成功，请重新登录'];
    }

}
