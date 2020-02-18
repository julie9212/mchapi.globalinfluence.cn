<?php
namespace app\home\controller;
use think\Db;

class Content extends Error
{
	/** 
     * @api {post} /index.php/home/content/index  导航
     * @param int  
     * 
     * @return array
     */
       public function indexApi()
    {
        $param = input('post.');

        $where['is_delete'] = 0;
        $where['examine'] = 2;
            
        $count = Db::table('zk_content')->where($where)->where('id',$param['id'])->count();
        if($count < 1){
            return [200 ,'数据不存在'];
        }
        $info = Db::table('zk_content')->where($where)->where('id',$param['id'])->field("content")->find();

        // $user = Db::table('zk_user')->where('id',$info['user_id'])->field("id,head_url,username,abstract,vip")->find();
        // $user_total = Db::table('zk_content')->where($where)->where('user_id',$user['id'])->count();
        // $user_article = Db::table('zk_content')->where($where)->where('user_id',$user['id'])->order(['sort'=>'desc','id'=>'desc'])->page(1,3)->select();
        // foreach($user_article as &$v){
        //     $v['create_time'] = date( "Y-m-d H:i",$v['create_time']);
        // }

        return [200 , ['info'=>$info]];
    }

    public function infoApi()
    {
        $param = input('post.');

        $where['is_delete'] = 0;
        $where['examine'] = 2;
        $info = Db::table('zk_content')->where($where)->where('id',$param['id'])->field("id,title,source,writer,create_time,user_id,source,keyword,abstract,img_url,file_url,video_url")->find();
        $info['create_time'] = date( "Y-m-d H:i",$info['create_time']);

        // right
        $meeting =  Db::table('zk_content')->where($where)->where('column_id',49)->field("id,title,source,writer")->order(['sort'=>'desc','id'=>'desc'])->page(1,3)->select();
        $ad2 = Db::table('zk_ad')->where('is_delete',0)->where('type',2)->field("id,title,img_url,url")->page(1,4)->select();
        $ad1 = Db::table('zk_ad')->where('is_delete',0)->where('type',1)->field("id,title,img_url,url")->page(1,1)->find();

        return [200 , ['info'=>$info,'meeting'=>$meeting,'ad2'=>$ad2,'ad1'=>$ad1]];
    }

    /** 
     * @api {post} /index.php/home/model/aboutList  关于我们列表
     * @param int  
     * 
     * @return array
     */
    public function aboutListApi()
    {
        $param = input('post.');
        $page = $param['page'] ?? 1;

        $data = Db::table('zk_column')->where('is_delete',0)->where('pid',6)->order('sort')->select();
        
        $total = Db::table('zk_content')->where('is_delete',0)->where('examine',2)->where('column_id',$param['id'])->count(); 
        $info = Db::table('zk_content')->where('is_delete',0)->where('examine',2)->where('column_id',$param['id'])->field("id,title,source,writer,create_time,img_url,file_urlabstract")->order(['sort'=>'desc','id'=>'desc'])->page($page,10)->select(); 

        return [200 , ['total'=>$total,'data'=>$data,'info'=>$info,'id'=>$param['id']]];
    }


    /** 
     * @api {post} /index.php/home/content/insertform   meeting表单提交
     * @return array
     */
    public function insertMeetingApi()
    {
        $param = input('post.');
        $param['create_time'] = time();
        $param['is_delete'] = 0;
        $param['examine'] = 1;

        // 添加栏目
        $row = Db::table('zk_meeting')->insert($param);
        if($row < 1){
            return [200,'添加栏目失败'];
        }
        return [200,true];
    }

}
