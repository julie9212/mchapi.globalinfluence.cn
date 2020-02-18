<?php
namespace app\admin\controller;
use think\Db;

class Listcontent extends Error
{
    // 顶级栏目操作
    /** 
     * @api {post} /admin.php/admin/listcontent/list  后台内容列表
     * @param int     id   page
     *        string  search,searchvalue 
     * @return array
     */
    public function listApi()
    {
        $param = input('post.');
        $page = $param['page'] ?? 1;

        //模糊搜索
        if (!empty($param['search'] && $param['searchvalue'])) {
            // $where['title'] = $param['title'];
            $where[] = [$param['search'],'like','%'.$param['searchvalue'].'%'];
        }else{
            $where = '';
        }

        $column = Db::table('zk_column')->where('is_delete',0)->where('pid',"neq",0)->field("id,name,pid")->order('pid')->select();
        foreach($column as &$v){
            $v['pid_name'] = Db::table('zk_column')->where('is_delete',0)->where('id',$v['pid'])->value('name');
        }

        $total = Db::table('zk_content')->where('is_delete',0)->where($where)->where('examine',2)->where('column_id',$param['id'])->count();
        if($total == 0){
            return [200,['total'=>0,'data'=>[]]];
        }
        $data = Db::table('zk_content')->where('is_delete',0)->where($where)->where('examine',2)->where('column_id',$param['id'])->field("id,title,create_time,sort,column_id,examine,examine_reason,authority,writer")->order(['sort'=>'desc','id'=>'desc'])->page($page,10)->select();

       foreach($data as &$v){
            $v['create_time'] = date( "Y-m-d H:i:s",$v['create_time']);
        }
        return [200,['total'=>$total,'data'=>$data,'column'=>$column,'column_id'=>$param['id']]];
    }

    /** 
     * @api {post} /admin.php/admin/listcontent/info  后台修改内容信息
     * @param int      id
     * 
     * @return array
     */
    public function infoApi()
    {
        $param = input('post.');

        $info = Db::table('zk_content')->where('id',$param['id'])->where('is_delete',0)->field("id,title,sort,authority,column_id,examine,examine_reason,writer")->find();
        if(!$info){
            return [501,'内容不存在'];  
        }
        
        return [200,$info];
    }

    /** 
     * @api {post} /admin.php/admin/listcontent/update  后台内容修改
     * @param int      id,sort,authority,column_id
              string   title
     * 
     * @return array
     */
    public function updateApi()
    {
        $param = input('post.');

        // 判断内容是否存在
        $column = Db::table('zk_content')->where('is_delete',0)->where('id',$param['id'])->find();
        if(!$column){
            return [501,'该内容不存在'];
        }
        // 修改栏目
        $row = Db::table('zk_content')->where('id', $param['id'])->update([
            'title' => $param['title'],
            'writer' => $param['writer'],
            'sort' => $param['sort'],
            'authority' => $param['authority'],
            'examine' => $param['examine'],
            'column_id' => $param['column_id'],
            'examine_reason' => $param['examine_reason'],
        ]);
        if($row < 1){
            return [200,'修改内容失败'];
        }
        return [200,[true]];
    }

    /** 
     * @api {post} /admin.php/admin/listcontent/delete  后台内容删除
     * @param int     id 
     *
     * @return array
     */
    public function deleteApi()
    {
        $param = input('post.');
        $info = Db::table('zk_content')->where('id',$param['id'])->where('is_delete',0)->find();
        if(!$info){
            return [501,'数据错误'];
        }    
        // 删除数据
        $row = Db::table('zk_content')->where('id',$param['id'])->update(['is_delete' => 1]);
        if($row < 1){
            return [501,'删除失败'];
        }
        return [200,true];
    }

}
