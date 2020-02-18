<?php
namespace app\home\controller;
use think\Db;
use think\facade\Cache;
use think\Cookie;
use MESSAGEXsend;

class Code extends Error
{
	/** 
     * @api {post} /index.php/home/code/code
     * @param int  
     * 
     * @return array
     */
    public function CodeApi()
    {
        $param = input('post.');
        // return [200,['param' => $param]];
        $phone = $param['phone'];
        $rands = rand(1000,9999);

        //引入文件
        require "../extend/Hprose/app_config.php";
        require_once("../extend/Hprose/SUBMAILAutoload.php");
        $submail=new MESSAGEXsend($message_configs);

        //调用信息
        $submail->setTo($phone);
        $submail->SetProject('clDlQ3');
        $submail->AddVar('code',$rands);
        $xsend=$submail->xsend();


        if(!empty($xsend)){
            // Cookie::set('code',$rands,60);
            // Cookie::set('code',$rands,180);
            Cache::set('code',$rands,180);
        }else{
            return [300,'验证码发送失败，请重新发送'];
        }


        return [200,'验证码发送成功'];
    }
}
