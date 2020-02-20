<?php
namespace app\home\controller;
use think\Db;

class Index extends Error
{
	/** 
     * @api {post} /index.php/home/index/index  首页信息
     * @param int  
     * 
     * @return array
     */
    public function indexApi()
    {
        // return [501,'数据'];
        $param = input('post.');
        // $id = $param['id'];
        // $pid = $param['pid'];

        // if($id == 0 && $pid == 0){
        //     $columnName = '首页';
        // }else if($pid == 0 && $id != 0){
        //     $columnName =Db::table('zk_column')->where('is_delete',0)->where('id',$id)->value('name');
        // }else if($pid != 0 && $id != 0){
        //     $columnName =Db::table('zk_column')->where('is_delete',0)->where('id',$pid)->value('name');
        // }


        // 栏目
        $total['column'] = Db::table('zk_column')->where('is_delete',0)->where('pid',0)->count();
         if ($total['column'] == 0) {
            return [200,['total'=>0, 'data' => []]];
        }
        $column = Db::table('zk_column')->where('is_delete',0)->where('pid',0)->field("id,name,pid,sort,template")->order('sort')->select();

        // 参数设置
        $total['admininfo'] = Db::table('zk_admininfo')->where('id',1)->count();
         if ($total['admininfo'] == 0) {
            return [200,['total'=>0, 'data' => []]];
        }
        $admininfo = Db::table('zk_admininfo')->field("id,title,logo,keywords,description,phone,phone2,email,address,copyright")->find();

        return [200 , ['admininfo'=>$admininfo,'column'=>$column]];
    }


        /** 
     * @api {post} /index.php/home/index/nav  导航信息
     * @param int  
     * 
     * @return array
     */
    public function indexNavApi()
    {
        $param = input('post.');
        $id = $param['id'];
        $pid = $param['pid'];

        if($id == 0 && $pid == 0){
            $columnName = 0;
        }else if($pid == 0 && $id != 0){
            $columnName =Db::table('zk_column')->where('is_delete',0)->where('id',$id)->value('id');
        }else if($pid != 0 && $id != 0){
            $columnName =Db::table('zk_column')->where('is_delete',0)->where('id',$pid)->value('id');
        }

        return [200 , ['param'=>$param,'columnName'=>$columnName]];
    }

    /** 
     * @api {post} /index.php/home/index/index  首页信息
     * @param int  
     * 
     * @return array
     */
    public function indexListApi()
    {
        // 内容（按一级栏目划分）
        $column_list = Db::table('zk_column')->where('is_delete',0)->where('pid',0)->field("id,pid,name")->select();

        foreach($column_list as &$v){
            $v['sub_column'] = Db::table('zk_column')->where('is_delete',0)->where('pid',$v['id'])->field("id,name")->select();
            foreach($v['sub_column'] as &$value){
            	$value['content'] = Db::table('zk_content')->where('is_delete',0)->where('examine',2)->where('column_id',$value['id'])->field("id,title,source,writer,create_time,img_url")->order(['sort'=>'desc','id'=>'desc'])->page(1,6)->select();
            	foreach($value['content'] as &$vs){
		            $vs['create_time'] = date( "Y-m-d H:i:s",$vs['create_time']);
		        }
            }
        }
        $where['is_delete'] = 0;
        $where['examine'] = 2;

        $banner = Db::table('zk_content')->where($where)->where('column_id',7)->field("id,title,source,writer,create_time,img_url")->order(['sort'=>'desc','id'=>'desc'])->page(1,8)->select();
        // 智库新闻
        $news = Db::table('zk_content')->where($where)->where('column_id',7)->field("id,title,source,writer,create_time,img_url,url,is_url")->order(['sort'=>'desc','id'=>'desc'])->page(1,5)->select();
    	foreach($news as &$v){
            $v['create_time'] = date( "Y-m-d H:i",$v['create_time']);
        }
        // 智库视频
        $video = Db::table('zk_content')->where($where)->where('column_id',50)->field("id,title,source,writer,create_time,img_url,video_url")->order(['sort'=>'desc','id'=>'desc'])->page(1,3)->select();
        // 研究观点
        $viewpoint =  Db::table('zk_column')->where('is_delete',0)->where('pid',2)->field("id,pid,name,template")->select();
        foreach($viewpoint as &$v){
            $v['content'] = Db::table('zk_content')->where($where)->where('column_id',$v['id'])->field("id,title,source,writer,create_time,img_url")->order(['sort'=>'desc','id'=>'desc'])->page(1,3)->select();
        }
        // 报告厅
        $report =  Db::table('zk_column')->where('is_delete',0)->where('pid',3)->field("id,pid,name,template")->select();
        foreach($report as &$v){
            $v['content'] = Db::table('zk_content')->where($where)->where('column_id',$v['id'])->field("id,title,source,writer,create_time,img_url")->order(['sort'=>'desc','id'=>'desc'])->page(1,4)->select();
        }



        // 智库会议
        $meeting =  Db::table('zk_content')->where($where)->where('column_id',49)->field("id,title,source,writer")->order(['sort'=>'desc','id'=>'desc'])->page(1,3)->select();

        // 媒体报道 
        $media =  Db::table('zk_content')->where($where)->where('column_id',8)->field("id,title,img_url,url,source,url,is_url")->order(['sort'=>'desc','id'=>'desc'])->page(1,6)->select();

        // 报告厅  智库报告id:44,外部报告id:45
        $report[1] = Db::table('zk_content')->where($where)->where('column_id',44)->field("id,title,img_url,url,create_time")->order(['sort'=>'desc','id'=>'desc'])->page(1,3)->select();
        $report[2] = Db::table('zk_content')->where($where)->where('column_id',45)->field("id,title,img_url,url,create_time")->order(['sort'=>'desc','id'=>'desc'])->page(1,3)->select();
        foreach($report[1] as &$v){
            $v['create_time'] = date( "Y-m-d",$v['create_time']);
        }
        foreach($report[2] as &$v){
            $v['create_time'] = date( "Y-m-d",$v['create_time']);
        }

        // 广告
        $ad1 = Db::table('zk_ad')->where('is_delete',0)->where('type',1)->field("id,title,img_url,url")->page(1,1)->find();
        $ad2 = Db::table('zk_ad')->where('is_delete',0)->where('type',2)->field("id,title,img_url,url")->order(['sort','id'=>'desc'])->page(1,4)->select();
        $ad3 = Db::table('zk_ad')->where('is_delete',0)->where('type',3)->field("id,title,img_url,url")->order(['sort','id'=>'desc'])->page(1,4)->select();
        $ad4 = Db::table('zk_ad')->where('is_delete',0)->where('type',4)->field("id,title,img_url,url")->order(['sort','id'=>'desc'])->page(1,100)->select();


        return [200 , ['column_list'=>$column_list,'banner'=>$banner,'news'=>$news,'video'=>$video,'viewpoint'=>$viewpoint,'report'=>$report,'meeting'=>$meeting,'media'=>$media,'report'=>$report,'ad1'=>$ad1,'ad2'=>$ad2,'ad3'=>$ad3,'ad4'=>$ad4]];
    }

}
