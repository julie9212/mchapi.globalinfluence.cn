<?php
namespace app\admin\controller;
use think\Db;

class Subcolumn extends Error
{
    // 顶级栏目操作
    /** 
     * @api {post} /admin.php/admin/subcolumn/list  后台二级栏目列表
     * @param int id(column传过来的id)
     * 
     * @return array total,data,column
     */
    public function listApi()
    {
        $param = input('post.');
         // return [200,'idyou'=>$param];

        $where['is_delete'] = 0;

        $column = Db::table('zk_column')->where('is_delete',0)->where('pid',0)->field("id,name,sort")->order('sort')->select();

        $total = Db::table('zk_column')->where('is_delete',0)->where('pid',$param['id'])->count();
        if($total == 0){
            return [200,['total'=>0,'data'=>[],'column'=>$column]];
        }

        $data = Db::table('zk_column')->where('is_delete',0)->where('pid',$param['id'])->field("id,name,create_time,sort,pid,type")->order('sort')->select();
        foreach($data as &$v){
            $v['create_time'] = date( "Y-m-d H:i:s",$v['create_time']);
        }
        return [200,['total'=>$total,'data'=>$data,'column'=>$column]];
    }

    /** 
     * @api {post} /admin.php/admin/subcolumn/insert  后台顶级栏目添加
     * @param int      name,pid,sort
              string   name
     * 
     * @return array
     */
    public function insertApi()
    {
        $param = input('post.');

        // 验证器

        // 判断栏目是否存在
        $column = Db::table('zk_column')->where('is_delete',0)->where('name',$param['name'])->find();
        if($column){
            return [501,'该栏目已存在'];
        }
        // return [501,$param];

        // 添加栏目
        $row = Db::table('zk_column')->insert([
            'name' => $param['name'],
            'create_time' => time(),
            'pid' => $param['pid'],
            'sort' => $param['sort'],
            'type' => 0,
            'is_delete' => 0,
        ]);
        if($row < 1){
            return [200,'添加栏目失败'];
        }
        return [200,true];
    }

    /** 
     * @api {post} /admin.php/admin/subcolumn/info  后台修改栏目信息
     * @param int      id
     * 
     * @return array
     */
    public function infoApi()
    {
        $param = input('post.');

        $where['is_delete'] = 0;

        $info = Db::table('zk_column')->where('id',$param['id'])->where('is_delete',0)->field("id,name,pid,create_time,sort")->find();
        if(!$info){
            return [501,'栏目不存在'];  
        }
        
        return [200,$info];
    }

    /** 
     * @api {post} /admin.php/admin/subcolumn/update  后台顶级栏目修改
     * @param int      id,sort,pid
              string   name
     * 
     * @return array
     */
    public function updateApi()
    {
        $param = input('post.');

        // 判断栏目是否存在
        $column = Db::table('zk_column')->where('is_delete',0)->where('name',$param['name'])->find();
        if($column){
            return [501,'该栏目名称已存在'];
        }

        // 修改栏目

        $row = Db::table('zk_column')->where('id', $param['id'])->update([
            'name' => $param['name'],
            'pid' => $param['pid'],
            'sort' => $param['sort'],
        ]);
        if($row < 1){
            return [200,'修改栏目失败'];
        }
        return [200,[true]];
    }


        /** 
     * @api {post} /admin.php/admin/subcolumn/delete  后台栏目删除
     * @param int     id 
     *
     * @return array
     */
    public function deleteApi()
    {
        $param = input('post.');
        $info = Db::table('zk_column')->where('id',$param['id'])->where('is_delete',0)->find();
        if(!$info){
            return [501,'数据错误'];
        }
        // 查看是否有文章
        $subcolumn = Db::table('zk_content')->where('column_id',$param['id'])->where('is_delete',0)->count();
        if($subcolumn > 0){
            return [501,'请先删除该栏目下的内容'];
        }       
        // 删除数据
        $row = Db::table('zk_column')->where('id',$param['id'])->update(['is_delete' => 1]);
        if($row < 1){
            return [501,'删除失败'];
        }
        return [200,true];
    }

    /** 
     * @api {post} /admin.php/admin/subcolumn/changestatus  后台切换列表/内容状态
     * @param int     id 
     * 备注L：type 0.列表 2.文章
     * @return array
     */
    public function changeTypeApi()
    {
        $id = input('post.id');
        $info = Db::table('zk_column')->where('id',$id)->where('is_delete',0)->find();
        if(!$info){
            return [501,'数据错误'];
        }

        $info['type'] == 0 ? $info['type'] = 1 : $info['type'] = 0;
        $row = Db::table('zk_column')->where('id',$id)->update(['type' => $info['type']]);
        if($row < 1){
            return [501,'数据错误'];
        }
        return [200,true];
    }

}
