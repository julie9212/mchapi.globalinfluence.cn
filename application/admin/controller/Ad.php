<?php
namespace app\admin\controller;
use think\Db;

class Ad extends Error
{
    // 顶级栏目操作
    /** 
     * @api {post} /admin.php/admin/ad/list  后台广告图栏目列表
     * @param int  type
     * 
     * @return array
     */
    public function listApi()
    {
        $param = input('post.');
        $page = $param['page'] ?? 1;
        if($param['type']){
            if($param['type'] !== 'all'){
                $where['type'] = $param['type'];
            }
        }
        $where['is_delete'] = 0;
        $total = Db::table('zk_ad')->where($where)->count();
        if($total == 0){
            return [200,['total'=>0,'data'=>[]]];
        }
        $data = Db::table('zk_ad')->where($where)->field("id,title,update_time,url,sort,img_url,type")->order(['type','sort'=>'desc'])->page($page,10)->select();
        foreach($data as &$v){
            $v['update_time'] = date( "Y-m-d H:i:s",$v['update_time']);
        }
        return [200,['total'=>$total,'data'=>$data]];
    }

    /** 
     * @api {post} /admin.php/admin/ad/insert  后台广告图添加
     * @param int      sort
              string   name
     * 
     * @return array
     */
    public function insertApi()
    {
        $param = input('post.');
        // 判断栏目是否存在
        $column = Db::table('zk_ad')->where('is_delete',0)->where('title',$param['title'])->count();
        if($column){
            return [501,'该名称已存在'];
        }

        // 添加栏目
        $row = Db::table('zk_ad')->insert([
            'title' => $param['title'],
            'update_time' => time(),
            'sort' => $param['sort'],
            'url' => $param['url'],
            'img_url' => $param['img_url'],
            'type' => $param['type'],
            'is_delete' => 0,
        ]);
        if($row < 1){
            return [200,'添加失败'];
        }
        return [200,true];
    }

    /** 
     * @api {post} /admin.php/admin/ad/info  后台广告图信息
     * @param int      id
     * 
     * @return array
     */
    public function infoApi()
    {
        $param = input('post.');
        $info = Db::table('zk_ad')->where('id',$param['id'])->where('is_delete',0)->field("id,title,update_time,url,sort,img_url,type")->find();
        if(!$info){
            return [501,'请求不存在'];  
        }
        
        return [200,$info];
    }

    /** 
     * @api {post} /admin.php/admin/ad/update  后台广告图修改
     * @param int      id,sort
              string   name
     * 
     * @return array
     */
    public function updateApi()
    {
        $param = input('post.');

        // 判断是否存在
        $info = Db::table('zk_ad')->where('is_delete',0)->where('id',$param['id'])->count();
        if(!$info){
            return [501,'请求不存在'];
        }

        // 修改
        $row = Db::table('zk_ad')->where('id', $param['id'])->update([
            'title' => $param['title'],
            'update_time' => time(),
            'sort' => $param['sort'],
            'url' => $param['url'],
            'img_url' => $param['img_url'],
            'type' => $param['type'],
            'is_delete' => 0,
        ]);
        if($row < 1){
            return [200,'修改失败'];
        }
        return [200,[true]];
    }


    /** 
     * @api {post} /admin.php/admin/ad/delete  后台广告图删除
     * @param int     id 
     *
     * @return array
     */
    public function deleteApi()
    {
        $param = input('post.');
        // 查看是存在
        $row = Db::table('zk_ad')->where('id',$param['id'])->where('is_delete',0)->count();
        if($row < 1){
            return [501,'请求不存在'];
        }       
        // 删除数据
        $info = Db::table('zk_ad')->where('id',$param['id'])->update(['is_delete' => 1]);
        if($info < 1){
            return [501,'删除失败'];
        }
        return [200,true];
    }

}
