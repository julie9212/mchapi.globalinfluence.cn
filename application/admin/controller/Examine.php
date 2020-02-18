<?php
namespace app\admin\controller;
use think\Db;

class Examine extends Error
{
    /** 
     * @api {post} /admin.php/admin/application/list  后台审核文章列表
     * @param int  examine
     * 
     * @return array
     */
    public function listApi()
    {
        $param = input('post.');
        $page = $param['page'] ?? 1;


         //模糊搜索
        if (!empty($param['search'] && $param['searchvalue'])) {
            $where[] = [$param['search'],'like','%'.$param['searchvalue'].'%'];
        }else{
            $where = '';
        }
        // 查看是否审核
        if($param['examine']){
            if($param['examine'] !== 'all'){
                $data['examine'] = $param['examine'];
            }
        }
        $data['is_delete'] = 0;
        $total = Db::table('zk_content')->where('is_delete',0)->where($where)->where($data)->count();
        if($total == 0){
            return [200,['total'=>0,'data'=>[]]];
        }
        $column = Db::table('zk_column')->where('is_delete',0)->where('pid',"neq",0)->field("id,name,pid")->order('pid')->select();
        foreach($column as &$v){
            $v['pid_name'] = Db::table('zk_column')->where('is_delete',0)->where('id',$v['pid'])->value('name');
        }
        $data = Db::table('zk_content')->where('is_delete',0)->where($where)->where($data)->field("id,title,create_time,sort,column_id,examine,examine_reason,authority,writer")->order(['create_time'=>'desc'])->page($page,10)->select();

       foreach($data as &$v){
            $v['create_time'] = date( "Y-m-d H:i:s",$v['create_time']);
        }
        return [200,['total'=>$total,'data'=>$data,'column'=>$column]];
    }


    /** 
     * @api {post} /admin.php/admin/examine/examine  后台审核文章审核
     * @param int      id,sort
              string   name
     * 
     * @return array
     */
    public function examineApi()
    {
        $param = input('post.');

        // 判断是否存在
        $info = Db::table('zk_content')->where('is_delete',0)->where('id',$param['id'])->count();
        if(!$info){
            return [501,'请求不存在'];
        }

        // 修改
        $row = Db::table('zk_content')->where('id', $param['id'])->update(['examine' => $param['examine'],'examine_reason' => $param['examine_reason']]);
        if($row < 1){
            return [200,'修改失败'];
        }
        return [200,[true]];
    }


    /** 
     * @api {post} /admin.php/admin/examine/delete  后台审核文章删除
     * @param int     id 
     *
     * @return array
     */
    public function deleteApi()
    {
        $param = input('post.');
        // 查看是存在
        $row = Db::table('zk_content')->where('id',$param['id'])->where('is_delete',0)->count();
        if($row < 1){
            return [501,'请求不存在'];
        }       
        // 删除数据
        $info = Db::table('zk_content')->where('id',$param['id'])->update(['is_delete' => 1]);
        if($info < 1){
            return [501,'删除失败'];
        }
        return [200,true];
    }

}
