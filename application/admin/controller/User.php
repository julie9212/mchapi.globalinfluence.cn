<?php
namespace app\admin\controller;
use think\Db;
use think\facade\Cache;

class User extends Error
{
    /** 
     * @api {post} /admin.php/admin/user/list  后台用户列表
     * @param int  page null
     * 
     * @return array
     */
    public function listApi()
    {
        $param = input('post.');
        $page = $param['page'] ?? 1;

        $where['is_delete'] = 0;

        //用户搜索
        if (!empty($param['username'])) {
            $where['username'] = $param['username'];
        }

        $total = Db::table('zk_user')->where($where)->count();
        if ($total == 0) {
            return [200,['total'=>0, 'data' => []]];
        }

        $data = Db::table('zk_user')->where($where)->field("id,username,phone,head_url,vip,abstract,time,status")->order('id','desc')->page($page,10)->select();
        foreach($data as &$v){
            $v['time'] = date( "Y-m-d H:i:s",$v['time']);
        }
        return [200,['total'=>$total,'data'=>$data]];
    }


    /** 
     * @api {post} /admin.php/admin/user/insert  后台用户添加
     * @param int     vip status
     * @param string  username password head_url
	 *
     * @return array
     */
    public function insertApi()
    {
        $param = input('post.');

        // 验证器
        $error = $this->validate($param,'User.insert');
        if (true !== $error) {
            return [501,$error];
        }

        // 判断用户是否存在
        $user = Db::table('zk_user')->where('username',$param['username'])->find();
        if($user){
            return [501,'该用户已存在'];
        }

        // 添加用户
        $row = Db::table('zk_user')->insert([
            'username' => $param['username'],
            'password' => $param['password'],
            'head_url' => $param['head_url'],
            'vip' => $param['vip'],
            'abstract' => $param['abstract'],
            'status' => $param['status'],
            'phone' => $param['phone'],
            'time' => time(),
            'is_delete' => 0,
        ]);
        if($row < 1){
            return [501,'操作失败'];
        }
        return [200,true];
    }


    /** 
     * @api {post} /admin.php/admin/user/update  后台用户修改
     * @param int     id vip status
     * @param string  username password head_url
	 *
     * @return array
     */
    public function updateApi()
    {
        $param = input('post.');
        // 验证器
        $error = $this->validate($param,'User.update');
        if (true !== $error) {
            return [501,$error];
        }

        // 判断用户是否存在
        // $user = Db::table('zk_user')->where('id',$param['id'])->where('is_delete',0)->find();
        // if(!$user){
        //     return [501,'该用户不存在'];
        // }


        // 修改用户
        $row = Db::table('zk_user') ->where('id', $param['id'])->update([
            'username' => $param['username'],
            'head_url' => $param['head_url'],
            'vip' => $param['vip'],
            'abstract' => $param['abstract'],
            'status' => $param['status'],
            'phone' => $param['phone'],
            'is_delete' => 0,
        ]);
        if($row == 0){
            return [501,'操作失败'];
        }
        return [200,true];
    }


	/** 
     * @api {post} /admin.php/admin/user/info  后台用户信息
     * @param int     id 
	 *
     * @return array
     */
    public function infoApi()
    {
        $param = input('post.');
        $info = Db::table('zk_user')->where('id',$param['id'])->where('is_delete',0)->field("id,username,phone,head_url,vip,abstract,time,status")->find();
        $info['time'] = date( "Y-m-d H:i:s",$info['time']);

        if(!$info){
            return [501,'数据错误'];
        }
        return [200,$info];
    }

    /** 
     * @api {post} /admin.php/admin/user/pass 后台用户修改
     * @param int     id 
     * @param string  password
     *
     * @return array
     */
    public function passupApi()
    {
        $param = input('post.');
      
        // 判断用户是否存在
        $user = Db::table('zk_user')->where('id',$param['id'])->where('is_delete',0)->find();
        if(!$user){
            return [501,'该用户不存在'];
        }
        // 修改用户
        $row = Db::table('zk_user') ->where('id', $param['id'])->update([
            'password' => $param['password'],
        ]);
        if($row == 0){
            return [501,'操作失败，或密码已存在'];
        }
        return [200,true];
    }

	/** 
     * @api {post} /admin.php/admin/user/delete  后台用户删除
     * @param int     id 
	 *
     * @return array
     */
    public function deleteApi()
    {
        $param = input('post.');
        $info = Db::table('zk_user')->where('id',$param['id'])->where('is_delete',0)->find();
        if(!$info){
            return [501,'数据错误'];
        }
        // 删除数据
        $row = Db::table('zk_user')->where('id',$param['id'])->update(['is_delete' => 1]);
        if($row < 1){
            return [501,'删除失败'];
        }
        return [200,true];
    }

    /** 
     * @api {post} /admin.php/admin/user/collectInfo  后台收藏信息
     * @param int     id 
     *
     * @return array
     */
    public function collectInfoApi()
    {
        $param = input('post.');
        $info = Db::table('zk_user')->where('id',$param['id'])->where('is_delete',0)->field("id,collect")->find();
        if(!$info){
            return [501,'数据错误'];
        }
        if($info['collect'] == ''){
            return [200,['total'=>0, 'collect' => []]];
        }
        $info['collect'] = explode(",",$info['collect']);
        $total = count($info['collect']);
        foreach($info['collect'] as &$v){
            $v = Db::table('zk_content')->where('id',$v)->where('is_delete',0)->field("id,title,url")->select();
        }
        $info['collect'] = array_reduce($info['collect'], 'array_merge', array());
        return [200,['total'=>$total,'info'=>$info]];
    }

    /** 
     * @api {post} /admin.php/admin/user/collectInfo  后台收藏信息
     * @param int     id 
     *
     * @return array
     */
    public function downloadInfoApi()
    {
        $param = input('post.');
        $info = Db::table('zk_user')->where('id',$param['id'])->where('is_delete',0)->field("id,download")->find();
        if(!$info){
            return [501,'数据错误'];
        }
        if($info['download'] == ''){
            return [200,['total'=>0, 'download' => []]];
        }
        $info['download'] = explode(",",$info['download']);
        $total = count($info['download']);
        foreach($info['download'] as &$v){
            $v = Db::table('zk_content')->where('id',$v)->where('is_delete',0)->field("id,title,url")->select();
        }
        $info['download'] = array_reduce($info['download'], 'array_merge', array());
        return [200,['total'=>$total,'info'=>$info]];
    }
}
