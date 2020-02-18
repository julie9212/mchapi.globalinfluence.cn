<?php
namespace app\home\controller;
use think\Db;

class Models extends Error
{
	/** 
     * @api {post} /index.php/home/models/list  导航
     * @param int  
     * 
     * @return array
     */
    public function listApi()
    {
        $param = input('post.');
        $page = $param['page'] ?? 1;
        
        $id = $param['id'];
        $pid = $param['pid'];

        $where['is_delete'] = 0;
        $where['examine'] = 2;

        $template = Db::table('zk_column')->where('is_delete',0)->where('id',$id)->value('template');

        if($pid == 0 || $pid == ''){
            // 顶级栏目查询全部子栏目数据,pid=0为顶级栏目
            $column = Db::table('zk_column')->where('is_delete',0)->where('pid',$id)->field("id,name,pid,template")->order('sort')->select();
            $sub = Db::table('zk_column')->where('is_delete',0)->where('pid',$id)->order('sort')->column('id');
            $total = Db::table('zk_content')->where($where)->where(['column_id'=> $sub])->count();
            $info = Db::table('zk_content')->where($where)->where(['column_id'=> $sub])->field("id,title,title_small,source,writer,create_time,img_url,abstract,video_url,url,is_url")->order(['sort'=>'desc','id'=>'desc'])->page($page,10)->select();
            $bannerNum = $id;
        }else if($pid != 0){
            $col_pid = Db::table('zk_column')->where('is_delete',0)->where('id',$id)->value('pid');
            if($pid != $col_pid){
                return [500 , '栏目不存在'];
            }
            // 二级栏目
            $column = Db::table('zk_column')->where('is_delete',0)->where('pid',$pid)->field("id,name,pid,template")->order('sort')->select();
            $total = Db::table('zk_content')->where($where)->where(['column_id'=> $id])->count();
            $info = Db::table('zk_content')->where($where)->where(['column_id'=> $id])->field("id,title,title_small,source,writer,create_time,img_url,abstract,video_url,url,is_url")->order(['sort'=>'desc','id'=>'desc'])->page($page,10)->select();
            $bannerNum = $pid;
        }else{
            return [500 , '栏目错误'];
        }

        // right
        $meeting =  Db::table('zk_content')->where($where)->where('column_id',49)->field("id,title,source,writer")->order(['sort'=>'desc','id'=>'desc'])->page(1,3)->select();
        $ad2 = Db::table('zk_ad')->where('is_delete',0)->where('type',2)->field("id,title,img_url,url")->page(1,4)->select();
        $banner = Db::table('zk_ad')->where('is_delete',0)->where('type',5)->where('sort',$bannerNum)->field("id,title,img_url,url")->page(1,1)->find();
        if(!$banner){
            $banner = '没有上传banner';
        }

        return [200 , ['total'=>$total,'template'=>$template,'column'=>$column,'info'=>$info,'meeting'=>$meeting,'ad2'=>$ad2,'banner'=>$banner]];
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
        $info = Db::table('zk_content')->where('is_delete',0)->where('examine',2)->where('column_id',$param['id'])->field("id,title,title_small,source,writer,create_time,img_url,abstract")->order(['sort'=>'desc','id'=>'desc'])->page($page,10)->select(); 

        $banner = Db::table('zk_ad')->where('is_delete',0)->where('type',5)->where('sort',6)->field("id,title,img_url,url")->page(1,1)->find();
        if(!$banner){
            $banner = '没有上传banner';
        }

        return [200 , ['total'=>$total,'data'=>$data,'info'=>$info,'id'=>$param['id'],'banner'=>$banner]];
    }



    public function aboutProfileApi()
    {
        $param = input('post.');
        $data = Db::table('zk_column')->where('is_delete',0)->where('pid',$param['id'])->order('sort')->select();

        $info = Db::table('zk_content')->where('is_delete',0)->where('examine',2)->where('column_id',$data[0]['id'])->field("id,title,source,writer,create_time,img_url,abstract,content")->order(['sort'=>'desc','id'=>'desc'])->find(); 

        // right
        $where['is_delete'] = 0;
        $where['examine'] = 2;
        $meeting =  Db::table('zk_content')->where($where)->where('column_id',49)->field("id,title,source,writer")->order(['sort'=>'desc','id'=>'desc'])->page(1,3)->select();
        $ad2 = Db::table('zk_ad')->where('is_delete',0)->where('type',2)->field("id,title,img_url,url")->page(1,4)->select();

        $banner = Db::table('zk_ad')->where('is_delete',0)->where('type',5)->where('sort',6)->field("id,title,img_url,url")->page(1,1)->find();
        if(!$banner){
            $banner = '没有上传banner';
        }

        return [200 , ['data'=>$data,'info'=>$info,'meeting'=>$meeting,'ad2'=>$ad2,'banner'=>$banner]];
    }


    // 组织架构
    public function aboutFrameworkApi()
    {
        $param = input('post.');
        $data = Db::table('zk_column')->where('is_delete',0)->where('pid',6)->order('sort')->select();

        // right
        $where['is_delete'] = 0;
        $where['examine'] = 2;
        $meeting =  Db::table('zk_content')->where($where)->where('column_id',49)->field("id,title,source,writer")->order(['sort'=>'desc','id'=>'desc'])->page(1,3)->select();
        $ad2 = Db::table('zk_ad')->where('is_delete',0)->where('type',2)->field("id,title,img_url,url")->page(1,4)->select();

         $banner = Db::table('zk_ad')->where('is_delete',0)->where('type',5)->where('sort',6)->field("id,title,img_url,url")->page(1,1)->find();
        if(!$banner){
            $banner = '没有上传banner';
        }

        return [200 , ['data'=>$data,'meeting'=>$meeting,'ad2'=>$ad2,'banner'=>$banner]];
    }
    /** 
     * @api {post} /index.php/home/models/aboutform 
     * @param int      name,pid,sort
              string   name
     * 
     * @return array
     */
    public function aboutFormApi()
    {
        $param = input('post.');
        $data = Db::table('zk_column')->where('is_delete',0)->where('pid',6)->order('sort')->select();

        // right
        $where['is_delete'] = 0;
        $where['examine'] = 2;
        $meeting =  Db::table('zk_content')->where($where)->where('column_id',49)->field("id,title,source,writer")->order(['sort'=>'desc','id'=>'desc'])->page(1,3)->select();
        $ad2 = Db::table('zk_ad')->where('is_delete',0)->where('type',2)->field("id,title,img_url,url")->page(1,4)->select();

         $banner = Db::table('zk_ad')->where('is_delete',0)->where('type',5)->where('sort',6)->field("id,title,img_url,url")->page(1,1)->find();
        if(!$banner){
            $banner = '没有上传banner';
        }

        return [200 , ['data'=>$data,'meeting'=>$meeting,'ad2'=>$ad2,'banner'=>$banner]];
    }

    /** 
     * @api {post} /index.php/home/models/aboutform   后台顶级栏目添加
     * @return array
     */
    public function insertFormApi()
    {
        $param = input('post.');
        $param['create_time'] = time();
        $param['is_delete'] = 0;
        $param['examine'] = 1;

        // 添加栏目
        $row = Db::table('zk_application')->insert($param);
        if($row < 1){
            return [200,'添加栏目失败'];
        }
        return [200,true];
    }

}
