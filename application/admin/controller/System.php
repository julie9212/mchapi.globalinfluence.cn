<?php
namespace app\admin\controller;

    /** 
     * @api {post} /admin.php/admin/system/upload  后台图片上传
     * @param int     id vip status
     * @param string  username password head_url
	 *
     * @return array
     */
class System extends Error
{
    public function uploadApi()
    {
       // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file('file');

        if(!$file){
            return [501,'图片不存在，上传失败'];
        }

        // 移动到框架应用根目录/uploads/ 目录下
        $info = $file->move( 'uploads');
        if(!$info){
            return [501,$file->getError()];
        }
        $name = $info->getSaveName();

        return [200,'http://uploads.globalinfluence.cn/'.$name];
    }

}
