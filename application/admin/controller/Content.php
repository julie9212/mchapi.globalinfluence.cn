<?php
namespace app\admin\controller;
use think\Db;

class Content extends Error
{

    /** 
     * @api {post} /admin.php/admin/content/list  后台用户列表
     * @param int  page null
     * 
     * @return array
     */
    public function listApi()
    {
        $param = input('post.');

        // return [200,['param'=>$param]];
        $column = Db::table('zk_column')->where('is_delete',0)->where('pid',"neq",0)->field("id,name,pid")->order('pid')->select();
        foreach($column as &$v){
            $v['pid_name'] = Db::table('zk_column')->where('is_delete',0)->where('id',$v['pid'])->value('name');
        }

        if($param['id'] == 'insert'){   // 添加内容
            return [200,['column'=>$column,'column_id'=>$param['column']]];
        }else{                          // 修改内容
            $info = Db::table('zk_content')->where('id',$param['id'])->where('is_delete',0)->field("id,title,title_small,source,writer,sort,good,authority,examine,keyword,abstract,column_id,img_url,video_url,file_url,content,user_id,url,is_url")->find();

            return [200,['column'=>$column,'column_id'=>$param['column'],'info'=>$info]];
        }

        return [200,['column'=>$column,'column_id'=>$param['column']]];
    }

    /** 
     * @api {post} /admin.php/admin/content/insert  后台内容添加
     * @param int     vip status
     * @param string  username password head_url
     *
     * @return array
     */
    public function insertApi()
    {
        $param = input('post.');
        // return [200,['true'=>$param]];
        // 判断是否存在
        $user = Db::table('zk_content')->where('title',$param['title'])->find();
        if($user){
            return [501,'标题已存在'];
        }

        // 添加用户
        $row = Db::table('zk_content')->insert([
            'title' => $param['title'],
            'title_small' => $param['title_small'],
            'source' => $param['source'],
            'writer' => $param['writer'],
            'sort' => $param['sort'],
            'good' => $param['good'],
            'authority' => $param['authority'],
            'examine' => 2,
            'keyword' => $param['keyword'],
            'abstract' => $param['abstract'],
            'column_id' => $param['column_id'],
            'img_url' => $param['img_url'],
            'video_url' => $param['video_url'],
            'file_url' => $param['file_url'],
            'create_time' => time(),
            'content' => $param['content'],
            'is_url' => $param['is_url'],
            'url' => $param['url'],
            'user_id' => 0,
            'is_delete' =>0,
        ]);
        if($row < 1){
            return [501,'操作失败'];
        }
        return [200,true];
    }

    /** 
     * @api {post} /admin.php/admin/user/update  后台内容修改
     * @param int     id vip status
     * @param string  username password head_url
     *
     * @return array
     */
    public function updateApi()
    {
        $param = input('post.');
        $param = $param['form'];
        // return [200,['param'=>$param['form']]];
       // 判断是否存在
        $row = Db::table('zk_content')->where('id', $param['id'])->count();
        if($row<1){
            return [501,'修改目标不存在'];
        }


        // 修改
        $info = Db::table('zk_content') ->where('id', $param['id'])->update($param);
        if($info == 0){
            return [501,'操作失败'];
        }
        return [200,true];
    }

}
