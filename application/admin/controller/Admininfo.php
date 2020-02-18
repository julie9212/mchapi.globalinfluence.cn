<?php
namespace app\admin\controller;
use think\Db;

class Admininfo extends Error
{

    /** 
     * @api {post} /admin.php/admin/admininfo/list  后台系统信息
     * @param int  
     * 
     * @return array
     */
    public function listApi()
    {
        $param = input('post.');
        $total = Db::table('zk_admininfo')->where('id',1)->count();
        if ($total == 0) {
            return [200,['total'=>0, 'data' => []]];
        }
        // 修改内容
        $info = Db::table('zk_admininfo')->where('id',1)->field("id,admin,password,title,logo,keywords,description,update_time,phone,phone2,email,address,copyright")->find();
        return [200,['info'=>$info]];
    }

    /** 
     * @api {post} /admin.php/admin/admininfo/update  后台内容修改
     * @param int     id vip status
     * @param string  username password head_url
     *
     * @return array
     */
    public function updateApi()
    {
        $param = input('post.');
       // 判断是否存在
        $row = Db::table('zk_admininfo')->where('id',1)->count();
        if($row<1){
            return [501,'修改目标不存在'];
        }
        // 修改
        $info = Db::table('zk_admininfo') ->where('id',1)->update($param['form']);
        if($info == 0){
            return [501,'操作失败'];
        }
        return [200,true];
    }

    /** 
     * @api {post} /admin.php/admin/admininfo/update  后台内容修改
     * @param int     id vip status
     * @param string  username password head_url
     *
     * @return array
     */
    public function passupdateApi()
    {
        $param = input('post.');
        $passOld = Db::table('zk_admininfo')->where('id',1)->value('password');
        if($passOld != md5($param['passwordOld'])){
            return [501,'密码错误'];
        }
        $pass['password'] = md5($param['password']);
        $info = Db::table('zk_admininfo') ->where('id',1)->update($pass);
        if($info == 0){
            return [501,'操作失败'];
        }
        return [200,true];
    } 

}
