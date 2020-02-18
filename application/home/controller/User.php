<?php
namespace app\home\controller;
use think\Db;
use think\facade\Cache;


class User extends Error
{
	/** 
     * @api {post} /index.php/home/user/user 登录
     * @param int  
     * 
     * @return array
     */
    public function userApi()
    {
        if (!$this->s_user) {
            $this->s_user = false;
        }
        $data = $this->s_user;
        $id = $data['id'];

        $userInfo = Db::table('zk_user')->where('id',$id)->field("id,username,head_url,phone,abstract,vip,download")->find();

        $where['is_delete'] = 0;
        $where['examine'] = 2;

        $count = Db::table('zk_content')->where($where)->where('id',$id)->count();

        $user_operate = Db::table('zk_content')->where($where)->where('user_id',$id)->order(['sort'=>'desc','id'=>'desc'])->select();

        // 可发稿栏目 研究观点子栏目 pid=2
        $column = Db::table('zk_column')->where('is_delete',0)->where('pid',2)->field("id,name")->select();

        // 文稿查看
        $release['unaudited'] = Db::table('zk_content')->where('is_delete',0)->where('user_id',$id)->where('examine',1)->field("id,img_url,title,writer,create_time,abstract")->select();
        $release['adopt'] = Db::table('zk_content')->where('is_delete',0)->where('user_id',$id)->where('examine',2)->field("id,img_url,title,writer,create_time,abstract")->select();
        $release['reject'] = Db::table('zk_content')->where('is_delete',0)->where('user_id',$id)->where('examine',3)->field("id,img_url,title,writer,create_time,abstract")->select();
      
      
        return [200,['userInfo'=>$userInfo,'user_operate'=>$user_operate,'column'=>$column,'release'=>$release]];
    }

    /** 
     * @api {post} /index.php/home/user/update  用户修改信息
     * @param int     id vip status
     * @param string  username password head_url
     *
     * @return array
     */
    public function updateApi()
    {
        $param = input('post.');
        // 判断用户是否存在
        $user = Db::table('zk_user')->where('id',$param['id'])->where('is_delete',0)->find();
        if(!$user){
            return [501,'该用户不存在'];
        }

        // 修改用户
        $row = Db::table('zk_user') ->where('id', $param['id'])->update([
            'username' => $param['username'],
            'head_url' => $param['head_url'],
            'abstract' => $param['abstract'],
            'phone' => $param['phone'],
            'is_delete' => 0,
        ]);
        if($row == 0){
            return [501,'操作失败'];
        }
        return [200,true];
    }

    /** 
     * @api {post} /index.php/home/user/updatePass  用户修改信息
     * @param int     id vip status
     * @param string  username password head_url
     *
     * @return array
     */
    public function updatePassApi()
    {
        $param = input('post.');
        if (!$this->s_user) {
            $this->s_user = false;
        }
        $data = $this->s_user;
        $param['id'] = $data['id'];
    
        // 判断用户是否存在
        $user = Db::table('zk_user')->where('id',$param['id'])->where('is_delete',0)->find();
        if(!$user){
            return [501,'该用户不存在'];
        }

        $oldpass = Db::table('zk_user')->where('is_delete',0)->where('id',$param['id'])->where('password',md5($param['oldpass']))->find();
        if(!$oldpass){
            return [501,'旧密码错误，请重新提交'];
        }

        // 修改密码
        $row = Db::table('zk_user') ->where('id', $param['id'])->update([
            'password' => md5($param['newpass']),
        ]);
        if($row == 0){
            return [501,'操作失败'];
        }
        return [200,true];
    }

    /** 
     * @api {post} /index.php/home/user/userRelease  用户提交文稿
     * @param int     
     * @param string  
     *
     * @return array
     */
    public function insertReleaseApi()
    {
        $param = input('post.');
        if (!$this->s_user) {
            $this->s_user = false;
        }
        $data = $this->s_user;
        $user_id = $data['id'];

        // 添加用户
        $row = Db::table('zk_content')->insert([
            'title' => $param['title'],
            'source' => $param['source'],
            'writer' => $param['writer'],
            'examine' => 1,
            'abstract' => $param['abstract'],
            'column_id' => $param['column_id'],
            'img_url' => $param['img_url'],
            'create_time' => time(),
            'content' => $param['content'],
            'user_id' => $user_id,
            'is_delete' =>0,
        ]);
        if($row < 1){
            return [501,'操作失败'];
        }
        return [200,true];
    }

}
