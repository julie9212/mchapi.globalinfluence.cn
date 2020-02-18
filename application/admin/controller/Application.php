<?php
namespace app\admin\controller;
use think\Db;

class Application extends Error
{
    // 顶级栏目操作
    /** 
     * @api {post} /admin.php/admin/application/list  后台合作申请列表
     * @param int  examine
     * 
     * @return array
     */
    public function listApi()
    {
        $param = input('post.');
        $page = $param['page'] ?? 1;
        if($param['examine']){
            if($param['examine'] !== 'all'){
                $where['examine'] = $param['examine'];
            }
        }
        
        $where['is_delete'] = 0;
        
        $total = Db::table('zk_application')->where($where)->count();
        if($total == 0){
            return [200,['total'=>0,'data'=>[]]];
        }

        $data = Db::table('zk_application')->where($where)->field("id,head_url,name,sex,phone,email,introduce,company,post,examine,create_time")->order(['id'=>'desc'])->page($page,10)->select();
        foreach($data as &$v){
            $v['create_time'] = date( "Y-m-d H:i:s",$v['create_time']);
        }
        return [200,['total'=>$total,'data'=>$data]];
    }


    /** 
     * @api {post} /admin.php/admin/application/info  后台合作申请信息
     * @param int      id
     * 
     * @return array
     */
    public function infoApi()
    {
        $param = input('post.');

        $info = Db::table('zk_application')->where('id',$param['id'])->where('is_delete',0)->field("id,head_url,name,sex,phone,email,introduce,company,post,examine,create_time")->find();
        if(!$info){
            return [501,'请求不存在'];  
        }
        return [200,$info];
    }

    /** 
     * @api {post} /admin.php/admin/application/examine  后台广告图审核
     * @param int      id,sort
              string   name
     * 
     * @return array
     */
    public function examineApi()
    {
        $param = input('post.');

        // 判断是否存在
        $info = Db::table('zk_application')->where('is_delete',0)->where('id',$param['id'])->count();
        if(!$info){
            return [501,'请求不存在'];
        }

        // 修改
        $row = Db::table('zk_application')->where('id', $param['id'])->update(['examine' => $param['examine'],]);
        if($row < 1){
            return [200,'修改失败'];
        }
        return [200,[true]];
    }


    /** 
     * @api {post} /admin.php/admin/application/delete  后台广告图删除
     * @param int     id 
     *
     * @return array
     */
    public function deleteApi()
    {
        $param = input('post.');
        // 查看是存在
        $row = Db::table('zk_application')->where('id',$param['id'])->where('is_delete',0)->count();
        if($row < 1){
            return [501,'请求不存在'];
        }       
        // 删除数据
        $info = Db::table('zk_application')->where('id',$param['id'])->update(['is_delete' => 1]);
        if($info < 1){
            return [501,'删除失败'];
        }
        return [200,true];
    }

}
