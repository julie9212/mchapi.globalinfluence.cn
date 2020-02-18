<?php
namespace app\admin\controller;
use think\Db;

class Meeting extends Error
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
        if($param['examine']){
            if($param['examine'] !== 'all'){
                $where['examine'] = $param['examine'];
            }
        }
        $where['is_delete'] = 0;
        $total = Db::table('zk_meeting')->where($where)->count();
        if($total == 0){
            return [200,['total'=>0,'data'=>[]]];
        }
        $data = Db::table('zk_meeting')->where($where)->field("id,meeting,name,phone,email,company,post,create_time,examine")->order(['create_time'=>'desc'])->page($page,10)->select();
        foreach($data as &$v){
            $v['create_time'] = date( "Y-m-d H:i:s",$v['create_time']);
        }
        return [200,['total'=>$total,'data'=>$data]];
    }

    public function examineApi()
    {
        $param = input('post.');

        // 判断是否存在
        $info = Db::table('zk_meeting')->where('is_delete',0)->where('id',$param['id'])->count();
        if(!$info){
            return [501,'请求不存在'];
        }

        // 修改
        $row = Db::table('zk_meeting')->where('id', $param['id'])->update(['examine' => $param['examine'],]);
        if($row < 1){
            return [200,'修改失败'];
        }
        return [200,[true]];
    }

}
